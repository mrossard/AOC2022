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
            $toExplore->push([$neighbour, $distance + 1]);
        }
    }

    return $shortest;
}

function getDistances(array $valves): array
{
    $distances = [];

    foreach ($valves as $id => $valve) {
        if ($valve['flowrate'] <= 0 && $id !== 'AA') {
            continue;
        }
        foreach ($valves as $otherId => $other) {
            if ($id === $otherId || ($other['flowrate'] <= 0 && $otherId !== 'AA')) {
                continue;
            }
            $distance = getDistance($id, $otherId, $valves);
            $distances[$id][$otherId] = $distance;
            $distances[$otherId][$id] = $distance;
        }
    }
    return $distances;
}

function possibleMoves($distances, $currentPosition, $remainingTime, $valves): array
{
    $positions = [];
    foreach ($distances[$currentPosition] as $move => $distance) {
//        if ($valves[$move]['flowrate'] <= 0) {
//            continue;
//        }
        $willRemain = $remainingTime - $distance;
        if ($willRemain > 1 && $valves[$move]['open'] === false) {
            $positions[$move] = $willRemain;
        }
    }
    return $positions;
}

function maxReleased(string $currentPosition, array $valves, array $distances, int $remainingTime, bool $withElephant = false)
{
    $releasedByCurrent = ($remainingTime - 1) * $valves[$currentPosition]['flowrate'];
    if ($remainingTime <= 1) {
        return [$currentPosition, $releasedByCurrent];
    }
    //on ouvre
    if ($currentPosition != 'AA') {
        $remainingTime--;
        $valves[$currentPosition]['open'] = true;
    }

    $possibleMoves = possibleMoves($distances, $currentPosition, $remainingTime, $valves);

    if (count($possibleMoves) === 0) {
        return [$currentPosition, $releasedByCurrent];
    }

    $released = [];
    $bestPath = [];
    $relevantDistances = $distances;
    unset($relevantDistances[$currentPosition]);
    foreach ($relevantDistances as $position => $distance) {
        unset($relevantDistances[$position][$currentPosition]);
    }
    //si éléphant, il faut lister les combinaisons dispo et appeler sur cette base.
    //
    foreach ($possibleMoves as $valve => $willRemain) {
        $next = maxReleased($valve, $valves, $relevantDistances, $willRemain);
        $released[$valve] = $releasedByCurrent + $next[1];
        $bestPath[$valve] = $next[0];
    }

    $max = PHP_INT_MIN;
    $nextValves = '';
    foreach ($released as $next => $value) {
        if ($value > $max) {
            $max = $value;
            $nextValves = $currentPosition . '->' . $bestPath[$next];
        }
    }

    return [$nextValves, $max];
}

$valves = [];
foreach ($input as $line) {
    $valve = readInput($line);
    $valves[$valve['nom']] = $valve;
}

$distances = getDistances($valves);

$released = maxReleased('AA', $valves, $distances, $argv[2]);

echo 'Part 1 : ', $released[0], ' ', $released[1], PHP_EOL;