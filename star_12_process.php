<?php

ini_set('memory_limit', '1024M');
$rows = explode(PHP_EOL, trim(file_get_contents('./day_6_input.txt')));
$ids = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

$max_x = 0;
$max_y = 0;

$max_size_estimate = 400;
$distance_limit = 10000;

function getDistance($x1, $y1, $x2, $y2) {
    return abs($x1 - $x2) + abs($y1 - $y2);
}

$grid = array_fill(0, $max_size_estimate, array_fill(0, $max_size_estimate, []));
$coordiates = [];

foreach ($rows as $idx => $row) {
    $coordinate = explode(', ', $row);
    $coordinates[] = $coordinate;
}

$eligibleCells = 0;
for ($x = 0, $c = count($grid); $x < $c; $x++) {
    for ($y = 0, $d = count($grid[$x]); $y < $d; $y++) {
        $total = 0;
        foreach ($coordinates as $coordinate) {
            $total += getDistance($coordinate[0], $coordinate[1], $x, $y);
        }
        if ($total < $distance_limit) {
            $eligibleCells++;
        }
    }
}

var_dump($eligibleCells);
