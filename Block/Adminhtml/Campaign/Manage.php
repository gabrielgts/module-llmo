<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Block\Adminhtml\Campaign;

use Gtstudio\Llmo\Api\Feed\ExporterInterface;
use Gtstudio\Llmo\Model\Campaign;
use Gtstudio\Llmo\Model\CampaignManager;
use Gtstudio\Llmo\Model\Feed\ExporterPool;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Backing block for the LLMO Campaign admin page.
 */
class Manage extends Template
{
    /**
     * @param Context $context
     * @param CampaignManager $campaignManager
     * @param ExporterPool $exporterPool
     * @param array $data
     */
    // phpcs:ignore
    public function __construct(
        Context $context,
        private readonly CampaignManager $campaignManager,
        private readonly ExporterPool $exporterPool,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * All campaigns, newest first.
     *
     * @return Campaign[]
     */
    public function getCampaigns(): array
    {
        return $this->campaignManager->getAll();
    }

    /**
     * Registered exporters for the form dropdown.
     *
     * @return ExporterInterface[]
     */
    public function getExporters(): array
    {
        return $this->exporterPool->all();
    }

    /**
     * URL of the Save action.
     *
     * @return string
     */
    public function getSaveUrl(): string
    {
        return $this->getUrl('llmo/campaign/save');
    }

    /**
     * URL of the Delete action.
     *
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('llmo/campaign/delete');
    }

    /**
     * URL of the Run action.
     *
     * @return string
     */
    public function getRunUrl(): string
    {
        return $this->getUrl('llmo/campaign/run');
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
}
