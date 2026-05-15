<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\ResourceModel\CrawlerHit;

use Gtstudio\Llmo\Model\CrawlerHit;
use Gtstudio\Llmo\Model\ResourceModel\CrawlerHit as CrawlerHitResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection of AI-crawler hit entries.
 */
class Collection extends AbstractCollection
{
    /** @var string */
    protected $_idFieldName = 'hit_id';

    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_crawler_hit_collection';

    /**
     * Initialize collection model and resource.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(CrawlerHit::class, CrawlerHitResource::class);
    }
}
