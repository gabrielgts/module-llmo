<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Cache;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;

class FeedCache extends TagScope
{
    public const TYPE_IDENTIFIER = 'llmo_feed_cache';
    public const CACHE_TAG = 'LLMO_FEED_CACHE';

    // phpcs:ignore
    public function __construct(FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}
