<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Block\Adminhtml\Attribution;

use Gtstudio\Llmo\Model\AttributionEvent;
use Gtstudio\Llmo\Model\ResourceModel\AttributionEvent\CollectionFactory;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Backing block for the LLMO Attribution admin page.
 */
class Manage extends Template
{
    public const RECENT_LIMIT = 50;
    public const SUMMARY_DAYS = 30;

    /**
     * @param Context $context
     * @param CollectionFactory $eventCollectionFactory
     * @param array $data
     */
    // phpcs:ignore
    public function __construct(
        Context $context,
        private readonly CollectionFactory $eventCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Recent attribution events, newest first.
     *
     * @return AttributionEvent[]
     */
    public function getRecentEvents(): array
    {
        $collection = $this->eventCollectionFactory->create();
        $collection->setOrder('event_at', 'DESC');
        $collection->setPageSize(self::RECENT_LIMIT);
        return \array_values($collection->getItems());
    }

    /**
     * Aggregated revenue & order count per source for the last `SUMMARY_DAYS` days.
     *
     * Rows: `source` (string), `orders` (int), `revenue` (float).
     *
     * @return array
     */
    public function getSourceSummary(): array
    {
        $collection = $this->eventCollectionFactory->create();
        $since = \gmdate('Y-m-d H:i:s', \strtotime('-' . self::SUMMARY_DAYS . ' days') ?: 0);
        $collection->addFieldToFilter('event_at', ['gteq' => $since]);

        $connection = $collection->getConnection();
        $select = $collection->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns([
                'source' => new \Zend_Db_Expr(
                    "COALESCE("
                    . "NULLIF(utm_source, ''), "
                    . "NULLIF(referrer_agent, ''), "
                    . "NULLIF(http_referrer_source, ''), "
                    . "'direct')"
                ),
                'orders' => new \Zend_Db_Expr('COUNT(*)'),
                'revenue' => new \Zend_Db_Expr('SUM(revenue)'),
            ])
            ->group('source')
            ->order('revenue DESC');

        $rows = [];
        foreach ($connection->fetchAll($select) as $row) {
            $rows[] = [
                'source' => (string) ($row['source'] ?? 'direct'),
                'orders' => (int) ($row['orders'] ?? 0),
                'revenue' => (float) ($row['revenue'] ?? 0),
            ];
        }
        return $rows;
    }
}
