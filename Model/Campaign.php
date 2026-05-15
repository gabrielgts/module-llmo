<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model;

use Gtstudio\Llmo\Api\Data\CampaignInterface;
use Gtstudio\Llmo\Model\ResourceModel\Campaign as CampaignResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Default implementation of {@see CampaignInterface}.
 */
class Campaign extends AbstractModel implements CampaignInterface
{
    /** @var string */
    protected $_eventPrefix = 'gtstudio_llmo_campaign';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(CampaignResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getCampaignId(): ?int
    {
        $id = $this->getData('campaign_id');
        return $id === null ? null : (int) $id;
    }

    /**
     * @inheritDoc
     */
    public function setCampaignId(int $id): self
    {
        return $this->setData('campaign_id', $id);
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
    public function getName(): string
    {
        return (string) $this->getData('name');
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): self
    {
        return $this->setData('name', $name);
    }

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
    public function getTargetUrl(): ?string
    {
        $value = $this->getData('target_url');
        return $value === null ? null : (string) $value;
    }

    /**
     * @inheritDoc
     */
    public function setTargetUrl(?string $url): self
    {
        return $this->setData('target_url', $url);
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return (int) $this->getData('is_active') === 1;
    }

    /**
     * @inheritDoc
     */
    public function setIsActive(bool $active): self
    {
        return $this->setData('is_active', $active ? 1 : 0);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $raw = (string) ($this->getData('config_json') ?? '');
        if ($raw === '') {
            return [];
        }
        $decoded = \json_decode($raw, true);
        return \is_array($decoded) ? $decoded : [];
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config): self
    {
        $encoded = \json_encode($config, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
        return $this->setData('config_json', $encoded === false ? null : $encoded);
    }

    /**
     * @inheritDoc
     */
    public function getLastRunAt(): ?string
    {
        $value = $this->getData('last_run_at');
        return $value === null ? null : (string) $value;
    }

    /**
     * @inheritDoc
     */
    public function setLastRunAt(?string $datetime): self
    {
        return $this->setData('last_run_at', $datetime);
    }

    /**
     * @inheritDoc
     */
    public function getLastStatus(): ?string
    {
        $value = $this->getData('last_status');
        return $value === null ? null : (string) $value;
    }

    /**
     * @inheritDoc
     */
    public function setLastStatus(?string $status): self
    {
        return $this->setData('last_status', $status);
    }

    /**
     * @inheritDoc
     */
    public function getLastMessage(): ?string
    {
        $value = $this->getData('last_message');
        return $value === null ? null : (string) $value;
    }

    /**
     * @inheritDoc
     */
    public function setLastMessage(?string $message): self
    {
        return $this->setData('last_message', $message);
    }
}
