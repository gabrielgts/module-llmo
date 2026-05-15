<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Block\Adminhtml\Crawler;

use Gtstudio\Llmo\Model\CrawlerHit;
use Gtstudio\Llmo\Model\ResourceModel\CrawlerHit\CollectionFactory;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Backing block for the LLMO crawler activity admin page.
 */
class Activity extends Template
{
    public const RECENT_LIMIT = 50;
    public const SUMMARY_DAYS = 30;

    /**
     * @param Context $context
     * @param CollectionFactory $hitCollectionFactory
     * @param array $data
     */
    // phpcs:ignore
    public function __construct(
        Context $context,
        private readonly CollectionFactory $hitCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Recent crawler hits, newest first.
     *
     * @return CrawlerHit[]
     */
    public function getRecentHits(): array
    {
        $collection = $this->hitCollectionFactory->create();
        $collection->setOrder('hit_at', 'DESC');
        $collection->setPageSize(self::RECENT_LIMIT);
        return \array_values($collection->getItems());
    }

    /**
     * Aggregate hits per bot over the last `SUMMARY_DAYS` days.
     *
     * Returns rows of `['bot_code' => string, 'count' => int]`, descending.
     *
     * @return array
     */
    public function getBotSummary(): array
    {
        $collection = $this->hitCollectionFactory->create();
        $since = \gmdate('Y-m-d H:i:s', \strtotime('-' . self::SUMMARY_DAYS . ' days') ?: 0);
        $collection->addFieldToFilter('hit_at', ['gteq' => $since]);
        $collection->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(['bot_code', 'count' => new \Zend_Db_Expr('COUNT(*)')])
            ->group('bot_code')
            ->order('count DESC');

        $rows = [];
        foreach ($collection->getConnection()->fetchAll($collection->getSelect()) as $row) {
            $rows[] = [
                'bot_code' => (string) ($row['bot_code'] ?? ''),
                'count' => (int) ($row['count'] ?? 0),
            ];
        }
        return $rows;
    }
}
