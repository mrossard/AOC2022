<?php

$input = file('input.txt',FILE_IGNORE_NEW_LINES);

$calories = [];
$start = 0;

$sep = array_keys(array: $input, filter_value: '');
foreach($sep as $id){
    $calories[] = array_sum(array: array_slice(array: $input, offset: $start, length:$id-$start));
    $start = $id;
}
sort($calories);

$top[] = array_pop($calories);
echo 'part 1 : ', $top[0], PHP_EOL;

$top[] = array_pop($calories);
$top[] = array_pop($calories);
echo 'part 2 : ', array_sum($top), PHP_EOL;