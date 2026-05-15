<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Plugin\Robots;

use Gtstudio\Llmo\Model\Robots\AiDirectivesBuilder;
use Magento\Robots\Model\Robots as Subject;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Appends the LLMO AI-bot directives to the robots.txt body returned by
 * `Magento\Robots\Model\Robots::getData()`.
 */
class AfterGetData
{
    /**
     * @param AiDirectivesBuilder $directivesBuilder
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     */
    // phpcs:ignore
    public function __construct(
        private readonly AiDirectivesBuilder $directivesBuilder,
        private readonly LoggerInterface $logger,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * Append AI bot directives after the core merchant-configured robots block.
     *
     * @param Subject $subject
     * @param string $result
     * @return string
     */
    public function afterGetData(Subject $subject, string $result): string
    {
        try {
            $storeId = (int) $this->storeManager->getStore()->getId();
            $extra = $this->directivesBuilder->build($storeId);
            if ($extra === '') {
                return $result;
            }
            $separator = $result === '' ? '' : "\n\n";
            return $result . $separator . $extra;
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] Failed to append AI bot directives', [
                'exception' => $th,
            ]);
            return $result;
        }
    }
}
