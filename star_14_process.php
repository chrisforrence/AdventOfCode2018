<?php

ini_set('memory_limit', '1024M');
$rows = explode(PHP_EOL, trim(file_get_contents('./day_7_input.txt')));

// Step A must be finished before step D can begin.

$regex = '/Step ([A-Z]) must be finished before step ([A-Z]) can begin./';

$steps = str_split(' ABCDEFGHIJKLMNOPQRSTUVWXYZ');
$steps_costs = array_filter(array_flip($steps));

$froms = [];
$tos = [];

$number_workers = 5;
$minimum_time = 60;
array_walk($steps_costs, function(&$value, $key) use ($minimum_time) {
    $value = $value + $minimum_time;
});

$prerequisites = [];

$mapping = getStepMapping($rows);

$output = [];
$processing = [];

// Recruit workers!
$workers = [];
for ($i = 0; $i < $number_workers; $i++) {
    $workers[] = new Worker;
}

$iteration = 0;
$workersAreBusy = true;

while ($workersAreBusy) {
    $workersAreBusy = false;
    $current = null;

    // Start by processing available
    for ($i = 0, $c = count($workers); $i < $c; $i++) {
        $worker = $workers[$i];
        if (!$worker->isBusy()) {
            continue;
        }

        $worker->tick();

        if ($worker->isFinished()) {
            $completedStep = $worker->getStep();
            // echo $completedStep . ' is done!';
            $worker->finish();

            $output[] = $completedStep;
        }
        $workers[$i] = $worker;
    }

    // Figure out what steps are available to tackle
    $availableSteps = getAvailableStepsForProcessing($output, array_reduce($workers, function($carry, $worker) {
        if ($worker->getStep()) {
            $carry[] = $worker->getStep();
        }
        return $carry;
    }, []), $mapping);

    // Next, load up available workers with new steps
    for ($i = 0, $c = count($workers); $i < $c; $i++) {
        $worker = $workers[$i];
        // var_dump('Worker ' . $i . ' is ' . ($worker->isBusy() ? 'busy' : 'idle'));
        // Do not disturb busy workers!
        if ($worker->isBusy()) {
            continue;
        }

        // Nothing to work on
        if (empty($availableSteps)) {
            continue;
        }

        $step = array_shift($availableSteps);
        $cost = $steps_costs[$step];

        // var_dump($i . ' starting ' . $step . '/' . $cost);
        $worker->start($step, $cost);
        $workers[$i] = $worker;
    }

    foreach ($workers as $worker) {
        if ($worker->isBusy()) {
            $workersAreBusy = true;
            break;
        }
    }

    // Output
    echo 'Second ' . ($iteration++) . ': ';

    foreach ($workers as $worker) {
        echo $worker->getStep() ?? '.';
    }
    echo PHP_EOL;
}

function getAvailableStepsForProcessing($alreadyCompleted, $inProgress, $mapping) {
    // $mapping = ['A' => ['B', 'C']];
    // $alreadyCompleted = ['D', 'E', 'F'];
    $response = [];
    foreach ($mapping as $step => $prerequisites) {
        if (count(array_diff($prerequisites, $alreadyCompleted)) === 0
            && !in_array($step, $alreadyCompleted)
            && !in_array($step, $inProgress)) {
            $response[] = $step;
        }
    }
    return $response;
}

function getStepMapping($rows) {
    global $regex;
    foreach ($rows as $row) {
        preg_match($regex, $row, $matches);
        $from = $matches[1];
        $to = $matches[2];

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

    $froms = array_keys($froms);
    $tos = array_keys($tos);

    return $prerequisites;
}

class Worker {
    private $step;
    private $cost;

    public function __construct() {
        $this->start(null, null);
    }

    public function start($step, $cost) {
        $this->step = $step;
        $this->cost = intval($cost);
    }

    public function getStep() {
        return $this->step;
    }

    public function getCost() {
        return $this->cost;
    }

    public function tick() {
        $this->cost -= 1;
    }

    public function isBusy() {
        return $this->step !== null;
    }

    public function isFinished() {
        return $this->cost === 0;
    }

    public function finish() {
        $this->start(null, null);
    }
}
