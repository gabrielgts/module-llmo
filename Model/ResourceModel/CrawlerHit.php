<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource model for AI-crawler hit entries.
 */
class CrawlerHit extends AbstractDb
{
    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_crawler_hit_resource';

    /**
     * Initialize resource table and primary key.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('gtstudio_llmo_crawler_hit', 'hit_id');
        $this->_useIsObjectNew = true;
    }
}
