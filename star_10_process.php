<?php

$input = trim(file_get_contents('./day_5_input.txt'));

$doContinue = false;
$iterations = 0;
$length = strlen($input);

function process($input) {
    $lowerLetters = str_split('qwertyuiopasdfghjklzxcvbnm');
    $upperLetters = str_split('QWERTYUIOPASDFGHJKLZXCVBNM');

    do {
        $doContinue = false;
        for ($i = 0, $c = count($lowerLetters); $i < $c; $i++) {
            $input = str_replace($lowerLetters[$i] . $upperLetters[$i], '', $input);
            $input = str_replace($upperLetters[$i] . $lowerLetters[$i], '', $input);
        }
        if ($length !== strlen($input)) {
            $doContinue = true;
            $length = strlen($input);
        }
    } while ($doContinue);

    return $length;
}

foreach (str_split('abcdefghijklmnopqrstuvwxyz') as $letter) {
    echo 'Removing ' . $letter . ' yields a polymer of ' . process(str_ireplace($letter, '', $input)) . PHP_EOL;
}
