{**
 * plugins/themes/tricore/templates/frontend/components/tricore/hero.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Homepage hero: title, subtitle, call-to-action buttons and the
 *  journal homepage image. Child themes override this partial to change the
 *  hero without touching the rest of the homepage.
 *
 * @uses $tricoreTheme array heroTitle, heroSubtitle, ctas
 * @uses $currentContext Journal
 * @uses $homepageImage array|null
 *}
<section class="tricore_hero{if $homepageImage} has_image{/if}" aria-labelledby="tricoreHeroTitle">
	<div class="tricore_hero_body">
		<h2 class="tricore_hero_title" id="tricoreHeroTitle">
			{$tricoreTheme.heroTitle|default:$currentContext->getLocalizedName()|escape}
		</h2>
		{if $tricoreTheme.heroSubtitle}
			<p class="tricore_hero_subtitle">{$tricoreTheme.heroSubtitle|escape|nl2br}</p>
		{elseif $currentContext->getLocalizedDescription()}
			<p class="tricore_hero_subtitle">{$currentContext->getLocalizedDescription()|strip_tags|unescape:"html"|truncate:260:"…"|escape}</p>
		{/if}
		{if $tricoreTheme.ctas}
			<div class="tricore_hero_actions">
				{foreach from=$tricoreTheme.ctas item=cta}
					<a class="tricore_btn{if $cta.isPrimary} tricore_btn_primary{else} tricore_btn_outline{/if}" href="{$cta.url|escape}">{$cta.label|escape}</a>
				{/foreach}
			</div>
		{/if}
	</div>
	{if $homepageImage}
		<div class="tricore_hero_media">
			<img src="{$publicFilesDir}/{$homepageImage.uploadName|escape:"url"}" alt="{$homepageImage.altText|escape}">
		</div>
	{/if}
</section>
