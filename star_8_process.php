<?php

require_once('./vendor/autoload.php');
use Carbon\Carbon;

$input = file_get_contents('./day_4_input.txt');
$rows = array_filter(explode(PHP_EOL, $input));

$entries = [];

foreach ($rows as $row) {
    preg_match('/\[1518\-(\d{1,2})\-(\d{1,2}) (\d{2}:\d{2})\] (.*)$/', $row, $matches);

    $entries[strtotime(
        '2018-'
            . $matches[1]
            . '-' .$matches[2]
            . ' ' . $matches[3] . ':00'
    )] = $matches;
}

// [1518-03-15 00:01] Guard #709 begins shift
// [1518-03-15 00:12] falls asleep
// [1518-03-15 00:53] wakes up

ksort($entries);

$guards = [];
$currentGuard = null;
$currentSleeptime = null;
foreach ($entries as $timestamp => $entry) {
    if (strpos($entry[4], 'begins shift') !== false) {
        // We have a new guard on shift
        preg_match('/Guard #(\d+) begins/', $entry[4], $matches);
        $currentGuard = $matches[1];
        if (!isset($guards[$currentGuard])) {
            $guards[$currentGuard] = ['id' => $currentGuard, 'totalMinutes' => 0, 'times' => []];
        }
    } else if ($entry[4] === 'falls asleep') {
        $currentSleeptime = $timestamp;
    } else if ($entry[4] === 'wakes up') {
        $guards[$currentGuard]['totalMinutes'] += ($timestamp - $currentSleeptime) / 60;
        $guards[$currentGuard]['times'][] = [$currentSleeptime, $timestamp];
        $currentSleeptime = null;
    } else {
        echo 'wat.';
        die();
    }
}

uasort($guards, function ($a, $b) {
    return ($a['totalMinutes'] <=> $b['totalMinutes']);
});

$sleepyMinutes = [];

$dtStart = Carbon::now();
$dtEnd = Carbon::now();

foreach ($guards as $guard) {
    if (empty($guard['times'])) {
        continue;
    }
    $minutes = [];
    foreach ($guard['times'] as $pair) {
        $dtStart->timestamp($pair[0]);
        $dtEnd->timestamp($pair[1]);
        while ($dtStart->lessThan($dtEnd)) {
            if (!isset($minutes[$dtStart->minute])) {
                $minutes[$dtStart->minute] = 0;
            }
            $minutes[$dtStart->minute]++;
            $dtStart->addMinute();
        }
    }
    asort($minutes);
    $value = end( $minutes );
            $key = key( $minutes );
    $sleepyMinutes[$guard['id']] = [$key => $value];
}

var_dump($sleepyMinutes);
