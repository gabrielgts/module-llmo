<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Api\Data;

/**
 * One LLMO campaign — a named binding of an exporter to an export target.
 *
 * The foundation for plugging in paid AI ad adapters (Perplexity, Google AI
 * Shopping) without touching the feed builder.
 *
 * @api
 */
interface CampaignInterface
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_PENDING = 'pending';

    /**
     * Get the campaign primary key.
     *
     * @return int|null
     */
    public function getCampaignId(): ?int;

    /**
     * Set the campaign primary key.
     *
     * @param int $id
     * @return self
     */
    public function setCampaignId(int $id): self;

    /**
     * Get the stable string code identifying this campaign.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Set the stable code.
     *
     * @param string $code
     * @return self
     */
    public function setCode(string $code): self;

    /**
     * Get the display name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set the display name.
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self;

    /**
     * Get the exporter code bound to this campaign.
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
     * Get the optional push-target URL.
     *
     * @return string|null
     */
    public function getTargetUrl(): ?string;

    /**
     * Set the optional push-target URL.
     *
     * @param string|null $url
     * @return self
     */
    public function setTargetUrl(?string $url): self;

    /**
     * Whether the campaign is active.
     *
     * @return bool
     */
    public function isActive(): bool;

    /**
     * Set the active flag.
     *
     * @param bool $active
     * @return self
     */
    public function setIsActive(bool $active): self;

    /**
     * Get exporter-specific JSON configuration as a decoded array.
     *
     * @return array
     */
    public function getConfig(): array;

    /**
     * Set exporter-specific configuration.
     *
     * @param array $config
     * @return self
     */
    public function setConfig(array $config): self;

    /**
     * Get the timestamp of the last run.
     *
     * @return string|null
     */
    public function getLastRunAt(): ?string;

    /**
     * Set the timestamp of the last run.
     *
     * @param string|null $datetime
     * @return self
     */
    public function setLastRunAt(?string $datetime): self;

    /**
     * Get the status of the last run.
     *
     * @return string|null
     */
    public function getLastStatus(): ?string;

    /**
     * Set the status of the last run.
     *
     * @param string|null $status
     * @return self
     */
    public function setLastStatus(?string $status): self;

    /**
     * Get the last-run status message.
     *
     * @return string|null
     */
    public function getLastMessage(): ?string;

    /**
     * Set the last-run status message.
     *
     * @param string|null $message
     * @return self
     */
    public function setLastMessage(?string $message): self;
}
