<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model;

use Gtstudio\Llmo\Model\ResourceModel\AttributionEvent as AttributionEventResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Persisted record of one conversion event with its AI/UTM attribution.
 */
class AttributionEvent extends AbstractModel
{
    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_attribution_event';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(AttributionEventResource::class);
    }
}
