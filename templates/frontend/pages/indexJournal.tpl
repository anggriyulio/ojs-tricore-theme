{**
 * plugins/themes/tricore/templates/frontend/pages/indexJournal.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3.
 *
 * @brief Journal homepage of the Tricore theme. Keeps the blocks and hook of the
 *  OJS homepage (announcements, current issue, additional content) and adds
 *  the Tricore blocks. Each block is a partial in components/tricore/ that child
 *  themes can override on its own.
 *
 * @uses $currentJournal Journal This journal
 * @uses $homepageImage object Image to be displayed on the homepage
 * @uses $additionalHomeContent string Arbitrary input from HTML text editor
 * @uses $announcements array List of announcements
 * @uses $numAnnouncementsHomepage int Number of announcements on the homepage
 * @uses $issue Issue Current issue
 * @uses $tricoreTheme array Data prepared by TricoreThemePlugin::loadTemplateData()
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$currentJournal->getLocalizedName()}

<div class="page_index_journal tricore_home">

	{call_hook name="Templates::Index::journal"}

	{if $tricoreTheme.showHero}
		{include file="frontend/components/tricore/hero.tpl"}
	{elseif $homepageImage}
		<div class="homepage_image">
			<img src="{$publicFilesDir}/{$homepageImage.uploadName|escape:"url"}"{if $homepageImage.altText} alt="{$homepageImage.altText|escape}"{/if}>
		</div>
	{/if}

	{if $tricoreTheme.metrics}
		{include file="frontend/components/tricore/metrics.tpl" metrics=$tricoreTheme.metrics}
	{/if}

	{* Journal Description *}
	{if $tricoreTheme.showDescription}
		<section class="tricore_section homepage_about">
			<a id="homepageAbout"></a>
			<h2 class="tricore_section_title">{translate key="about.aboutContext"}</h2>
			{$currentContext->getLocalizedData('description')}
		</section>
	{/if}

	{* Announcements *}
	{if $numAnnouncementsHomepage && $announcements|@count}
		<section class="tricore_section cmp_announcements highlight_first">
			<a id="homepageAnnouncements"></a>
			<h2 class="tricore_section_title">
				{translate key="announcement.announcements"}
			</h2>
			{foreach name=announcements from=$announcements item=announcement}
				{if $smarty.foreach.announcements.iteration > $numAnnouncementsHomepage}
					{break}
				{/if}
				{if $smarty.foreach.announcements.iteration == 1}
					{include file="frontend/objects/announcement_summary.tpl" heading="h3"}
					<div class="more">
				{else}
					<article class="obj_announcement_summary">
						<h4>
							<a href="{url router=$smarty.const.ROUTE_PAGE page="announcement" op="view" path=$announcement->getId()}">
								{$announcement->getLocalizedTitle()|escape}
							</a>
						</h4>
						<div class="date">
							{$announcement->getDatePosted()|date_format:$dateFormatShort}
						</div>
					</article>
				{/if}
			{/foreach}
			</div><!-- .more -->
		</section>
	{/if}

	{if $tricoreTheme.latestArticles}
		{include file="frontend/components/tricore/latestArticles.tpl" articles=$tricoreTheme.latestArticles}
	{/if}

	{* Latest issue *}
	{if $tricoreTheme.showCurrentIssue && $issue}
		<section class="tricore_section current_issue">
			<a id="homepageIssue"></a>
			<h2 class="tricore_section_title">
				{translate key="journal.currentIssue"}
			</h2>
			<div class="current_issue_title">
				{$issue->getIssueIdentification()|strip_unsafe_html}
			</div>
			{include file="frontend/objects/issue_toc.tpl" heading="h3"}
			<a href="{url router=$smarty.const.ROUTE_PAGE page="issue" op="archive"}" class="read_more tricore_btn tricore_btn_outline">
				{translate key="journal.viewAllIssues"}
			</a>
		</section>
	{/if}

	{if $tricoreTheme.indexingInfo}
		{include file="frontend/components/tricore/indexing.tpl" indexingInfo=$tricoreTheme.indexingInfo}
	{/if}

	{* Additional Homepage Content *}
	{if $additionalHomeContent}
		<div class="tricore_section additional_content">
			{$additionalHomeContent}
		</div>
	{/if}
</div><!-- .page -->

{include file="frontend/components/footer.tpl"}
