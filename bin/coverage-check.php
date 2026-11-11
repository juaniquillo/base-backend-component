<?php

declare(strict_types=1);

/*
 * Fails when any statement in src/ is uncovered.
 *
 * PHPUnit 12 has no built-in coverage threshold, so this reads the Clover
 * report produced by phpunit.xml.dist and exits non-zero on gaps.
 */

$clover = $argv[1] ?? 'build/logs/clover.xml';

if (!is_file($clover)) {
    fwrite(\STDERR, "Coverage report not found at {$clover}. Run composer test:coverage first.".\PHP_EOL);

    exit(1);
}

$xml = simplexml_load_file($clover);

if ($xml === false) {
    fwrite(\STDERR, "Could not parse {$clover}.".\PHP_EOL);

    exit(1);
}

$missing = [];
$total = 0;

foreach ($xml->project->package as $package) {
    foreach ($package->file as $file) {
        $uncovered = [];

        foreach ($file->line as $line) {
            if ((string) $line['type'] !== 'stmt') {
                continue;
            }

            ++$total;

            if ((string) $line['count'] === '0') {
                $uncovered[] = (string) $line['num'];
            }
        }

        if ($uncovered !== []) {
            $name = str_replace('\\', '/', (string) $file['name']);
            $missing[basename($name)] = $uncovered;
        }
    }
}

if ($missing === []) {
    echo "Coverage check passed: 100% of {$total} statements.".\PHP_EOL;

    exit(0);
}

$uncoveredCount = array_sum(array_map('count', $missing));

echo "Coverage check failed: {$uncoveredCount} uncovered statements.".\PHP_EOL;

foreach ($missing as $file => $lines) {
    echo "  {$file}: ".implode(', ', $lines).\PHP_EOL;
}

exit(1);
