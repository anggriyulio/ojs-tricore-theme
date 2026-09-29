<?php

/**
 * @file plugins/themes/tricore/classes/Tokens.php
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Turns theme option values into LESS variables (design tokens).
 *
 * The generated string is passed to ThemePlugin::modifyStyle() as
 * `addLessVariables`. styles/tokens.less declares the same variables with
 * default values and exposes them as CSS custom properties (--tricore-*).
 *
 * Every value is validated here: colours must be hex, choices must be in the
 * allowed list. Nothing from the database reaches the LESS compiler unchecked.
 *
 * Pure PHP (7.3+) without OJS dependencies.
 */

namespace Tricore\Theme;

class Tokens
{
    /** Colour option => LESS variable name */
    const COLOURS = [
        'primaryColour' => 'tricore-primary',
        'secondaryColour' => 'tricore-secondary',
        'accentColour' => 'tricore-accent',
        'headerBackground' => 'tricore-header-bg',
        'footerBackground' => 'tricore-footer-bg',
    ];

    /** Readable text colour variable for each colour variable */
    const ON_COLOURS = [
        'tricore-primary' => 'tricore-on-primary',
        'tricore-secondary' => 'tricore-on-secondary',
        'tricore-accent' => 'tricore-on-accent',
        'tricore-header-bg' => 'tricore-header-text',
        'tricore-footer-bg' => 'tricore-footer-text',
    ];

    const RADII = [
        'none' => '0',
        'small' => '4px',
        'medium' => '8px',
        'large' => '16px',
    ];

    const CONTAINERS = [
        'narrow' => '1080px',
        'normal' => '1200px',
        'wide' => '1360px',
    ];

    const SYSTEM_SANS = 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif';

    /** typography option => [body stack, heading stack] */
    const FONTS = [
        'system' => [self::SYSTEM_SANS, self::SYSTEM_SANS],
        'notoSans' => ['"Noto Sans", ' . self::SYSTEM_SANS, '"Noto Sans", ' . self::SYSTEM_SANS],
        'notoSerif_notoSans' => ['"Noto Sans", ' . self::SYSTEM_SANS, '"Noto Serif", Georgia, serif'],
        'lora_openSans' => ['"Open Sans", ' . self::SYSTEM_SANS, 'Lora, Georgia, serif'],
        'lato' => ['Lato, ' . self::SYSTEM_SANS, 'Lato, ' . self::SYSTEM_SANS],
    ];

    /**
     * Resolved token values for a set of option values.
     *
     * Only options present in $defaults produce tokens. A child theme that
     * removes an option (ThemePlugin::removeOption) therefore keeps control of
     * that token through its own LESS. Missing or invalid values fall back to
     * the given default, and an invalid default falls back to the Tricore default
     * from OptionDefinitions.
     *
     * @param array $options option name => value
     * @param array $defaults option name => default (usually the live option
     *   configs, which a child theme may have changed)
     * @return array<string, string> LESS variable name (without @) => value
     */
    public static function resolve(array $options, array $defaults): array
    {
        $core = OptionDefinitions::defaults();
        $tokens = [];

        foreach (self::COLOURS as $option => $variable) {
            if (!array_key_exists($option, $defaults)) {
                continue;
            }
            $fallback = Color::normalize($defaults[$option], $core[$option]);
            $colour = Color::normalize($options[$option] ?? null, $fallback);
            $tokens[$variable] = $colour;
            $tokens[self::ON_COLOURS[$variable]] = Color::readableOn($colour);
        }

        $choices = [
            'borderRadius' => ['tricore-radius', self::RADII],
            'containerWidth' => ['tricore-container', self::CONTAINERS],
        ];
        foreach ($choices as $option => list($variable, $table)) {
            if (!array_key_exists($option, $defaults)) {
                continue;
            }
            $key = self::pick($options[$option] ?? null, $defaults[$option], $core[$option], array_keys($table));
            $tokens[$variable] = $table[$key];
        }

        if (array_key_exists('typography', $defaults)) {
            $key = self::pick($options['typography'] ?? null, $defaults['typography'], $core['typography'], array_keys(self::FONTS));
            $tokens['tricore-font-body'] = self::FONTS[$key][0];
            $tokens['tricore-font-heading'] = self::FONTS[$key][1];
        }

        return $tokens;
    }

    /**
     * Value if allowed, else default if allowed, else the Tricore default.
     *
     * @param mixed $value
     * @param mixed $default
     * @param mixed $tricoreDefault
     * @return mixed
     */
    private static function pick($value, $default, $tricoreDefault, array $allowed)
    {
        return OptionSanitizer::choice($value, $allowed, OptionSanitizer::choice($default, $allowed, $tricoreDefault));
    }

    /**
     * LESS source declaring the resolved tokens, one variable per line.
     */
    public static function toLess(array $tokens): string
    {
        $lines = [];
        foreach ($tokens as $name => $value) {
            if (!preg_match('/^[a-z][a-z0-9-]*$/D', $name)) {
                throw new \InvalidArgumentException('Invalid token name: ' . $name);
            }
            // Values are produced by resolve() from fixed lists or validated
            // hex colours; this guard stops anything that could close the
            // declaration or open a block if the lists are ever extended.
            if (preg_match('/[;{}@\\\\]/', $value)) {
                throw new \InvalidArgumentException('Invalid token value for ' . $name);
            }
            $lines[] = '@' . $name . ': ' . $value . ';';
        }
        return implode("\n", $lines);
    }

}
