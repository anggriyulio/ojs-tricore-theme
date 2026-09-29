{**
 * plugins/themes/tricore/templates/frontend/components/tricore/articleMetrics.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Views/downloads under an article in a list. Rendered by the
 *  Templates::Issue::Issue::Article hook (end of article_summary.tpl).
 *
 * @uses $tricoreArticleMetrics array Metrics::present()
 *}
{include file="frontend/components/tricore/metricsInline.tpl" metrics=$tricoreArticleMetrics metricsTitle=null}
