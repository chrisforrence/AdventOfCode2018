<?php

$input = 1133;

$grid = array_fill(0, 300, array_fill(0, 300, 0));

for ($i = 0; $i < count($grid); $i++) {
    for ($j = 0; $j < count($grid[$i]); $j++) {
        $x = $i + 1;
        $y = $j + 1;
        $grid[$i][$j] = calculatePowerValue($x, $y, $input);

        /*
Find the fuel cell's rack ID, which is its X coordinate plus 10.
Begin with a power level of the rack ID times the Y coordinate.
Increase the power level by the value of the grid serial number (your puzzle input).
Set the power level to itself multiplied by the rack ID.
Keep only the hundreds digit of the power level (so 12345 becomes 3; numbers with no hundreds digit become 0).
Subtract 5 from the power level.
         */
    }
}

$topLeftX = null;
$topLeftY = null;
$cellsValue = -99;
for ($i = 0; $i < count($grid); $i++) {
    if (!isset($grid[$i + 2])) {
        continue;
    }
    for ($j = 0; $j < count($grid[$i]); $j++) {
        if (!isset($grid[$i][$j + 2])) {
            continue;
        }
        $tmpSum = $grid[$i][$j]
            + $grid[$i][$j + 1]
            + $grid[$i][$j + 2]
            + $grid[$i + 1][$j]
            + $grid[$i + 1][$j + 1]
            + $grid[$i + 1][$j + 2]
            + $grid[$i + 2][$j]
            + $grid[$i + 2][$j + 1]
            + $grid[$i + 2][$j + 2];

        if ($tmpSum > $cellsValue) {
            $topLeftX = $i;
            $topLeftY = $j;
            $cellsValue = $tmpSum;

        }
    }
}

var_dump(json_encode([$topLeftX + 1, $topLeftY + 1, $cellsValue]));


function calculatePowerValue($x, $y, $serial) {
    $rackID = $x + 10;
    $value = $rackID * $y;
    $value += $serial;
    $value *= $rackID;
    $value = $value < 100 ? 0 : floor($value / 100) % 10;
    $value -= 5;
    return $value;
}
