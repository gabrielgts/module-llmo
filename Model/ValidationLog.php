<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model;

use Gtstudio\Llmo\Model\ResourceModel\ValidationLog as ValidationLogResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Persisted record of one validation run against a built feed.
 */
class ValidationLog extends AbstractModel
{
    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_validation_log';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ValidationLogResource::class);
    }
}
