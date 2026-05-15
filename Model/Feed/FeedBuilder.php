<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Feed;

use Gtstudio\Llmo\Api\Data\AiFeedInterface;
use Gtstudio\Llmo\Api\Data\AiFeedInterfaceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class FeedBuilder
{
    private const CONFIG_PAGE_SIZE = 'llmo/feed/page_size';
    private const DEFAULT_PAGE_SIZE = 200;

    // phpcs:ignore
    public function __construct(
        private readonly AiFeedInterfaceFactory $feedFactory,
        private readonly ItemMapper $itemMapper,
        private readonly ProductCollector $productCollector,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreRepositoryInterface $storeRepository
    ) {
    }

    public function build(int $storeId, string $exporterCode): AiFeedInterface
    {
        $store = $this->storeRepository->getById($storeId);
        $pageSize = $this->resolvePageSize($storeId);
        $items = [];

        foreach ($this->productCollector->batches($storeId, $pageSize) as $batch) {
            foreach ($batch as $product) {
                $items[] = $this->itemMapper->map($product, $store);
            }
        }

        $feed = $this->feedFactory->create();
        $feed->setExporterCode($exporterCode);
        $feed->setStoreCode((string) $store->getCode());
        $feed->setCurrency((string) $store->getCurrentCurrencyCode());
        $feed->setGeneratedAt(\gmdate(\DateTimeInterface::ATOM));
        $feed->setItems($items);
        $feed->setCount(\count($items));

        return $feed;
    }

    private function resolvePageSize(int $storeId): int
    {
        $configured = (int) $this->scopeConfig->getValue(
            self::CONFIG_PAGE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($configured < 50) {
            return self::DEFAULT_PAGE_SIZE;
        }
        if ($configured > 1000) {
            return 1000;
        }
        return $configured;
    }
}
