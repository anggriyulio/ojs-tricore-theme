{**
 * plugins/themes/tricore/templates/frontend/components/header.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3.
 *
 * @brief Site header of the Tricore theme. Based on the OJS 3.3 frontend header:
 *  keeps its template hooks, element IDs and skip link targets.
 *
 * @uses $isFullWidth bool Display the page without sidebars
 * @uses $tricoreTheme array Data prepared by TricoreThemePlugin::loadTemplateData()
 *}
{strip}
	{assign var="showingLogo" value=true}
	{if !$displayPageHeaderLogo}
		{assign var="showingLogo" value=false}
	{/if}
	{assign var="tricoreHeaderLayout" value=$tricoreTheme.headerLayout|default:"left"}
{/strip}
<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"}" xml:lang="{$currentLocale|replace:"_":"-"}">
{if !$pageTitleTranslated}{capture assign="pageTitleTranslated"}{translate key=$pageTitle}{/capture}{/if}
{include file="frontend/components/headerHead.tpl"}
<body class="pkp_page_{$requestedPage|escape|default:"index"} pkp_op_{$requestedOp|escape|default:"index"}{if $showingLogo} has_site_logo{/if} tricore_theme tricore_header_{$tricoreHeaderLayout|escape} tricore_page_{$tricoreTheme.pageKey|default:"other"|escape}" dir="{$currentLocaleLangDir|escape|default:"ltr"}" data-tricore-submenu-label="{translate|escape key="plugins.themes.tricore.submenu.toggle" title="%s"}">

	<div class="pkp_structure_page">

		<header class="tricore_header" id="headerNavigationContainer" role="banner">
			{include file="frontend/components/skipLinks.tpl"}

			<div class="tricore_header_inner tricore_container">
				<div class="tricore_brand">
					{if !$requestedPage || $requestedPage === 'index'}
						<h1 class="pkp_screen_reader">
							{if $currentContext}
								{$displayPageHeaderTitle|escape}
							{else}
								{$siteTitle|escape}
							{/if}
						</h1>
					{/if}
					{capture assign="homeUrl"}{url page="index" router=\PKP\core\PKPApplication::ROUTE_PAGE}{/capture}
					{if $displayPageHeaderLogo}
						<a href="{$homeUrl}" class="tricore_brand_link is_img">
							<img src="{$publicFilesDir}/{$displayPageHeaderLogo.uploadName|escape:"url"}" width="{$displayPageHeaderLogo.width|escape}" height="{$displayPageHeaderLogo.height|escape}" alt="{$displayPageHeaderLogo.altText|default:$displayPageHeaderTitle|escape}">
						</a>
					{elseif $displayPageHeaderTitle}
						<a href="{$homeUrl}" class="tricore_brand_link is_text">{$displayPageHeaderTitle|escape}</a>
					{else}
						<a href="{$homeUrl}" class="tricore_brand_link is_img">
							<img src="{$baseUrl}/templates/images/structure/logo.png" alt="{$applicationName|escape}" width="180" height="90">
						</a>
					{/if}
				</div>

				<button class="tricore_nav_toggle" type="button" aria-controls="tricoreSiteNav" aria-expanded="false">
					<span class="tricore_nav_toggle_icon" aria-hidden="true"></span>
					<span class="tricore_nav_toggle_label">{translate key="plugins.themes.tricore.menu.toggle"}</span>
				</button>

				<nav class="tricore_nav" id="tricoreSiteNav" aria-label="{translate|escape key="common.navigation.site"}">
					<a id="siteNav"></a>
					<div class="tricore_nav_primary">
						{load_menu name="primary" id="navigationPrimary" ulClass="tricore_menu tricore_menu_primary"}
					</div>
					<div class="tricore_nav_utility">
						{if $currentContext && $requestedPage !== 'search'}
							<a href="{url page="search"}" class="tricore_search_link">
								<span class="fa fa-search" aria-hidden="true"></span>
								<span class="tricore_search_label">{translate key="common.search"}</span>
							</a>
						{/if}
						<div class="tricore_nav_user" id="navigationUserWrapper">
							{load_menu name="user" id="navigationUser" ulClass="tricore_menu tricore_menu_user" liClass="profile"}
						</div>
					</div>
				</nav>
			</div>
		</header>

		{* Wrapper for page content and sidebars *}
		{if $isFullWidth}
			{assign var=hasSidebar value=0}
		{/if}
		<div class="pkp_structure_content tricore_container{if $hasSidebar} has_sidebar{/if}">
			<div class="pkp_structure_main" role="main">
				<a id="pkp_content_main"></a>
