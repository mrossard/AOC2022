<?php

$input = file($argv[1], FILE_IGNORE_NEW_LINES);

function part1(int $opp, int $me): int
{
    $diff = $me - ord('X') + ord('A') - $opp;
    return match ($diff) {
        -1, 2 => $me - ord('W'),
        0 => 3 + $me - ord('W'),
        1, -2 => 6 + $me - ord('W')
    };
}

function part2(int $opp, int $result): int
{
    $oppval = $opp - ord('A') + 1;
    return match ($result - ord('X')) {
        0 => (($oppval + 4) % 3) + 1,
        1 => $oppval + 3,
        2 => $oppval % 3 + 7
    };
}

function getScores($input, $part): array
{
    return array_map(
        callback: function ($line) use ($part) {
            [$opponent, $me] = array_map(
                callback: function ($char) {
                    return ord($char);
                },
                array   : explode(' ', $line));
            return $part($opponent, $me);
        },
        array   : $input);
}

echo 'part 1: ', array_sum(getScores($input, part1(...))), PHP_EOL;
echo 'part 2: ', array_sum(getScores($input, part2(...))), PHP_EOL;
