<?php

$input = trim(file_get_contents('./day_5_input.txt'));

$doContinue = false;
$iterations = 0;
$length = strlen($input);

$lowerLetters = str_split('qwertyuiopasdfghjklzxcvbnm');
$upperLetters = str_split('QWERTYUIOPASDFGHJKLZXCVBNM');

do {
    $doContinue = false;
    echo 'Iteration #' . ($iterations++) . ': ' . $length . PHP_EOL;
    for ($i = 0, $c = count($lowerLetters); $i < $c; $i++) {
        $input = str_replace($lowerLetters[$i] . $upperLetters[$i], '', $input);
        $input = str_replace($upperLetters[$i] . $lowerLetters[$i], '', $input);
    }
    if ($length !== strlen($input)) {
        $doContinue = true;
        $length = strlen($input);
    }
} while ($doContinue);

echo $length;
/*
do {
    echo "Iteration " . ($iterations++) . ': ' . ' (' . strlen($input) . ')' . PHP_EOL;
    $doContinue = false;
    $previousChar = null;
    $output = '';
    for ($i = 0, $c = strlen($input); $i < $c; $i++) {
        $char = substr($input, $i, 1);
        if ($previousChar !== null && strtolower($previousChar) === strtolower($char) && $previousChar !== $char) {
            echo "...Characters {$i} and " . ($i - 1) . ' are ' . $char . $previousChar . PHP_EOL;
            $doContinue = true;
            $previousChar = null;
        } else {
            if ($previousChar !== null) {
                $output .= $previousChar;
            }
            $previousChar = $char;
            if ($i + 1 === $c) {
                $output .= $char;
            }
        }
    }

    $input = $output;
} while ($doContinue);
echo $output . PHP_EOL;
echo strlen($output);

*/
