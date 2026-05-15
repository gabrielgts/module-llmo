<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Block\Adminhtml\Dashboard;

use Gtstudio\Llmo\Api\Data\LlmoSnapshotInterface;
use Gtstudio\Llmo\Model\Dashboard\SnapshotService;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Backing block for the LLMO Dashboard admin page.
 */
class Snapshot extends Template
{
    /**
     * @param Context $context
     * @param SnapshotService $snapshotService
     * @param array $data
     */
    // phpcs:ignore
    public function __construct(
        Context $context,
        private readonly SnapshotService $snapshotService,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Snapshot rendered by collectors (cached).
     *
     * @return LlmoSnapshotInterface
     */
    public function getSnapshot(): LlmoSnapshotInterface
    {
        return $this->snapshotService->get();
    }

    /**
     * URL of the manual refresh action.
     *
     * @return string
     */
    public function getRefreshUrl(): string
    {
        return $this->getUrl('llmo/dashboard/refresh');
    }

    /**
     * Admin form key.
     *
     * @return string
     */
    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }
}
