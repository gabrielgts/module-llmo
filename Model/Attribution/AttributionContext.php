<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Attribution;

/**
 * Immutable bundle of LLMO attribution signals captured for one session.
 *
 * Keys: `referrer_agent`, `utm_source`, `utm_medium`, `utm_campaign`,
 * `utm_content`, `first_touch_at` (UTC ISO-8601).
 */
class AttributionContext
{
    /**
     * @param string|null $referrerAgent
     * @param string|null $utmSource
     * @param string|null $utmMedium
     * @param string|null $utmCampaign
     * @param string|null $utmContent
     * @param string|null $firstTouchAt
     */
    // phpcs:ignore
    public function __construct(
        public readonly ?string $referrerAgent = null,
        public readonly ?string $utmSource = null,
        public readonly ?string $utmMedium = null,
        public readonly ?string $utmCampaign = null,
        public readonly ?string $utmContent = null,
        public readonly ?string $firstTouchAt = null
    ) {
    }

    /**
     * Serialise to a plain array suitable for session storage.
     *
     * @return array<string, string|null>
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
            && $this->utmContent === null;
    }
}
