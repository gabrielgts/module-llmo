<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Feed;

use Gtstudio\Llmo\Api\Feed\ExporterInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ExporterPool
{
    /** @param ExporterInterface[] $exporters */
    // phpcs:ignore
    public function __construct(
        private readonly array $exporters = []
    ) {
    }

    /** @throws NoSuchEntityException */
    public function get(string $code): ExporterInterface
    {
        if (!isset($this->exporters[$code])) {
            throw new NoSuchEntityException(__('No LLMO exporter registered for code "%1".', $code));
        }
        return $this->exporters[$code];
    }

    public function has(string $code): bool
    {
        return isset($this->exporters[$code]);
    }

    /** @return ExporterInterface[] */
    public function all(): array
    {
        return $this->exporters;
    }
}
