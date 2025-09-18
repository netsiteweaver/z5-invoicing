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
            // Prefer HTML changelog
            foreach (['_CHANGELOG.html', 'CHANGELOG.html'] as $file) {
                $path = $docsDir . DIRECTORY_SEPARATOR . $file;
                if (is_file($path)) {
                    $releases = $this->parseHtmlChangelog(file_get_contents($path));
                    break;
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

        $getInnerHtml = static function(\DOMNode $node): string {
            $innerHTML = '';
            foreach ($node->childNodes as $child) {
                $innerHTML .= $node->ownerDocument->saveHTML($child);
            }
            return $innerHTML;
        };

        $sections = [];
        foreach ($h2Nodes as $h2) {
            $sections[] = $h2;
        }
        // If no h2 sections, wrap whole html as a single release for current version
        if (count($sections) === 0) {
            $releases[] = [
                'version' => config('app.version'),
                'date' => date('c'),
                'notesHtml' => $html,
            ];
            return $releases;
        }

        foreach ($sections as $index => $h2) {
            $title = trim($h2->textContent);
            $version = $this->extractVersion($title) ?? config('app.version');
            $date = $this->extractDate($title) ?? date('c');

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
                'version' => $version,
                'date' => $this->normalizeDate($date),
                'notesHtml' => $notesHtml,
            ];
        }

        // Sort by date desc
        usort($releases, function ($a, $b) {
            return strtotime($b['date']) <=> strtotime($a['date']);
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
                'date' => date('c'),
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
                'version' => $version,
                'date' => $this->normalizeDate($date),
                'notesHtml' => nl2br(e(trim($section))),
            ];
        }

        // Sort by date desc
        usort($releases, function ($a, $b) {
            return strtotime($b['date']) <=> strtotime($a['date']);
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
        // If it's just YYYY-MM-DD, convert to ISO8601 midnight UTC
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return date('c', strtotime($date . ' 00:00:00'));
        }
        return date('c', strtotime($date));
    }
}


