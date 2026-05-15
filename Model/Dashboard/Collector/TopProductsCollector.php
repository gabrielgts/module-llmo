<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Dashboard\Collector;

use Gtstudio\Llmo\Model\ResourceModel\AttributionEvent\CollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

/**
 * Joins attribution events to order items to surface the top products that
 * AI traffic has purchased over the last `LOOKBACK_DAYS` days.
 */
class TopProductsCollector
{
    public const LOOKBACK_DAYS = 30;
    public const TOP_LIMIT = 10;

    /**
     * @param CollectionFactory $eventCollectionFactory
     * @param ResourceConnection $resource
     */
    // phpcs:ignore
    public function __construct(
        private readonly CollectionFactory $eventCollectionFactory,
        private readonly ResourceConnection $resource
    ) {
    }

    /**
     * Collect the top products driven by AI traffic.
     *
     * Rows: `sku`, `name`, `units`, `revenue`, `top_source`.
     *
     * @return array
     */
    public function collect(): array
    {
        $connection = $this->resource->getConnection();
        $eventTable = $this->resource->getTableName('gtstudio_llmo_attribution_event');
        $itemTable = $this->resource->getTableName('sales_order_item');

        $since = \gmdate('Y-m-d H:i:s', \strtotime('-' . self::LOOKBACK_DAYS . ' days') ?: 0);

        $select = $connection->select()
            ->from(['e' => $eventTable], [])
            ->joinInner(
                ['i' => $itemTable],
                'i.order_id = e.order_id AND i.parent_item_id IS NULL',
                [
                    'sku' => 'i.sku',
                    'name' => 'i.name',
                    'units' => new \Zend_Db_Expr('SUM(i.qty_ordered)'),
                    'revenue' => new \Zend_Db_Expr('SUM(i.row_total)'),
                    'top_source' => new \Zend_Db_Expr(
                        "MAX(COALESCE(NULLIF(e.referrer_agent, ''), NULLIF(e.utm_source, ''), 'direct'))"
                    ),
                ]
            )
            ->where('e.event_at >= ?', $since)
            ->group('i.sku')
            ->order('revenue DESC')
            ->limit(self::TOP_LIMIT);

        $rows = [];
        foreach ($connection->fetchAll($select) as $row) {
            $rows[] = [
                'sku' => (string) ($row['sku'] ?? ''),
                'name' => (string) ($row['name'] ?? ''),
                'units' => (float) ($row['units'] ?? 0),
                'revenue' => (float) ($row['revenue'] ?? 0),
                'top_source' => (string) ($row['top_source'] ?? 'direct'),
            ];
        }
        return $rows;
    }
}
