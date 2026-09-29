<?php

/**
 * @file plugins/themes/tricore/classes/Color.php
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Hex colour validation, normalisation and WCAG contrast helpers.
 *
 * Pure PHP (7.3+) without OJS dependencies, shared unchanged by every OJS
 * version branch of this theme.
 */

namespace Tricore\Theme;

class Color
{
    /** Minimum contrast ratio for normal text (WCAG 2.x level AA) */
    const AA_NORMAL = 4.5;

    /** Dark foreground used on light backgrounds */
    const DARK_TEXT = '#1a1d23';

    /** Light foreground used on dark backgrounds */
    const LIGHT_TEXT = '#ffffff';

    /**
     * Whether the value is a 3 or 6 digit hex colour such as #fff or #1d5fd1.
     *
     * Stricter than the check in the default theme (pkp/pkp-lib#11974), which
     * also accepts 1, 2, 4 and 5 digit values that CSS does not understand.
     *
     * @param mixed $value
     */
    public static function isValidHex($value): bool
    {
        return is_string($value) && preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/iD', $value) === 1;
    }

    /**
     * Normalise a colour to lowercase 6 digit hex, or return the fallback
     * when the value is not a valid hex colour.
     *
     * @param mixed $value
     */
    public static function normalize($value, string $fallback): string
    {
        if (!self::isValidHex($value)) {
            if (!self::isValidHex($fallback)) {
                throw new \InvalidArgumentException('Invalid fallback colour: ' . $fallback);
            }
            $value = $fallback;
        }
        $hex = strtolower(substr($value, 1));
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return '#' . $hex;
    }

    /**
     * Relative luminance as defined by WCAG 2.x (0 = black, 1 = white).
     */
    public static function luminance(string $hex): float
    {
        $hex = substr(self::normalize($hex, '#000000'), 1);
        $channels = [];
        foreach ([0, 2, 4] as $offset) {
            $c = hexdec(substr($hex, $offset, 2)) / 255;
            $channels[] = $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
        }
        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    /**
     * WCAG contrast ratio between two colours, from 1 (none) to 21 (black on white).
     */
    public static function contrastRatio(string $a, string $b): float
    {
        $la = self::luminance($a);
        $lb = self::luminance($b);
        $lighter = max($la, $lb);
        $darker = min($la, $lb);
        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * Pick the foreground (dark or light) with the highest contrast on a background.
     */
    public static function readableOn(string $background, string $dark = self::DARK_TEXT, string $light = self::LIGHT_TEXT): string
    {
        return self::contrastRatio($background, $dark) >= self::contrastRatio($background, $light) ? $dark : $light;
    }

    /**
     * Whether text in the readable foreground colour meets WCAG AA on this background.
     */
    public static function meetsAA(string $foreground, string $background): bool
    {
        return self::contrastRatio($foreground, $background) >= self::AA_NORMAL;
    }
}
