<?php

ini_set('memory_limit', '1024M');
$rows = explode(PHP_EOL, trim(file_get_contents('./day_6_input.txt')));
$ids = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

$max_x = 0;
$max_y = 0;

$max_size_estimate = 400;

$grid = array_fill(0, $max_size_estimate, array_fill(0, $max_size_estimate, []));

foreach ($rows as $idx => $row) {
    $coordinates = explode(', ', $row);
    $cx = $coordinates[0];
    $cy = $coordinates[1];

    for ($x = 0, $c = count($grid); $x < $c; $x++) {
        for ($y = 0, $d = count($grid[$x]); $y < $d; $y++) {
            $grid[$x][$y][$ids[$idx]] = getDistance($cx, $cy, $x, $y);
        }
    }
}

function getDistance($x1, $y1, $x2, $y2) {
    return abs($x1 - $x2) + abs($y1 - $y2);
}

for ($x = 0, $c = count($grid); $x < $c; $x++) {
    for ($y = 0, $d = count($grid[$x]); $y < $d; $y++) {
        $tmp = $grid[$x][$y];
        asort($tmp);
        $first = reset($tmp);
        $firstK = key($tmp);
        $second = next($tmp);
        if ($first === $second) {
            $grid[$x][$y] = '.';
        } else {
            $grid[$x][$y] = $firstK;
        }
    }
}

// Filter out edges
$eliminated = [];
for ($edge = 0, $c = count($grid); $edge < $c; $edge++) {
    $minPerp = 0;
    $maxPerp = count($grid[$edge]);

    if(in_array($grid[$edge][$minPerp], $ids)) {
        $eliminated[] = $grid[$edge][$minPerp];
    }
    if(in_array($grid[$edge][$maxPerp - 1], $ids)) {
        $eliminated[] = $grid[$edge][$maxPerp - 1];
    }
    if(in_array($grid[$minPerp][$edge], $ids)) {
        $eliminated[] = $grid[$minPerp][$edge];
    }
    if(in_array($grid[$maxPerp - 1][$edge], $ids)) {
        $eliminated[] = $grid[$maxPerp - 1][$edge];
    }
}

$ids = array_values(array_diff($ids, $eliminated));
$eligibleIds = [];
foreach ($ids as $id) {
    $eligibleIds[$id] = 0;
}

for ($x = 0, $c = count($grid); $x < $c; $x++) {
    for ($y = 0, $d = count($grid[$x]); $y < $d; $y++) {
        echo $grid[$x][$y];
    }
    echo PHP_EOL;
}

for ($x = 0, $c = count($grid); $x < $c; $x++) {
    for ($y = 0, $d = count($grid[$x]); $y < $d; $y++) {
        if (in_array($grid[$x][$y], $ids)) {
            $eligibleIds[$grid[$x][$y]]++;
        }
    }
}
asort($eligibleIds);
var_dump($eligibleIds);
