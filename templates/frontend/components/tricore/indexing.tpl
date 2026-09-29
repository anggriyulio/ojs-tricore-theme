{**
 * plugins/themes/tricore/templates/frontend/components/tricore/indexing.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Indexing information (rich text from the theme options).
 *
 * @uses $indexingInfo string HTML
 *}
<section class="tricore_section tricore_indexing" aria-labelledby="tricoreIndexingTitle">
	<h2 class="tricore_section_title" id="tricoreIndexingTitle">{translate key="plugins.themes.tricore.indexing"}</h2>
	<div class="tricore_indexing_body">
		{$indexingInfo|strip_unsafe_html}
	</div>
</section>
