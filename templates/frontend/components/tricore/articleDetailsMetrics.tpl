{**
 * plugins/themes/tricore/templates/frontend/components/tricore/articleDetailsMetrics.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Views/downloads in the details column of the article page.
 *  Rendered by the Templates::Article::Details hook.
 *
 * @uses $tricoreArticleMetrics array Metrics::present()
 *}
<div class="item tricore_article_metrics">
	<section class="sub_item">
		<h2 class="label">{translate key="plugins.themes.tricore.option.group.metrics"}</h2>
		<dl class="tricore_article_metrics_list">
			<div class="tricore_article_metric">
				<dt><span class="fa fa-eye" aria-hidden="true"></span> {translate key="plugins.themes.tricore.metrics.views"}</dt>
				<dd title="{$tricoreArticleMetrics.views|escape}">{$tricoreArticleMetrics.viewsShort|escape}</dd>
			</div>
			<div class="tricore_article_metric">
				<dt><span class="fa fa-download" aria-hidden="true"></span> {translate key="plugins.themes.tricore.metrics.downloads"}</dt>
				<dd title="{$tricoreArticleMetrics.downloads|escape}">{$tricoreArticleMetrics.downloadsShort|escape}</dd>
			</div>
		</dl>
	</section>
</div>
