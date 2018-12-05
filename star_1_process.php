<?php

/**
 * +1
 * -2
 * +3
 */

$input = file_get_contents('./day_1_input.txt');
$values = explode(PHP_EOL, $input);

$start = 0;

foreach ($values as $value) {
    $start += intval($value);
}

echo $start . PHP_EOL;
