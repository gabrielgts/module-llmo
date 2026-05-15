<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Robots;

use Gtstudio\Llmo\Model\Crawler\UserAgentDetector;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Composes the AI-bot section appended to `robots.txt`.
 *
 * Selected bots get `Allow: /`, everything else in the canonical list gets
 * `Disallow: /`, followed by any free-form `extra_directives` from config.
 */
class AiDirectivesBuilder
{
    private const CONFIG_ALLOWED_BOTS = 'llmo/crawlers/allowed_bots';
    private const CONFIG_EXTRA = 'llmo/crawlers/extra_directives';
    private const CONFIG_ENABLED = 'llmo/general/enabled';

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param UserAgentDetector $userAgentDetector
     */
    // phpcs:ignore
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly UserAgentDetector $userAgentDetector
    ) {
    }

    /**
     * Build the AI section of robots.txt for the given store scope.
     *
     * Returns an empty string if LLMO is disabled — caller appends raw.
     *
     * @param int|null $storeId
     * @return string
     */
    public function build(?int $storeId = null): string
    {
        if (!$this->scopeConfig->isSetFlag(self::CONFIG_ENABLED, ScopeInterface::SCOPE_STORE, $storeId)) {
            return '';
        }

        $allowed = $this->resolveAllowed($storeId);
        $lines = ['# --- LLMO AI bot directives ---'];

        foreach ($this->userAgentDetector->knownBotCodes() as $botCode) {
            $rule = isset($allowed[$botCode]) ? 'Allow' : 'Disallow';
            $lines[] = 'User-agent: ' . $botCode;
            $lines[] = $rule . ': /';
            $lines[] = '';
        }

        $extra = (string) $this->scopeConfig->getValue(
            self::CONFIG_EXTRA,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (\trim($extra) !== '') {
            $lines[] = '# --- LLMO extra directives ---';
            $lines[] = \trim($extra);
            $lines[] = '';
        }

        return \implode("\n", $lines);
    }

    /**
     * Resolve the allowed-bots set as `[bot_code => true]`.
     *
     * @param int|null $storeId
     * @return array<string, true>
     */
    private function resolveAllowed(?int $storeId): array
    {
        $raw = (string) $this->scopeConfig->getValue(
            self::CONFIG_ALLOWED_BOTS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($raw === '') {
            return [];
        }

        $codes = \array_filter(\array_map('trim', \explode(',', $raw)));
        return \array_fill_keys($codes, true);
    }
}
