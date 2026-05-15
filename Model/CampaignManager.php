<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model;

use Gtstudio\Llmo\Api\Data\CampaignInterface;
use Gtstudio\Llmo\Model\Feed\ExporterPool;
use Gtstudio\Llmo\Model\Feed\FeedPublisher;
use Gtstudio\Llmo\Model\ResourceModel\Campaign as CampaignResource;
use Gtstudio\Llmo\Model\ResourceModel\Campaign\CollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Service facade for LLMO campaign CRUD and execution.
 *
 * Kept deliberately compact for the MVP: no SearchCriteria-driven repository,
 * just the operations the admin UI and run controller actually need.
 */
class CampaignManager
{
    /**
     * @param CampaignFactory $campaignFactory
     * @param CampaignResource $campaignResource
     * @param CollectionFactory $collectionFactory
     * @param ExporterPool $exporterPool
     * @param FeedPublisher $feedPublisher
     * @param LoggerInterface $logger
     */
    // phpcs:ignore
    public function __construct(
        private readonly CampaignFactory $campaignFactory,
        private readonly CampaignResource $campaignResource,
        private readonly CollectionFactory $collectionFactory,
        private readonly ExporterPool $exporterPool,
        private readonly FeedPublisher $feedPublisher,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Load a campaign by primary key.
     *
     * @param int $campaignId
     * @return Campaign
     * @throws NoSuchEntityException
     */
    public function getById(int $campaignId): Campaign
    {
        $campaign = $this->campaignFactory->create();
        $this->campaignResource->load($campaign, $campaignId);
        if ($campaign->getCampaignId() === null) {
            throw NoSuchEntityException::singleField('campaign_id', $campaignId);
        }
        return $campaign;
    }

    /**
     * Return all campaigns ordered by id descending.
     *
     * @return Campaign[]
     */
    public function getAll(): array
    {
        $collection = $this->collectionFactory->create();
        $collection->setOrder('campaign_id', 'DESC');
        return \array_values($collection->getItems());
    }

    /**
     * Persist a campaign; validates the exporter exists.
     *
     * @param Campaign $campaign
     * @return Campaign
     * @throws CouldNotSaveException
     */
    public function save(Campaign $campaign): Campaign
    {
        if (!$this->exporterPool->has($campaign->getExporterCode())) {
            throw new CouldNotSaveException(
                __('Unknown exporter code "%1".', $campaign->getExporterCode())
            );
        }
        try {
            $this->campaignResource->save($campaign);
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] Campaign save failed', [
                'exception' => $th,
                'context' => ['code' => $campaign->getCode()],
            ]);
            throw new CouldNotSaveException(
                __('Could not save campaign: %1', $th->getMessage())
            );
        }
        return $campaign;
    }

    /**
     * Delete a campaign by id.
     *
     * @param int $campaignId
     * @return void
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $campaignId): void
    {
        $campaign = $this->getById($campaignId);
        try {
            $this->campaignResource->delete($campaign);
        } catch (\Throwable $th) {
            throw new CouldNotDeleteException(
                __('Could not delete campaign: %1', $th->getMessage())
            );
        }
    }

    /**
     * Run a campaign and stamp the result on the row.
     *
     * Publishes the campaign's exporter across all stores, then writes
     * `last_run_at`, `last_status`, and `last_message` back to the campaign.
     *
     * @param int $campaignId
     * @return Campaign
     * @throws NoSuchEntityException
     */
    public function run(int $campaignId): Campaign
    {
        $campaign = $this->getById($campaignId);
        $code = $campaign->getExporterCode();

        try {
            $runs = $this->feedPublisher->publishAllStores($code);
            $campaign->setLastStatus(CampaignInterface::STATUS_SUCCESS);
            $campaign->setLastMessage(\sprintf('Published across %d store(s).', \count($runs)));
        } catch (\Throwable $th) {
            $campaign->setLastStatus(CampaignInterface::STATUS_FAILED);
            $campaign->setLastMessage($th->getMessage());
            $this->logger->error('[Gtstudio_Llmo] Campaign run failed', [
                'exception' => $th,
                'context' => ['campaign_id' => $campaignId, 'exporter' => $code],
            ]);
        }

        $campaign->setLastRunAt(\gmdate('Y-m-d H:i:s'));
        $this->campaignResource->save($campaign);
        return $campaign;
    }
}
