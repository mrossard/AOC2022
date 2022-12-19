<?php

$rocks = [
    0 => [
        0 => [1, 1, 1, 1],
    ],
    1 => [
        0 => [0, 1, 0],
        1 => [1, 1, 1],
        2 => [0, 1, 0],
    ],
    2 => [
        0 => [0, 0, 1],
        1 => [0, 0, 1],
        2 => [1, 1, 1],
    ],
    3 => [
        0 => [1],
        1 => [1],
        2 => [1],
        3 => [1],
    ],
    4 => [
        0 => [1, 1],
        1 => [1, 1],
    ],
];

function moveRock($rockShape, $position, $move, $grid, $debug): array
{
    $xSize = count($rockShape[0]);
    $ySize = count($rockShape);

    switch ($move) {
        case '>':
            if ($debug) {
                echo 'Trying to move right', PHP_EOL;
            }
            //dépasse à droite?
            if ($position[0] + count($rockShape[0]) - 1 >= 6) {
                return $position;
            }
            //bloqué par un autre rocher?
            foreach ($rockShape as $yOffset => $rockLine) {
                $minX = -1;
                for ($xOffset = $xSize - 1; $xOffset >= 0; $xOffset--) {
                    if ($rockLine[$xOffset] === 0) {
                        continue;
                    }
                    $minX = $xOffset;
                    break;
                }
                if (($grid[$position[1] - $yOffset][$position[0] + $minX + 1] ?? 0) === 1) {
                    return $position;
                }
            }
            //pas bloqué, on bouge!
            return [$position[0] + 1, $position[1]];
        case '<':
            if ($debug) {
                echo 'Trying to move left', PHP_EOL;
            }
            //dépasse à gauche?
            if ($position[0] < 1) {
                return $position;
            }
            //bloqué par un autre rocher?
            foreach ($rockShape as $yOffset => $rockLine) {
                $minX = 7;
                for ($xOffset = 0; $xOffset < $xSize; $xOffset++) {
                    if ($rockLine[$xOffset] === 0) {
                        continue;
                    }
                    $minX = $xOffset;
                    break;
                }
                if (($grid[$position[1] - $yOffset][$position[0] + $minX - 1] ?? 0) === 1) {
                    return $position;
                }
            }
            //pas bloqué, on bouge!
            return [$position[0] - 1, $position[1]];
        default:
            if ($debug) {
                echo 'Trying to move down', PHP_EOL;
            }
            //down
            $bottom = $position[1] - $ySize + 1;
            if ($bottom === 1) {
                return $position;
            }
            foreach ($rockShape as $yOffset => $rockLine) {
                foreach ($rockLine as $xOffset => $rockSpace) {
                    if (($grid[$position[1] - $yOffset - 1][$position[0] + $xOffset] ?? 0) === 1 && $rockSpace === 1) {
                        return $position;
                    }
                }
            }
            //On peut descendre
            return [$position[0], $position[1] - 1];
    }
}

function settleRock(mixed $nextRock, array $grid, array $newPosition): array
{
    foreach ($nextRock as $y => $rockLine) {
        foreach ($rockLine as $x => $rockSpace) {
            if ($rockSpace === 1) {
                $grid[$newPosition[1] - $y][$newPosition[0] + $x] = 1;
            }
        }
    }
    return $grid;
}

function drawGridAndFallingRock($grid, $rockShape, $rockPosition)
{
    $rockInGrid = [];
    foreach ($rockShape as $y => $rockLine) {
        foreach ($rockLine as $x => $rockSpace) {
            $rockInGrid[$rockPosition[1] - $y][$x + $rockPosition[0]] = $rockSpace;
        }
    }

    $strGrid = '';
    for ($y = max($rockPosition[1], count($grid)); $y >= 0; $y--) {
        $bord = ($y === 0) ? '+' : '|';
        $strGrid .= $bord;
        for ($x = 0; $x < 7; $x++) {
            if ($y === 0) {
                $strGrid .= '-';
                continue;
            }
            if (($grid[$y][$x] ?? 0) === 1) {
                $strGrid .= '#';
                continue;
            }
            if (($rockInGrid[$y][$x] ?? 0) === 1) {
                $strGrid .= '@';
                continue;
            }
            $strGrid .= '.';
        }
        $strGrid .= $bord . PHP_EOL;
    }
    return $strGrid;
}

$moves = str_split(file($argv[1], FILE_IGNORE_NEW_LINES)[0]);
$maxRocks = (int)$argv[2];
$print = (($argv[3] ?? '0') == '1');
$fallenRocks = 0;
$grid = [];
$highestPosition = 0;
$ticks = 0;

while ($fallenRocks < $maxRocks) {
    if ($print) {
        echo "Rock ", $fallenRocks + 1, " starts falling", PHP_EOL;
    }
    $nextRock = $rocks[$fallenRocks % 5];
    //position de départ du (0,0) du rocher
    $rockPosition = [2, $highestPosition + 3 + count($nextRock)];
    $stopped = false;
    if ($print) {
        echo drawGridAndFallingRock($grid, $nextRock, $rockPosition), PHP_EOL;
    }
    while (!$stopped) {
        $move = $moves[$ticks % count($moves)];
        $newPosition = moveRock($nextRock, $rockPosition, $move, $grid, $print);
        if ($print) {
            echo drawGridAndFallingRock($grid, $nextRock, $newPosition), PHP_EOL;
        }
        $ticks++;
        $newPosition = moveRock($nextRock, $newPosition, 'down', $grid, $print);
        if ($print) {
            echo drawGridAndFallingRock($grid, $nextRock, $newPosition), PHP_EOL;
        }
        if ($newPosition[1] === $rockPosition[1]) {
            $stopped = true;
            //ajouter à la grille
            $grid = settleRock($nextRock, $grid, $newPosition);
            if ($newPosition[1] > $highestPosition) {
                $highestPosition = $newPosition[1];
            }
            // echo "Rock ", $fallenRocks + 1, " stops", PHP_EOL, 'highest : ', $highestPosition, PHP_EOL;

        }
        $rockPosition = $newPosition;
    }
    $fallenRocks++;
}


echo 'Highest : ', $highestPosition, PHP_EOL;