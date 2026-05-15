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
 *
 * All UTM values are validated before being stored: values that exceed the
 * matching DB column width, or contain characters outside the safe set, are
 * silently dropped to null.  An oversized / malformed UTM is never legitimate
 * and is treated as an injection attempt.
 */
class AttributionExtractor
{
    /**
     * Max lengths matching the db_schema.xml column definitions for
     * `gtstudio_llmo_attribution_event`.
     *
     * @var array<string, int>
     */
    private const UTM_MAX_LENGTHS = [
        'utm_source' => 128,
        'utm_medium' => 64,
        'utm_campaign' => 128,
        'utm_content' => 255,
    ];

    /**
     * Safe character set for UTM values.
     *
     * Allows letters, digits, dash, underscore, dot, plus, colon, forward-slash,
     * at-sign, and percent-encoded sequences — covering every real-world UTM
     * tag format while blocking HTML/script injection.
     */
    private const UTM_SAFE_PATTERN = '/^[a-zA-Z0-9._\-+:\/@%]+$/';

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
        foreach (self::UTM_MAX_LENGTHS as $key => $maxLength) {
            $utm[$key] = $this->sanitizeUtm($request->getParam($key), $maxLength);
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

    /**
     * Validate and return a UTM param value, or null if it fails validation.
     *
     * @param mixed $raw
     * @param int $maxLength
     * @return string|null
     */
    private function sanitizeUtm($raw, int $maxLength): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        $value = (string) $raw;

        if (\strlen($value) > $maxLength) {
            return null;
        }

        if (!\preg_match(self::UTM_SAFE_PATTERN, $value)) {
            return null;
        }

        return $value;
    }
}
