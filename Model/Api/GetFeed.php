<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Api;

use Gtstudio\Llmo\Api\Data\AiFeedInterface;
use Gtstudio\Llmo\Api\GetFeedInterface;
use Gtstudio\Llmo\Model\Feed\FeedBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Default implementation of {@see GetFeedInterface}.
 *
 * The endpoint is anonymous in webapi.xml so AI platforms can reach it without
 * OAuth. Access is gated by `llmo/feed/public_rest_api` (default: 0/off).
 * When disabled the caller receives HTTP 401; use the static file at
 * `/llmo/feed/index` for unauthenticated crawler access instead.
 */
class GetFeed implements GetFeedInterface
{
    private const CONFIG_PUBLIC_REST_API = 'llmo/feed/public_rest_api';

    /**
     * @param FeedBuilder $feedBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param StoreRepositoryInterface $storeRepository
     */
    // phpcs:ignore
    public function __construct(
        private readonly FeedBuilder $feedBuilder,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly StoreRepositoryInterface $storeRepository
    ) {
    }

    /**
     * Return the built feed for a given exporter and store.
     *
     * @param string $exporter
     * @param string|null $storeCode
     * @return AiFeedInterface
     * @throws AuthorizationException When the public REST endpoint is disabled.
     * @throws NoSuchEntityException  When the requested store code does not exist.
     */
    public function execute(string $exporter = 'acp', ?string $storeCode = null): AiFeedInterface
    {
        if (!$this->scopeConfig->isSetFlag(self::CONFIG_PUBLIC_REST_API)) {
            throw new AuthorizationException(
                __('The LLMO feed REST endpoint is disabled. '
                    . 'Enable it under Stores > Configuration > LLMO > Product Feed.')
            );
        }

        $storeId = $storeCode === null
            ? (int) $this->storeManager->getStore()->getId()
            : (int) $this->storeRepository->get($storeCode)->getId();

        return $this->feedBuilder->build($storeId, $exporter);
    }
}
