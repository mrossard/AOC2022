<?php

$input = str_split(file($argv[1], FILE_IGNORE_NEW_LINES)[0]);
$nb = (int)$argv[2];

function run($nb, $input)
{
    $reduced = $input;
    return array_reduce($input, function ($carry, $item) use (&$reduced, $nb) {
        if (null !== $carry) {
            return $carry;
        }
        $current = array_slice($reduced, 0, $nb);
        if (count(array_unique($current)) === $nb) {
            return $current;
        }
        array_shift($reduced);
        return null;
    });
}

$reduced = run($nb, $input);
echo 'Result : ', strpos(implode('', $input), implode('', $reduced)) + $nb, PHP_EOL;