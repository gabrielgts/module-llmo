<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Validator;

use Gtstudio\Llmo\Api\Data\ValidationIssueInterface;
use Gtstudio\Llmo\Api\Data\ValidationResultInterface;
use Gtstudio\Llmo\Model\ResourceModel\ValidationLog as ValidationLogResource;
use Gtstudio\Llmo\Model\ValidationLog;
use Gtstudio\Llmo\Model\ValidationLogFactory;
use Psr\Log\LoggerInterface;

/**
 * Persists a `ValidationResultInterface` as a `gtstudio_llmo_validation_log` row.
 *
 * Run-id is optional so ad-hoc validations (admin button, REST call) record
 * cleanly alongside cron-triggered ones.
 */
class ValidationLogger
{
    /**
     * @param LoggerInterface $logger
     * @param ValidationLogFactory $logFactory
     * @param ValidationLogResource $logResource
     */
    // phpcs:ignore
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ValidationLogFactory $logFactory,
        private readonly ValidationLogResource $logResource
    ) {
    }

    /**
     * Persist the validation result.
     *
     * @param ValidationResultInterface $result
     * @param int $storeId
     * @param int|null $runId
     * @return ValidationLog
     */
    public function record(ValidationResultInterface $result, int $storeId, ?int $runId = null): ValidationLog
    {
        $log = $this->logFactory->create();
        $log->setData('run_id', $runId);
        $log->setData('exporter_code', $result->getExporterCode());
        $log->setData('store_id', $storeId);
        $log->setData('passed', $result->isPassed() ? 1 : 0);
        $log->setData('error_count', $result->getErrorCount());
        $log->setData('warning_count', $result->getWarningCount());
        $log->setData('report_json', $this->encodeReport($result));
        $log->setData('validated_at', \gmdate('Y-m-d H:i:s'));

        try {
            $this->logResource->save($log);
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] Failed to persist validation log', [
                'exception' => $th,
                'context' => ['exporter' => $result->getExporterCode(), 'store_id' => $storeId, 'run_id' => $runId],
            ]);
        }

        return $log;
    }

    /**
     * Encode the result issues as a compact JSON report.
     *
     * @param ValidationResultInterface $result
     * @return string
     */
    private function encodeReport(ValidationResultInterface $result): string
    {
        $rows = [];
        foreach ($result->getIssues() as $issue) {
            $rows[] = [
                'severity' => $issue->getSeverity(),
                'source' => $issue->getSource(),
                'code' => $issue->getCode(),
                'message' => $issue->getMessage(),
                'path' => $issue->getPath(),
            ];
        }
        $encoded = \json_encode(
            ['issues' => $rows, 'errors' => $result->getErrorCount(), 'warnings' => $result->getWarningCount()],
            \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE
        );
        return $encoded === false ? '{}' : $encoded;
    }
}
