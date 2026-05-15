<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Validator;

use Gtstudio\Llmo\Api\Data\ValidationIssueInterface;
use Gtstudio\Llmo\Api\Data\ValidationResultInterface;
use Magento\Framework\DataObject;

/**
 * DataObject-backed implementation of {@see ValidationResultInterface}.
 */
class ValidationResult extends DataObject implements ValidationResultInterface
{
    /**
     * @inheritDoc
     */
    public function getExporterCode(): string
    {
        return (string) $this->getData('exporter_code');
    }

    /**
     * @inheritDoc
     */
    public function setExporterCode(string $code): self
    {
        return $this->setData('exporter_code', $code);
    }

    /**
     * @inheritDoc
     */
    public function isPassed(): bool
    {
        return (bool) $this->getData('passed');
    }

    /**
     * @inheritDoc
     */
    public function setPassed(bool $passed): self
    {
        return $this->setData('passed', $passed);
    }

    /**
     * @inheritDoc
     */
    public function getErrorCount(): int
    {
        return (int) $this->getData('error_count');
    }

    /**
     * @inheritDoc
     */
    public function setErrorCount(int $count): self
    {
        return $this->setData('error_count', $count);
    }

    /**
     * @inheritDoc
     */
    public function getWarningCount(): int
    {
        return (int) $this->getData('warning_count');
    }

    /**
     * @inheritDoc
     */
    public function setWarningCount(int $count): self
    {
        return $this->setData('warning_count', $count);
    }

    /**
     * @inheritDoc
     */
    public function getIssues(): array
    {
        $issues = $this->getData('issues');
        if (!\is_array($issues)) {
            return [];
        }
        return \array_values(\array_filter(
            $issues,
            static fn($i) => $i instanceof ValidationIssueInterface
        ));
    }

    /**
     * @inheritDoc
     */
    public function setIssues(array $issues): self
    {
        return $this->setData('issues', $issues);
    }
}
