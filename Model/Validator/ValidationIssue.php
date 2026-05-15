<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Validator;

use Gtstudio\Llmo\Api\Data\ValidationIssueInterface;
use Magento\Framework\DataObject;

/**
 * DataObject-backed implementation of {@see ValidationIssueInterface}.
 */
class ValidationIssue extends DataObject implements ValidationIssueInterface
{
    /**
     * @inheritDoc
     */
    public function getSeverity(): string
    {
        return (string) ($this->getData('severity') ?? self::SEVERITY_ERROR);
    }

    /**
     * @inheritDoc
     */
    public function setSeverity(string $severity): self
    {
        return $this->setData('severity', $severity);
    }

    /**
     * @inheritDoc
     */
    public function getSource(): string
    {
        return (string) $this->getData('source');
    }

    /**
     * @inheritDoc
     */
    public function setSource(string $source): self
    {
        return $this->setData('source', $source);
    }

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return (string) $this->getData('code');
    }

    /**
     * @inheritDoc
     */
    public function setCode(string $code): self
    {
        return $this->setData('code', $code);
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return (string) $this->getData('message');
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $message): self
    {
        return $this->setData('message', $message);
    }

    /**
     * @inheritDoc
     */
    public function getPath(): ?string
    {
        $value = $this->getData('path');
        return $value === null ? null : (string) $value;
    }

    /**
     * @inheritDoc
     */
    public function setPath(?string $path): self
    {
        return $this->setData('path', $path);
    }
}
