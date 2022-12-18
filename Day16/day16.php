<?php

use Ds\Map;
use Ds\Queue;
use Ds\Set;
use Ds\Vector;

$input = file($argv[1], FILE_IGNORE_NEW_LINES);

function readInput($line): array
{
    $valve = [];
    [$valveStr, $tunnelsStr] = explode(';', $line);
    $valve['nom'] = substr($valveStr, 6, 2);
    $valve['flowrate'] = (int)substr($valveStr, strpos($valveStr, '=') + 1);
    $valve['neighbours'] = explode(', ', $tunnelsStr);
    $valve['neighbours'][0] = substr($valve['neighbours'][0], -2);
    $valve['open'] = false;
    return $valve;
}

function getDistance(string $from, string $to, $valves): int
{
    $shortest = PHP_INT_MAX;
    $toExplore = new Queue();
    $toExplore->push([$from, 0]);
    $explored = new Map();
    while ($toExplore->count() !== 0) {
        [$current, $distance] = $toExplore->pop();
        $explored->put($current, $distance);
        foreach ($valves[$current]['neighbours'] as $neighbour) {
            if ($neighbour === $to) {
                return $distance + 1;
            }
            if ($explored->hasKey($neighbour)) {
                continue;
            }
            $toExplore->push($neighbour, $distance + 1);
        }
    }

    return $shortest;
}

function getDistances(array $valves): Map
{
    $distances = new Map;

    foreach ($valves as $id => $valve) {
        foreach ($valves as $otherId => $other) {
            if ($id === $other || $other['flowrate'] === 0 || $distances->hasKey([$id, $otherId])) {
                continue;
            }
            $distance = getDistance($id, $otherId, $valves);
            $distances->put([$id, $otherId], $distance);
            $distances->put([$otherId, $id], $distance);
        }
    }
    return $distances;
}

$valves = [];
foreach ($input as $line) {
    $valve = readInput($line);
    $valves[$valve['nom']] = $valve;
}

$distances = getDistances($valves);

var_dump($distances);
