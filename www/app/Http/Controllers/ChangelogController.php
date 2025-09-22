<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChangelogController extends Controller
{
    public function feed(Request $request)
    {
        $releases = [];

        $docsDir = base_path('..' . DIRECTORY_SEPARATOR . 'docs');

        if (is_dir($docsDir)) {
            // First, load discrete per-version files if present
            $perVersion = $this->loadPerVersionFiles($docsDir);
            if (!empty($perVersion)) {
                $releases = array_merge($releases, $perVersion);
            }

            // Prefer HTML changelog
            if (empty($releases)) {
                foreach (['_CHANGELOG.html', 'CHANGELOG.html'] as $file) {
                    $path = $docsDir . DIRECTORY_SEPARATOR . $file;
                    if (is_file($path)) {
                        $releases = $this->parseHtmlChangelog(file_get_contents($path));
                        break;
                    }
                }
            }

            // Fallback to Markdown changelog
            if (empty($releases)) {
                foreach (['CHANGELOG.md', 'changelog.md'] as $file) {
                    $path = $docsDir . DIRECTORY_SEPARATOR . $file;
                    if (is_file($path)) {
                        $releases = $this->parseMarkdownChangelog(file_get_contents($path));
                        break;
                    }
                }
            }
        }

        return response()->json([ 'releases' => $releases ]);
    }

    private function loadPerVersionFiles(string $docsDir): array
    {
        $releases = [];
        $candidates = [];
        $releaseDir = $docsDir . DIRECTORY_SEPARATOR . 'releases';
        if (is_dir($releaseDir)) {
            $candidates = array_merge($candidates, glob($releaseDir . DIRECTORY_SEPARATOR . 'changes_*.*'));
        }
        $candidates = array_merge($candidates, glob($docsDir . DIRECTORY_SEPARATOR . 'changes_*.*'));

        foreach ($candidates as $path) {
            if (!is_file($path)) continue;
            $filename = basename($path);
            if (!preg_match('/^changes_(\d+_\d+_\d+)\.(txt|md|html)$/i', $filename, $m)) {
                continue;
            }
            $version = str_replace('_', '.', $m[1]);
            $ext = strtolower($m[2]);
            $raw = file_get_contents($path) ?: '';

            // Extract first non-empty line as date if matches YYYY-MM-DD or YYYY-MM-DD HH:MM:SS
            $releaseDate = null;
            $body = $raw;
            if ($ext !== 'html') {
                $lines = preg_split("/\r\n|\r|\n/", $raw);
                foreach ($lines as $idx => $line) {
                    $trim = trim($line);
                    if ($trim === '') continue;
                    // Support both YYYY-MM-DD and YYYY-MM-DD HH:MM:SS formats
                    if (preg_match('/^\d{4}-\d{2}-\d{2}(?:\s+\d{2}:\d{2}:\d{2})?$/', $trim)) {
                        $releaseDate = $trim;
                        // Remove this first non-empty date line from body
                        unset($lines[$idx]);
                        $body = implode("\n", $lines);
                    }
                    break;
                }
            }

            $notesHtml = '';
            if ($ext === 'html') {
                $notesHtml = $raw;
            } elseif ($ext === 'md') {
                $notesHtml = nl2br(e(trim($body)));
            } else {
                // txt
                $notesHtml = nl2br(e(trim($body)));
            }
            $releases[] = [
                'version' => 'v' . $version, // Ensure version has 'v' prefix to match app version
                'date' => $releaseDate ? $releaseDate : date('Y-m-d', filemtime($path) ?: time()),
                'notesHtml' => $notesHtml,
            ];
        }

        // Sort desc by date/time, then by version (newer versions first)
        usort($releases, function ($a, $b) {
            // Convert dates to timestamps for proper comparison
            $timeA = strtotime($a['date']);
            $timeB = strtotime($b['date']);
            $timeCompare = $timeB - $timeA;
            if ($timeCompare !== 0) {
                return $timeCompare;
            }
            // If dates/times are equal, sort by version (newer first)
            return version_compare($b['version'], $a['version']);
        });

        return $releases;
    }

    /**
     * Parse a HTML changelog where each release is under an <h2> heading containing version and date.
     */
    private function parseHtmlChangelog(string $html): array
    {
        $releases = [];
        if (trim($html) === '') {
            return $releases;
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        $xpath = new \DOMXPath($doc);
        $h2Nodes = $doc->getElementsByTagName('h2');

        $sections = [];
        foreach ($h2Nodes as $h2) {
            $sections[] = $h2;
        }
        // If no h2 sections, wrap whole html as a single release for current version
        if (count($sections) === 0) {
            $releases[] = [
                'version' => config('app.version'),
                'date' => date('Y-m-d'),
                'notesHtml' => $html,
            ];
            return $releases;
        }

        foreach ($sections as $index => $h2) {
            $title = trim($h2->textContent);
            $version = $this->extractVersion($title) ?? config('app.version');
            $date = $this->extractDate($title) ?? date('Y-m-d');

            // Collect nodes until next <h2>
            $notesHtml = '';
            for ($n = $h2->nextSibling; $n !== null && !($n instanceof \DOMElement && strtolower($n->tagName) === 'h2'); $n = $n->nextSibling) {
                // Skip empty text nodes
                if ($n instanceof \DOMText && trim($n->wholeText) === '') {
                    continue;
                }
                if ($n instanceof \DOMNode) {
                    $notesHtml .= $doc->saveHTML($n);
                }
            }

            $releases[] = [
                'version' => 'v' . $version, // Ensure version has 'v' prefix
                'date' => $this->normalizeDate($date),
                'notesHtml' => $notesHtml,
            ];
        }

        // Sort by date/time desc, then by version (newer versions first)
        usort($releases, function ($a, $b) {
            // Convert dates to timestamps for proper comparison
            $timeA = strtotime($a['date']);
            $timeB = strtotime($b['date']);
            $timeCompare = $timeB - $timeA;
            if ($timeCompare !== 0) {
                return $timeCompare;
            }
            // If dates/times are equal, sort by version (newer first)
            return version_compare($b['version'], $a['version']);
        });

        return $releases;
    }

    /**
     * Parse a Markdown changelog with headings like:
     * ## 1.4.0 - 2025-09-15
     * or ## [1.4.0] - 2025-09-15
     */
    private function parseMarkdownChangelog(string $md): array
    {
        $releases = [];
        if (trim($md) === '') {
            return $releases;
        }

        $pattern = '/^##\s*\[?(?<version>\d+\.\d+\.\d+)\]?\s*-\s*(?<date>\d{4}-\d{2}-\d{2}).*$/m';
        if (!preg_match_all($pattern, $md, $matches, PREG_OFFSET_CAPTURE)) {
            // No matches; return the whole file as a single release
            $releases[] = [
                'version' => config('app.version'),
                'date' => date('Y-m-d'),
                'notesHtml' => nl2br(e($md)),
            ];
            return $releases;
        }

        $count = count($matches['version']);
        for ($i = 0; $i < $count; $i++) {
            $version = $matches['version'][$i][0];
            $date = $matches['date'][$i][0];
            $start = $matches[0][$i][1] + strlen($matches[0][$i][0]);
            $end = ($i + 1 < $count) ? $matches[0][$i + 1][1] : strlen($md);
            $section = substr($md, $start, $end - $start);

            $releases[] = [
                'version' => 'v' . $version, // Ensure version has 'v' prefix
                'date' => $this->normalizeDate($date),
                'notesHtml' => nl2br(e(trim($section))),
            ];
        }

        // Sort by date/time desc, then by version (newer versions first)
        usort($releases, function ($a, $b) {
            // Convert dates to timestamps for proper comparison
            $timeA = strtotime($a['date']);
            $timeB = strtotime($b['date']);
            $timeCompare = $timeB - $timeA;
            if ($timeCompare !== 0) {
                return $timeCompare;
            }
            // If dates/times are equal, sort by version (newer first)
            return version_compare($b['version'], $a['version']);
        });

        return $releases;
    }

    private function extractVersion(string $text): ?string
    {
        if (preg_match('/(\d+\.\d+\.\d+)/', $text, $m)) {
            return $m[1];
        }
        return null;
    }

    private function extractDate(string $text): ?string
    {
        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $text, $m)) {
            return $m[1];
        }
        return null;
    }

    private function normalizeDate(string $date): string
    {
        // Always return YYYY-MM-DD without timezone to avoid shifts
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        return date('Y-m-d', strtotime($date));
    }
}


