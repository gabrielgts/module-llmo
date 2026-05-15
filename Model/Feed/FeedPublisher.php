<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Feed;

use Gtstudio\Llmo\Model\FeedRun;
use Gtstudio\Llmo\Model\Validator\ValidationCoordinator;
use Gtstudio\Llmo\Model\Validator\ValidationLogger;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Single entry point for "build + format + write" a feed.
 *
 * Used by both the rebuild cron and the admin Rebuild controller so the
 * lifecycle (FeedRun row, file write, error capture) is identical.
 */
class FeedPublisher
{
    // phpcs:ignore
    public function __construct(
        private readonly ExporterPool $exporterPool,
        private readonly FeedBuilder $feedBuilder,
        private readonly FeedRunRecorder $recorder,
        private readonly FeedWriter $feedWriter,
        private readonly LoggerInterface $logger,
        private readonly StoreManagerInterface $storeManager,
        private readonly ValidationCoordinator $validationCoordinator,
        private readonly ValidationLogger $validationLogger
    ) {
    }

    /**
     * Build, format, write and validate one feed for a single store.
     *
     * @param string $exporterCode
     * @param int $storeId
     * @return FeedRun
     * @throws NoSuchEntityException
     */
    public function publish(string $exporterCode, int $storeId): FeedRun
    {
        $exporter = $this->exporterPool->get($exporterCode);
        $run = $this->recorder->start($exporterCode, $storeId);

        try {
            $feed = $this->feedBuilder->build($storeId, $exporterCode);
            $body = $exporter->format($feed);
            $store = $this->storeManager->getStore($storeId);
            $path = $this->feedWriter->write(
                $exporterCode,
                (string) $store->getCode(),
                $exporter->fileExtension(),
                $body
            );

            $this->recorder->complete($run, $feed->getCount(), $path);
            $this->logger->info('[Gtstudio_Llmo] Feed published', [
                'exporter' => $exporterCode,
                'store_id' => $storeId,
                'count'    => $feed->getCount(),
                'path'     => $path,
            ]);

            $this->runValidation($feed, $storeId, (int) $run->getData('run_id'));

            return $run;
        } catch (\Throwable $th) {
            $this->recorder->fail($run, $th);
            $this->logger->error('[Gtstudio_Llmo] Feed publish failed', [
                'exception' => $th,
                'context'   => ['exporter' => $exporterCode, 'store_id' => $storeId],
            ]);
            throw $th;
        }
    }

    /**
     * Run post-publish validation; never throws. Logs the result row.
     *
     * @param \Gtstudio\Llmo\Api\Data\AiFeedInterface $feed
     * @param int $storeId
     * @param int $runId
     * @return void
     */
    private function runValidation(
        \Gtstudio\Llmo\Api\Data\AiFeedInterface $feed,
        int $storeId,
        int $runId
    ): void {
        try {
            $result = $this->validationCoordinator->validate($feed);
            $this->validationLogger->record($result, $storeId, $runId);
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] Post-publish validation failed', [
                'exception' => $th,
                'context'   => ['store_id' => $storeId, 'run_id' => $runId],
            ]);
        }
    }

    /**
     * Publish feed for every store using the given exporter; never throws.
     *
     * @param string $exporterCode
     * @return FeedRun[]
     */
    public function publishAllStores(string $exporterCode): array
    {
        $runs = [];

        foreach ($this->storeManager->getStores() as $store) {
            try {
                $runs[] = $this->publish($exporterCode, (int) $store->getId());
            } catch (\Throwable $th) {
                $this->logger->error('[Gtstudio_Llmo] Store feed skipped', [
                    'exception' => $th,
                    'context'   => ['exporter' => $exporterCode, 'store_id' => $store->getId()],
                ]);
            }
        }

        return $runs;
    }
}
