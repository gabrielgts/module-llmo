<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\ResourceModel\AttributionEvent;

use Gtstudio\Llmo\Model\AttributionEvent;
use Gtstudio\Llmo\Model\ResourceModel\AttributionEvent as AttributionEventResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection of LLMO attribution events.
 */
class Collection extends AbstractCollection
{
    /** @var string */
    protected $_idFieldName = 'event_id';

    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_attribution_event_collection';

    /**
     * Initialize collection model and resource.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(AttributionEvent::class, AttributionEventResource::class);
    }
}
