<?php

/**
 * @file plugins/themes/tricore/classes/PageContext.php
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Classifies the current frontend page and decides whether the
 *  sidebar is shown on it (theme option "Show the sidebar on").
 *
 * Pure PHP (7.3+) without OJS dependencies.
 */

namespace Tricore\Theme;

class PageContext
{
    /** Page types with their own "show sidebar" checkbox, in display order */
    const SIDEBAR_PAGES = ['homepage', 'archive', 'issue', 'article', 'about', 'staticPage', 'search', 'announcements'];

    /** Login, registration and password pages: always without sidebar */
    const AUTH = 'auth';

    /** Site homepage (portal landing page): always full width */
    const SITE = 'site';

    /** Any other page: OJS default (sidebar when blocks are configured) */
    const OTHER = 'other';

    /**
     * Page type from the requested page/op and the template being displayed.
     *
     * @param string|null $page requested page, e.g. "issue" (null/"" = index)
     * @param string|null $op requested operation, e.g. "archive"
     * @param string $template template name passed to TemplateManager::display()
     */
    public static function pageKey($page, $op, string $template): string
    {
        $page = (string) $page;
        $op = (string) $op;

        // Custom pages: Static Pages plugin (3.3/3.4) and custom navigation
        // menu items (all versions, 3.5 stores static pages this way)
        if (stripos($template, 'staticPages') !== false || strpos($template, 'navigationMenuItemViewContent') !== false) {
            return 'staticPage';
        }

        if (strpos($template, 'frontend/pages/indexSite.tpl') !== false) {
            return self::SITE;
        }

        switch ($page) {
            case '':
            case 'index':
                return 'homepage';
            case 'issue':
                return $op === 'archive' ? 'archive' : 'issue';
            case 'article':
                return 'article';
            case 'about':
                return 'about';
            case 'search':
                return 'search';
            case 'announcement':
                return 'announcements';
            case 'login':
            case 'user':
                return self::AUTH;
        }
        return self::OTHER;
    }

    /**
     * Whether the sidebar must be hidden on a page type.
     *
     * @param mixed $enabledPages value of the sidebarPages option (list of page types)
     */
    public static function hideSidebar(string $pageKey, $enabledPages): bool
    {
        if ($pageKey === self::AUTH || $pageKey === self::SITE) {
            return true;
        }
        if ($pageKey === self::OTHER) {
            return false;
        }
        return !in_array($pageKey, self::sanitizePages($enabledPages), true);
    }

    /**
     * Keep only known page types, in canonical order.
     *
     * @param mixed $pages
     * @return string[]
     */
    public static function sanitizePages($pages): array
    {
        if (!is_array($pages)) {
            return [];
        }
        return array_values(array_intersect(self::SIDEBAR_PAGES, $pages));
    }
}
