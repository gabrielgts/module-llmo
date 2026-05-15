<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model;

use Gtstudio\Llmo\Model\ResourceModel\FeedRun as FeedRunResource;
use Magento\Framework\Model\AbstractModel;

class FeedRun extends AbstractModel
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RUNNING = 'running';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED  = 'failed';
    public const STATUS_PARTIAL = 'partial';

    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_feed_run';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(FeedRunResource::class);
    }
}
