<?php

$input = file('input.txt',FILE_IGNORE_NEW_LINES);

$calories = [];
$start = 0;

$sep = array_keys(array: $input, filter_value: '');
foreach($sep as $id){
    $calories[] = array_sum(array: array_slice(array: $input, offset: $start, length:$id-$start));
    $start = $id;
}
rsort($calories);

echo 'part 1 : ', $calories[0], PHP_EOL;
echo 'part 2 : ', array_sum(array_slice($calories, 0,3)), PHP_EOL;