<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model;

use Gtstudio\Llmo\Model\ResourceModel\CrawlerHit as CrawlerHitResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Persisted record of a single AI-crawler request to the storefront.
 */
class CrawlerHit extends AbstractModel
{
    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_crawler_hit';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(CrawlerHitResource::class);
    }
}
