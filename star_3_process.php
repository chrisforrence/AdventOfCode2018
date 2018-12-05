<?php


$input = file_get_contents('./day_2_input.txt');
$values = array_filter(explode(PHP_EOL, $input));

function checkHasExactly($target, $values) {
    $counter = 0;
    foreach ($values as $row) {
        $letters = str_split(strtolower($row));
        $counts = [];
        foreach ($letters as $letter) {
            if (!isset($counts[$letter])) {
                $counts[$letter] = 0;
            }
            $counts[$letter]++;
        }
        if (count(array_filter($counts, function ($letterCount) use ($target) {
            return $letterCount === $target;
        })) > 0) {
            $counter++;
        }
    }
    return $counter;
}

$x = checkHasExactly(2, $values);
$y = checkHasExactly(3, $values);

echo $x . ' * ' . $y . ' = ' . ($x * $y) . PHP_EOL;
