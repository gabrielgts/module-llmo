<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Block\Adminhtml\Feed;

use Gtstudio\Llmo\Api\Feed\ExporterInterface;
use Gtstudio\Llmo\Model\Feed\ExporterPool;
use Gtstudio\Llmo\Model\Feed\FeedWriter;
use Gtstudio\Llmo\Model\FeedRun;
use Gtstudio\Llmo\Model\ResourceModel\FeedRun\CollectionFactory as FeedRunCollectionFactory;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class Manage extends Template
{
    public const RECENT_RUNS_LIMIT = 20;

    // phpcs:ignore
    public function __construct(
        Context $context,
        private readonly ExporterPool $exporterPool,
        private readonly FeedRunCollectionFactory $feedRunCollectionFactory,
        private readonly FeedWriter $feedWriter,
        private readonly StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /** @return ExporterInterface[] */
    public function getExporters(): array
    {
        return $this->exporterPool->all();
    }

    /** @return StoreInterface[] */
    public function getStores(): array
    {
        $stores = [];
        foreach ($this->storeManager->getStores() as $store) {
            $stores[(int) $store->getId()] = $store;
        }
        return $stores;
    }

    /** @return FeedRun[] */
    public function getRecentRuns(): array
    {
        $collection = $this->feedRunCollectionFactory->create();
        $collection->setOrder('started_at', 'DESC');
        $collection->setPageSize(self::RECENT_RUNS_LIMIT);
        return \array_values($collection->getItems());
    }

    public function getFeedFilePath(ExporterInterface $exporter, StoreInterface $store): ?string
    {
        $storeCode = (string) $store->getCode();
        if (!$this->feedWriter->exists($exporter->code(), $storeCode, $exporter->fileExtension())) {
            return null;
        }
        return $this->feedWriter->absolutePath($exporter->code(), $storeCode, $exporter->fileExtension());
    }

    public function getRebuildUrl(): string
    {
        return $this->getUrl('llmo/feed/rebuild');
    }

    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }
}
