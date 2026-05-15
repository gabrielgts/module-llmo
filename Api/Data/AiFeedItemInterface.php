<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Api\Data;

/**
 * One product entry in an LLMO product feed.
 *
 * Field set is modeled on the Agentic Commerce Protocol product feed
 * schema; exporters may project to/from this DTO for other targets.
 *
 * @api
 */
interface AiFeedItemInterface
{
    public function getId(): string;

    public function setId(string $id): self;

    public function getGtin(): ?string;

    public function setGtin(?string $gtin): self;

    public function getTitle(): string;

    public function setTitle(string $title): self;

    public function getDescription(): string;

    public function setDescription(string $description): self;

    public function getLink(): string;

    public function setLink(string $link): self;

    public function getImageLink(): ?string;

    public function setImageLink(?string $imageLink): self;

    /** @return string[] */
    public function getAdditionalImageLinks(): array;

    /** @param string[] $links */
    public function setAdditionalImageLinks(array $links): self;

    /** @return string `in_stock` | `out_of_stock` | `preorder` */
    public function getAvailability(): string;

    public function setAvailability(string $availability): self;

    public function getPrice(): ?float;

    public function setPrice(?float $price): self;

    public function getCurrency(): string;

    public function setCurrency(string $currency): self;

    public function getBrand(): ?string;

    public function setBrand(?string $brand): self;

    /** @return string `new` | `refurbished` | `used` */
    public function getCondition(): string;

    public function setCondition(string $condition): self;

    /** @return string[] */
    public function getCategoryPath(): array;

    /** @param string[] $path */
    public function setCategoryPath(array $path): self;

    /** @return array<string, mixed> */
    public function getAdditionalAttributes(): array;

    /** @param array<string, mixed> $attributes */
    public function setAdditionalAttributes(array $attributes): self;
}
