# Gtstudio_Llmo

Large Language Model Optimization (LLMO) for Magento 2 — the AI-shopping equivalent of SEO. Surfaces your catalog to ChatGPT, Claude, Perplexity, Google AI Overviews, and emerging agentic-commerce platforms via a structured product feed; controls which AI crawlers are allowed; attributes orders to AI traffic; and reports visibility on a dedicated dashboard.

## AI Studio Ecosystem

Part of the **AI Studio** suite for Magento 2. See all modules:

| Module | Repository | Description |
|--------|-----------|-------------|
| **Gtstudio_AiConnector** | [module-aiconnector](https://github.com/gabrielgts/module-aiconnector) | Core AI provider abstraction |
| **Gtstudio_AiAgents** | [module-ai-agents](https://github.com/gabrielgts/module-ai-agents) | Agent & tool orchestration, cron scheduling, execution log |
| **Gtstudio_AiWidgets** | [module-ai-widgets](https://github.com/gabrielgts/module-ai-widgets) | Floating admin chat widget + PageBuilder AI generator |
| **Gtstudio_AiDataQuery** | [module-ai-data-query](https://github.com/gabrielgts/module-ai-data-query) | Natural-language store analytics (privacy-first) |
| **Gtstudio_AiKnowledgeBase** | [module-ai-knowledge-base](https://github.com/gabrielgts/module-ai-knowledge-base) | Document upload & RAG retrieval for agents |
| **Gtstudio_AiDashboard** | [module-ai-dashboard](https://github.com/gabrielgts/module-ai-dashboard) | AI-powered KPI dashboard with ML insights |
| **Gtstudio_Llmo** | *(this module)* | LLMO product feed, AI crawler controls, attribution, visibility dashboard |

## What It Does

- **ACP-style product feed** — exports the catalog as a structured JSON feed modeled on the Agentic Commerce Protocol. Pluggable exporters for future targets (Perplexity Ads, Google AI Shopping, etc.).
- **Feed validation** — ships a JSON Schema for ACP and runs quality checks (missing images, short descriptions, duplicate IDs); validation history persisted.
- **AI crawler controls** — picks which AI bots (GPTBot, ClaudeBot, PerplexityBot, Google-Extended, …) are allowed in `robots.txt` and logs every hit.
- **AI-ready content fields** — adds 5 EAV product attributes (`llmo_ai_summary`, `llmo_ai_keywords`, `llmo_ai_use_cases`, `llmo_ai_faq`, `llmo_excluded_from_feed`) flowing into the feed.
- **Attribution & conversion tracking** — captures UTM parameters and AI-bot referrers on first touch, persists them on `sales_order`, and writes a denormalized event table for fast dashboard reads.
- **Campaign / export abstraction** — DB-backed campaign records reference exporters in a registry pool; foundation for future paid-AI-ads adapters.
- **LLMO dashboard** — feed health, crawler activity, attribution funnel, and top products by AI source. Cron-built, cache-aside, optional AI insights via `Gtstudio_AiAgents`.

## Requirements

- Magento 2.4.4+
- PHP 8.1+
- `gtstudio/module-aiconnector`
- `gtstudio/module-ai-agents`

## Installation

```bash
composer require gtstudio/module-llmo
bin/magento module:enable Gtstudio_Llmo
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

## Configuration

`Stores → Configuration → Service → LLMO`

- **General → Enable LLMO** — master switch.
- **Product Feed** — builder page size, rebuild cron schedule.
- **AI Crawler Controls** — allowed bots multiselect, extra `robots.txt` directives, hit-log sampling rate.
- **Dashboard** — snapshot TTL, rebuild cron schedule.

## Admin

`AI Studio → LLMO`

- **Dashboard** — visibility snapshot.
- **Product Feed** — run history, manual rebuild.
- **Validation Log** — schema + quality validation runs.
- **Crawlers** — hit log per bot.
- **Attribution** — conversion events by AI source.
- **Campaigns** — export-target CRUD.
- **Settings** — direct link to system config.

## License

Business Source License 1.1 — see [LICENSE](LICENSE). Converts to MIT four years after release.
