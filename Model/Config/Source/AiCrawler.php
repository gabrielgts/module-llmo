<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AiCrawler implements OptionSourceInterface
{
    public const BOTS = [
        'GPTBot' => 'OpenAI — GPTBot (training crawler)',
        'OAI-SearchBot' => 'OpenAI — SearchBot (ChatGPT search)',
        'ChatGPT-User' => 'OpenAI — ChatGPT-User (live retrieval)',
        'ClaudeBot' => 'Anthropic — ClaudeBot (training crawler)',
        'anthropic-ai' => 'Anthropic — anthropic-ai (legacy)',
        'Claude-Web' => 'Anthropic — Claude-Web (live retrieval)',
        'PerplexityBot' => 'Perplexity — PerplexityBot',
        'Perplexity-User' => 'Perplexity — Perplexity-User (live retrieval)',
        'Google-Extended' => 'Google — Google-Extended (Gemini training)',
        'GoogleOther' => 'Google — GoogleOther',
        'Applebot-Extended' => 'Apple — Applebot-Extended',
        'Bytespider' => 'ByteDance — Bytespider',
        'Amazonbot' => 'Amazon — Amazonbot',
        'Meta-ExternalAgent' => 'Meta — Meta-ExternalAgent',
        'Meta-ExternalFetcher' => 'Meta — Meta-ExternalFetcher',
        'CCBot' => 'Common Crawl — CCBot',
        'Diffbot' => 'Diffbot',
        'cohere-ai' => 'Cohere — cohere-ai',
        'YouBot' => 'You.com — YouBot',
    ];

    public function toOptionArray(): array
    {
        $options = [];
        foreach (self::BOTS as $value => $label) {
            $options[] = ['value' => $value, 'label' => $label];
        }
        return $options;
    }
}
