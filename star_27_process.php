<?php

$input = 864801;
// $input = 2018;
//
$recipebook = new Ds\Deque;
$recipebook->push(3);
$recipebook->push(7);
$elf1 = 0;
$elf2 = 1;

do {
    list($recipebook, $elf1, $elf2) = iteration($recipebook, $elf1, $elf2);
} while ($recipebook->count() < $input);

// echo 'Recipes ' . count($recipes) . ': ' . json_encode(['recipes' => $recipes, 'elves' => $elves]) . PHP_EOL;

// Generate score
for ($j = $recipebook->count(); $j < ($input + 10); $j++) {
    list($recipebook, $elf1, $elf2) = iteration($recipebook, $elf1, $elf2);
}

// echo 'Recipes ' . count($recipes) . ': ' . json_encode(['recipes' => $recipes, 'elves' => $elves]) . PHP_EOL;

$count = $recipebook->count();
var_dump(
    $recipebook->get($input + 0)
    . $recipebook->get($input + 1)
    . $recipebook->get($input + 2)
    . $recipebook->get($input + 3)
    . $recipebook->get($input + 4)
    . $recipebook->get($input + 5)
    . $recipebook->get($input + 6)
    . $recipebook->get($input + 7)
    . $recipebook->get($input + 8)
    . $recipebook->get($input + 9)
);

function iteration ($recipebook, $elf1, $elf2) {
    $elf1Value = $recipebook->get($elf1);
    $elf2Value = $recipebook->get($elf2);
    $score = $elf1Value + $elf2Value;
//     echo "Elf1: $elf1, Elf2: $elf2" . PHP_EOL;
//     echo "$elf1Value + $elf2Value = $score" . PHP_EOL;

    // Add new recipes
    if ($score >= 10) {
        $recipebook->push(intval(floor($score / 10)));
    }
    $recipebook->push($score % 10);
//     var_dump(json_encode($recipebook->toArray()));

    // Elves choose new recipe
    $elf1Rotations = 1 + $elf1Value;
    $elf2Rotations = 1 + $elf2Value;

//     echo "Elf1 rotates $elf1Rotations, Elf2 rotates $elf2Rotations" . PHP_EOL . PHP_EOL;

    $elf1 = ($elf1 + $elf1Rotations) % $recipebook->count();
    $elf2 = ($elf2 + $elf2Rotations) % $recipebook->count();

    return [$recipebook, $elf1, $elf2];
}

