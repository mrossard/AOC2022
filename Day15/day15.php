<?php

use Ds\Map;
use Ds\Set;

class Sensor
{
    public $distanceBeacon;

    public function __construct(public array $position, public array $nearestBeacon)
    {
        $this->distanceBeacon = $this->distance($this->nearestBeacon[0], $this->nearestBeacon[1]);
    }

    public function couvre($x, $y)
    {
        $distance = $this->distance($x, $y);
        return gmp_cmp($distance, $this->distanceBeacon) <= 0;
    }

    public function distance($x, $y)
    {
        return gmp_add(gmp_abs($this->position[0] - $x), gmp_abs($this->position[1] - $y));
    }

    public function xRangeParY($y): ?array
    {
        if (!$this->couvre($this->position[0], $y)) {
            return null;
        }
        $distanceX = $this->distanceX($y);
        return [
            'min' => gmp_sub($this->position[0], $distanceX),
            'max' => gmp_add($this->position[0], $distanceX),
        ];
    }

    public function distanceX($y)
    {
        return gmp_sub($this->distanceBeacon, $this->distance($this->position[0], $y));
    }

    public function justOutOfReach(): array
    {
        $outOfReach = [];
        for ($offsetX = 1; $offsetX < $this->distanceBeacon; $offsetX++) {
            $offsetY = $this->distanceBeacon - $offsetX + 1;
            $outOfReach[] = [$this->position[0] - $offsetX, $this->position[1] - $offsetY];
            $outOfReach[] = [$this->position[0] + $offsetX, $this->position[1] + $offsetY];
            $outOfReach[] = [$this->position[0] - $offsetX, $this->position[1] + $offsetY];
            $outOfReach[] = [$this->position[0] + $offsetX, $this->position[1] - $offsetY];
        }
        return $outOfReach;
    }
}

/**
 * @param $filename
 * @return Sensor[]
 */
function readInput($filename): array
{
    $input = file($filename, FILE_IGNORE_NEW_LINES);
    $sensors = array_map(function ($line) {
        [$sensorStr, $beaconStr] = explode(':', $line);
        $sensorY = gmp_init(explode('=', explode(',', $sensorStr)[1])[1]);
        $sensorX = gmp_init(explode('=', explode(',', $sensorStr)[0])[1]);
        $beaconY = gmp_init(explode('=', explode(',', $beaconStr)[1])[1]);
        $beaconX = gmp_init(explode('=', explode(',', $beaconStr)[0])[1]);
        return new Sensor([$sensorX, $sensorY], [$beaconX, $beaconY]);
    }, $input);

    return $sensors;
}

/**
 * @param Sensor[] $sensors
 * @param          $y
 * @return array
 * @throws Exception
 */
function xRanges(array $sensors, $y, $minVal = PHP_INT_MIN, $maxVal = PHP_INT_MAX): Set
{
    $xRanges = new Set();
    foreach ($sensors as $sensor) {
        $range = $sensor->xRangeParY($y);
        if (null !== $range && $range['max'] > $minVal && $range['min'] < $maxVal) {
            $xRanges[] = $range;
        }
    }
    $xRanges->sort(function ($a, $b) {
        return $a['min'] <=> $b['min'];
    });

    return $xRanges->reduce(function ($carry, $item) {
        if (null === $carry) {
            $carry = new Set();
            $carry->add($item);
        }
        foreach ($carry as $range) {
            if ($range['min'] >= $item['min'] && $range['min'] <= $item['max'] ||
                $item['min'] >= $range['min'] && $item['min'] <= $range['max']) {
                $carry->remove($range);
                $carry->add(['min' => min($item['min'], $range['min']), 'max' => max($item['max'], $range['max'])]);
                return $carry;
            } else {
                $carry->add($item);
            }
        }
        return $carry;
    });
}

/**
 * @param array    $sensors
 * @param          $y
 * @param          $minVal
 * @param          $maxVal
 * @return GMP|resource|null
 * @throws Exception
 */
function findUncovered(array $sensors, $y, $minVal, $maxVal)
{
    $xRanges = xRanges($sensors, $y, $minVal, $maxVal);

    for ($first = 0; $first < $xRanges->count() - 1; $first++) {
        $next = gmp_add($xRanges[$first]['max'], 1);
        if ($next <= $xRanges[$first + 1]['min']) {
            return $next;
        }
    }
    return null;
}

$sensors = readinput($argv[1]);

//part 1
$ranges = xRanges($sensors, gmp_init($argv[2]));
$part1 = $ranges->reduce(function ($carry, $range) {
    return gmp_add($carry, gmp_sub($range['max'], $range['min']));
}, 0);
echo 'Part 1 : ', $part1, PHP_EOL;

//Part 2
[$min, $max] = [gmp_init(0), gmp_init($argv[3])];
$outOfReach = new Map();
foreach ($sensors as $id => $sensor) {
    foreach ($sensor->justOutOfReach() as $candidate) {
        if ($candidate[0] >= $min && $candidate[0] <= $max && $candidate[1] >= $min && $candidate[1] <= $max) {
            foreach ($sensors as $otherId => $other) {
                if ($otherId === $id) {
                    continue;
                }
                if ($other->couvre($candidate[0], $candidate[1])) {
                    continue 2;
                }
            }
            echo 'found : ', $candidate[0], ' ', $candidate[1], ' frequency : ',
            gmp_add($candidate[1], gmp_mul($candidate[0], 4000000)), PHP_EOL;
            die;
        }
    }

}


var_dump($outOfReach);