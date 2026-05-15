<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Campaign;

use Gtstudio\Llmo\Model\CampaignManager;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Delete one LLMO campaign.
 */
class Delete extends Action implements HttpPostActionInterface
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
        if ($campaignId <= 0) {
            $this->messageManager->addErrorMessage(__('Missing campaign id.'));
        } else {
            try {
                $this->campaignManager->deleteById($campaignId);
                $this->messageManager->addSuccessMessage(__('Campaign deleted.'));
            } catch (\Throwable $th) {
                $this->messageManager->addErrorMessage(
                    __('Could not delete campaign: %1', $th->getMessage())
                );
            }
        }

        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $redirect->setPath('llmo/campaign/index');
    }
}
