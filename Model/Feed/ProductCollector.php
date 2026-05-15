<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Feed;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Model\Config as EavConfig;

/**
 * Pages over the catalog yielding only products eligible for the LLMO feed.
 *
 * Filters: enabled, visible in catalog or both, store-scoped. The
 * `llmo_excluded_from_feed` filter is applied only when the attribute exists
 * (created by the Phase 4 data patch); this keeps the module loadable even
 * before `setup:upgrade` has run.
 */
class ProductCollector
{
    private const ATTRIBUTES = [
        'sku',
        'name',
        'description',
        'short_description',
        'price',
        'special_price',
        'image',
        'small_image',
        'thumbnail',
        'media_gallery',
        'manufacturer',
        'url_key',
        'visibility',
        'status',
        'llmo_ai_summary',
        'llmo_ai_keywords',
        'llmo_ai_use_cases',
        'llmo_ai_faq',
        'llmo_excluded_from_feed',
    ];

    /**
     * @param CollectionFactory $collectionFactory
     * @param EavConfig $eavConfig
     */
    // phpcs:ignore
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly EavConfig $eavConfig
    ) {
    }

    /**
     * Yield batches of products for a given store.
     *
     * @param int $storeId
     * @param int $pageSize
     * @return \Generator Yields batches of ProductInterface[].
     */
    public function batches(int $storeId, int $pageSize = 200): \Generator
    {
        $page = 1;

        while (true) {
            $collection = $this->buildCollection($storeId, $pageSize, $page);
            $items = $collection->getItems();

            if ($items === []) {
                return;
            }

            yield $items;

            if (\count($items) < $pageSize) {
                return;
            }

            $page++;
        }
    }

    /**
     * Build the underlying product collection for one page.
     *
     * @param int $storeId
     * @param int $pageSize
     * @param int $page
     * @return Collection
     */
    private function buildCollection(int $storeId, int $pageSize, int $page): Collection
    {
        $collection = $this->collectionFactory->create();
        $collection->addStoreFilter($storeId);
        $collection->addAttributeToSelect(self::ATTRIBUTES);
        $collection->addAttributeToFilter('status', 1);
        $collection->addAttributeToFilter(
            'visibility',
            ['in' => [Visibility::VISIBILITY_IN_CATALOG, Visibility::VISIBILITY_BOTH]]
        );

        if ($this->hasExclusionAttribute()) {
            $collection->addAttributeToFilter(
                'llmo_excluded_from_feed',
                [['neq' => 1], ['null' => true]],
                'left'
            );
        }

        $collection->addUrlRewrite();
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        $collection->setOrder('entity_id', 'ASC');

        return $collection;
    }

    /**
     * Check whether the Phase-4 exclusion attribute exists in EAV yet.
     *
     * @return bool
     */
    private function hasExclusionAttribute(): bool
    {
        try {
            $attribute = $this->eavConfig->getAttribute('catalog_product', 'llmo_excluded_from_feed');
            return $attribute !== null && (int) $attribute->getId() > 0;
        } catch (\Throwable) {
            return false;
        }
    }
}
