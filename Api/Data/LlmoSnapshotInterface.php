<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Api\Data;

/**
 * Aggregate snapshot of LLMO visibility & performance metrics shown on the
 * dashboard. Built by collectors, cached by `SnapshotService`.
 *
 * @api
 */
interface LlmoSnapshotInterface
{
    /**
     * Get the UTC timestamp when this snapshot was built.
     *
     * @return string
     */
    public function getGeneratedAt(): string;

    /**
     * Set the generated-at timestamp.
     *
     * @param string $generatedAt
     * @return self
     */
    public function setGeneratedAt(string $generatedAt): self;

    /**
     * Get feed health rows.
     *
     * @return array
     */
    public function getFeedHealth(): array;

    /**
     * Set feed health rows.
     *
     * @param array $rows
     * @return self
     */
    public function setFeedHealth(array $rows): self;

    /**
     * Get crawler activity rows.
     *
     * @return array
     */
    public function getCrawlerActivity(): array;

    /**
     * Set crawler activity rows.
     *
     * @param array $rows
     * @return self
     */
    public function setCrawlerActivity(array $rows): self;

    /**
     * Get attribution funnel rows.
     *
     * @return array
     */
    public function getAttributionFunnel(): array;

    /**
     * Set attribution funnel rows.
     *
     * @param array $rows
     * @return self
     */
    public function setAttributionFunnel(array $rows): self;

    /**
     * Get top-products rows.
     *
     * @return array
     */
    public function getTopProducts(): array;

    /**
     * Set top-products rows.
     *
     * @param array $rows
     * @return self
     */
    public function setTopProducts(array $rows): self;
}
