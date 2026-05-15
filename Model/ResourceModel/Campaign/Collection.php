<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\ResourceModel\Campaign;

use Gtstudio\Llmo\Model\Campaign;
use Gtstudio\Llmo\Model\ResourceModel\Campaign as CampaignResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection of LLMO campaigns.
 */
class Collection extends AbstractCollection
{
    /** @var string */
    protected $_idFieldName = 'campaign_id';

    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_campaign_collection';

    /**
     * Initialize collection model and resource.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Campaign::class, CampaignResource::class);
    }
}
