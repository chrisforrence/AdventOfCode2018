<?php

$input = file_get_contents('./day_2_input.txt');
$values = array_filter(explode(PHP_EOL, $input));

foreach ($values as $x) {
    foreach ($values as $y) {
        if (levenshtein($x, $y, 1, 1, 1) === 1) {
            var_dump($x);
            var_dump($y);
            die();
        }
    }
}
