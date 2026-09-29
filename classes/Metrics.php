<?php

/**
 * @file plugins/themes/tricore/classes/Metrics.php
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Article usage metrics: views and downloads.
 *
 * Same definitions as the OJS statistics (PKPStatsPublicationService):
 *  - views     = abstract page views (ASSOC_TYPE_SUBMISSION)
 *  - downloads = galley file and supplementary file downloads
 *                (ASSOC_TYPE_SUBMISSION_FILE, ASSOC_TYPE_SUBMISSION_FILE_COUNTER_OTHER)
 * The assoc type values are the same in OJS 3.3, 3.4 and 3.5.
 *
 * Pure PHP (7.3+) without OJS dependencies; the adapter runs the query.
 */

namespace Tricore\Theme;

class Metrics
{
    const ASSOC_TYPE_SUBMISSION = 0x0100009;
    const ASSOC_TYPE_SUBMISSION_FILE = 0x0000203;
    const ASSOC_TYPE_SUBMISSION_FILE_COUNTER_OTHER = 0x0000213;

    /** Assoc types to query */
    const ASSOC_TYPES = [self::ASSOC_TYPE_SUBMISSION, self::ASSOC_TYPE_SUBMISSION_FILE, self::ASSOC_TYPE_SUBMISSION_FILE_COUNTER_OTHER];

    /**
     * @return array{views: int, downloads: int}
     */
    public static function zero(): array
    {
        return ['views' => 0, 'downloads' => 0];
    }

    /**
     * Totals per submission from rows of (submission_id, assoc_type, total).
     *
     * Every requested id gets an entry, with zeros when it has no rows.
     *
     * @param iterable $rows arrays or objects with submission_id, assoc_type, total
     * @param int[] $submissionIds
     * @return array<int, array{views: int, downloads: int}>
     */
    public static function summarize($rows, array $submissionIds = []): array
    {
        $totals = [];
        foreach ($submissionIds as $id) {
            $totals[(int) $id] = self::zero();
        }
        foreach ($rows as $row) {
            $row = (array) $row;
            $id = (int) $row['submission_id'];
            if (!isset($totals[$id])) {
                $totals[$id] = self::zero();
            }
            $type = (int) $row['assoc_type'];
            $total = max(0, (int) $row['total']);
            if ($type === self::ASSOC_TYPE_SUBMISSION) {
                $totals[$id]['views'] += $total;
            } elseif ($type === self::ASSOC_TYPE_SUBMISSION_FILE || $type === self::ASSOC_TYPE_SUBMISSION_FILE_COUNTER_OTHER) {
                $totals[$id]['downloads'] += $total;
            }
        }
        return $totals;
    }

    /**
     * Sum of several totals (e.g. all articles of an issue).
     *
     * @param array[] $list
     * @return array{views: int, downloads: int}
     */
    public static function sum(array $list): array
    {
        $sum = self::zero();
        foreach ($list as $item) {
            $sum['views'] += (int) ($item['views'] ?? 0);
            $sum['downloads'] += (int) ($item['downloads'] ?? 0);
        }
        return $sum;
    }

    /**
     * Values for the templates: exact counts and short display forms.
     *
     * @param array $metrics ['views' => int, 'downloads' => int]
     * @return array{views: int, downloads: int, viewsShort: string, downloadsShort: string}
     */
    public static function present(array $metrics): array
    {
        $views = max(0, (int) ($metrics['views'] ?? 0));
        $downloads = max(0, (int) ($metrics['downloads'] ?? 0));
        return [
            'views' => $views,
            'downloads' => $downloads,
            'viewsShort' => self::compact($views),
            'downloadsShort' => self::compact($downloads),
        ];
    }

    /**
     * Short display form: 999, 1.2K, 12K, 1.2M.
     */
    public static function compact(int $value): string
    {
        $value = max(0, $value);
        foreach ([1000000000 => 'B', 1000000 => 'M', 1000 => 'K'] as $unit => $suffix) {
            if ($value >= $unit) {
                $short = $value / $unit;
                $text = $short < 10 ? number_format(floor($short * 10) / 10, 1, '.', '') : (string) floor($short);
                return preg_replace('/\.0$/', '', $text) . $suffix;
            }
        }
        return (string) $value;
    }
}
