<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\ResourceModel\ValidationLog;

use Gtstudio\Llmo\Model\ResourceModel\ValidationLog as ValidationLogResource;
use Gtstudio\Llmo\Model\ValidationLog;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection of validation log entries.
 */
class Collection extends AbstractCollection
{
    /** @var string */
    protected $_idFieldName = 'validation_id';

    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_validation_log_collection';

    /**
     * Initialize collection model and resource.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ValidationLog::class, ValidationLogResource::class);
    }
}
