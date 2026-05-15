<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Feed;

use Gtstudio\Llmo\Model\Feed\ExporterPool;
use Gtstudio\Llmo\Model\Feed\FeedPublisher;
use Gtstudio\Llmo\Model\Feed\FeedWriter;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Public endpoint serving the static feed file to AI crawlers.
 *
 * Routes:
 *   GET /llmo/feed/index/exporter/acp   (default: acp)
 *   GET /llmo/feed/index?exporter=acp
 *
 * If the file is missing it is built on-demand. Crawler controls
 * (robots.txt rules from Phase 3) gate access from the network side.
 */
class Index implements HttpGetActionInterface
{
    private const CONFIG_ENABLED = 'llmo/general/enabled';

    // phpcs:ignore
    public function __construct(
        private readonly ExporterPool $exporterPool,
        private readonly FeedPublisher $feedPublisher,
        private readonly FeedWriter $feedWriter,
        private readonly HttpRequest $request,
        private readonly HttpResponse $response,
        private readonly LoggerInterface $logger,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function execute(): ResultInterface|HttpResponse
    {
        if (!$this->scopeConfig->isSetFlag(self::CONFIG_ENABLED)) {
            return $this->plainResponse(404, 'text/plain', 'LLMO disabled');
        }

        $exporterCode = (string) ($this->request->getParam('exporter') ?? 'acp');

        if (!$this->exporterPool->has($exporterCode)) {
            return $this->plainResponse(404, 'text/plain', 'Unknown exporter');
        }

        $exporter = $this->exporterPool->get($exporterCode);
        $store    = $this->storeManager->getStore();
        $storeCode = (string) $store->getCode();
        $extension = $exporter->fileExtension();

        try {
            if (!$this->feedWriter->exists($exporterCode, $storeCode, $extension)) {
                $this->feedPublisher->publish($exporterCode, (int) $store->getId());
            }
            $body = $this->feedWriter->read($exporterCode, $storeCode, $extension);
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] Feed serve failed', [
                'exception' => $th,
                'context'   => ['exporter' => $exporterCode, 'store' => $storeCode],
            ]);
            return $this->plainResponse(500, 'text/plain', 'Feed unavailable');
        }

        return $this->plainResponse(200, $exporter->mimeType(), $body);
    }

    private function plainResponse(int $status, string $contentType, string $body): HttpResponse
    {
        $this->response->setHttpResponseCode($status);
        $this->response->setHeader('Content-Type', $contentType, true);
        $this->response->setHeader('X-Robots-Tag', 'noindex', true);
        $this->response->setBody($body);
        return $this->response;
    }
}
