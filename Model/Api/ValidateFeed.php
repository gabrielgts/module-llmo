<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Api;

use Gtstudio\Llmo\Api\Data\ValidationResultInterface;
use Gtstudio\Llmo\Api\ValidateFeedInterface;
use Gtstudio\Llmo\Model\Feed\FeedBuilder;
use Gtstudio\Llmo\Model\Validator\ValidationCoordinator;
use Gtstudio\Llmo\Model\Validator\ValidationLogger;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Default implementation of {@see ValidateFeedInterface}.
 */
class ValidateFeed implements ValidateFeedInterface
{
    /**
     * @param FeedBuilder $feedBuilder
     * @param StoreManagerInterface $storeManager
     * @param StoreRepositoryInterface $storeRepository
     * @param ValidationCoordinator $validationCoordinator
     * @param ValidationLogger $validationLogger
     */
    // phpcs:ignore
    public function __construct(
        private readonly FeedBuilder $feedBuilder,
        private readonly StoreManagerInterface $storeManager,
        private readonly StoreRepositoryInterface $storeRepository,
        private readonly ValidationCoordinator $validationCoordinator,
        private readonly ValidationLogger $validationLogger
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(string $exporter = 'acp', ?string $storeCode = null): ValidationResultInterface
    {
        $storeId = $storeCode === null
            ? (int) $this->storeManager->getStore()->getId()
            : (int) $this->storeRepository->get($storeCode)->getId();

        $feed = $this->feedBuilder->build($storeId, $exporter);
        $result = $this->validationCoordinator->validate($feed);
        $this->validationLogger->record($result, $storeId);

        return $result;
    }
}
