<?php

$input = file('input.txt', FILE_IGNORE_NEW_LINES);
$sep = array_keys(array: $input, filter_value: '');

$calories = array_map(
    callback: function ($elf): int {
        return array_sum($elf);
    },
    array   : array_map(
        callback: function ($end) use ($sep, $input): array {
            $start = $sep[array_search(needle: $end, haystack: $sep) - 1] ?? 0;
            return array_slice(
                array : $input,
                offset: $start,
                length: $end - $start);
        },
        array   : $sep));
rsort($calories);

echo 'part 1 : ', $calories[0], PHP_EOL;
echo 'part 2 : ', array_sum(array_slice($calories, 0, 3)), PHP_EOL;