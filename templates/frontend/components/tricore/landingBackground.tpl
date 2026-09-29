{**
 * plugins/themes/tricore/templates/frontend/components/tricore/landingBackground.tpl
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Decorative background in the 3Core hero style: light "paper"
 *  gradient, a fine grid masked to an ellipse, and two soft radial glows
 *  (cyan and primary) that drift slowly. Colours come from the theme tokens;
 *  motion stops with prefers-reduced-motion. Child themes override this
 *  partial to change the background only.
 *}
<div class="tricore_landing_bg" aria-hidden="true">
	<div class="tricore_landing_grid"></div>
	<span class="tricore_landing_glow tricore_landing_glow_cyan"></span>
	<span class="tricore_landing_glow tricore_landing_glow_accent"></span>
</div>
