{**
 * plugins/themes/tricore/templates/frontend/components/tricore/metricsInline.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Compact views/downloads counters (article lists, cards, archive).
 *
 * @uses $metrics array views, downloads, viewsShort, downloadsShort (Metrics::present)
 * @uses $metricsTitle string|null Optional explanation shown as a tooltip
 *}
<ul class="tricore_metrics_inline"{if $metricsTitle} title="{$metricsTitle|escape}"{/if}>
	<li class="tricore_metrics_views">
		<span class="fa fa-eye" aria-hidden="true"></span>
		<span aria-hidden="true">{$metrics.viewsShort|escape}</span>
		<span class="pkp_screen_reader">{translate key="plugins.themes.tricore.metrics.viewsCount" number=$metrics.views}</span>
	</li>
	<li class="tricore_metrics_downloads">
		<span class="fa fa-download" aria-hidden="true"></span>
		<span aria-hidden="true">{$metrics.downloadsShort|escape}</span>
		<span class="pkp_screen_reader">{translate key="plugins.themes.tricore.metrics.downloadsCount" number=$metrics.downloads}</span>
	</li>
</ul>
