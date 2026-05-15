<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Api\Data;

/**
 * One issue produced by an LLMO feed validator.
 *
 * @api
 */
interface ValidationIssueInterface
{
    public const SEVERITY_ERROR = 'error';
    public const SEVERITY_WARNING = 'warning';

    /**
     * Get the issue severity (`error` or `warning`).
     *
     * @return string
     */
    public function getSeverity(): string;

    /**
     * Set the issue severity.
     *
     * @param string $severity
     * @return self
     */
    public function setSeverity(string $severity): self;

    /**
     * Validator origin (e.g. `schema`, `quality`).
     *
     * @return string
     */
    public function getSource(): string;

    /**
     * Set the validator origin.
     *
     * @param string $source
     * @return self
     */
    public function setSource(string $source): self;

    /**
     * Stable machine-readable code (e.g. `missing_image`, `schema_required`).
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Set the issue code.
     *
     * @param string $code
     * @return self
     */
    public function setCode(string $code): self;

    /**
     * Human-readable message describing the issue.
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Set the message.
     *
     * @param string $message
     * @return self
     */
    public function setMessage(string $message): self;

    /**
     * Pointer into the feed body — JSON Pointer-ish path or product SKU.
     *
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * Set the path/pointer.
     *
     * @param string|null $path
     * @return self
     */
    public function setPath(?string $path): self;
}
