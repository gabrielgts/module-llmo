<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Feed;

use Gtstudio\Llmo\Model\FeedRun;
use Gtstudio\Llmo\Model\FeedRunFactory;
use Gtstudio\Llmo\Model\ResourceModel\FeedRun as FeedRunResource;

/**
 * Single entry point for recording feed-run lifecycle (start/finish/fail).
 *
 * Keeps `RebuildFeed` cron and admin `Rebuild` controller free of resource-model
 * boilerplate, and lets us evolve the audit format in one place.
 */
class FeedRunRecorder
{
    // phpcs:ignore
    public function __construct(
        private readonly FeedRunFactory $feedRunFactory,
        private readonly FeedRunResource $resource
    ) {
    }

    public function start(string $exporterCode, int $storeId): FeedRun
    {
        $run = $this->feedRunFactory->create();
        $run->setData('exporter_code', $exporterCode);
        $run->setData('store_id', $storeId);
        $run->setData('status', FeedRun::STATUS_RUNNING);
        $run->setData('started_at', \gmdate('Y-m-d H:i:s'));
        $this->resource->save($run);
        return $run;
    }

    public function complete(FeedRun $run, int $itemCount, string $filePath): void
    {
        $run->setData('status', FeedRun::STATUS_SUCCESS);
        $run->setData('item_count', $itemCount);
        $run->setData('file_path', $filePath);
        $run->setData('finished_at', \gmdate('Y-m-d H:i:s'));
        $this->resource->save($run);
    }

    public function fail(FeedRun $run, \Throwable $error): void
    {
        $run->setData('status', FeedRun::STATUS_FAILED);
        $run->setData('error_message', $error->getMessage());
        $run->setData('finished_at', \gmdate('Y-m-d H:i:s'));
        $this->resource->save($run);
    }
}
