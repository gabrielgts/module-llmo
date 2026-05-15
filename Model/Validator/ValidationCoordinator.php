<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Validator;

use Gtstudio\Llmo\Api\Data\AiFeedInterface;
use Gtstudio\Llmo\Api\Data\ValidationResultInterface;
use Gtstudio\Llmo\Api\Validator\QualityValidatorInterface;
use Gtstudio\Llmo\Api\Validator\SchemaValidatorInterface;
use Gtstudio\Llmo\Model\Feed\ExporterPool;

/**
 * Runs both schema and quality validators over a feed and produces a single
 * merged result. The schema validator gets the formatted body (via the
 * exporter); the quality validator gets the structured DTO.
 */
class ValidationCoordinator
{
    /**
     * @param ExporterPool $exporterPool
     * @param QualityValidatorInterface $qualityValidator
     * @param ValidationResultBuilder $resultBuilder
     * @param SchemaValidatorInterface $schemaValidator
     */
    // phpcs:ignore
    public function __construct(
        private readonly ExporterPool $exporterPool,
        private readonly QualityValidatorInterface $qualityValidator,
        private readonly ValidationResultBuilder $resultBuilder,
        private readonly SchemaValidatorInterface $schemaValidator
    ) {
    }

    /**
     * Run all validators against the given feed and return a merged result.
     *
     * @param AiFeedInterface $feed
     * @return ValidationResultInterface
     */
    public function validate(AiFeedInterface $feed): ValidationResultInterface
    {
        $exporterCode = $feed->getExporterCode();
        $exporter = $this->exporterPool->get($exporterCode);
        $body = $exporter->format($feed);

        $schemaResult = $this->schemaValidator->validateBody($exporterCode, $body);
        $qualityResult = $this->qualityValidator->validateFeed($feed);

        return $this->resultBuilder->merge($exporterCode, [$schemaResult, $qualityResult]);
    }
}
