<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Campaign;

use Gtstudio\Llmo\Model\CampaignFactory;
use Gtstudio\Llmo\Model\CampaignManager;
use Gtstudio\Llmo\Model\Validator\UrlValidator;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Create or update one LLMO campaign from the inline admin form.
 */
class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Gtstudio_Llmo::campaigns';

    /**
     * @param Context $context
     * @param CampaignFactory $campaignFactory
     * @param CampaignManager $campaignManager
     * @param UrlValidator $urlValidator
     */
    // phpcs:ignore
    public function __construct(
        Context $context,
        private readonly CampaignFactory $campaignFactory,
        private readonly CampaignManager $campaignManager,
        private readonly UrlValidator $urlValidator
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        $request = $this->getRequest();
        $campaignId = (int) ($request->getParam('campaign_id') ?? 0);
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $rawTargetUrl = (string) ($request->getParam('target_url') ?? '');
        if ($rawTargetUrl !== '' && !$this->urlValidator->isValid($rawTargetUrl)) {
            $this->messageManager->addErrorMessage(
                __('Target URL must be a valid HTTPS address (e.g. https://example.com/feed).')
            );
            return $redirect->setPath('llmo/campaign/index');
        }

        try {
            $campaign = $campaignId > 0
                ? $this->campaignManager->getById($campaignId)
                : $this->campaignFactory->create();

            $campaign->setCode((string) $request->getParam('code'));
            $campaign->setName((string) $request->getParam('name'));
            $campaign->setExporterCode((string) $request->getParam('exporter_code'));
            $campaign->setTargetUrl($rawTargetUrl !== '' ? $rawTargetUrl : null);
            $campaign->setIsActive((bool) $request->getParam('is_active'));

            $this->campaignManager->save($campaign);

            $this->messageManager->addSuccessMessage(
                __('Campaign "%1" saved.', $campaign->getCode())
            );
        } catch (\Throwable $th) {
            $this->messageManager->addErrorMessage(
                __('Could not save campaign: %1', $th->getMessage())
            );
        }

        return $redirect->setPath('llmo/campaign/index');
    }
}
