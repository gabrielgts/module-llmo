<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Cron;

use Gtstudio\Llmo\Model\Dashboard\SnapshotService;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * Cron job: rebuilds the LLMO dashboard snapshot.
 */
class BuildLlmoDashboard
{
    private const CONFIG_ENABLED = 'llmo/general/enabled';

    /**
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param SnapshotService $snapshotService
     */
    // phpcs:ignore
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly SnapshotService $snapshotService
    ) {
    }

    /**
     * Rebuild the dashboard snapshot.
     *
     * @return void
     */
    public function execute(): void
    {
        if (!$this->scopeConfig->isSetFlag(self::CONFIG_ENABLED)) {
            return;
        }

        try {
            $this->snapshotService->rebuild();
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] Dashboard cron failed', [
                'exception' => $th,
            ]);
        }
    }
}
