<?php

/**
 * @file plugins/themes/tricore/index.php
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Wrapper for the Tricore theme plugin.
 *
 * OJS 3.4/3.5 only auto-load a namespaced class named <Folder>Plugin
 * (CorePlugin); like the default theme, theme plugins use this wrapper.
 */

return new \APP\plugins\themes\tricore\TricoreThemePlugin();
