<?php

$subSum = function (array $array, $start, $length): int {
    return array_sum(array_slice($array, $start, $length));
};

$input = file('input.txt', FILE_IGNORE_NEW_LINES);
$sep = array_keys(array: $input, filter_value: '');
$elves = array_map(
    callback: function ($end) use ($sep): array {
        return [$sep[array_search(needle: $end, haystack: $sep) - 1] ?? 0, $end];
    },
    array   : $sep);

$calories = array_map(
    callback: function ($elf) use ($input, $subSum): int {
        return $subSum($input, $elf[0], $elf[1] - $elf[0]);
    },
    array   : $elves);
rsort($calories);

echo 'part 1 : ', $calories[0], PHP_EOL;
echo 'part 2 : ', $subSum($calories, 0, 3), PHP_EOL;