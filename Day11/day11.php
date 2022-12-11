<?php

use Ds\Map;
use Ds\Queue;

function readInput(string $input): Map
{
    $monkeys = new Map();
    foreach (explode("\n\n", $input) as $id => $monkeyDefinition) {
        $lines = explode("\n", $monkeyDefinition);
        $monkey = new Map();
        $monkey->put("items", new Queue(array_map(intval(...), explode(', ', explode(': ', $lines[1])[1]))));
        [, , , , , , $operation["operator"], $operation["number"]] = explode(' ', $lines[2]);

        $divisibleBy = array_reverse(explode(' ', $lines[3]))[0];
        $target = [
            true => (int)array_reverse(explode(' ', $lines[4]))[0],
            false => (int)array_reverse(explode(' ', $lines[5]))[0],
        ];
        $monkey->put("operation", $operation);
        $monkey->put('test', ['division' => $divisibleBy, 'targets' => $target]);
        $monkeys->put('division', $monkeys->get('division', 1) * (int)$divisibleBy);
        $monkey->put('inspected', 0);
        $monkeys->put($id, $monkey);
    }
    return $monkeys;
}

function runOperation(string $operator, $worryLevel, $number)
{
    return match ($operator) {
        '*' => gmp_mul($worryLevel, $number),
        '+' => gmp_add($worryLevel, $number)
    };
}

function monkeyBusinessRound(Map $monkeys, bool $worryLess, int $divideBy): Map
{
    foreach ($monkeys as $monkey) {
        foreach ($monkey->get("items") as $item) {
            $number = is_numeric($monkey->get('operation')['number']) ? $monkey->get('operation')['number'] : $item;
            $item = runOperation($monkey->get('operation')['operator'], $item, $number);
            $monkey->put('inspected', $monkey->get('inspected') + 1);
            if ($worryLess) {
                $item = gmp_div_q($item, 3);
            } else {
                $item = gmp_div_r($item, $divideBy);

            }
            $targetId = gmp_mod($item, $monkey->get('test')['division']) == 0;
            $target = $monkeys->get($monkey->get('test')['targets'][$targetId]);
            $target->get('items')->push($item);
        }
    }
    return $monkeys;
}

$input = file_get_contents($argv[1]);
$monkeys = readInput($input);
$divideBy = $monkeys->get('division');
$monkeys->remove('division');

for ($i = 0; $i < $argv[2]; $i++) {
    $monkeys = monkeyBusinessRound($monkeys, $argv[3] == 1, $divideBy);
}

$inspected = [];
foreach ($monkeys as $id => $monkey) {
    $inspected[$id] = $monkey->get('inspected');
    echo $id, ' : ', $monkey->get('inspected'), PHP_EOL;
}
sort($inspected);
echo 'part 1 : ', gmp_mul(array_pop($inspected), array_pop($inspected)), PHP_EOL;