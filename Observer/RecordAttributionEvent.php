<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Observer;

use Gtstudio\Llmo\Model\Attribution\AttributionSession;
use Gtstudio\Llmo\Model\AttributionEventFactory;
use Gtstudio\Llmo\Model\ResourceModel\AttributionEvent as AttributionEventResource;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

/**
 * After an order is placed, denormalise its attribution into the
 * `gtstudio_llmo_attribution_event` table for fast dashboard queries.
 */
class RecordAttributionEvent implements ObserverInterface
{
    /**
     * @param AttributionEventFactory $eventFactory
     * @param AttributionEventResource $eventResource
     * @param AttributionSession $attributionSession
     * @param LoggerInterface $logger
     */
    // phpcs:ignore
    public function __construct(
        private readonly AttributionEventFactory $eventFactory,
        private readonly AttributionEventResource $eventResource,
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
            $this->recordEvent($order);
            $this->attributionSession->clear();
        } catch (\Throwable $th) {
            $this->logger->critical('[Gtstudio_Llmo] RecordAttributionEvent failed', [
                'exception' => $th,
            ]);
        }
    }

    /**
     * Persist the denormalised attribution row.
     *
     * @param OrderInterface $order
     * @return void
     */
    private function recordEvent(OrderInterface $order): void
    {
        $event = $this->eventFactory->create();
        $event->setData('order_id', $order->getEntityId());
        $event->setData('order_increment_id', $order->getIncrementId());
        $event->setData('referrer_agent', $order->getData('llmo_referrer_agent'));
        $event->setData('utm_source', $order->getData('llmo_utm_source'));
        $event->setData('utm_medium', $order->getData('llmo_utm_medium'));
        $event->setData('utm_campaign', $order->getData('llmo_utm_campaign'));
        $event->setData('utm_content', $order->getData('llmo_utm_content'));
        $event->setData('http_referrer_source', $order->getData('llmo_referrer_source'));
        $event->setData('revenue', (float) $order->getGrandTotal());
        $event->setData('currency', (string) ($order->getOrderCurrencyCode() ?? 'USD'));
        $event->setData('store_id', (int) $order->getStoreId());
        $event->setData('event_at', \gmdate('Y-m-d H:i:s'));
        $this->eventResource->save($event);
    }
}
