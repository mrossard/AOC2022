<?php

use Ds\Map;
use Ds\Queue;

$input = file($argv[1], FILE_IGNORE_NEW_LINES);

$grid = [];
$startP1 = null;
$allAs = [];
$target = null;
foreach ($input as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        switch ($char) {
            case 'S':
                $startP1 = [$x, $y];
                $grid[$y][$x] = ord('a');
                $allAs[] = [$x, $y];
                break;
            case'a':
                $grid[$y][$x] = ord('a');
                $allAs[] = [$x, $y];
                break;
            case'E':
                $target = [$x, $y];
                $grid[$y][$x] = ord('z');
                break;
            default:
                $grid[$y][$x] = ord($char);
                break;
        }

    }
}

function stepsRequired($grid, $start, $target, $avoidAs = false)
{
    $toVisit = new Queue([[$start, 0]]);
    $visited = new Map();

    while ($toVisit->count() > 0) {
        $current = $toVisit->pop();
        if (null !== $visited->get($current[0], null)) {
            continue;
        }
        $visited->put($current[0], $current[1]);
        [$currentX, $currentY] = $current[0];

        foreach ([[$currentX, $currentY - 1], [$currentX, $currentY + 1], [$currentX - 1, $currentY], [$currentX + 1, $currentY]] as [$x, $y]) {
            if (!array_key_exists($y, $grid) || !array_key_exists($x, $grid[$y]) || $grid[$y][$x] - $grid[$currentY][$currentX] > 1) {
                continue;
            }
            $alreadyVisitedWithMovements = $visited->get([$x, $y], null);
            if (null !== $alreadyVisitedWithMovements && $alreadyVisitedWithMovements <= $current[1] + 1) {
                continue;
            }
            if (!$avoidAs || $grid[$y][$x] !== ord('a')) {
                $toVisit->push([[$x, $y], $current[1] + 1]);
            }
        }
    }

    return $visited->get($target, 'not visited lol');
}


echo 'Part 1 : ', stepsRequired($grid, $startP1, $target), PHP_EOL;


$part2 = array_reduce(
    array_map(function ($start) use ($grid, $target) {
        return stepsRequired($grid, $start, $target, true);
    }, $allAs),
    function ($carry, $item) {
        return min([$carry ?? 9999999999999, $item]);
    }
);

echo 'Part 2 : ', $part2, PHP_EOL;