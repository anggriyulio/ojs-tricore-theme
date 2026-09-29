<?php

/**
 * @file plugins/themes/tricore/classes/SiteStats.php
 *
 * Copyright (c) 2026 Tricore Innovations (3Core)
 * Distributed under the GNU GPL v3.
 *
 * @brief Counts for the site landing page: journals, published issues and
 *  published articles, in total and per journal.
 *
 * Pure PHP (7.3+) without OJS dependencies; the adapter runs the queries
 * (same SQL in OJS 3.3, 3.4 and 3.5).
 */

namespace Tricore\Theme;

class SiteStats
{
    /**
     * @param iterable $issueRows rows of (context_id, total): published issues per journal
     * @param iterable $articleRows rows of (context_id, total): published articles per journal
     * @param int[] $journalIds journals shown on the site (enabled); others are ignored
     * @return array{journals: int, issues: int, articles: int, perJournal: array<int, array{issues: int, articles: int}>}
     */
    public static function summarize($issueRows, $articleRows, array $journalIds): array
    {
        $perJournal = [];
        foreach ($journalIds as $id) {
            $perJournal[(int) $id] = ['issues' => 0, 'articles' => 0];
        }
        foreach (['issues' => $issueRows, 'articles' => $articleRows] as $key => $rows) {
            foreach ($rows as $row) {
                $row = (array) $row;
                $id = (int) $row['context_id'];
                if (isset($perJournal[$id])) {
                    $perJournal[$id][$key] += max(0, (int) $row['total']);
                }
            }
        }
        return [
            'journals' => count($perJournal),
            'issues' => array_sum(array_column($perJournal, 'issues')),
            'articles' => array_sum(array_column($perJournal, 'articles')),
            'perJournal' => $perJournal,
        ];
    }
}
