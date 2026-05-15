<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FeedRun extends AbstractDb
{
    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_feed_run_resource';

    /**
     * Initialize resource table and primary key.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('gtstudio_llmo_feed_run', 'run_id');
        $this->_useIsObjectNew = true;
    }
}
