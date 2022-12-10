<?php

$input = file($argv[1], FILE_IGNORE_NEW_LINES);
$instructions = array_map(function ($line) {
    $data = explode(' ', $line);
    return [$data[0], (int)($data[1] ?? 0)];
}, $input);

$register[0] = 1;
$offset = 1;
foreach ($instructions as $i => $instruction) {
    $register[$i + $offset] = $register[$i + $offset] ?? $register[$i + $offset - 1];
    if ($instruction[0] === 'addx') {
        $register[$i + $offset + 1] = $register[$i + $offset];
        $register[$i + $offset + 2] = $register[$i + $offset + 1] + $instruction[1];
        $offset++;
    }
}

$total = 0;
foreach ([20, 60, 100, 140, 180, 220] as $cycle) {
    $total += $register[$cycle] * $cycle;
}
echo 'Part 1 :', $total, PHP_EOL;

array_shift($register);
$pixels = [];
foreach ($register as $cycle => $value) {
    $spritePos = $value % 240;
    $pixels[$cycle] = match (true) {
        $spritePos >= ($cycle % 40) - 1 && $spritePos <= ($cycle % 40) + 1 => '#',
        default => '.'
    };
}
echo 'Part 2 :', PHP_EOL;
foreach ($pixels as $cycle => $pixel) {
    echo $pixel;
    if (($cycle + 1) % 40 === 0) {
        echo PHP_EOL;
    }
}