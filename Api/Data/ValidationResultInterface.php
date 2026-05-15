<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Api\Data;

/**
 * Aggregate outcome of running one or more validators over an LLMO feed.
 *
 * @api
 */
interface ValidationResultInterface
{
    /**
     * Exporter code that produced the feed under validation.
     *
     * @return string
     */
    public function getExporterCode(): string;

    /**
     * Set the exporter code.
     *
     * @param string $code
     * @return self
     */
    public function setExporterCode(string $code): self;

    /**
     * Whether every active validator passed (no `error` issues).
     *
     * @return bool
     */
    public function isPassed(): bool;

    /**
     * Mark the overall pass flag.
     *
     * @param bool $passed
     * @return self
     */
    public function setPassed(bool $passed): self;

    /**
     * Count of `error` severity issues across all validators.
     *
     * @return int
     */
    public function getErrorCount(): int;

    /**
     * Set the error count.
     *
     * @param int $count
     * @return self
     */
    public function setErrorCount(int $count): self;

    /**
     * Count of `warning` severity issues across all validators.
     *
     * @return int
     */
    public function getWarningCount(): int;

    /**
     * Set the warning count.
     *
     * @param int $count
     * @return self
     */
    public function setWarningCount(int $count): self;

    /**
     * Full list of issues raised by all validators.
     *
     * @return ValidationIssueInterface[]
     */
    public function getIssues(): array;

    /**
     * Set the issues list.
     *
     * @param ValidationIssueInterface[] $issues
     * @return self
     */
    public function setIssues(array $issues): self;
}
