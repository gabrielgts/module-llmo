<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Api;

use Gtstudio\Llmo\Api\Data\ValidationResultInterface;

/**
 * Runs the configured validators against a fresh build of the feed and
 * persists the result in the validation log.
 *
 * @api
 */
interface ValidateFeedInterface
{
    /**
     * Build, validate, and log a result for the given exporter + store.
     *
     * @param string $exporter Exporter code; defaults to "acp".
     * @param string|null $storeCode Store code; defaults to current store.
     * @return ValidationResultInterface
     */
    public function execute(string $exporter = 'acp', ?string $storeCode = null): ValidationResultInterface;
}
