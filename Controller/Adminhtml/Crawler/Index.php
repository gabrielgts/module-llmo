<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Crawler;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * LLMO AI Crawler activity page.
 */
class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Gtstudio_Llmo::crawlers';

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Gtstudio_Llmo::crawlers');
        $resultPage->getConfig()->getTitle()->prepend(__('LLMO Crawler Activity'));
        $resultPage->addBreadcrumb(__('LLMO'), __('LLMO'));
        $resultPage->addBreadcrumb(__('Crawlers'), __('Crawlers'));
        return $resultPage;
    }
}
