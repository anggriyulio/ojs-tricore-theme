<?php

/**
 * @file plugins/themes/tricore/classes/OptionSanitizer.php
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Sanitises theme option values before they are saved or rendered.
 *
 * Pure PHP (7.3+) without OJS dependencies.
 */

namespace Tricore\Theme;

class OptionSanitizer
{
    /** Maximum number of metric rows rendered on the homepage */
    const MAX_METRICS = 8;

    /**
     * Return the URL when it is safe to use in an href, otherwise ''.
     *
     * Accepted: absolute http(s) URLs and, when $allowRelative is true, site
     * relative paths ("/path", "path", "#anchor", "?query"). Rejected:
     * javascript:, data:, protocol-relative "//host" and any other scheme.
     *
     * @param mixed $value
     */
    public static function url($value, bool $allowRelative = true): string
    {
        if (!is_string($value)) {
            return '';
        }
        $value = trim($value);
        if ($value === '' || preg_match('/[\x00-\x1f\x7f\s]/', $value)) {
            return '';
        }
        if (preg_match('#^https?://[^/?\#]+#i', $value)) {
            return filter_var($value, FILTER_VALIDATE_URL) !== false ? $value : '';
        }
        if (!$allowRelative || strpos($value, '//') === 0 || strpos($value, '\\') !== false) {
            return '';
        }
        // A colon before the first "/", "?" or "#" means a scheme (javascript:, data:, mailto:)
        $firstDelimiter = strcspn($value, '/?#');
        if (strpos(substr($value, 0, $firstDelimiter), ':') !== false) {
            return '';
        }
        return $value;
    }

    /**
     * Parse "Label|Value" lines into metric rows.
     *
     * Empty lines, lines without a "|" and rows with an empty label or value
     * are skipped. Output is plain text; escaping happens in the template.
     *
     * @param mixed $text
     * @return array<int, array{label: string, value: string}>
     */
    public static function metrics($text): array
    {
        if (!is_string($text) || trim($text) === '') {
            return [];
        }
        $rows = [];
        foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
            if (strpos($line, '|') === false) {
                continue;
            }
            list($label, $value) = array_map('trim', explode('|', $line, 2));
            if ($label === '' || $value === '') {
                continue;
            }
            $rows[] = ['label' => $label, 'value' => $value];
            if (count($rows) >= self::MAX_METRICS) {
                break;
            }
        }
        return $rows;
    }

    /**
     * Clean HTML from a rich text option.
     *
     * Removes the field description the OJS inline editor can capture into
     * the value (<div class="pkpFormField__description">), and returns '' for
     * content without visible text such as "<p><br></p>". Unsafe HTML is
     * removed at render time with strip_unsafe_html.
     *
     * @param mixed $html
     */
    public static function richText($html): string
    {
        if (!is_string($html)) {
            return '';
        }
        $html = preg_replace('#<div\b[^>]*\bclass="[^"]*\bpkpFormField__description\b[^"]*"[^>]*>.*?</div>#is', '', $html);
        $html = trim($html);
        if (trim(strip_tags($html, '<img>')) === '') {
            return '';
        }
        return $html;
    }

    /**
     * Return the value when it is one of the allowed choices, otherwise the default.
     *
     * @param mixed $value
     * @param array $allowed
     * @param mixed $default
     * @return mixed
     */
    public static function choice($value, array $allowed, $default)
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }
}
