<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource model for validation log entries.
 */
class ValidationLog extends AbstractDb
{
    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_validation_log_resource';

    /**
     * Initialize resource table and primary key.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('gtstudio_llmo_validation_log', 'validation_id');
        $this->_useIsObjectNew = true;
    }
}
