<?php

function viewingDistance(array $grid, int $x, int $y, string $direction): int
{
    $start = match ($direction) {
        'droite' => $x + 1,
        'gauche' => $x - 1,
        'bas' => $y + 1,
        'haut' => $y - 1
    };
    $stop = match ($direction) {
        'droite' => count($grid[0]),
        'bas' => count($grid),
        'gauche', 'haut' => 0
    };

    $continue = function ($item, $borne) use ($direction) {
        return match ($direction) {
            'droite', 'bas' => $item < $borne,
            'gauche', 'haut' => $item >= $borne,
        };
    };
    $inc = function ($item) use ($direction) {
        return match ($direction) {
            'droite', 'bas' => $item + 1,
            'gauche', 'haut' => $item - 1,
        };
    };
    $higher = function ($grid, $start, $x, $y, $direction) {
        return match ($direction) {
            'droite', 'gauche' => $grid[$start][$y] >= $grid[$x][$y],
            'haut', 'bas' => $grid[$x][$start] >= $grid[$x][$y],
        };
    };

    $viewingDistance = 0;
    while ($continue($start, $stop)) {
        $viewingDistance++;
        if ($higher($grid, $start, $x, $y, $direction)) {
            break;
        }
        $start = $inc($start);
    }

    return $viewingDistance;
}

function visible(array $grid, int $x, int $y)
{
    $sizeX = count($grid[0]);
    $sizeY = count($grid);
    if ($x === 0 || $y === 0 || $x === $sizeX - 1 || $y === $sizeY - 1) {
        return true;
    }

    $dDroite = viewingDistance($grid, $x, $y, 'droite');
    $dGauche = viewingDistance($grid, $x, $y, 'gauche');
    $dBas = viewingDistance($grid, $x, $y, 'bas');
    $dHaut = viewingDistance($grid, $x, $y, 'haut');
    return (
        ($dDroite + $x == $sizeX - 1 && $grid[$x][$y] > $grid[$sizeX - 1][$y]) ||
        ($x - $dGauche == 0 && $grid[$x][$y] > $grid[0][$y]) ||
        ($dBas + $y == $sizeY - 1 && $grid[$x][$y] > $grid[$x][$sizeY - 1]) ||
        ($y - $dHaut == 0 && $grid[$x][$y] > $grid[$x][0])
    );
}


function scenicScore(array $grid, int $x, int $y): int
{
    return (viewingDistance($grid, $x, $y, 'droite') *
        viewingDistance($grid, $x, $y, 'gauche') *
        viewingDistance($grid, $x, $y, 'bas') *
        viewingDistance($grid, $x, $y, 'haut'));

}

$input = file($argv[1], FILE_IGNORE_NEW_LINES);

$grid = array_map(function ($line) {
    return array_map(intval(...), str_split($line));
}, $input);


$visibleCount = array_sum(array_map(function ($col, $x) use ($grid) {
    return array_sum(array_map(function ($tree, $y) use ($grid, $x) {
        return visible($grid, $x, $y) ? 1 : 0;
    }, $col, array_keys($col)));
}, $grid, array_keys($grid)));

echo 'part 1 : ', $visibleCount, PHP_EOL;

$scores = [];
$distances = [];
$visible = [];
foreach ($grid as $x => $line) {
    foreach ($line as $y => $col) {
        foreach (['droite', 'gauche', 'bas', 'haut'] as $direction) {
            $distances[$x][$y][$direction] = viewingDistance($grid, $x, $y, $direction);
        }
        $visible[$x][$y] = visible($grid, $x, $y);
        $scores[] = scenicScore($grid, $x, $y);
    }
}

echo 'part 2 : ', max($scores), PHP_EOL;