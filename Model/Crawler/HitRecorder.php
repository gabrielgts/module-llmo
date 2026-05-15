<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Crawler;

use Gtstudio\Llmo\Model\CrawlerHit;
use Gtstudio\Llmo\Model\CrawlerHitFactory;
use Gtstudio\Llmo\Model\ResourceModel\CrawlerHit as CrawlerHitResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Detects AI-bot requests and persists a row in `gtstudio_llmo_crawler_hit`.
 *
 * Sampling rate is honoured per store via `llmo/crawlers/log_sampling`.
 * Errors are swallowed and logged so we never break a customer-facing request.
 */
class HitRecorder
{
    private const CONFIG_ENABLED = 'llmo/general/enabled';
    private const CONFIG_SAMPLING = 'llmo/crawlers/log_sampling';

    /**
     * @param CrawlerHitFactory $hitFactory
     * @param CrawlerHitResource $hitResource
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param UserAgentDetector $userAgentDetector
     */
    // phpcs:ignore
    public function __construct(
        private readonly CrawlerHitFactory $hitFactory,
        private readonly CrawlerHitResource $hitResource,
        private readonly LoggerInterface $logger,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly UserAgentDetector $userAgentDetector
    ) {
    }

    /**
     * Inspect a request and record a hit if it matches a known AI bot.
     *
     * @param RequestInterface $request
     * @return void
     */
    public function record(RequestInterface $request): void
    {
        try {
            if (!$this->scopeConfig->isSetFlag(self::CONFIG_ENABLED, ScopeInterface::SCOPE_STORE)) {
                return;
            }

            $userAgent = (string) $request->getServer('HTTP_USER_AGENT', '');
            $botCode = $this->userAgentDetector->detect($userAgent);
            if ($botCode === null) {
                return;
            }

            if (!$this->shouldSample()) {
                return;
            }

            $hit = $this->hitFactory->create();
            $this->fill($hit, $request, $botCode, $userAgent);
            $this->hitResource->save($hit);
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] Failed to record crawler hit', [
                'exception' => $th,
            ]);
        }
    }

    /**
     * Decide whether this hit is kept under the configured 1-in-N sampling.
     *
     * @return bool
     */
    private function shouldSample(): bool
    {
        $rate = (int) $this->scopeConfig->getValue(self::CONFIG_SAMPLING, ScopeInterface::SCOPE_STORE);
        if ($rate <= 1) {
            return true;
        }
        return \random_int(1, $rate) === 1;
    }

    /**
     * Fill the hit row from the incoming request.
     *
     * @param CrawlerHit $hit
     * @param RequestInterface $request
     * @param string $botCode
     * @param string $userAgent
     * @return void
     */
    private function fill(CrawlerHit $hit, RequestInterface $request, string $botCode, string $userAgent): void
    {
        $storeId = (int) $this->storeManager->getStore()->getId();
        $path = (string) $request->getRequestUri();
        $referrer = (string) $request->getServer('HTTP_REFERER', '');

        $hit->setData('bot_code', $botCode);
        $hit->setData('user_agent', \substr($userAgent, 0, 512));
        $hit->setData('path', \substr($path, 0, 512));
        $hit->setData('store_id', $storeId);
        $hit->setData('referrer', $referrer === '' ? null : \substr($referrer, 0, 512));
        $hit->setData('hit_at', \gmdate('Y-m-d H:i:s'));
    }
}
