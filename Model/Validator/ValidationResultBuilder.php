<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Validator;

use Gtstudio\Llmo\Api\Data\ValidationIssueInterface;
use Gtstudio\Llmo\Api\Data\ValidationIssueInterfaceFactory;
use Gtstudio\Llmo\Api\Data\ValidationResultInterface;
use Gtstudio\Llmo\Api\Data\ValidationResultInterfaceFactory;

/**
 * Constructs `ValidationResultInterface` instances from raw issue rows.
 *
 * Keeps validator implementations free of factory boilerplate and ensures
 * pass/fail counts stay in sync with the issue list.
 */
class ValidationResultBuilder
{
    /**
     * @param ValidationIssueInterfaceFactory $issueFactory
     * @param ValidationResultInterfaceFactory $resultFactory
     */
    // phpcs:ignore
    public function __construct(
        private readonly ValidationIssueInterfaceFactory $issueFactory,
        private readonly ValidationResultInterfaceFactory $resultFactory
    ) {
    }

    /**
     * Build a result from a list of issue rows.
     *
     * Each row is an associative array with keys: `severity`, `source`, `code`,
     * `message`, and optional `path`.
     *
     * @param string $exporterCode
     * @param array $rawIssues
     * @return ValidationResultInterface
     */
    public function build(string $exporterCode, array $rawIssues): ValidationResultInterface
    {
        $issues = [];
        $errors = 0;
        $warnings = 0;

        foreach ($rawIssues as $row) {
            $issue = $this->issueFactory->create();
            $issue->setSeverity((string) $row['severity']);
            $issue->setSource((string) $row['source']);
            $issue->setCode((string) $row['code']);
            $issue->setMessage((string) $row['message']);
            $issue->setPath(isset($row['path']) ? (string) $row['path'] : null);

            if ($issue->getSeverity() === ValidationIssueInterface::SEVERITY_ERROR) {
                $errors++;
            } else {
                $warnings++;
            }
            $issues[] = $issue;
        }

        $result = $this->resultFactory->create();
        $result->setExporterCode($exporterCode);
        $result->setIssues($issues);
        $result->setErrorCount($errors);
        $result->setWarningCount($warnings);
        $result->setPassed($errors === 0);

        return $result;
    }

    /**
     * Merge multiple validation results into a single result.
     *
     * @param string $exporterCode
     * @param ValidationResultInterface[] $results
     * @return ValidationResultInterface
     */
    public function merge(string $exporterCode, array $results): ValidationResultInterface
    {
        $merged = $this->resultFactory->create();
        $issues = [];
        $errors = 0;
        $warnings = 0;

        foreach ($results as $result) {
            if (!$result instanceof ValidationResultInterface) {
                continue;
            }
            foreach ($result->getIssues() as $issue) {
                $issues[] = $issue;
            }
            $errors += $result->getErrorCount();
            $warnings += $result->getWarningCount();
        }

        $merged->setExporterCode($exporterCode);
        $merged->setIssues($issues);
        $merged->setErrorCount($errors);
        $merged->setWarningCount($warnings);
        $merged->setPassed($errors === 0);

        return $merged;
    }
}
