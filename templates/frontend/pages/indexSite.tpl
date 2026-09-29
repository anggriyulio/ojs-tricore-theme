{**
 * plugins/themes/tricore/templates/frontend/pages/indexSite.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3.
 *
 * @brief Site homepage as a light landing page: soft light-curtain
 *  background, centered headline with a badge of journal covers, the
 *  journals as plan-style cards (one highlighted), a search across all
 *  journals with site statistics, and a call to action.
 *
 * @uses $about string Site "About" text (Administration > Site Settings)
 * @uses $journals array Journals of the site
 * @uses $journalFilesPath string Base URL of the journals' public files
 * @uses $tricoreTheme.site array SiteStats::summarize() (journals, issues, articles, perJournal)
 *}
{include file="frontend/components/header.tpl"}
{assign var="tricoreSiteStats" value=$tricoreTheme.site}
{assign var="tricoreJournalCount" value=$journals|@count}

<div class="page_index_site tricore_landing">

	<section class="tricore_landing_hero" aria-labelledby="tricoreSiteTitle">
		{include file="frontend/components/tricore/landingBackground.tpl"}

		<div class="tricore_landing_inner">
			<p class="tricore_landing_badge">
				{if $tricoreJournalCount}
					<span class="tricore_landing_avatars" aria-hidden="true">
						{foreach from=$journals item=journal name=avatars}
							{if $smarty.foreach.avatars.iteration > 4}{break}{/if}
							{assign var="thumb" value=$journal->getLocalizedData('journalThumbnail')}
							{if $thumb}
								<img src="{$journalFilesPath}{$journal->getId()}/{$thumb.uploadName|escape:"url"}" alt="">
							{else}
								<span>{$journal->getLocalizedName()|truncate:1:""|escape}</span>
							{/if}
						{/foreach}
					</span>
				{/if}
				<span>{translate key="plugins.themes.tricore.site.badge" journals=$tricoreSiteStats.journals articles=$tricoreSiteStats.articles}</span>
			</p>
			<h2 class="tricore_landing_title" id="tricoreSiteTitle">{$siteTitle|default:$applicationName|escape}</h2>
			{if $about}
				<p class="tricore_landing_lead">{$about|strip_tags|unescape:"html"|truncate:260:"…"|escape}</p>
			{/if}
		</div>

		{if !$tricoreJournalCount}
			<p class="tricore_landing_empty">{translate key="site.noJournals"}</p>
		{else}
			<h2 class="pkp_screen_reader" id="tricoreJournals">{translate key="plugins.themes.tricore.site.journals"}</h2>
			<ul class="tricore_plan_grid{if $tricoreJournalCount == 1} is_single{/if}">
				{foreach from=$journals item=journal name=plans}
					{capture assign="url"}{url journal=$journal->getPath()}{/capture}
					{assign var="thumb" value=$journal->getLocalizedData('journalThumbnail')}
					{assign var="description" value=$journal->getLocalizedDescription()}
					{assign var="journalStats" value=$tricoreSiteStats.perJournal[$journal->getId()]}
					{assign var="acronym" value=$journal->getLocalizedAcronym()}
					{assign var="onlineIssn" value=$journal->getData('onlineIssn')}
					{assign var="printIssn" value=$journal->getData('printIssn')}
					{* Highlight the middle card of the first row (the first one if fewer than 3) *}
					{if ($tricoreJournalCount >= 3 && $smarty.foreach.plans.iteration == 2) || ($tricoreJournalCount < 3 && $smarty.foreach.plans.first)}
						{assign var="featured" value=true}
					{else}
						{assign var="featured" value=false}
					{/if}
					<li class="tricore_plan{if $featured} is_featured{/if}">
						<div class="tricore_plan_head">
							<div class="tricore_plan_head_text">
								<p class="tricore_plan_kicker">{$acronym|default:$journal->getPath()|escape}</p>
								{if $journalStats}
									<p class="tricore_plan_tagline">
										{translate key="plugins.themes.tricore.site.issuesCount" number=$journalStats.issues}
										&middot;
										{translate key="plugins.themes.tricore.site.articlesCount" number=$journalStats.articles}
									</p>
								{/if}
							</div>
							{if $thumb}
								<img class="tricore_plan_cover" src="{$journalFilesPath}{$journal->getId()}/{$thumb.uploadName|escape:"url"}" alt="" loading="lazy">
							{/if}
						</div>

						<h3 class="tricore_plan_name">
							<a href="{$url}" rel="bookmark">{$journal->getLocalizedName()|escape}</a>
						</h3>
						{if $description}
							<p class="tricore_plan_desc">{$description|strip_tags|unescape:"html"|truncate:220:"…"|escape}</p>
						{/if}

						<div class="tricore_plan_actions">
							<a class="tricore_plan_btn" href="{$url}">
								{translate key="site.journalView"}
								<span class="fa fa-angle-right" aria-hidden="true"></span>
							</a>
							<a class="tricore_plan_link" href="{url journal=$journal->getPath() page="issue" op="current"}">{translate key="site.journalCurrent"}</a>
						</div>

						{if $onlineIssn || $printIssn}
							<div class="tricore_plan_foot">
								<p class="tricore_plan_foot_label">ISSN</p>
								<ul class="tricore_plan_foot_items">
									{if $onlineIssn}<li><span class="fa fa-globe" aria-hidden="true"></span> {$onlineIssn|escape} <span class="tricore_plan_foot_note">{translate key="plugins.themes.tricore.site.online"}</span></li>{/if}
									{if $printIssn}<li><span class="fa fa-print" aria-hidden="true"></span> {$printIssn|escape} <span class="tricore_plan_foot_note">{translate key="plugins.themes.tricore.site.print"}</span></li>{/if}
								</ul>
							</div>
						{/if}
					</li>
				{/foreach}
			</ul>
		{/if}
	</section>

	{* Highlights carousel (new in OJS 3.5) *}
	{if $highlights && $highlights->count()}
		{include file="frontend/components/highlights.tpl" highlights=$highlights}
	{/if}

	{* Site announcements (OJS 3.4+) *}
	{include file="frontend/objects/announcements_list.tpl" numAnnouncements=$numAnnouncementsHomepage}

	<section class="tricore_landing_search" aria-labelledby="tricoreSearchTitle">
		<h2 class="tricore_landing_search_title" id="tricoreSearchTitle">
			{translate key="plugins.themes.tricore.site.searchTitle"} <span class="tricore_landing_star" aria-hidden="true">*</span>
		</h2>
		<p class="tricore_landing_search_lead">{translate key="plugins.themes.tricore.site.searchLead"}</p>
		<form class="tricore_landing_search_form" method="get" action="{url page="search" op="search"}" role="search">
			<label class="pkp_screen_reader" for="tricoreSiteQuery">{translate key="common.searchQuery"}</label>
			<span class="fa fa-search" aria-hidden="true"></span>
			<input type="search" id="tricoreSiteQuery" name="query" placeholder="{translate|escape key="plugins.themes.tricore.site.searchPlaceholder"}">
			<button type="submit" class="tricore_plan_btn">{translate key="common.search"}</button>
		</form>
		{if $tricoreSiteStats}
			<dl class="tricore_landing_stats">
				<div class="tricore_landing_stat">
					<dt>{translate key="plugins.themes.tricore.site.stats.journals"}</dt>
					<dd>{$tricoreSiteStats.journals|escape}</dd>
				</div>
				<div class="tricore_landing_stat">
					<dt>{translate key="plugins.themes.tricore.site.stats.issues"}</dt>
					<dd>{$tricoreSiteStats.issues|escape}</dd>
				</div>
				<div class="tricore_landing_stat">
					<dt>{translate key="plugins.themes.tricore.site.stats.articles"}</dt>
					<dd>{$tricoreSiteStats.articles|escape}</dd>
				</div>
			</dl>
		{/if}
		<p class="tricore_landing_note">* {translate key="plugins.themes.tricore.site.searchNote"}</p>
	</section>

	{if !$isUserLoggedIn}
		<section class="tricore_landing_cta" aria-labelledby="tricoreCtaTitle">
			{include file="frontend/components/tricore/landingBackground.tpl"}
			<div class="tricore_landing_cta_inner">
				<h2 class="tricore_landing_cta_title" id="tricoreCtaTitle">{translate key="plugins.themes.tricore.site.ctaTitle"}</h2>
				<p class="tricore_landing_cta_text">{translate key="plugins.themes.tricore.site.ctaText"}</p>
				<a class="tricore_plan_btn" href="{url page="user" op="register"}">
					{translate key="plugins.themes.tricore.site.register"}
					<span class="fa fa-angle-right" aria-hidden="true"></span>
				</a>
			</div>
		</section>
	{/if}

</div><!-- .page -->

{include file="frontend/components/footer.tpl"}
