<?php

$input = file('input.txt',FILE_IGNORE_NEW_LINES);

$nbElves = 0;
$calories = [];

foreach ($input as $line){
    if($line == ''){
        $nbElves++;
        continue;
    }
    $calories[$nbElves] = ($calories[$nbElves] ?? 0) + (int) $line;
}

sort($calories);
$top[] = array_pop($calories);
echo 'part 1 : ', $top[0], PHP_EOL;

$top[] = array_pop($calories);
$top[] = array_pop($calories);

echo 'part 2 : ', array_sum($top), PHP_EOL;