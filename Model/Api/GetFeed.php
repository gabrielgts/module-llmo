<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Api;

use Gtstudio\Llmo\Api\Data\AiFeedInterface;
use Gtstudio\Llmo\Api\GetFeedInterface;
use Gtstudio\Llmo\Model\Feed\FeedBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class GetFeed implements GetFeedInterface
{
    // phpcs:ignore
    public function __construct(
        private readonly FeedBuilder $feedBuilder,
        private readonly StoreManagerInterface $storeManager,
        private readonly StoreRepositoryInterface $storeRepository
    ) {
    }

    /** @throws NoSuchEntityException */
    public function execute(string $exporter = 'acp', ?string $storeCode = null): AiFeedInterface
    {
        $storeId = $storeCode === null
            ? (int) $this->storeManager->getStore()->getId()
            : (int) $this->storeRepository->get($storeCode)->getId();

        return $this->feedBuilder->build($storeId, $exporter);
    }
}
