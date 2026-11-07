<?php
// Inject the shared Library navigation loader into every comparison HTML page.

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This tool must run from the command line.\n");
    exit(1);
}

$write = in_array('--write', $argv, true);
$check = in_array('--check', $argv, true);
$root = null;
foreach ($argv as $argument) {
    if (str_starts_with($argument, '--root=')) {
        $root = substr($argument, strlen('--root='));
        break;
    }
}
$root = $root !== null ? rtrim($root, DIRECTORY_SEPARATOR) : getcwd() . '/study-guide/comparisons';
if (!is_dir($root)) {
    fwrite(STDERR, "Comparison directory does not exist: {$root}\n");
    exit(1);
}

$source = '/study-guide/comparisons/library-navigation.js?v=20261107-9';
$marker = '/study-guide/comparisons/library-navigation.js';
$tag = '<script src="' . $source . '" defer></script>';
$files = glob($root . DIRECTORY_SEPARATOR . '*.html') ?: [];
$installed = 0;
$missing = 0;
$changed = 0;
$failures = [];

foreach ($files as $file) {
    $original = file_get_contents($file);
    if ($original === false) {
        $failures[] = basename($file);
        continue;
    }
    if (str_contains($original, $marker)) {
        $installed++;
        if ($write) {
            $normalized = preg_replace(
                '~<script\s+src=["\']/study-guide/comparisons/library-navigation\.js(?:\?[^"\']*)?["\']\s+defer></script>~i',
                $tag,
                $original,
                1
            );
            if ($normalized !== null && $normalized !== $original) {
                file_put_contents($file, $normalized);
                $changed++;
            }
        }
        continue;
    }
    $missing++;
    if (!$write) {
        continue;
    }
    if (preg_match('~</body>~i', $original) !== 1) {
        $failures[] = basename($file);
        continue;
    }
    $updated = preg_replace('~</body>~i', $tag . '</body>', $original, 1);
    if ($updated === null || file_put_contents($file, $updated) === false) {
        $failures[] = basename($file);
        continue;
    }
    $changed++;
}

echo json_encode([
    'root' => $root,
    'htmlFiles' => count($files),
    'installed' => $installed,
    'missing' => $missing,
    'changed' => $changed,
    'failures' => count($failures),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

if ($failures) {
    fwrite(STDERR, 'Could not inject Library navigation into: ' . implode(', ', array_slice($failures, 0, 20)) . PHP_EOL);
    exit(1);
}
if (!$files) {
    fwrite(STDERR, "No comparison HTML files were found.\n");
    exit(1);
}
if ($check && $missing > 0) {
    fwrite(STDERR, "{$missing} comparison pages are missing the Library navigation loader.\n");
    exit(1);
}
