<?php

ini_set('memory_limit', '-1');
$input = 864801;
// $input = 2018;
//
$recipebook = new Ds\Deque;
$recipebook->push(3);
$recipebook->push(7);
$elf1 = 0;
$elf2 = 1;
$found = 0;

do {
    list($recipebook, $elf1, $elf2) = iteration($recipebook, $elf1, $elf2);
    $countRecipes = $recipebook->count();
    if ($countRecipes < 7) continue;
    if (
        // 01245
        $recipebook->get($countRecipes - 6) === 8
        && $recipebook->get($countRecipes - 5) === 6
        && $recipebook->get($countRecipes - 4) === 4
        && $recipebook->get($countRecipes - 3) === 8
        && $recipebook->get($countRecipes - 2) === 0
        && $recipebook->get($countRecipes - 1) === 1
    ) {
        $found = $countRecipes - 6;
    }
    if (
        $recipebook->get($countRecipes - 7) === 8
        && $recipebook->get($countRecipes - 6) === 6
        && $recipebook->get($countRecipes - 5) === 4
        && $recipebook->get($countRecipes - 4) === 8
        && $recipebook->get($countRecipes - 3) === 0
        && $recipebook->get($countRecipes - 2) === 1
    ) {
        $found = $countRecipes - 7;
    }
} while (!$found);
var_dump(json_encode($recipebook->toArray()));
var_dump($found);


// echo 'Recipes ' . count($recipes) . ': ' . json_encode(['recipes' => $recipes, 'elves' => $elves]) . PHP_EOL;

function iteration ($recipebook, $elf1, $elf2) {
    $elf1Value = $recipebook->get($elf1);
    $elf2Value = $recipebook->get($elf2);
    $score = $elf1Value + $elf2Value;

    // Add new recipes
    if ($score >= 10) {
        $recipebook->push(intval(floor($score / 10)));
    }
    $recipebook->push($score % 10);

    // Elves choose new recipe
    $elf1Rotations = 1 + $elf1Value;
    $elf2Rotations = 1 + $elf2Value;

    $elf1 = ($elf1 + $elf1Rotations) % $recipebook->count();
    $elf2 = ($elf2 + $elf2Rotations) % $recipebook->count();

    return [$recipebook, $elf1, $elf2];
}

