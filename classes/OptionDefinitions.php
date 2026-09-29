<?php

/**
 * @file plugins/themes/tricore/classes/OptionDefinitions.php
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Declarative list of the Tricore theme options.
 *
 * Labels and descriptions are locale keys; the OJS adapter translates them
 * and turns each entry into ThemePlugin::addOption(). Keeping the list here
 * (pure PHP 7.3+, no OJS dependency) lets unit tests check defaults and
 * allowed values, and keeps it identical across the OJS version branches.
 *
 * Entry format:
 *   name => [
 *     'type'        => Field class name (FieldColor, FieldText, ...),
 *     'group'       => group key, rendered as a heading in the settings form,
 *     'label'       => locale key,
 *     'description' => locale key (optional),
 *     'default'     => default value,
 *     'options'     => [[value, label locale key], ...] for FieldOptions/FieldSelect,
 *     'optionType'  => 'radio' | 'checkbox' (FieldOptions only),
 *     'multilingual'=> true for per-locale values,
 *   ]
 */

namespace Tricore\Theme;

class OptionDefinitions
{
    const PREFIX = 'plugins.themes.tricore.option.';

    /** Group keys in display order */
    const GROUPS = ['colours', 'typography', 'layout', 'sidebar', 'homepage', 'metrics', 'social'];

    const TYPOGRAPHY = ['system', 'notoSans', 'notoSerif_notoSans', 'lora_openSans', 'lato'];
    const HEADER_LAYOUTS = ['left', 'centered'];
    const CONTAINER_WIDTHS = ['narrow', 'normal', 'wide'];
    const RADII = ['none', 'small', 'medium', 'large'];
    const LATEST_COUNTS = [0, 3, 6, 9];
    const SOCIAL_NETWORKS = ['Facebook', 'X', 'Instagram', 'Linkedin', 'Youtube'];

    /**
     * @return array<string, array>
     */
    public static function all(): array
    {
        $p = self::PREFIX;
        $options = [];

        // Colours
        $colours = [
            'primaryColour' => '#1f5fbf',
            'secondaryColour' => '#0f2a44',
            'accentColour' => '#e0a526',
            'headerBackground' => '#ffffff',
            'footerBackground' => '#0f2a44',
        ];
        foreach ($colours as $name => $default) {
            $options[$name] = [
                'type' => 'FieldColor',
                'group' => 'colours',
                'label' => $p . $name . '.label',
                'description' => $p . $name . '.description',
                'default' => $default,
            ];
        }

        // Typography
        $options['typography'] = [
            'type' => 'FieldOptions',
            'optionType' => 'radio',
            'group' => 'typography',
            'label' => $p . 'typography.label',
            'description' => $p . 'typography.description',
            'default' => 'system',
            'options' => self::choices('typography', self::TYPOGRAPHY),
        ];

        // Layout
        $options['headerLayout'] = [
            'type' => 'FieldOptions',
            'optionType' => 'radio',
            'group' => 'layout',
            'label' => $p . 'headerLayout.label',
            'default' => 'left',
            'options' => self::choices('headerLayout', self::HEADER_LAYOUTS),
        ];
        $options['containerWidth'] = [
            'type' => 'FieldSelect',
            'group' => 'layout',
            'label' => $p . 'containerWidth.label',
            'default' => 'normal',
            'options' => self::choices('containerWidth', self::CONTAINER_WIDTHS),
        ];
        $options['borderRadius'] = [
            'type' => 'FieldSelect',
            'group' => 'layout',
            'label' => $p . 'borderRadius.label',
            'description' => $p . 'borderRadius.description',
            'default' => 'medium',
            'options' => self::choices('borderRadius', self::RADII),
        ];

        // Sidebar: one checkbox per page type, each independent
        $options['sidebarPages'] = [
            'type' => 'FieldOptions',
            'optionType' => 'checkbox',
            'group' => 'sidebar',
            'label' => $p . 'sidebarPages.label',
            'description' => $p . 'sidebarPages.description',
            'default' => PageContext::SIDEBAR_PAGES,
            'options' => self::choices('sidebarPages', PageContext::SIDEBAR_PAGES),
        ];

        // Homepage content blocks
        $options['showHero'] = self::checkbox('showHero', true);
        $options['heroTitle'] = self::text('heroTitle', 'FieldText', true);
        $options['heroSubtitle'] = self::text('heroSubtitle', 'FieldTextarea', true);
        foreach (['ctaPrimary', 'ctaSecondary'] as $cta) {
            $options[$cta . 'Label'] = self::text($cta . 'Label', 'FieldText', true);
            $options[$cta . 'Url'] = self::text($cta . 'Url', 'FieldText', false);
        }
        $options['showDescription'] = self::checkbox('showDescription', false);
        $options['showCurrentIssue'] = self::checkbox('showCurrentIssue', true);
        $options['latestArticlesCount'] = [
            'type' => 'FieldSelect',
            'group' => 'homepage',
            'label' => $p . 'latestArticlesCount.label',
            'default' => 6,
            'options' => array_map(function ($count) {
                return ['value' => $count, 'label' => (string) $count];
            }, self::LATEST_COUNTS),
        ];
        $options['metrics'] = self::text('metrics', 'FieldTextarea', true);
        $options['indexingInfo'] = self::text('indexingInfo', 'FieldRichTextarea', true);

        // Article usage metrics (views and downloads)
        $options['showMetrics'] = self::checkbox('showMetrics', true, 'metrics');
        $options['showMetrics']['description'] = $p . 'showMetrics.description';

        // Social links
        foreach (self::SOCIAL_NETWORKS as $network) {
            $name = 'social' . $network;
            $options[$name] = [
                'type' => 'FieldText',
                'group' => 'social',
                'label' => $p . $name . '.label',
                'default' => '',
            ];
        }
        $options['socialFacebook']['description'] = $p . 'social.description';

        return $options;
    }

    /**
     * Option name => default value.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return array_map(function ($option) {
            return $option['default'];
        }, self::all());
    }

    /**
     * Names of all options of a given type.
     *
     * @return string[]
     */
    public static function namesOfType(string $type): array
    {
        return array_keys(array_filter(self::all(), function ($option) use ($type) {
            return $option['type'] === $type;
        }));
    }

    /**
     * Allowed values for an option with fixed choices, or null for free input.
     */
    public static function allowedValues(string $name): ?array
    {
        $options = self::all();
        if (!isset($options[$name]['options'])) {
            return null;
        }
        return array_column($options[$name]['options'], 'value');
    }

    private static function choices(string $name, array $values): array
    {
        return array_map(function ($value) use ($name) {
            return ['value' => $value, 'label' => self::PREFIX . $name . '.' . $value];
        }, $values);
    }

    private static function checkbox(string $name, bool $default, string $group = 'homepage'): array
    {
        return [
            'type' => 'FieldOptions',
            'optionType' => 'checkbox',
            'group' => $group,
            'label' => self::PREFIX . $name . '.label',
            'default' => $default,
            'options' => [['value' => true, 'label' => self::PREFIX . $name . '.option']],
        ];
    }

    private static function text(string $name, string $type, bool $multilingual): array
    {
        return [
            'type' => $type,
            'group' => 'homepage',
            'label' => self::PREFIX . $name . '.label',
            'description' => self::PREFIX . $name . '.description',
            // Multilingual values are arrays keyed by locale. A null default makes
            // ThemePlugin::getLocalizedOption() return null instead of indexing
            // an empty string.
            'default' => $multilingual ? null : '',
            'multilingual' => $multilingual,
        ];
    }
}
