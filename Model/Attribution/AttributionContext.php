<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Attribution;

/**
 * Immutable bundle of LLMO attribution signals captured for one session.
 *
 * Priority (highest-confidence first):
 *   1. `utmSource`       — explicit UTM tag on the inbound URL
 *   2. `referrerAgent`   — AI bot User-Agent (crawler-driven)
 *   3. `referrerSource`  — canonical AI platform name derived from HTTP Referer
 *   4. (falls through to "direct")
 */
class AttributionContext
{
    /**
     * @param string|null $referrerAgent   AI bot UA code from UserAgentDetector.
     * @param string|null $utmSource       Value of `utm_source` query param.
     * @param string|null $utmMedium       Value of `utm_medium` query param.
     * @param string|null $utmCampaign     Value of `utm_campaign` query param.
     * @param string|null $utmContent      Value of `utm_content` query param.
     * @param string|null $firstTouchAt    UTC ISO-8601 timestamp of first touch.
     * @param string|null $referrerSource  Canonical label from HTTP Referer host.
     */
    // phpcs:ignore
    public function __construct(
        public readonly ?string $referrerAgent = null,
        public readonly ?string $utmSource = null,
        public readonly ?string $utmMedium = null,
        public readonly ?string $utmCampaign = null,
        public readonly ?string $utmContent = null,
        public readonly ?string $firstTouchAt = null,
        public readonly ?string $referrerSource = null
    ) {
    }

    /**
     * Serialise to a plain array suitable for session storage.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'referrer_agent' => $this->referrerAgent,
            'utm_source' => $this->utmSource,
            'utm_medium' => $this->utmMedium,
            'utm_campaign' => $this->utmCampaign,
            'utm_content' => $this->utmContent,
            'first_touch_at' => $this->firstTouchAt,
            'referrer_source' => $this->referrerSource,
        ];
    }

    /**
     * Whether any attribution signal is present.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->referrerAgent === null
            && $this->utmSource === null
            && $this->utmMedium === null
            && $this->utmCampaign === null
            && $this->utmContent === null
            && $this->referrerSource === null;
    }
}
