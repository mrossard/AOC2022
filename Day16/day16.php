<?php

use Ds\Map;
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

function reachablePositions(Map $minutePositions, $timeRemaining): Map
{
    if ($timeRemaining <= 1) {
        //rien d'intéressant à faire de plus...
        return new Map();
    }
    $reachable = new Map();
    foreach ($minutePositions as $path => $position) {
        $current = $position['current'];
        $released = $position['released'];
        $valves = $position['valves'];
        $open = $valves[$current]['open'];
        foreach ($valves[$current]['neighbours'] as $neighbour) {
            $move = $current . ($open ? 'o' : 'c') . ' -> ' . $neighbour;
            if (str_contains($path, $move)) {
                continue;
            }
            //si voisin pas ouvert, on peut aller l'ouvrir
            if (!$valves[$neighbour]['open'] && $valves[$neighbour]['flowrate'] > 0) {
                $valves[$neighbour]['open'] = true;
                $reachable->put($path . ' -> ' . $neighbour . 'o',
                    [
                        'current' => $neighbour,
                        'released' => $released + ($timeRemaining * $valves[$neighbour]['flowrate']),
                        'open' => true,
                        'valves' => $valves,
                    ]);
            }
            //dans tous les cas on peut y aller sans ouvrir - si assez de temps pour continuer!
            if ($timeRemaining > 2) {
                $newPath = $path . ' -> ' . $neighbour . 'c';
                $reachable->put($newPath, [
                    'current' => $neighbour,
                    'released' => $released,
                    'open' => false,
                    'valves' => $valves,
                ]);
            }

        }
    }

    return $reachable;
}

$valves = [];
foreach ($input as $line) {
    $valve = readInput($line);
    $valves[$valve['nom']] = $valve;
}

$minutes = (int)$argv[2];
$minutePositions = new Map();
$start = new Map();
$minutePositions[0] = new Map();
$minutePositions[0]->put('AAc', ['current' => 'AA', 'released' => 0, 'valves' => $valves]);

for ($minute = 1; $minute <= $minutes; $minute++) {
    echo '=== Minute ', $minute, ' === ', PHP_EOL;
    $reachable = reachablePositions($minutePositions[$minute - 1], $minutes - $minute + 1);
    /*    foreach ($reachable as $path => $position) {
            echo ' - ', $path, PHP_EOL;
        }*/
    $minutePositions->put($minute, $reachable);
}

foreach ($minutePositions as $minute => $positions) {
    foreach ($positions as $path => $position) {
        echo $path, PHP_EOL;
    }
}

