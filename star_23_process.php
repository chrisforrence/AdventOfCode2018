<?php

$generations = 40;

$input = explode(PHP_EOL, trim(file_get_contents('./day_11_input.txt')));

$initial = array_shift($input);
array_shift($input);

// initial state: #..#.#..##......###...###
// ...## => #
preg_match('/initial state: ([\.\#]+)/', $initial, $matches);
$garden = new Garden($matches[1]);

$regex = '/([\.\#]+) => ([\.\#])/';
while ($row = array_pop($input)) {
    preg_match($regex, $row, $matches);
    $garden->addRule($matches[1], $matches[2]);
}

$garden->start();
for ($i = 0; $i < $generations; $i++) {
    $garden->tick();
    echo 'Generation ' . ($i + 1) . ': ' . $garden->print() . ', score is ' . $garden->calculateScore() . PHP_EOL;
}

echo $garden->calculateScore();



class Garden {
    private $initialState;
    public $state;
    private $rules;

    public function __construct($initialState) {
        $this->initialState = $initialState;
        $this->rules = [];
        $this->state = [];
    }

    public function addRule($pattern, $result) {
        $this->rules[] = ['pattern' => $pattern, 'result' => $result === '#'];

        return $this;
    }

    public function start() {
        $state = str_split($this->initialState);
        var_dump(array_column($this->rules, 'pattern'));
        foreach ($state as $key => $value) {
            $this->state[$key] = new Plant($value === '#', $this->rules);
        }
        $this->state[-1] = new Plant(false, $this->rules);
        $this->state[-2] = new Plant(false, $this->rules);
        $this->state[] = new Plant(false, $this->rules);
        $this->state[] = new Plant(false, $this->rules);
        ksort($this->state);
        return $this;
    }

    public function print() {
        $response = '';
        foreach ($this->state as $key => $plant) {
            $response .= $plant->showStatus();
        }
        return $response;
    }

    public function calculateScore() {
        $result = 0;
        foreach ($this->state as $key => $plant) {
            if ($plant->isAlive()) {
                $result += $key;
            }
        }
        return $result;
    }

    public function tick() {
        ksort($this->state);
        $nextGeneration = [];
        foreach ($this->state as $key => $plant) {
            $nextGeneration[$key] = new Plant($plant->check(
                isset($this->state[$key - 2]) ? $this->state[$key - 2] : Plant::dead(),
                isset($this->state[$key - 1]) ? $this->state[$key - 1] : Plant::dead(),
                isset($this->state[$key + 1]) ? $this->state[$key + 1] : Plant::dead(),
                isset($this->state[$key + 2]) ? $this->state[$key + 2] : Plant::dead()
            ), $this->rules);
        }
        $keepAdding = true;
        while ($keepAdding) {
            $keepAdding = false;
            $plant = end($nextGeneration);
            $kPlant = key($nextGeneration);
            $plant2 = prev($nextGeneration);
            if ($plant->isAlive() || $plant2->isAlive()) {
                $nextGeneration[] = new Plant(false, $this->rules);
                $keepAdding = true;
            }
        }
        $keepAdding = true;
        while ($keepAdding) {
            $keepAdding = false;
            $plant = reset($nextGeneration);
            $kPlant = key($nextGeneration);
            $plant2 = next($nextGeneration);
            if ($plant->isAlive() || $plant2->isAlive()) {
                $nextGeneration[$kPlant - 1] = new Plant(false, $this->rules);
                $keepAdding = true;
                ksort($nextGeneration);
            }
        }
        $this->state = $nextGeneration;
        ksort($this->state);
        return $this;
    }
}

class Plant {
    private $status;
    public function __construct($status, $rules) {
        $this->status = $status;
        $this->rules = $rules;

    }

    public static function dead() {
        return new Plant(false, []);
    }

    public function isAlive() {
        return $this->status;
    }

    public function showStatus() {
        return $this->status ? '#' : '.';
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function check($left2, $left1, $right1, $right2) {
        $pattern = $left2->showStatus()
            . $left1->showStatus()
            . $this->showStatus()
            . $right1->showStatus()
            . $right2->showStatus();
        $key = array_search($pattern, array_column($this->rules, 'pattern'));
        if ($key === false) {
            return false;
        }
        return $this->rules[$key]['result'];
    }
}
