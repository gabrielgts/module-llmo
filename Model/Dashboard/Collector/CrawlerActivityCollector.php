<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Dashboard\Collector;

use Gtstudio\Llmo\Model\ResourceModel\CrawlerHit\CollectionFactory;
use Magento\Framework\DB\Select;

/**
 * Aggregates AI-crawler hits per bot over the last `LOOKBACK_DAYS` days.
 */
class CrawlerActivityCollector
{
    public const LOOKBACK_DAYS = 30;

    /**
     * @param CollectionFactory $hitCollectionFactory
     */
    // phpcs:ignore
    public function __construct(
        private readonly CollectionFactory $hitCollectionFactory
    ) {
    }

    /**
     * Collect crawler hit counts per bot.
     *
     * Rows: `bot_code`, `count`.
     *
     * @return array
     */
    public function collect(): array
    {
        $collection = $this->hitCollectionFactory->create();
        $since = \gmdate('Y-m-d H:i:s', \strtotime('-' . self::LOOKBACK_DAYS . ' days') ?: 0);
        $collection->addFieldToFilter('hit_at', ['gteq' => $since]);

        $select = $collection->getSelect()
            ->reset(Select::COLUMNS)
            ->columns([
                'bot_code',
                'count' => new \Zend_Db_Expr('COUNT(*)'),
            ])
            ->group('bot_code')
            ->order('count DESC');

        $rows = [];
        foreach ($collection->getConnection()->fetchAll($select) as $row) {
            $rows[] = [
                'bot_code' => (string) ($row['bot_code'] ?? ''),
                'count' => (int) ($row['count'] ?? 0),
            ];
        }
        return $rows;
    }
}
