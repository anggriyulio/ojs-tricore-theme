{**
 * plugins/themes/tricore/templates/frontend/components/tricore/latestArticles.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Cards of the most recently published articles. The plugin passes
 *  plain arrays, so this partial is identical for every OJS version.
 *
 * @uses $articles array [['id', 'title', 'authors', 'datePublished', 'url'], ...]
 *}
<section class="tricore_section tricore_latest" aria-labelledby="tricoreLatestTitle">
	<a id="homepageLatest"></a>
	<h2 class="tricore_section_title" id="tricoreLatestTitle">{translate key="plugins.themes.tricore.latestArticles"}</h2>
	<ul class="tricore_card_grid">
		{foreach from=$articles item=article}
			<li class="tricore_card tricore_article_card">
				<h3 class="tricore_card_title">
					<a href="{$article.url|escape}">{$article.title|strip_unsafe_html}</a>
				</h3>
				{if $article.authors}
					<p class="tricore_card_meta tricore_card_authors">{$article.authors|escape}</p>
				{/if}
				{if $article.datePublished}
					<p class="tricore_card_meta tricore_card_date">
						<time datetime="{$article.datePublished|date_format:"%Y-%m-%d"}">{$article.datePublished|date_format:$dateFormatShort}</time>
					</p>
				{/if}
				{if $article.metrics}
					{include file="frontend/components/tricore/metricsInline.tpl" metrics=$article.metrics metricsTitle=null}
				{/if}
			</li>
		{/foreach}
	</ul>
</section>
