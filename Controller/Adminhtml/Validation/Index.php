<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Validation;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * LLMO Validation Log index page.
 */
class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Gtstudio_Llmo::validation';

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Gtstudio_Llmo::validation');
        $resultPage->getConfig()->getTitle()->prepend(__('LLMO Validation Log'));
        $resultPage->addBreadcrumb(__('LLMO'), __('LLMO'));
        $resultPage->addBreadcrumb(__('Validation Log'), __('Validation Log'));
        return $resultPage;
    }
}
