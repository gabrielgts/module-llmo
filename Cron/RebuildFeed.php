<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Cron;

use Gtstudio\Llmo\Model\Feed\ExporterPool;
use Gtstudio\Llmo\Model\Feed\FeedPublisher;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * Rebuilds every registered exporter's feed across all stores.
 *
 * No-op when LLMO is globally disabled. Per-store failures are logged by
 * `FeedPublisher::publishAllStores` and do not abort the run.
 */
class RebuildFeed
{
    private const CONFIG_ENABLED = 'llmo/general/enabled';

    // phpcs:ignore
    public function __construct(
        private readonly ExporterPool $exporterPool,
        private readonly FeedPublisher $feedPublisher,
        private readonly LoggerInterface $logger,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function execute(): void
    {
        if (!$this->scopeConfig->isSetFlag(self::CONFIG_ENABLED)) {
            return;
        }

        foreach ($this->exporterPool->all() as $exporter) {
            try {
                $this->feedPublisher->publishAllStores($exporter->code());
            } catch (\Throwable $th) {
                $this->logger->error('[Gtstudio_Llmo] Rebuild cron failed for exporter', [
                    'exception' => $th,
                    'context'   => ['exporter' => $exporter->code()],
                ]);
            }
        }
    }
}
