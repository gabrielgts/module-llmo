<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Controller\Adminhtml\Validation;

use Gtstudio\Llmo\Api\ValidateFeedInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Manual validation trigger: builds the requested feed and records a row.
 */
class Run extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Gtstudio_Llmo::validation';

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param StoreRepositoryInterface $storeRepository
     * @param ValidateFeedInterface $validateFeed
     */
    // phpcs:ignore
    public function __construct(
        Context $context,
        private readonly StoreManagerInterface $storeManager,
        private readonly StoreRepositoryInterface $storeRepository,
        private readonly ValidateFeedInterface $validateFeed
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        $exporter = (string) ($this->getRequest()->getParam('exporter') ?? 'acp');
        $storeIdParam = $this->getRequest()->getParam('store_id');

        try {
            $storeCode = $this->resolveStoreCode($storeIdParam);
            $result = $this->validateFeed->execute($exporter, $storeCode);

            if ($result->isPassed()) {
                $this->messageManager->addSuccessMessage(
                    __('Validation passed for "%1" (%2 warnings).', $exporter, $result->getWarningCount())
                );
            } else {
                $this->messageManager->addErrorMessage(
                    __(
                        'Validation failed for "%1": %2 errors, %3 warnings.',
                        $exporter,
                        $result->getErrorCount(),
                        $result->getWarningCount()
                    )
                );
            }
        } catch (\Throwable $th) {
            $this->messageManager->addErrorMessage(
                __('Validation run failed: %1', $th->getMessage())
            );
        }

        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $redirect->setPath('llmo/validation/index');
    }

    /**
     * Resolve the store code from a request param (id or empty).
     *
     * @param mixed $storeIdParam
     * @return string|null
     */
    private function resolveStoreCode($storeIdParam): ?string
    {
        if ($storeIdParam === null || $storeIdParam === '') {
            return null;
        }
        $store = $this->storeManager->getStore((int) $storeIdParam);
        return (string) $store->getCode();
    }
}
