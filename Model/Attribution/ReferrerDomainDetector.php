<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Attribution;

/**
 * Derives a canonical AI-platform name from an HTTP `Referer` header value.
 *
 * Covers direct user traffic arriving from AI assistant web UIs — the
 * complement to `UserAgentDetector`, which handles AI crawlers.
 */
class ReferrerDomainDetector
{
    /**
     * Map of needle (checked via `stripos` against the host) to source label.
     *
     * Longer / more-specific entries come first so they win over shorter ones.
     *
     * @var array<string, string>
     */
    private const DOMAIN_MAP = [
        'chat.openai.com' => 'chatgpt',
        'chatgpt.com' => 'chatgpt',
        'copilot.microsoft.com' => 'copilot',
        'bing.com/chat' => 'copilot',
        'gemini.google.com' => 'gemini',
        'bard.google.com' => 'gemini',
        'claude.ai' => 'claude',
        'perplexity.ai' => 'perplexity',
        'you.com' => 'you',
        'phind.com' => 'phind',
        'poe.com' => 'poe',
        'character.ai' => 'character-ai',
        'pi.ai' => 'pi',
    ];

    /**
     * Derive the canonical source name from a raw `Referer` URL.
     *
     * Returns null when the referrer is empty or does not belong to a known
     * AI platform — caller should treat `null` as "no AI referrer detected".
     *
     * @param string $refererHeader Raw value of the HTTP Referer header.
     * @return string|null Canonical label (e.g. `chatgpt`, `perplexity`) or null.
     */
    public function detect(string $refererHeader): ?string
    {
        if ($refererHeader === '') {
            return null;
        }

        $host = $this->extractHost($refererHeader);

        foreach (self::DOMAIN_MAP as $needle => $label) {
            if (\stripos($host, $needle) !== false) {
                return $label;
            }
        }

        return null;
    }

    /**
     * Return all known AI platform labels for UI display.
     *
     * @return string[]
     */
    public function knownLabels(): array
    {
        return \array_unique(\array_values(self::DOMAIN_MAP));
    }

    /**
     * Extract the `host + path` portion of a URL for matching.
     *
     * Falls back to the raw string when the regex produces no groups.
     *
     * @param string $url
     * @return string
     */
    private function extractHost(string $url): string
    {
        if (\preg_match('~^(?:https?://)?([^/?#\s]+)([^?#]*)~i', $url, $matches) === 1) {
            return $matches[1] . $matches[2];
        }
        return $url;
    }
}
