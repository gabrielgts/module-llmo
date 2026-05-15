<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Block\Adminhtml\Validation;

use Gtstudio\Llmo\Api\Feed\ExporterInterface;
use Gtstudio\Llmo\Model\Feed\ExporterPool;
use Gtstudio\Llmo\Model\ResourceModel\ValidationLog\CollectionFactory;
use Gtstudio\Llmo\Model\ValidationLog;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Backing block for the admin LLMO Validation Log page.
 */
class Manage extends Template
{
    public const RECENT_LIMIT = 30;

    /**
     * @param Context $context
     * @param ExporterPool $exporterPool
     * @param CollectionFactory $logCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    // phpcs:ignore
    public function __construct(
        Context $context,
        private readonly ExporterPool $exporterPool,
        private readonly CollectionFactory $logCollectionFactory,
        private readonly StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Registered exporters for the admin UI multiselect.
     *
     * @return ExporterInterface[]
     */
    public function getExporters(): array
    {
        return $this->exporterPool->all();
    }

    /**
     * All frontend stores.
     *
     * @return StoreInterface[]
     */
    public function getStores(): array
    {
        $stores = [];
        foreach ($this->storeManager->getStores() as $store) {
            $stores[(int) $store->getId()] = $store;
        }
        return $stores;
    }

    /**
     * Most recent validation log rows, newest first.
     *
     * @return ValidationLog[]
     */
    public function getRecentLogs(): array
    {
        $collection = $this->logCollectionFactory->create();
        $collection->setOrder('validated_at', 'DESC');
        $collection->setPageSize(self::RECENT_LIMIT);
        return \array_values($collection->getItems());
    }

    /**
     * URL of the manual Run action.
     *
     * @return string
     */
    public function getRunUrl(): string
    {
        return $this->getUrl('llmo/validation/run');
    }

    /**
     * Admin form key.
     *
     * @return string
     */
    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Decode the persisted JSON report into a list of issue rows.
     *
     * @param ValidationLog $log
     * @return array
     */
    public function decodeReport(ValidationLog $log): array
    {
        $raw = (string) ($log->getData('report_json') ?? '');
        if ($raw === '') {
            return [];
        }
        $decoded = \json_decode($raw, true);
        if (!\is_array($decoded) || !isset($decoded['issues']) || !\is_array($decoded['issues'])) {
            return [];
        }
        return $decoded['issues'];
    }
}
