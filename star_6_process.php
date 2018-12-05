<?php

ini_set('memory_limit', '1024M');
$input = file_get_contents('./day_3_input.txt');
$values = array_filter(explode(PHP_EOL, $input));
// #514 @ 801,448: 10x16


$cloth = array_fill(0, 1000, array_fill(0, 1000, []));
$claims = [];

foreach ($values as $value) {
    $process = preg_match('/#(\d{1,4}) @ (\d{1,4}),(\d{1,4}): (\d{1,4})x(\d{1,4})/', $value, $matches);

    $claimID = intval($matches[1]);
    $top = intval($matches[2]);
    $left = intval($matches[3]);
    $bottom = $top + intval($matches[4]);
    $right = $left + intval($matches[5]);

    $claims[$claimID] = true;

    for ($x = $top; $x < $bottom; $x++) {
        for ($y = $left; $y < $right; $y++) {
            $cloth[$x][$y][] = $claimID;
        }
    }
}

for ($x = 0; $x < count($cloth); $x++) {
    for ($y = 0; $y < count($cloth[$x]); $y++) {
        if (count($cloth[$x][$y]) >= 2) {
            foreach($cloth[$x][$y] as $invalid) {
                $claims[$invalid] = false;
            }
        }
    }
}

var_dump(array_filter($claims));
