{**
 * plugins/themes/tricore/templates/frontend/components/tricore/social.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Social media links. URLs are validated (https only) by the plugin.
 *
 * @uses $links array [['network' => 'facebook', 'label' => 'Facebook', 'url' => '...'], ...]
 *}
{assign var="tricoreSocialIcons" value=['facebook' => 'fa-facebook', 'x' => 'fa-twitter', 'instagram' => 'fa-instagram', 'linkedin' => 'fa-linkedin', 'youtube' => 'fa-youtube-play']}
<div class="tricore_social">
	<h2 class="tricore_footer_heading">{translate key="plugins.themes.tricore.followUs"}</h2>
	<ul class="tricore_social_list">
		{foreach from=$links item=link}
			<li>
				<a href="{$link.url|escape}" target="_blank" rel="noopener noreferrer" class="tricore_social_link tricore_social_{$link.network|escape}">
					<span class="fa {$tricoreSocialIcons[$link.network]|default:'fa-link'}" aria-hidden="true"></span>
					<span class="pkp_screen_reader">{translate key="plugins.themes.tricore.social.label" network=$link.label|escape}</span>
				</a>
			</li>
		{/foreach}
	</ul>
</div>
