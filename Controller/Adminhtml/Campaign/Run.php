<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Campaign;

use Gtstudio\Llmo\Model\CampaignManager;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Run one LLMO campaign on-demand (publishes the bound exporter).
 */
class Run extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Gtstudio_Llmo::campaigns';

    /**
     * @param Context $context
     * @param CampaignManager $campaignManager
     */
    // phpcs:ignore
    public function __construct(
        Context $context,
        private readonly CampaignManager $campaignManager
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        $campaignId = (int) ($this->getRequest()->getParam('campaign_id') ?? 0);

        try {
            $campaign = $this->campaignManager->run($campaignId);
            if ($campaign->getLastStatus() === \Gtstudio\Llmo\Api\Data\CampaignInterface::STATUS_SUCCESS) {
                $this->messageManager->addSuccessMessage(
                    __('Campaign "%1" ran successfully: %2', $campaign->getCode(), $campaign->getLastMessage())
                );
            } else {
                $this->messageManager->addErrorMessage(
                    __('Campaign "%1" failed: %2', $campaign->getCode(), $campaign->getLastMessage())
                );
            }
        } catch (\Throwable $th) {
            $this->messageManager->addErrorMessage(
                __('Could not run campaign: %1', $th->getMessage())
            );
        }

        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $redirect->setPath('llmo/campaign/index');
    }
}
