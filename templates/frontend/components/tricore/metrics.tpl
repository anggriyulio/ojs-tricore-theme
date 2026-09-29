{**
 * plugins/themes/tricore/templates/frontend/components/tricore/metrics.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Journal metrics entered as "Label|Value" lines in the theme options.
 *
 * @uses $metrics array [['label' => '...', 'value' => '...'], ...]
 *}
<section class="tricore_metrics" aria-labelledby="tricoreMetricsTitle">
	<h2 class="pkp_screen_reader" id="tricoreMetricsTitle">{translate key="plugins.themes.tricore.metrics"}</h2>
	<dl class="tricore_metrics_list">
		{foreach from=$metrics item=metric}
			<div class="tricore_metric">
				<dt class="tricore_metric_label">{$metric.label|escape}</dt>
				<dd class="tricore_metric_value">{$metric.value|escape}</dd>
			</div>
		{/foreach}
	</dl>
</section>
