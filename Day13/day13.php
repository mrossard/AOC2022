<?php

use Ds\Vector;

function readPart(string $line): int|Vector|null
{
    if(is_numeric($line)){
        return (int)$line;
    }
    if(empty($line)){
        return null;
    }

    $splitLine = function($line){
        $parts = [];
        $currentPart = 0;
        $depth = 0;
        $chars = str_split($line);
        $position = $chars[0] == '['? 1 : 0;
        $end = $chars[0] == '['? count($chars) - 1 :count($chars);
        $parts[$currentPart] = '';
        while($position < $end){
            if($chars[$position] == ',' && $depth === 0){
                $currentPart ++;
                $position++;
                $parts[$currentPart] = '';
                continue;
            }
            if($chars[$position] == '['){
                $depth++;
            }
            if($chars[$position] == ']'){
                $depth--;
            }
            $parts[$currentPart] = ($parts[$currentPart]??'') . $chars[$position];
            $position++;
        }
        return $parts;
    };

    $components = $splitLine($line);
    return new Vector(array_map(readPart(...), $components));
}

function checkOrdered(Vector $pair) : bool|null
{
    if(null === $pair[0]){
        if(null === $pair[1]){
            return null;
        }
        return true;
    }
    if(null === $pair[1]){
        return false;
    }
    if(is_int($pair[0]) && is_int($pair[1]))
    {
        if($pair[0]>$pair[1]){
            return false;
        }
        if($pair[1]>$pair[0]){
            return true;
        }
        return null;
    }

    if(is_int($pair[0])){
        $newPair = new Vector();
        $newPair->push(new Vector([$pair[0]]));
        $newPair->push($pair[1]);
        return checkOrdered($newPair);
    }
    if(is_int($pair[1])){
        $newPair = new Vector();
        $newPair->push($pair[0]);
        $newPair->push(new Vector([$pair[1]]));
        return checkOrdered($newPair);
    }

    foreach ($pair[0] as $i=>$leftItem){
        if(!array_key_exists($i, $pair[1]->toArray())){
            return false;
        }
        $ordered = checkOrdered(new Vector([$leftItem, $pair[1][$i]]));
        if(null === $ordered){
            continue;
        }
        return $ordered;
    }
    if(count($pair[1]) === count($pair[0])){
        return null;
    }
    return true;
}

$input = file_get_contents($argv[1]);
$pairs = new Vector(array_map(function($pairString){
    return new Vector(array_map(readPart(...), explode("\n", $pairString)));
}, explode("\n\n", $input)));

$ordered = array_map(checkOrdered(...), $pairs->toArray());
$ordered = array_reduce($ordered,
    function($carry, $ordered){
        return [
            'index'=> ($carry['index'] ?? 1) + 1,
            'sum'=> ($carry['sum'] ?? 0) + ($ordered ? ($carry['index'] ?? 1) : 0)
        ];
    });

echo 'part 1 : ', $ordered['sum'], PHP_EOL;


$packets = new Vector();
foreach($pairs as $pair){
    $packets->push($pair[0]);
    $packets->push($pair[1]);
}

$divider6 = new Vector();
$divider6->push(new Vector([6]));
$divider2 = new Vector();
$divider2->push(new Vector([2]));

$packets->push($divider2, $divider6);

function compare(Vector $v1, Vector $v2){
    return !checkOrdered(new Vector([$v1, $v2]));
}

$packets->sort(compare(...));

echo 'part 2 : ', ($packets->find($divider2) +1) * ($packets->find($divider6) +1), PHP_EOL;