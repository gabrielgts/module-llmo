<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Attribution;

use Magento\Customer\Model\Session as CustomerSession;

/**
 * Stores and reads the first-touch `AttributionContext` on the customer
 * session. First touch wins: once a context is stored, it is not overwritten
 * within the same session.
 */
class AttributionSession
{
    public const SESSION_KEY = 'llmo_attribution';

    /**
     * @param CustomerSession $customerSession
     */
    // phpcs:ignore
    public function __construct(
        private readonly CustomerSession $customerSession
    ) {
    }

    /**
     * Read the attribution context from session, or null if never captured.
     *
     * @return AttributionContext|null
     */
    public function get(): ?AttributionContext
    {
        $raw = $this->customerSession->getData(self::SESSION_KEY);
        if (!\is_array($raw) || $raw === []) {
            return null;
        }
        return new AttributionContext(
            isset($raw['referrer_agent']) ? (string) $raw['referrer_agent'] : null,
            isset($raw['utm_source']) ? (string) $raw['utm_source'] : null,
            isset($raw['utm_medium']) ? (string) $raw['utm_medium'] : null,
            isset($raw['utm_campaign']) ? (string) $raw['utm_campaign'] : null,
            isset($raw['utm_content']) ? (string) $raw['utm_content'] : null,
            isset($raw['first_touch_at']) ? (string) $raw['first_touch_at'] : null,
            isset($raw['referrer_source']) ? (string) $raw['referrer_source'] : null
        );
    }

    /**
     * Save the context to session if no prior context is set.
     *
     * @param AttributionContext $context
     * @return void
     */
    public function captureIfEmpty(AttributionContext $context): void
    {
        if ($context->isEmpty()) {
            return;
        }
        if ($this->customerSession->getData(self::SESSION_KEY) !== null) {
            return;
        }
        $this->customerSession->setData(self::SESSION_KEY, $context->toArray());
    }

    /**
     * Remove the captured context (e.g. after order placement).
     *
     * @return void
     */
    public function clear(): void
    {
        $this->customerSession->unsetData(self::SESSION_KEY);
    }
}
