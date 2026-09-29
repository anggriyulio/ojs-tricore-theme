{**
 * plugins/themes/tricore/templates/frontend/components/footer.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3.
 *
 * @brief Site footer of the Tricore theme. Based on the OJS 3.3 frontend footer:
 *  keeps the sidebar hook, the footer hook and the skip link target.
 *
 * @uses $isFullWidth bool Display the page without sidebars
 * @uses $tricoreTheme array Data prepared by TricoreThemePlugin::loadTemplateData()
 *}

			</div><!-- pkp_structure_main -->

			{* Sidebars *}
			{if empty($isFullWidth)}
				{capture assign="sidebarCode"}{call_hook name="Templates::Common::Sidebar"}{/capture}
				{if $sidebarCode}
					<div class="pkp_structure_sidebar left" role="complementary" aria-label="{translate|escape key="common.navigation.sidebar"}">
						{$sidebarCode}
					</div><!-- pkp_sidebar.left -->
				{/if}
			{/if}
		</div><!-- pkp_structure_content -->

		<div class="tricore_footer" role="contentinfo">
			<a id="pkp_content_footer"></a>

			<div class="tricore_footer_inner tricore_container">
				{if $pageFooter}
					<div class="tricore_footer_content">
						{$pageFooter}
					</div>
				{/if}

				{if $tricoreTheme.social}
					{include file="frontend/components/tricore/social.tpl" links=$tricoreTheme.social}
				{/if}
			</div>

			<div class="tricore_footer_bottom tricore_container">
				<span class="tricore_footer_name">
					{if $currentContext}{$currentContext->getLocalizedName()|escape}{else}{$siteTitle|escape}{/if}
				</span>
				<div class="tricore_footer_credits">
					<a class="tricore_footer_credit" href="https://3core.web.id" target="_blank" rel="noopener">
						{translate key="plugins.themes.tricore.footer.credit"}
						<span class="pkp_screen_reader">{translate key="plugins.themes.tricore.footer.newTab"}</span>
					</a>
					<a class="tricore_footer_brand" href="{url page="about" op="aboutThisPublishingSystem"}">
						<img alt="{translate key="about.aboutThisPublishingSystem"}" src="{$baseUrl}/{$brandImage}">
					</a>
				</div>
			</div>
		</div>

	</div><!-- pkp_structure_page -->

{load_script context="frontend"}

{call_hook name="Templates::Common::Footer::PageFooter"}
</body>
</html>
