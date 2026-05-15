<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Api\Validator;

use Gtstudio\Llmo\Api\Data\ValidationResultInterface;

/**
 * Validates the raw, formatted feed body against a JSON Schema bundled with
 * each exporter. Registered implementations are looked up by exporter code.
 *
 * @api
 */
interface SchemaValidatorInterface
{
    /**
     * Validate the formatted feed body against this exporter's schema.
     *
     * @param string $exporterCode Exporter that produced the body.
     * @param string $body         Raw feed body (already serialised).
     * @return ValidationResultInterface
     */
    public function validateBody(string $exporterCode, string $body): ValidationResultInterface;
}
