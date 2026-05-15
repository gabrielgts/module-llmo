<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Api;

use Gtstudio\Llmo\Api\Data\AiFeedInterface;

/**
 * Returns a built LLMO product feed for a given exporter and store.
 *
 * Used by admin tooling and validators. AI crawlers should hit the static
 * file served by the frontend controller, not this endpoint.
 *
 * @api
 */
interface GetFeedInterface
{
    /**
     * @param string $exporter Exporter code (e.g. "acp"); defaults to "acp".
     * @param string|null $storeCode Store code; defaults to current store.
     * @return \Gtstudio\Llmo\Api\Data\AiFeedInterface
     */
    public function execute(string $exporter = 'acp', ?string $storeCode = null): AiFeedInterface;
}
