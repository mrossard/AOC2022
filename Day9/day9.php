<?php

use Ds\Set;

function move(array &$currentH, array &$currentT, string $direction, int $nb) : array
{
    $visits = [];
    $moveOne = match($direction){
      'R'=> [1,0],
      'L'=> [-1,0],
      'U'=> [0,1],
      'D'=> [0,-1],
    };
}

$input = file($argv[1], FILE_IGNORE_NEW_LINES);

$moves = array_map(function($line){
    [$direction, $nb] = explode(' ', $line);
    return [$direction, (int) $nb];
}, $input);

var_dump($moves);

$visited = new Set();
$currentH = [0,0];
$currentT = $currentH;

foreach($moves as $move){
    $visited->add(...move($currentH, $currentT, $move[0], $move[1]));
}