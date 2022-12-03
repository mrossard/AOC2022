<?php

$input = file($argv[1], FILE_IGNORE_NEW_LINES);

$getCommonChar = function (...$strings): string {
    foreach (str_split($strings[0]) as $charA) {
        for ($i = 1; $i < count($strings); $i++) {
            if (!str_contains($strings[$i], $charA)) {
                continue 2;
            }
        }
        return $charA;
    }
    throw new Exception('none found');
};

$charValue = function (string $char) {
    return ord($char) > ord('a') ?
        ord($char) - ord('a') + 1 :
        ord($char) - ord('A') + 27;
};

$needsTomove = array_map(function (string $line) use ($getCommonChar, $charValue) {
    return $charValue($getCommonChar(substr($line, strlen($line) / 2), substr($line, 0, strlen($line) / 2)));
}, $input);

echo 'part 1 : ', array_sum($needsTomove), PHP_EOL;

$groups = array_chunk($input, 3);

$badges = array_map(function ($group) use ($getCommonChar, $charValue) {
    return $charValue($getCommonChar($group[0], $group[1], $group[2]));
},
    $groups);

echo 'part 2 : ', array_sum($badges), PHP_EOL;
