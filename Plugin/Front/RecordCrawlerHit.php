<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Plugin\Front;

use Gtstudio\Llmo\Model\Crawler\HitRecorder;
use Magento\Framework\App\FrontController as Subject;
use Magento\Framework\App\RequestInterface;

/**
 * Records AI-bot hits before the front controller dispatches the request.
 *
 * Fast path: `HitRecorder` exits on the first config / UA check, so this
 * adds negligible overhead to non-bot traffic.
 */
class RecordCrawlerHit
{
    /**
     * @param HitRecorder $hitRecorder
     */
    // phpcs:ignore
    public function __construct(
        private readonly HitRecorder $hitRecorder
    ) {
    }

    /**
     * Inspect every dispatched request for an AI-bot User-Agent.
     *
     * @param Subject $subject
     * @param RequestInterface $request
     * @return void
     */
    public function beforeDispatch(Subject $subject, RequestInterface $request): void
    {
        $this->hitRecorder->record($request);
    }
}
