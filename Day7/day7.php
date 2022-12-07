<?php

class FsElement
{
    /**
     * @var FsElement[]
     */
    public array $children;

    public function __construct(public string $name, public int $size = 0, public ?FsElement $parent = null)
    {
    }

    public function fullname(): string
    {
        if (null === $this->parent) {
            return $this->name;
        }
        return $this->parent->fullname() . '/' . $this->name;
    }

    public function totalSize(): int
    {
        return $this->size + array_sum(array_map(function ($element) {
                return $element->totalSize();
            }, $this->children ?? []));
    }

    public function isDir(): bool
    {
        return (0 === $this->size);
    }
}

function readInput($file): array
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
        $fullName = $current?->fullname() . '/' . $dirName;
        if (array_key_exists($fullName, $elements)) {
            $current = $elements[$fullName];
        } else if ($dirName === '..') {
            $current = $current->parent;
        } else {
            //on est sur la racine
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
    if ($item->isDir() && $size <= 100000) {
        return ($carry ?? 0) + $size;
    }
    return $carry;
}), PHP_EOL;

$totalSpace = 70000000;
$spaceNeeded = 30000000 - ($totalSpace - $elements['/']->totalSize());
echo 'part 2 : ', array_reduce($elements, function ($carry, $item) use ($spaceNeeded, $totalSpace) {
    $size = $item->getSize();
    if ($item->isDir() && $size >= $spaceNeeded && $size < ($carry ?? $totalSpace)) {
        return $size;
    }
    return $carry;
}
), PHP_EOL;