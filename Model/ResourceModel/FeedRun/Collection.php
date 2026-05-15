<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\ResourceModel\FeedRun;

use Gtstudio\Llmo\Model\FeedRun;
use Gtstudio\Llmo\Model\ResourceModel\FeedRun as FeedRunResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /** @var string */
    protected $_idFieldName = 'run_id';

    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_feed_run_collection';

    /**
     * Initialize collection model and resource.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(FeedRun::class, FeedRunResource::class);
    }
}
