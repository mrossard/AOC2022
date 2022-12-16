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

function maxReleased($valves, $currentPosition, $previousPosition, $visited, $timeRemaining, $released)
{
    if ($timeRemaining < 1) {
        return ['path' => $currentPosition, 'released' => $released];
    }
    if (null !== $previousPosition) {
        $visited[] = $previousPosition . $currentPosition;
    }
    $nextMax = [];
    $tryCurrent = (!$valves[$currentPosition]['open']) && ($valves[$currentPosition]['flowrate'] > 0);
    foreach ($valves[$currentPosition]['neighbours'] as $neighbour) {
        if (!in_array($currentPosition . $neighbour, $visited, true)) {
            //sans ouvrir
            $nextMax[$neighbour . 'O'] = maxReleased($valves, $neighbour, $currentPosition, $visited, $timeRemaining - 1, $released);
            if ($tryCurrent) {
                $newValves = $valves;
                $newValves[$currentPosition]['open'] = true;
                $willRelease = $released + ($valves[$currentPosition]['flowrate'] * ($timeRemaining - 1));

                $nextMax[$neighbour . 'C'] = maxReleased($newValves, $neighbour, $currentPosition, $visited,
                    $timeRemaining - 2, $willRelease);
            }
        }
    }
    if (count($nextMax) === 0) {
        return ['path' => $currentPosition, 'released' => $released];
    }
    $path = '';
    $max = 0;
    foreach ($nextMax as $next) {
        if ($next['released'] > $max) {
            $max = $next['released'];
            $path = $currentPosition . '->' . $next['path'];
        }
    }
    //echo $timeRemaining, ' previous :', $previousPosition ?? '', ' current: ', $currentPosition, ' max: ', $max, PHP_EOL;
    return ['path' => $path, 'released' => $max];
}

$valves = [];
foreach ($input as $line) {
    $valve = readInput($line);
    $valves[$valve['nom']] = $valve;
}

$minutes = (int)$argv[2];
$maxReleased = maxReleased($valves, 'AA', null, [], $minutes, 0);

echo $maxReleased['path'], ' : ', $maxReleased['released'], PHP_EOL;


