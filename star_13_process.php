<?php

ini_set('memory_limit', '1024M');
$rows = explode(PHP_EOL, trim(file_get_contents('./day_7_input.txt')));

// Step A must be finished before step D can begin.

$regex = '/Step ([A-Z]) must be finished before step ([A-Z]) can begin./';

$steps = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
$froms = [];
$tos = [];

$prerequisites = [];

function getRoots($rows) {
    global $regex;
    foreach ($rows as $row) {
        preg_match($regex, $row, $matches);
        $from = $matches[1];
        $to = $matches[2];
        $steps[$from] = true;
        $steps[$to] = true;

        if (!isset($prerequisites[$from])) {
            $prerequisites[$from] = [];
        }
        if (!isset($prerequisites[$to])) {
            $prerequisites[$to] = [];
        }
        $prerequisites[$to][] = $from;

        $froms[$from] = true;
        $tos[$to] = true;
    }

    $steps = array_keys($steps);
    $froms = array_keys($froms);
    $tos = array_keys($tos);

    return $prerequisites;
}

$prerequisites = getRoots($rows);

$eligibles = array_keys(array_filter($prerequisites, function ($v) {
    return count($v) === 0;
}));

sort($eligibles);

$output = [];

while (!empty($eligibles)) {
    $current = null;
    for ($i = 0, $c = count($eligibles); $i < $c; $i++) {
        if (!in_array($eligibles[$i], $output) && empty(array_diff($prerequisites[$eligibles[$i]], $output))) {
            $current = $eligibles[$i];
            unset($eligibles[$i]);
            break;
        }
    }
    if ($current === null) {
        break;
    }

    $output[] = $current;

    $newNodes = getNodesFromPrerequisite($current, $rows);
    $eligibles = array_merge($eligibles, $newNodes);
    sort($eligibles);
    echo "Processed $current...added " . json_encode($newNodes) . ', new length ' . count($eligibles) . PHP_EOL;
}

echo implode('', $output);

function getNodesFromPrerequisite($prerequisite, $rows) {
    global $regex;
    $output = [];
    foreach ($rows as $row) {
        preg_match($regex, $row, $matches);
        $from = $matches[1];
        $to = $matches[2];

        if ($from !== $prerequisite) {
            continue;
        }
        $output[] = $to;

    }
    return $output;
}

function processIteration($rows, $availableSteps) {
    foreach ($rows as $row) {
        preg_match($regex, $row, $matches);
        $from = $matches[1];
        $to = $matches[2];

    }
}

$steps = array_keys($steps);
$froms = array_keys($froms);
$tos = array_keys($tos);
// J, N, S, V
