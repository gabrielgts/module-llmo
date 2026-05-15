<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Feed;

use Gtstudio\Llmo\Model\Feed\FeedPublisher;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Rebuild extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Gtstudio_Llmo::feed';

    // phpcs:ignore
    public function __construct(
        Context $context,
        private readonly FeedPublisher $feedPublisher
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $exporterCode = (string) ($this->getRequest()->getParam('exporter') ?? 'acp');
        $storeIdParam = $this->getRequest()->getParam('store_id');

        try {
            if ($storeIdParam === null || $storeIdParam === '') {
                $runs = $this->feedPublisher->publishAllStores($exporterCode);
                $this->messageManager->addSuccessMessage(
                    __('Rebuilt "%1" feed across %2 store(s).', $exporterCode, \count($runs))
                );
            } else {
                $this->feedPublisher->publish($exporterCode, (int) $storeIdParam);
                $this->messageManager->addSuccessMessage(
                    __('Rebuilt "%1" feed for store %2.', $exporterCode, $storeIdParam)
                );
            }
        } catch (\Throwable $th) {
            $this->messageManager->addErrorMessage(
                __('Feed rebuild failed: %1', $th->getMessage())
            );
        }

        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $redirect->setPath('llmo/feed/index');
    }
}
