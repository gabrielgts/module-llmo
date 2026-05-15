<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Feed\Exporter;

use Gtstudio\Llmo\Api\Data\AiFeedInterface;
use Gtstudio\Llmo\Api\Data\AiFeedItemInterface;
use Gtstudio\Llmo\Api\Feed\ExporterInterface;

class AcpExporter implements ExporterInterface
{
    public const CODE = 'acp';

    public function code(): string
    {
        return self::CODE;
    }

    public function label(): string
    {
        return 'Agentic Commerce Protocol (ACP)';
    }

    public function format(AiFeedInterface $feed): string
    {
        $payload = [
            'version' => '1.0',
            'protocol' => 'agentic-commerce',
            'generated_at' => $feed->getGeneratedAt(),
            'store' => $feed->getStoreCode(),
            'currency' => $feed->getCurrency(),
            'count' => $feed->getCount(),
            'products' => \array_map([$this, 'mapItem'], $feed->getItems()),
        ];

        $json = \json_encode(
            $payload,
            \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT
        );

        if ($json === false) {
            throw new \RuntimeException('Failed to encode ACP feed: ' . \json_last_error_msg());
        }

        return $json;
    }

    public function mimeType(): string
    {
        return 'application/json';
    }

    public function fileExtension(): string
    {
        return 'json';
    }

    /** @return array<string, mixed> */
    private function mapItem(AiFeedItemInterface $item): array
    {
        $payload = [
            'id' => $item->getId(),
            'title' => $item->getTitle(),
            'description' => $item->getDescription(),
            'link' => $item->getLink(),
            'availability' => $item->getAvailability(),
            'condition' => $item->getCondition(),
            'price' => $item->getPrice(),
            'currency' => $item->getCurrency(),
        ];

        if ($item->getGtin() !== null) {
            $payload['gtin'] = $item->getGtin();
        }
        if ($item->getBrand() !== null) {
            $payload['brand'] = $item->getBrand();
        }
        if ($item->getImageLink() !== null) {
            $payload['image_link'] = $item->getImageLink();
        }
        if ($item->getAdditionalImageLinks() !== []) {
            $payload['additional_image_links'] = $item->getAdditionalImageLinks();
        }
        if ($item->getCategoryPath() !== []) {
            $payload['category_path'] = $item->getCategoryPath();
        }
        if ($item->getAdditionalAttributes() !== []) {
            $payload['additional_attributes'] = $item->getAdditionalAttributes();
        }

        return $payload;
    }
}
