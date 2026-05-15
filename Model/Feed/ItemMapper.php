<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Feed;

use Gtstudio\Llmo\Api\Data\AiFeedItemInterface;
use Gtstudio\Llmo\Api\Data\AiFeedItemInterfaceFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Maps a Magento `ProductInterface` to a feed-neutral `AiFeedItemInterface`.
 *
 * Reads optional Phase-4 attributes (`llmo_ai_*`); they remain null when the
 * EAV patch has not yet run, leaving core fields as the only feed content.
 */
class ItemMapper
{
    // phpcs:ignore
    public function __construct(
        private readonly AiFeedItemInterfaceFactory $itemFactory,
        private readonly StockRegistryInterface $stockRegistry
    ) {
    }

    public function map(ProductInterface $product, StoreInterface $store): AiFeedItemInterface
    {
        $item = $this->itemFactory->create();
        $item->setId((string) $product->getSku());
        $item->setTitle((string) $product->getName());
        $item->setDescription($this->resolveDescription($product));
        $item->setLink($this->resolveLink($product, $store));
        $item->setImageLink($this->resolveImage($product, $store));
        $item->setAdditionalImageLinks($this->resolveAdditionalImages($product, $store));
        $item->setAvailability($this->resolveAvailability($product));
        $item->setCondition('new');
        $item->setPrice($this->resolvePrice($product));
        $item->setCurrency((string) $store->getCurrentCurrencyCode());

        $brand = $product->getData('manufacturer_value') ?: $product->getAttributeText('manufacturer');
        if ($brand !== false && $brand !== null && $brand !== '') {
            $item->setBrand((string) $brand);
        }

        $additional = $this->resolveAdditionalAttributes($product);
        if ($additional !== []) {
            $item->setAdditionalAttributes($additional);
        }

        return $item;
    }

    private function resolveDescription(ProductInterface $product): string
    {
        $aiSummary = (string) ($product->getData('llmo_ai_summary') ?? '');
        if ($aiSummary !== '') {
            return $aiSummary;
        }

        $description = (string) ($product->getData('description') ?? '');
        if ($description !== '') {
            return \strip_tags($description);
        }

        return (string) ($product->getData('short_description') ?? '');
    }

    private function resolveLink(ProductInterface $product, StoreInterface $store): string
    {
        $requestPath = (string) $product->getData('request_path');
        $base = $store->getBaseUrl(UrlInterface::URL_TYPE_LINK);

        if ($requestPath !== '') {
            return \rtrim($base, '/') . '/' . \ltrim($requestPath, '/');
        }

        $urlKey = (string) ($product->getData('url_key') ?? '');
        if ($urlKey !== '') {
            return \rtrim($base, '/') . '/' . $urlKey . '.html';
        }

        return \rtrim($base, '/') . '/catalog/product/view/id/' . $product->getId();
    }

    private function resolveImage(ProductInterface $product, StoreInterface $store): ?string
    {
        $image = (string) ($product->getData('image') ?? '');
        if ($image === '' || $image === 'no_selection') {
            return null;
        }
        return \rtrim($store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA), '/')
            . '/catalog/product' . $image;
    }

    /** @return string[] */
    private function resolveAdditionalImages(ProductInterface $product, StoreInterface $store): array
    {
        $gallery = $product->getData('media_gallery');
        if (!\is_array($gallery) || !isset($gallery['images']) || !\is_array($gallery['images'])) {
            return [];
        }

        $primary = (string) ($product->getData('image') ?? '');
        $base = \rtrim($store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA), '/') . '/catalog/product';
        $links = [];

        foreach ($gallery['images'] as $row) {
            if (!\is_array($row) || (int) ($row['disabled'] ?? 0) === 1) {
                continue;
            }
            $file = (string) ($row['file'] ?? '');
            if ($file === '' || $file === $primary) {
                continue;
            }
            $links[] = $base . $file;
        }

        return $links;
    }

    private function resolveAvailability(ProductInterface $product): string
    {
        try {
            $stock = $this->stockRegistry->getStockItem((int) $product->getId());
            return $stock->getIsInStock() ? 'in_stock' : 'out_of_stock';
        } catch (\Throwable) {
            return 'out_of_stock';
        }
    }

    private function resolvePrice(ProductInterface $product): ?float
    {
        $special = $product->getData('special_price');
        if ($special !== null && (float) $special > 0) {
            return (float) $special;
        }
        $price = $product->getData('price');
        return $price === null ? null : (float) $price;
    }

    /** @return array<string, mixed> */
    private function resolveAdditionalAttributes(ProductInterface $product): array
    {
        $attributes = [];

        foreach (['llmo_ai_keywords' => 'keywords', 'llmo_ai_use_cases' => 'use_cases', 'llmo_ai_faq' => 'faq'] as $code => $key) {
            $value = (string) ($product->getData($code) ?? '');
            if ($value !== '') {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }
}
