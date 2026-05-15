<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Api\Feed;

use Gtstudio\Llmo\Api\Data\AiFeedInterface;

/**
 * Contract for an LLMO feed exporter.
 *
 * Implementations format an `AiFeedInterface` into the wire format expected
 * by a specific consumer (ACP, Perplexity, Google AI Shopping, etc.).
 *
 * Register implementations in di.xml:
 *
 *   <type name="Gtstudio\Llmo\Model\Feed\ExporterPool">
 *       <arguments>
 *           <argument name="exporters" xsi:type="array">
 *               <item name="acp" xsi:type="object">Gtstudio\Llmo\Model\Feed\Exporter\AcpExporter</item>
 *           </argument>
 *       </arguments>
 *   </type>
 *
 * @api
 */
interface ExporterInterface
{
    /** Stable code identifying this exporter (e.g. `acp`, `perplexity`). */
    public function code(): string;

    /** Human-readable label for admin UI. */
    public function label(): string;

    /** Serialise the feed into the wire format. Returns the body to write/serve. */
    public function format(AiFeedInterface $feed): string;

    /** MIME type for the produced body, used when serving the feed over HTTP. */
    public function mimeType(): string;

    /** File extension (without leading dot) for the static feed file on disk. */
    public function fileExtension(): string;
}
