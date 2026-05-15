<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Validator;

use Gtstudio\Llmo\Api\Data\AiFeedInterface;
use Gtstudio\Llmo\Api\Data\AiFeedItemInterface;
use Gtstudio\Llmo\Api\Data\ValidationIssueInterface;
use Gtstudio\Llmo\Api\Data\ValidationResultInterface;
use Gtstudio\Llmo\Api\Validator\QualityValidatorInterface;

/**
 * Content-quality validator: catches missing images, thin descriptions,
 * duplicate ids, missing brand/GTIN — issues that the JSON Schema cannot
 * express as structural rules.
 *
 * All findings are reported as `warning` severity; quality is advisory.
 */
class QualityValidator implements QualityValidatorInterface
{
    public const SOURCE = 'quality';

    private const MIN_DESCRIPTION_LENGTH = 50;

    /**
     * @param ValidationResultBuilder $resultBuilder
     */
    // phpcs:ignore
    public function __construct(
        private readonly ValidationResultBuilder $resultBuilder
    ) {
    }

    /**
     * @inheritDoc
     */
    public function validateFeed(AiFeedInterface $feed): ValidationResultInterface
    {
        $issues = [];
        $seenIds = [];

        foreach ($feed->getItems() as $item) {
            $id = $item->getId();
            $path = 'products[' . $id . ']';

            if (isset($seenIds[$id])) {
                $issues[] = $this->warning('duplicate_id', \sprintf('Duplicate product id "%s".', $id), $path);
            }
            $seenIds[$id] = true;

            foreach ($this->inspectItem($item, $path) as $issue) {
                $issues[] = $issue;
            }
        }

        return $this->resultBuilder->build($feed->getExporterCode(), $issues);
    }

    /**
     * Inspect one feed item; return all issues raised.
     *
     * @param AiFeedItemInterface $item
     * @param string $path
     * @return array Array of issue rows.
     */
    private function inspectItem(AiFeedItemInterface $item, string $path): array
    {
        $issues = [];

        if ($item->getImageLink() === null) {
            $issues[] = $this->warning('missing_image', 'Product has no image link.', $path);
        }

        $description = \trim($item->getDescription());
        if ($description === '') {
            $issues[] = $this->warning('missing_description', 'Product description is empty.', $path);
        } elseif (\strlen($description) < self::MIN_DESCRIPTION_LENGTH) {
            $issues[] = $this->warning(
                'short_description',
                \sprintf('Description is shorter than %d characters.', self::MIN_DESCRIPTION_LENGTH),
                $path
            );
        }

        if ($item->getBrand() === null || $item->getBrand() === '') {
            $issues[] = $this->warning('missing_brand', 'Product has no brand attribute.', $path);
        }

        if ($item->getGtin() === null || $item->getGtin() === '') {
            $issues[] = $this->warning('missing_gtin', 'Product has no GTIN.', $path);
        }

        if ($item->getPrice() === null || $item->getPrice() <= 0.0) {
            $issues[] = $this->warning('missing_price', 'Product has no valid price.', $path);
        }

        return $issues;
    }

    /**
     * Build a warning row.
     *
     * @param string $code
     * @param string $message
     * @param string|null $path
     * @return array Issue row with severity, source, code, message, path keys.
     */
    private function warning(string $code, string $message, ?string $path): array
    {
        return [
            'severity' => ValidationIssueInterface::SEVERITY_WARNING,
            'source' => self::SOURCE,
            'code' => $code,
            'message' => $message,
            'path' => $path,
        ];
    }
}
