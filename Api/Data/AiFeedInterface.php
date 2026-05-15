<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Api\Data;

/**
 * Container for a built LLMO product feed.
 *
 * @api
 */
interface AiFeedInterface
{
    public function getExporterCode(): string;

    public function setExporterCode(string $code): self;

    public function getStoreCode(): string;

    public function setStoreCode(string $storeCode): self;

    public function getCurrency(): string;

    public function setCurrency(string $currency): self;

    public function getGeneratedAt(): string;

    public function setGeneratedAt(string $generatedAt): self;

    public function getCount(): int;

    public function setCount(int $count): self;

    /** @return AiFeedItemInterface[] */
    public function getItems(): array;

    /** @param AiFeedItemInterface[] $items */
    public function setItems(array $items): self;
}
