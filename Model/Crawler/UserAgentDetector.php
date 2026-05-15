<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Crawler;

/**
 * Matches a raw User-Agent string against the canonical list of AI crawler
 * tokens shipped with the module.
 *
 * Shared by Phase 3 (crawler hit log + robots.txt) and Phase 5 (order
 * attribution capture).
 */
class UserAgentDetector
{
    /**
     * Map of `bot_code` => list of case-insensitive UA needles.
     *
     * Ordering matters when one token is a substring of another: longer / more
     * specific tokens come first so they win the `stripos` race.
     *
     * @var array<string, string[]>
     */
    private const BOT_TOKENS = [
        'OAI-SearchBot' => ['OAI-SearchBot'],
        'ChatGPT-User' => ['ChatGPT-User'],
        'GPTBot' => ['GPTBot'],
        'Claude-Web' => ['Claude-Web'],
        'ClaudeBot' => ['ClaudeBot'],
        'anthropic-ai' => ['anthropic-ai'],
        'Perplexity-User' => ['Perplexity-User'],
        'PerplexityBot' => ['PerplexityBot'],
        'Google-Extended' => ['Google-Extended'],
        'GoogleOther' => ['GoogleOther'],
        'Applebot-Extended' => ['Applebot-Extended'],
        'Bytespider' => ['Bytespider'],
        'Amazonbot' => ['Amazonbot'],
        'Meta-ExternalFetcher' => ['Meta-ExternalFetcher'],
        'Meta-ExternalAgent' => ['Meta-ExternalAgent'],
        'CCBot' => ['CCBot'],
        'Diffbot' => ['Diffbot'],
        'cohere-ai' => ['cohere-ai'],
        'YouBot' => ['YouBot'],
    ];

    /**
     * Detect which AI crawler (if any) produced the given UA string.
     *
     * @param string $userAgent
     * @return string|null Bot code from `BOT_TOKENS` keys, or null for non-bots.
     */
    public function detect(string $userAgent): ?string
    {
        if ($userAgent === '') {
            return null;
        }

        foreach (self::BOT_TOKENS as $botCode => $needles) {
            foreach ($needles as $needle) {
                if (\stripos($userAgent, $needle) !== false) {
                    return $botCode;
                }
            }
        }

        return null;
    }

    /**
     * Full list of known bot codes.
     *
     * @return string[]
     */
    public function knownBotCodes(): array
    {
        return \array_keys(self::BOT_TOKENS);
    }
}
