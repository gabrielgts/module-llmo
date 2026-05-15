<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Dashboard\Collector;

use Gtstudio\Llmo\Model\ResourceModel\FeedRun\CollectionFactory as FeedRunCollectionFactory;
use Gtstudio\Llmo\Model\ResourceModel\ValidationLog\CollectionFactory as ValidationCollectionFactory;

/**
 * Reads the latest feed run + validation row per exporter so the dashboard
 * can render a "feed health" summary card.
 */
class FeedHealthCollector
{
    /**
     * @param FeedRunCollectionFactory $feedRunCollectionFactory
     * @param ValidationCollectionFactory $validationCollectionFactory
     */
    // phpcs:ignore
    public function __construct(
        private readonly FeedRunCollectionFactory $feedRunCollectionFactory,
        private readonly ValidationCollectionFactory $validationCollectionFactory
    ) {
    }

    /**
     * Collect the latest run per exporter.
     *
     * Each row has keys: `exporter_code`, `status`, `item_count`,
     * `started_at`, `finished_at`, `errors`, `warnings`, `last_run_id`.
     *
     * @return array
     */
    public function collect(): array
    {
        $rows = [];

        $feedCollection = $this->feedRunCollectionFactory->create();
        $feedCollection->setOrder('started_at', 'DESC');
        $feedCollection->setPageSize(50);

        foreach ($feedCollection as $run) {
            $exporterCode = (string) $run->getData('exporter_code');
            if (isset($rows[$exporterCode])) {
                continue;
            }
            $rows[$exporterCode] = [
                'exporter_code' => $exporterCode,
                'status' => (string) $run->getData('status'),
                'item_count' => (int) $run->getData('item_count'),
                'started_at' => (string) $run->getData('started_at'),
                'finished_at' => (string) ($run->getData('finished_at') ?? ''),
                'errors' => 0,
                'warnings' => 0,
                'last_run_id' => (int) $run->getData('run_id'),
            ];
        }

        $this->mergeValidationCounts($rows);

        return \array_values($rows);
    }

    /**
     * Merge per-exporter validation counts into the given rows in place.
     *
     * @param array $rows
     * @return void
     */
    private function mergeValidationCounts(array &$rows): void
    {
        if ($rows === []) {
            return;
        }

        $collection = $this->validationCollectionFactory->create();
        $collection->addFieldToFilter('exporter_code', ['in' => \array_keys($rows)]);
        $collection->setOrder('validated_at', 'DESC');
        $collection->setPageSize(50);

        $seen = [];
        foreach ($collection as $log) {
            $code = (string) $log->getData('exporter_code');
            if (isset($seen[$code])) {
                continue;
            }
            $seen[$code] = true;
            if (!isset($rows[$code])) {
                continue;
            }
            $rows[$code]['errors'] = (int) $log->getData('error_count');
            $rows[$code]['warnings'] = (int) $log->getData('warning_count');
        }
    }
}
