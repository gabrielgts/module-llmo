<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Dashboard\Collector;

use Gtstudio\Llmo\Model\ResourceModel\AttributionEvent\CollectionFactory;
use Magento\Framework\DB\Select;

/**
 * Aggregates conversion events per AI/UTM source over the last
 * `LOOKBACK_DAYS` days.
 */
class AttributionFunnelCollector
{
    public const LOOKBACK_DAYS = 30;

    /**
     * @param CollectionFactory $eventCollectionFactory
     */
    // phpcs:ignore
    public function __construct(
        private readonly CollectionFactory $eventCollectionFactory
    ) {
    }

    /**
     * Collect orders + revenue per source.
     *
     * Rows: `source`, `orders`, `revenue`.
     *
     * @return array
     */
    public function collect(): array
    {
        $collection = $this->eventCollectionFactory->create();
        $since = \gmdate('Y-m-d H:i:s', \strtotime('-' . self::LOOKBACK_DAYS . ' days') ?: 0);
        $collection->addFieldToFilter('event_at', ['gteq' => $since]);

        $select = $collection->getSelect()
            ->reset(Select::COLUMNS)
            ->columns([
                'source' => new \Zend_Db_Expr(
                    "COALESCE(NULLIF(referrer_agent, ''), NULLIF(utm_source, ''), 'direct')"
                ),
                'orders' => new \Zend_Db_Expr('COUNT(*)'),
                'revenue' => new \Zend_Db_Expr('SUM(revenue)'),
            ])
            ->group('source')
            ->order('revenue DESC');

        $rows = [];
        foreach ($collection->getConnection()->fetchAll($select) as $row) {
            $rows[] = [
                'source' => (string) ($row['source'] ?? 'direct'),
                'orders' => (int) ($row['orders'] ?? 0),
                'revenue' => (float) ($row['revenue'] ?? 0),
            ];
        }
        return $rows;
    }
}
