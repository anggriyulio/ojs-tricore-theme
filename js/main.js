/**
 * @file plugins/themes/tricore/js/main.js
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Tricore theme behaviour, without dependencies:
 *  - mobile menu toggle (aria-expanded)
 *  - submenu toggle buttons for keyboard and touch users
 *  - Escape closes open menus
 *  - homepage highlights carousel (OJS 3.5+, when Swiper is loaded)
 *  - registration form: privacy and reviewer interest toggles
 *    (same behaviour as the default theme, whose script is not loaded)
 */
(function () {
	'use strict';

	var root = document.documentElement;
	root.classList.add('js');

	function ready(fn) {
		if (document.readyState !== 'loading') {
			fn();
		} else {
			document.addEventListener('DOMContentLoaded', fn);
		}
	}

	function setExpanded(button, target, open) {
		button.setAttribute('aria-expanded', open ? 'true' : 'false');
		target.classList.toggle('is_open', open);
	}

	function initMenuToggle() {
		var toggle = document.querySelector('.tricore_nav_toggle');
		var nav = document.getElementById('tricoreSiteNav');
		if (!toggle || !nav) {
			return;
		}
		toggle.addEventListener('click', function () {
			setExpanded(toggle, nav, toggle.getAttribute('aria-expanded') !== 'true');
		});
	}

	function initSubmenus() {
		var label = document.body.getAttribute('data-tricore-submenu-label') || '';
		var items = document.querySelectorAll('.tricore_menu > li');
		Array.prototype.forEach.call(items, function (item, index) {
			var submenu = item.querySelector(':scope > ul');
			var link = item.querySelector(':scope > a');
			if (!submenu || !link) {
				return;
			}
			var id = submenu.id || 'tricoreSubmenu' + index;
			submenu.id = id;

			var button = document.createElement('button');
			button.type = 'button';
			button.className = 'tricore_submenu_toggle';
			button.setAttribute('aria-expanded', 'false');
			button.setAttribute('aria-controls', id);
			button.setAttribute('aria-label', (label || '%s').replace('%s', link.textContent.trim()));
			link.insertAdjacentElement('afterend', button);

			button.addEventListener('click', function (event) {
				event.stopPropagation();
				var open = button.getAttribute('aria-expanded') !== 'true';
				closeAll(item);
				setExpanded(button, item, open);
			});
		});

		document.addEventListener('click', function (event) {
			if (!event.target.closest('.tricore_menu')) {
				closeAll();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key !== 'Escape') {
				return;
			}
			var open = document.querySelector('.tricore_menu li.is_open');
			closeAll();
			if (open) {
				var button = open.querySelector(':scope > .tricore_submenu_toggle');
				if (button) {
					button.focus();
				}
			}
		});
	}

	function closeAll(except) {
		var open = document.querySelectorAll('.tricore_menu li.is_open');
		Array.prototype.forEach.call(open, function (item) {
			if (item === except) {
				return;
			}
			var button = item.querySelector(':scope > .tricore_submenu_toggle');
			if (button) {
				setExpanded(button, item, false);
			}
		});
	}

	function initRegistrationForm() {
		var optin = document.getElementById('contextOptinGroup');
		if (optin) {
			optin.addEventListener('change', function (event) {
				var roles = event.target.closest('.roles');
				if (!roles) {
					return;
				}
				var privacy = roles.parentNode.querySelector('.context_privacy');
				if (privacy) {
					privacy.classList.toggle('context_privacy_visible', !!roles.querySelector('input:checked'));
				}
			});
		}

		var reviewer = document.getElementById('reviewerOptinGroup');
		var interests = document.getElementById('reviewerInterests');
		if (reviewer && interests) {
			var update = function () {
				interests.classList.toggle('is_visible', !!reviewer.querySelector('input:checked'));
			};
			reviewer.addEventListener('change', update);
			update();
		}
	}

	// Homepage highlights carousel (OJS 3.5+). Swiper is only loaded where
	// the version supports highlights, so this is a no-op elsewhere.
	function initHighlights() {
		if (typeof window.Swiper === 'undefined' || !document.querySelector('.swiper')) {
			return;
		}
		var i18n = window.tricoreThemeI18N || {};
		new window.Swiper('.swiper', {
			a11y: {
				prevSlideMessage: i18n.prevSlide,
				nextSlideMessage: i18n.nextSlide
			},
			autoHeight: true,
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev'
			},
			pagination: {
				el: '.swiper-pagination',
				type: 'bullets'
			}
		});
	}

	ready(function () {
		initMenuToggle();
		initSubmenus();
		initRegistrationForm();
		initHighlights();
	});
})();
