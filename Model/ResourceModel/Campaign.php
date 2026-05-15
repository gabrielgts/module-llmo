<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource model for LLMO campaigns.
 */
class Campaign extends AbstractDb
{
    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_campaign_resource';

    /**
     * Initialize resource table and primary key.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('gtstudio_llmo_campaign', 'campaign_id');
        $this->_useIsObjectNew = true;
    }
}
