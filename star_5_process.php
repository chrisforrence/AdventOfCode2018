<?php

$input = file_get_contents('./day_3_input.txt');
$values = array_filter(explode(PHP_EOL, $input));
// #514 @ 801,448: 10x16


$cloth = array_fill(0, 1000, array_fill(0, 1000, 0));

foreach ($values as $value) {
    $process = preg_match('/(\d{1,4}),(\d{1,4}): (\d{1,4})x(\d{1,4})/', $value, $matches);
    $top = intval($matches[1]);
    $left = intval($matches[2]);
    $bottom = $top + intval($matches[3]);
    $right = $left + intval($matches[4]);

    for ($x = $top; $x < $bottom; $x++) {
        for ($y = $left; $y < $right; $y++) {
            $cloth[$x][$y]++;
        }
    }
}

$result = 0;
for ($x = 0; $x < count($cloth); $x++) {
    for ($y = 0; $y < count($cloth[$x]); $y++) {
        if ($cloth[$x][$y] >= 2) {
            $result++;
        }
    }
}

echo $result . PHP_EOL;
