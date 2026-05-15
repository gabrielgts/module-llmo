<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * LLMO Dashboard admin page.
 */
class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Gtstudio_Llmo::dashboard';

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Gtstudio_Llmo::dashboard');
        $resultPage->getConfig()->getTitle()->prepend(__('LLMO Dashboard'));
        $resultPage->addBreadcrumb(__('LLMO'), __('LLMO'));
        $resultPage->addBreadcrumb(__('Dashboard'), __('Dashboard'));
        return $resultPage;
    }
}
