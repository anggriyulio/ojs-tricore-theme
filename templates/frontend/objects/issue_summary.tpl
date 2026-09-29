{**
 * plugins/themes/tricore/templates/frontend/objects/issue_summary.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3.
 *
 * @brief Issue summary (archive card). Same markup as the OJS template
 *  (identical in 3.3, 3.4 and 3.5) plus the article views/downloads total.
 *
 * @uses $issue Issue The issue
 * @uses $tricoreIssueMetrics array|null Metrics::present() per issue id, set on the archive page
 *}
{if $issue->getShowTitle()}
	{assign var=issueTitle value=$issue->getLocalizedTitle()}
{/if}
{assign var=issueSeries value=$issue->getIssueSeries()}
{assign var=issueCover value=$issue->getLocalizedCoverImageUrl()}

<div class="obj_issue_summary">

	{if $issueCover}
		<a class="cover" href="{url op="view" path=$issue->getBestIssueId()}">
			<img src="{$issueCover|escape}" alt="{$issue->getLocalizedCoverImageAltText()|escape|default:''}">
		</a>
	{/if}

	<div class="tricore_issue_body">
	<h2>
		<a class="title" href="{url op="view" path=$issue->getBestIssueId()}">
			{if $issueTitle}
				{$issueTitle|escape}
			{else}
				{$issueSeries|escape}
			{/if}
		</a>
		{if $issueTitle && $issueSeries}
			<div class="series">
				{$issueSeries|escape}
			</div>
		{/if}
	</h2>

	<div class="description">
		{$issue->getLocalizedDescription()|strip_unsafe_html}
	</div>

	{if $tricoreIssueMetrics && $tricoreIssueMetrics[$issue->getId()]}
		{capture assign="tricoreIssueMetricsTitle"}{translate key="plugins.themes.tricore.metrics.issue"}{/capture}
		{include file="frontend/components/tricore/metricsInline.tpl" metrics=$tricoreIssueMetrics[$issue->getId()] metricsTitle=$tricoreIssueMetricsTitle}
	{/if}
	</div>
</div><!-- .obj_issue_summary -->
