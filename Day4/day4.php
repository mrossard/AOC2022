<?php

$input = file($argv[1], FILE_IGNORE_NEW_LINES);

function firstContainsSecond(array $first, array $second): bool
{
    if ($first[0] > $second[0]) {
        return false;
    }
    if ($first[1] < $second[1]) {
        return false;
    }
    return true;
}

function contained(array $a, array $b): bool
{
    return firstContainsSecond($a, $b) || firstContainsSecond($b, $a);
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
            $first = explode('-', $first);
            $second = explode('-', $second);
            return ($part($first, $second)) ? 1 : 0;
        },
        $input));
}

echo 'Part1 : ', run($input, contained(...)), PHP_EOL;
echo 'Part2 : ', run($input, overlap(...)), PHP_EOL;
