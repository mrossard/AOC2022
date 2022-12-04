<?php

$input = file($argv[1], FILE_IGNORE_NEW_LINES);

function contained(array $first, array $second): bool
{
    return !($first[0] > $second[0] || $first[1] < $second[1]) ||
        !($second[0] > $first[0] || $second[1] < $first[1]);
}

function overlap(array $a, array $b): bool
{
    return ($a[0] >= $b[0] && $a[0] <= $b[1]) ||
        ($a[1] >= $b[0] && $a[1] <= $b[1]) ||
        ($b[0] >= $a[0] && $b[0] <= $a[1]) ||
        ($b[1] >= $a[0] && $b[1] <= $a[1]);
}

function run($input, $part)
{
    return array_sum(array_map(
        function ($line) use ($part) {
            [$first, $second] = explode(',', $line);
            return ($part(explode('-', $first), explode('-', $second))) ? 1 : 0;
        },
        $input));
}

echo 'Part1 : ', run($input, contained(...)), PHP_EOL;
echo 'Part2 : ', run($input, overlap(...)), PHP_EOL;