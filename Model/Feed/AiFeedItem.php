<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Feed;

use Gtstudio\Llmo\Api\Data\AiFeedItemInterface;
use Magento\Framework\DataObject;

class AiFeedItem extends DataObject implements AiFeedItemInterface
{
    public function getId(): string
    {
        return (string) $this->getData('id');
    }

    public function setId(string $id): self
    {
        return $this->setData('id', $id);
    }

    public function getGtin(): ?string
    {
        $value = $this->getData('gtin');
        return $value === null ? null : (string) $value;
    }

    public function setGtin(?string $gtin): self
    {
        return $this->setData('gtin', $gtin);
    }

    public function getTitle(): string
    {
        return (string) $this->getData('title');
    }

    public function setTitle(string $title): self
    {
        return $this->setData('title', $title);
    }

    public function getDescription(): string
    {
        return (string) $this->getData('description');
    }

    public function setDescription(string $description): self
    {
        return $this->setData('description', $description);
    }

    public function getLink(): string
    {
        return (string) $this->getData('link');
    }

    public function setLink(string $link): self
    {
        return $this->setData('link', $link);
    }

    public function getImageLink(): ?string
    {
        $value = $this->getData('image_link');
        return $value === null ? null : (string) $value;
    }

    public function setImageLink(?string $imageLink): self
    {
        return $this->setData('image_link', $imageLink);
    }

    public function getAdditionalImageLinks(): array
    {
        return (array) ($this->getData('additional_image_links') ?? []);
    }

    public function setAdditionalImageLinks(array $links): self
    {
        return $this->setData('additional_image_links', $links);
    }

    public function getAvailability(): string
    {
        return (string) ($this->getData('availability') ?? 'out_of_stock');
    }

    public function setAvailability(string $availability): self
    {
        return $this->setData('availability', $availability);
    }

    public function getPrice(): ?float
    {
        $value = $this->getData('price');
        return $value === null ? null : (float) $value;
    }

    public function setPrice(?float $price): self
    {
        return $this->setData('price', $price);
    }

    public function getCurrency(): string
    {
        return (string) $this->getData('currency');
    }

    public function setCurrency(string $currency): self
    {
        return $this->setData('currency', $currency);
    }

    public function getBrand(): ?string
    {
        $value = $this->getData('brand');
        return $value === null ? null : (string) $value;
    }

    public function setBrand(?string $brand): self
    {
        return $this->setData('brand', $brand);
    }

    public function getCondition(): string
    {
        return (string) ($this->getData('condition') ?? 'new');
    }

    public function setCondition(string $condition): self
    {
        return $this->setData('condition', $condition);
    }

    public function getCategoryPath(): array
    {
        return (array) ($this->getData('category_path') ?? []);
    }

    public function setCategoryPath(array $path): self
    {
        return $this->setData('category_path', $path);
    }

    public function getAdditionalAttributes(): array
    {
        return (array) ($this->getData('additional_attributes') ?? []);
    }

    public function setAdditionalAttributes(array $attributes): self
    {
        return $this->setData('additional_attributes', $attributes);
    }
}
