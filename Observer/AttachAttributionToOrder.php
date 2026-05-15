<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Observer;

use Gtstudio\Llmo\Model\Attribution\AttributionSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

/**
 * Copies the captured attribution context onto a new order before save.
 *
 * Hooked on `sales_order_place_before`; runs once per order.
 */
class AttachAttributionToOrder implements ObserverInterface
{
    /**
     * @param AttributionSession $attributionSession
     * @param LoggerInterface $logger
     */
    // phpcs:ignore
    public function __construct(
        private readonly AttributionSession $attributionSession,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer): void
    {
        try {
            $order = $observer->getEvent()->getData('order');
            if (!$order instanceof OrderInterface) {
                return;
            }
            $context = $this->attributionSession->get();
            if ($context === null || $context->isEmpty()) {
                return;
            }
            $order->setData('llmo_referrer_agent', $context->referrerAgent);
            $order->setData('llmo_utm_source', $context->utmSource);
            $order->setData('llmo_utm_medium', $context->utmMedium);
            $order->setData('llmo_utm_campaign', $context->utmCampaign);
            $order->setData('llmo_utm_content', $context->utmContent);
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] AttachAttributionToOrder failed', [
                'exception' => $th,
            ]);
        }
    }
}
