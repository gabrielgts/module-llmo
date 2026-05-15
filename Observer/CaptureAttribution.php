<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Observer;

use Gtstudio\Llmo\Model\Attribution\AttributionExtractor;
use Gtstudio\Llmo\Model\Attribution\AttributionSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Captures first-touch LLMO attribution signals (UTM + AI bot referrer) on
 * the customer session at storefront predispatch.
 */
class CaptureAttribution implements ObserverInterface
{
    private const CONFIG_ENABLED = 'llmo/general/enabled';

    /**
     * @param AttributionExtractor $extractor
     * @param AttributionSession $attributionSession
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     */
    // phpcs:ignore
    public function __construct(
        private readonly AttributionExtractor $extractor,
        private readonly AttributionSession $attributionSession,
        private readonly LoggerInterface $logger,
        private readonly RequestInterface $request,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer): void
    {
        try {
            if (!$this->scopeConfig->isSetFlag(self::CONFIG_ENABLED, ScopeInterface::SCOPE_STORE)) {
                return;
            }
            $context = $this->extractor->extract($this->request);
            $this->attributionSession->captureIfEmpty($context);
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] CaptureAttribution observer failed', [
                'exception' => $th,
            ]);
        }
    }
}
