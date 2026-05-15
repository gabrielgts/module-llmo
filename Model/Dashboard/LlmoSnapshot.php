<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Dashboard;

use Gtstudio\Llmo\Api\Data\LlmoSnapshotInterface;
use Magento\Framework\DataObject;

/**
 * DataObject-backed implementation of {@see LlmoSnapshotInterface}.
 */
class LlmoSnapshot extends DataObject implements LlmoSnapshotInterface
{
    /**
     * @inheritDoc
     */
    public function getGeneratedAt(): string
    {
        return (string) $this->getData('generated_at');
    }

    /**
     * @inheritDoc
     */
    public function setGeneratedAt(string $generatedAt): self
    {
        return $this->setData('generated_at', $generatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getFeedHealth(): array
    {
        return (array) ($this->getData('feed_health') ?? []);
    }

    /**
     * @inheritDoc
     */
    public function setFeedHealth(array $rows): self
    {
        return $this->setData('feed_health', $rows);
    }

    /**
     * @inheritDoc
     */
    public function getCrawlerActivity(): array
    {
        return (array) ($this->getData('crawler_activity') ?? []);
    }

    /**
     * @inheritDoc
     */
    public function setCrawlerActivity(array $rows): self
    {
        return $this->setData('crawler_activity', $rows);
    }

    /**
     * @inheritDoc
     */
    public function getAttributionFunnel(): array
    {
        return (array) ($this->getData('attribution_funnel') ?? []);
    }

    /**
     * @inheritDoc
     */
    public function setAttributionFunnel(array $rows): self
    {
        return $this->setData('attribution_funnel', $rows);
    }

    /**
     * @inheritDoc
     */
    public function getTopProducts(): array
    {
        return (array) ($this->getData('top_products') ?? []);
    }

    /**
     * @inheritDoc
     */
    public function setTopProducts(array $rows): self
    {
        return $this->setData('top_products', $rows);
    }
}
