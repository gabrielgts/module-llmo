<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Dashboard;

use Gtstudio\Llmo\Api\Data\LlmoSnapshotInterface;
use Gtstudio\Llmo\Api\Data\LlmoSnapshotInterfaceFactory;
use Gtstudio\Llmo\Model\Cache\DashboardCache;
use Gtstudio\Llmo\Model\Dashboard\Collector\AttributionFunnelCollector;
use Gtstudio\Llmo\Model\Dashboard\Collector\CrawlerActivityCollector;
use Gtstudio\Llmo\Model\Dashboard\Collector\FeedHealthCollector;
use Gtstudio\Llmo\Model\Dashboard\Collector\TopProductsCollector;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * Builds and caches the dashboard snapshot.
 *
 * Cache-aside: `get()` returns the cached snapshot if fresh; `rebuild()`
 * forces a rebuild (called by cron and the manual Refresh action).
 */
class SnapshotService
{
    public const CACHE_KEY = 'llmo_dashboard_snapshot_v1';
    private const CONFIG_TTL = 'llmo/dashboard/snapshot_ttl';
    private const DEFAULT_TTL = 3600;

    /**
     * @param AttributionFunnelCollector $attributionCollector
     * @param CrawlerActivityCollector $crawlerCollector
     * @param DashboardCache $cache
     * @param FeedHealthCollector $feedHealthCollector
     * @param LoggerInterface $logger
     * @param LlmoSnapshotInterfaceFactory $snapshotFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param TopProductsCollector $topProductsCollector
     */
    // phpcs:ignore
    public function __construct(
        private readonly AttributionFunnelCollector $attributionCollector,
        private readonly CrawlerActivityCollector $crawlerCollector,
        private readonly DashboardCache $cache,
        private readonly FeedHealthCollector $feedHealthCollector,
        private readonly LoggerInterface $logger,
        private readonly LlmoSnapshotInterfaceFactory $snapshotFactory,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly TopProductsCollector $topProductsCollector
    ) {
    }

    /**
     * Return the cached snapshot, building it if missing.
     *
     * @return LlmoSnapshotInterface
     */
    public function get(): LlmoSnapshotInterface
    {
        $cached = $this->cache->load(self::CACHE_KEY);
        if ($cached !== false && $cached !== '') {
            $decoded = \json_decode($cached, true);
            if (\is_array($decoded)) {
                return $this->hydrate($decoded);
            }
        }
        return $this->rebuild();
    }

    /**
     * Force a rebuild and refresh the cache.
     *
     * @return LlmoSnapshotInterface
     */
    public function rebuild(): LlmoSnapshotInterface
    {
        $snapshot = $this->snapshotFactory->create();
        $snapshot->setGeneratedAt(\gmdate(\DateTimeInterface::ATOM));

        try {
            $snapshot->setFeedHealth($this->feedHealthCollector->collect());
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] FeedHealthCollector failed', ['exception' => $th]);
        }
        try {
            $snapshot->setCrawlerActivity($this->crawlerCollector->collect());
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] CrawlerActivityCollector failed', ['exception' => $th]);
        }
        try {
            $snapshot->setAttributionFunnel($this->attributionCollector->collect());
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] AttributionFunnelCollector failed', ['exception' => $th]);
        }
        try {
            $snapshot->setTopProducts($this->topProductsCollector->collect());
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] TopProductsCollector failed', ['exception' => $th]);
        }

        $this->persistToCache($snapshot);
        return $snapshot;
    }

    /**
     * Hydrate a snapshot DTO from a cached payload.
     *
     * @param array $payload
     * @return LlmoSnapshotInterface
     */
    private function hydrate(array $payload): LlmoSnapshotInterface
    {
        $snapshot = $this->snapshotFactory->create();
        $snapshot->setGeneratedAt((string) ($payload['generated_at'] ?? ''));
        $snapshot->setFeedHealth((array) ($payload['feed_health'] ?? []));
        $snapshot->setCrawlerActivity((array) ($payload['crawler_activity'] ?? []));
        $snapshot->setAttributionFunnel((array) ($payload['attribution_funnel'] ?? []));
        $snapshot->setTopProducts((array) ($payload['top_products'] ?? []));
        return $snapshot;
    }

    /**
     * Encode the snapshot to JSON and store in the LLMO dashboard cache.
     *
     * @param LlmoSnapshotInterface $snapshot
     * @return void
     */
    private function persistToCache(LlmoSnapshotInterface $snapshot): void
    {
        $payload = [
            'generated_at' => $snapshot->getGeneratedAt(),
            'feed_health' => $snapshot->getFeedHealth(),
            'crawler_activity' => $snapshot->getCrawlerActivity(),
            'attribution_funnel' => $snapshot->getAttributionFunnel(),
            'top_products' => $snapshot->getTopProducts(),
        ];
        $encoded = \json_encode($payload, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
        if ($encoded === false) {
            return;
        }
        $ttl = (int) $this->scopeConfig->getValue(self::CONFIG_TTL);
        $this->cache->save($encoded, self::CACHE_KEY, [DashboardCache::CACHE_TAG], $ttl > 0 ? $ttl : self::DEFAULT_TTL);
    }
}
