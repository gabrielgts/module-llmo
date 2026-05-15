<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Campaign;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * LLMO Campaigns admin index page.
 */
class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Gtstudio_Llmo::campaigns';

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Gtstudio_Llmo::campaigns');
        $resultPage->getConfig()->getTitle()->prepend(__('LLMO Campaigns'));
        $resultPage->addBreadcrumb(__('LLMO'), __('LLMO'));
        $resultPage->addBreadcrumb(__('Campaigns'), __('Campaigns'));
        return $resultPage;
    }
}
