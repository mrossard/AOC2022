<?php

use Ds\Set;

function distances($first, $second): array
{
    return [$first[0] - $second[0], $first[1] - $second[1]];
}

function move(array &$knots, array $move): Set
{
    $singleMove = $move[0];
    $nbMoves = $move[1];
    $visits = new Set();
    $followers = array_slice($knots, 1);
    for ($i = 0; $i < $nbMoves; $i++) {
        $knots[0] = [$knots[0][0] + $singleMove[0], $knots[0][1] + $singleMove[1]];
        $visits->add(...follow($knots[0], $followers));
    }
    $knots = [$knots[0], ...$followers];
    return $visits;
}

function follow(array $head, array &$followers): Set
{
    $visits = new Set();
    $next = array_shift($followers);
    $distances = distances($head, $next);
    if (abs($distances[0]) > 1 || abs($distances[1]) > 1) {
        //on bouge
        $next = match (true) {
            (abs($distances[0]) == 2 && abs($distances[1]) == 1) => [$next[0] + $distances[0] / 2, $next[1] + $distances[1]],
            (abs($distances[0]) == 1 && abs($distances[1]) == 2) => [$next[0] + $distances[0], $next[1] + $distances[1] / 2],
            default => [$next[0] + $distances[0] / 2, $next[1] + $distances[1] / 2]
        };
        $visits->add($next);
        if (!empty($followers)) {
            $visits = follow($next, $followers);
        }
    }
    array_unshift($followers, $next);
    return $visits;
}

$input = file($argv[1], FILE_IGNORE_NEW_LINES);

$moves = array_map(function ($line) {
    [$direction, $nb] = explode(' ', $line);
    return [match ($direction) {
        'R' => [1, 0],
        'L' => [-1, 0],
        'U' => [0, 1],
        'D' => [0, -1],
    }, (int)$nb];
}, $input);

$visited = new Set();
foreach (range(0, $argv[2]) as $index) {
    $knots[$index] = [0, 0];
}

foreach ($moves as $move) {
    $visited->add(...move($knots, $move));
}

echo 'part 1 : ', count($visited) + 1, PHP_EOL;