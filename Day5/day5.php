<?php

$input = file_get_contents($argv[1]);
[$stacksInput, $movesInput] = explode("\n\n", $input);

function move(array $stacks, int $number, int $from, int $to): array
{
    for ($i = 0; $i < $number; $i++) {
        $stacks[$to][] = array_pop($stacks[$from]);
    }
    return $stacks;
}

function move2(array $stacks, int $number, int $from, int $to): array
{
    $moved = [];
    for ($i = 0; $i < $number; $i++) {
        $moved[] = array_pop($stacks[$from]);
    }
    $stacks[$to] = [...$stacks[$to], ...array_reverse($moved)];
    return $stacks;
}

$stacks = [];
foreach (explode("\n", $stacksInput) as $line) {
    $chars = str_split($line, 4);
    foreach ($chars as $pos => $char) {
        if (strlen(trim($char)) > 1) {
            $stacks[$pos + 1][] = trim($char)[1];
        }
    }
}

foreach ($stacks as $i => $stack) {
    $stacks[$i] = array_reverse($stack);
}

$stacks1 = $stacks;
$stacks2 = $stacks;
foreach (explode("\n", $movesInput) as $moveStr) {
    [$number, $from, $to] = explode(' ', str_replace(['move ', ' from', ' to'], '', $moveStr));
    $stacks1 = move($stacks1, $number, $from, $to);
    $stacks2 = move2($stacks2, $number, $from, $to);
}

for ($i = 1; $i <= count($stacks1); $i++) {
    echo array_pop($stacks1[$i]);
}
echo PHP_EOL;

for ($i = 1; $i <= count($stacks2); $i++) {
    echo array_pop($stacks2[$i]);
}
echo PHP_EOL;