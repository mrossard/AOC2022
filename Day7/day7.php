<?php

class FsElement
{
    /**
     * @var FsElement[]
     */
    public array $children;

    function __construct(public string $name, public int $size = 0, public ?FsElement $parent = null)
    {
    }

    public function fullname(): string
    {
        if (null === $this->parent) {
            return $this->name;
        }
        return $this->parent->fullname() . '/' . $this->name;
    }

    function getSize(): int
    {
        return $this->size + array_sum(array_map(function ($element) {
                return $element->getSize();
            }, $this->children ?? []));
    }
}

function readInput($file)
{
    $commandStrings = explode("\n$ ", substr(file_get_contents($file), 2));
    $commands = [];
    foreach ($commandStrings as $commandString) {
        if (!empty($commandString)) {
            $commands[] = explode("\n", $commandString);
        }
    }
    return $commands;
}

/**
 * @var FsElement $current
 */
$current = null;
$elements = [];
foreach (readInput($argv[1]) as $command) {
    //cd
    if (count($command) === 1) {
        $dirName = substr($command[0], 3);
        $fullname = $current?->fullname() . '/' . $dirName;
        if (array_key_exists($fullname, $elements)) {
            $current = $elements[$fullname];
        } else if ($dirName === '..') {
            $current = $current->parent;
        } else {
            //on est sur la racine
            if ($current !== null) {
                throw new RuntimeException('wtf?' . $current?->fullname() . '/' . $dirName);
            }
            $fsElement = new FsElement($dirName, 0, null);
            $elements[$fsElement->fullname()] = $fsElement;
            $current = $fsElement;
        }
    } else {
        //ls, on ajoute le contenu
        for ($i = 1; $i < count($command); $i++) {
            [$size, $name] = explode(' ', $command[$i]);
            $fsElement1 = new FsElement($name, is_numeric($size) ? $size : 0, $current);
            $current->children[] = $fsElement1;
            $elements[$fsElement1->fullname()] = $fsElement1;
        }
    }
}

echo 'part 1 : ', array_reduce($elements, function ($carry, $item) {
    $size = $item->getSize();
    if ($size <= 100000 && 0 === $item->size) {
        return ($carry ?? 0) + $size;
    }
    return $carry;
}), PHP_EOL;

$spaceNeeded = 30000000 - (70000000 - $elements['/']->getSize());
echo 'part 2 : ', array_reduce($elements, function ($carry, $item) use ($spaceNeeded) {
    $size = $item->getSize();
    if (0 === $item->size && $size >= $spaceNeeded && $size < ($carry ?? 70000000)) {
        return $size;
    }
    return $carry;
}
), PHP_EOL;