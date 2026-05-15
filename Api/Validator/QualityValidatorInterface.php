<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Api\Validator;

use Gtstudio\Llmo\Api\Data\AiFeedInterface;
use Gtstudio\Llmo\Api\Data\ValidationResultInterface;

/**
 * Inspects the structured feed for content-quality issues that the schema
 * cannot catch (missing images, short descriptions, duplicate IDs, etc.).
 *
 * @api
 */
interface QualityValidatorInterface
{
    /**
     * Inspect the built feed for content quality issues.
     *
     * @param AiFeedInterface $feed
     * @return ValidationResultInterface
     */
    public function validateFeed(AiFeedInterface $feed): ValidationResultInterface;
}
