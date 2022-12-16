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

function reachablePositions(array $valves, array $minutePositions, $timeRemaining): array
{
    if ($timeRemaining <= 1) {
        //rien d'intéressant à faire de plus...
        return [];
    }
    $reachable = [];
    foreach ($minutePositions as $id => $position) {
        foreach ($position as $path => $state) {
            $current = $state['current'];
            $released = $state['released'];
            $open = $state['open'];
            foreach ($valves[$current]['neighbours'] as $neighbour) {
                //si voisin pas ouvert, on peut aller l'ouvrir
                if (!$valves[$neighbour]['open'] && $valves[$neighbour]['flowrate'] > 0) {
                    $openNext = new Map();
                    $openNext->put($path . ' -> ' . $neighbour . 'o',
                        [
                            'current' => $neighbour,
                            'released' => $released + ($timeRemaining * $valves[$neighbour]['flowrate']),
                            'open' => true,
                        ]);
                    $reachable[] = $openNext;
                }
                //dans tous les cas on peut y aller sans ouvrir - si assez de temps pour continuer!
                if ($timeRemaining > 2 && (!str_contains($path, $neighbour . 'c'))) {
                    $openNext = new Map();
                    $openNext->put($path . ' -> ' . $neighbour . 'c',
                        [
                            'current' => $neighbour,
                            'released' => $released,
                            'open' => false,
                        ]);
                    $reachable[] = $openNext;
                }

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
$start->put('AAc', ['current' => 'AA', 'released' => 0, 'open' => false]);
$minutePositions[0] = [$start];

for ($minute = 1; $minute <= $minutes; $minute++) {
    echo 'Minute ', $minute, PHP_EOL;
    $minutePositions[$minute] = reachablePositions($valves, $minutePositions[$minute - 1], $minutes - $minute + 1);
}

var_dump($minutePositions->toArray());

/*$bestPressureReleases = [];
foreach ($minutePositions as $minute => $positions) {
    if ($minute === 0) {
        $bestPressureReleases[$minute]['AA'] = 0;
        continue;
    }
    foreach ($positions as $position) {
        //on regarde la plus grande valeur dans les voisins à la minute précédente
        $max = 0;
        foreach ($valves[$position]['neighbours'] as $neighbour) {
            if (($bestPressureReleases[$minute - 1] ?? 0) > $max) {
                $max = $bestPressureReleases[$minute - 1];
            }
        }

    }
}*/