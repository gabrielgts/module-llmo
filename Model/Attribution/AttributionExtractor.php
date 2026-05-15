<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Attribution;

use Gtstudio\Llmo\Model\Crawler\UserAgentDetector;
use Magento\Framework\App\RequestInterface;

/**
 * Builds an `AttributionContext` from an incoming request's UTM params,
 * HTTP Referer header, and User-Agent.
 *
 * Signal priority (applied by downstream COALESCE):
 *   1. utm_source         — explicit, highest confidence
 *   2. bot UA             — AI crawler recognition
 *   3. HTTP Referer host  — fallback for un-tagged AI links (e.g. ChatGPT)
 */
class AttributionExtractor
{
    private const UTM_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content'];

    /**
     * @param ReferrerDomainDetector $referrerDomainDetector
     * @param UserAgentDetector $userAgentDetector
     */
    // phpcs:ignore
    public function __construct(
        private readonly ReferrerDomainDetector $referrerDomainDetector,
        private readonly UserAgentDetector $userAgentDetector
    ) {
    }

    /**
     * Build an attribution context from the given request.
     *
     * @param RequestInterface $request
     * @return AttributionContext
     */
    public function extract(RequestInterface $request): AttributionContext
    {
        $utm = [];
        foreach (self::UTM_KEYS as $key) {
            $value = $request->getParam($key);
            $utm[$key] = $value === null || $value === '' ? null : (string) $value;
        }

        $userAgent = (string) $request->getServer('HTTP_USER_AGENT', '');
        $botCode = $this->userAgentDetector->detect($userAgent);

        $referer = (string) $request->getServer('HTTP_REFERER', '');
        $referrerSource = $this->referrerDomainDetector->detect($referer);

        return new AttributionContext(
            $botCode,
            $utm['utm_source'],
            $utm['utm_medium'],
            $utm['utm_campaign'],
            $utm['utm_content'],
            \gmdate(\DateTimeInterface::ATOM),
            $referrerSource
        );
    }
}
