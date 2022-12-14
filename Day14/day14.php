<?php

use Ds\Map;
use Ds\Sequence;
use Ds\Vector;

function readinput($filename) : Sequence
{
    $input = new Vector(file($filename, FILE_IGNORE_NEW_LINES));
    $scan = $input->map(function ($line) {
        $pointsInput = explode(' -> ', $line);
        $points = new Map();
        for ($i = 0; $i<count($pointsInput)-1; $i++){
            $start = explode(',', $pointsInput[$i]);
            $start = [(int)$start[0], (int)$start[1]];
            $end = explode(',', $pointsInput[$i+1]);
            $end = [(int)$end[0], (int)$end[1]];
            $incX = $end[0] <=> $start[0];
            $incY = $end[1] <=> $start[1];
            if($incX == 0) {
                for ($y = $start[1]; $y !== $end[1]; $y += $incY) {
                    $points->put([$start[0], $y], '#');
                }
                $points->put([$start[0], $y], '#');
            }
            else{
                for($x = $start[0]; $x !== $end[0]; $x += $incX){
                    $points->put([$x, $start[1]], '#');

                }
                $points->put([$x, $start[1]], '#');
            }
        }
        return $points;

    });
    return $scan;
}

function settle(Map $grid, $maxDepth, $hasFloor = false, $sandPosition = [500, 0]) : ?array
{
    if(($sandPosition[1]+1 > $maxDepth) && !$hasFloor){
        return null;
    }
    $possibleMoves = [[0,1], [-1,1], [1,1]];
    foreach ($possibleMoves as $move){
        $next = [$sandPosition[0] + $move[0], $sandPosition[1] + $move[1]];
        if($grid->hasKey($next) === false && $next[1] <= $maxDepth+2){
            return settle($grid, $maxDepth, $hasFloor, $next);
        }
    }
    //no possible move
    return $sandPosition;
}

function run($grid, $gridDepth, $hasFloor = false) : int
{
    $units = 0;
    while(true){
        $position = settle($grid, $gridDepth, $hasFloor);
        if(null === $position || ($position[1] === 0 && $position[0]==500)){
            return $units;
        }
        if($position[1] < $gridDepth + 2) {
            $grid->put($position, 'o');
            $units++;
        }
        else{
            $grid->put($position, '#');
        }
    }
}

function strGrid(Map $grid) : string
{
    $gridDepth = $grid->reduce(function($carry, $point){
        return ($point[1]>($carry??0)) ? $point[1] : ($carry??0);
    });
    $minX = $grid->reduce(function($carry, $point){
        return ($point[0]<($carry??PHP_INT_MAX)) ? $point[0] : ($carry??PHP_INT_MAX);
    });
    $maxX = $grid->reduce(function($carry, $point){
        return ($point[0]>($carry??0)) ? $point[0] : ($carry??0);
    });

    $str = '';
    for($y = 0; $y <= $gridDepth; $y++){
        for($x = $minX; $x <= $maxX; $x++){
            if($grid->hasKey([$x, $y]) === false){
                $str .= '.';
            }
            else{
                $str .= $grid->get([$x, $y]);
            }
        }
        $str .= "\n";
    }
    return $str;

}

$segments = readinput($argv[1]);
$grid = new Map();
foreach($segments as $segment){
    $grid->putAll($segment);
}

$gridDepth = $grid->reduce(function($carry, $point){
    return ($point[1]>($carry??0)) ? $point[1] : ($carry??0);
});

$grid1 = clone($grid);
echo 'part 1 : ', run($grid1, $gridDepth), PHP_EOL;
$grid2 = clone($grid);
echo 'part 2 : ', run($grid2, $gridDepth, true) + 1, PHP_EOL;
