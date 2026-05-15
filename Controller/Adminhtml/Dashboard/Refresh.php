<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Dashboard;

use Gtstudio\Llmo\Model\Dashboard\SnapshotService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Manual rebuild of the LLMO dashboard snapshot.
 */
class Refresh extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Gtstudio_Llmo::dashboard';

    /**
     * @param Context $context
     * @param SnapshotService $snapshotService
     */
    // phpcs:ignore
    public function __construct(
        Context $context,
        private readonly SnapshotService $snapshotService
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        try {
            $this->snapshotService->rebuild();
            $this->messageManager->addSuccessMessage(__('LLMO dashboard rebuilt.'));
        } catch (\Throwable $th) {
            $this->messageManager->addErrorMessage(
                __('Could not rebuild dashboard: %1', $th->getMessage())
            );
        }
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $redirect->setPath('llmo/dashboard/index');
    }
}
