<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Attribution;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * LLMO Attribution Events admin page.
 */
class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Gtstudio_Llmo::attribution';

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Gtstudio_Llmo::attribution');
        $resultPage->getConfig()->getTitle()->prepend(__('LLMO Attribution'));
        $resultPage->addBreadcrumb(__('LLMO'), __('LLMO'));
        $resultPage->addBreadcrumb(__('Attribution'), __('Attribution'));
        return $resultPage;
    }
}
