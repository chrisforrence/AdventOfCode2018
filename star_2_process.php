<?php

/**
 * +1
 * -2
 * +3
 */

$input = file_get_contents('./day_1_input.txt');
$values = array_filter(explode(PHP_EOL, $input));

function getDuplicateFrequency ($input, $registry, $startingFrequency) {
    $frequency = $startingFrequency;

    foreach ($input as $value) {
        $frequency += intval($value);
        if (in_array($frequency, $registry)) {
            return [true, $registry, $frequency];
        }
        $registry[] = $frequency;
    }

    return [false, $registry, $frequency];
}

$registry = [];
$frequency = 0;
$iterations = 0;
$found = false;

while (!$found) {
    $iterations++;
    list($found, $registry, $frequency) = getDuplicateFrequency($values, $registry, $frequency);
    if ($found) {
        echo $frequency . PHP_EOL . ' after ' . $iterations . ' iterations' . PHP_EOL;
    }
    echo 'Current Frequency: ' . $frequency . ', ' . count($registry) . ' register size after ' . $iterations . ' iterations' . PHP_EOL;
}
