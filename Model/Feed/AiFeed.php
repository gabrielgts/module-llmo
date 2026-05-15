<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Feed;

use Gtstudio\Llmo\Api\Data\AiFeedInterface;
use Gtstudio\Llmo\Api\Data\AiFeedItemInterface;
use Magento\Framework\DataObject;

class AiFeed extends DataObject implements AiFeedInterface
{
    public function getExporterCode(): string
    {
        return (string) $this->getData('exporter_code');
    }

    public function setExporterCode(string $code): self
    {
        return $this->setData('exporter_code', $code);
    }

    public function getStoreCode(): string
    {
        return (string) $this->getData('store_code');
    }

    public function setStoreCode(string $storeCode): self
    {
        return $this->setData('store_code', $storeCode);
    }

    public function getCurrency(): string
    {
        return (string) $this->getData('currency');
    }

    public function setCurrency(string $currency): self
    {
        return $this->setData('currency', $currency);
    }

    public function getGeneratedAt(): string
    {
        return (string) $this->getData('generated_at');
    }

    public function setGeneratedAt(string $generatedAt): self
    {
        return $this->setData('generated_at', $generatedAt);
    }

    public function getCount(): int
    {
        return (int) $this->getData('count');
    }

    public function setCount(int $count): self
    {
        return $this->setData('count', $count);
    }

    /** @return AiFeedItemInterface[] */
    public function getItems(): array
    {
        return (array) ($this->getData('items') ?? []);
    }

    public function setItems(array $items): self
    {
        return $this->setData('items', $items);
    }
}
