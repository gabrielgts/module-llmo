<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Feed;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Gtstudio_Llmo::feed';

    public function execute(): ResultInterface
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Gtstudio_Llmo::feed');
        $resultPage->getConfig()->getTitle()->prepend(__('LLMO Product Feed'));
        $resultPage->addBreadcrumb(__('LLMO'), __('LLMO'));
        $resultPage->addBreadcrumb(__('Product Feed'), __('Product Feed'));
        return $resultPage;
    }
}
