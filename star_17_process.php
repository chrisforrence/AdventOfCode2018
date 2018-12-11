<?php

ini_set('memory_limit', '-1');
$data = trim(file_get_contents('./day_9_input.txt'));

// 411 players; last marble is worth 71058 points
$regex = '/(\d+) players; last marble is worth (\d+) points/';

preg_match($regex, $data, $matches);

$count_players = intval($matches[1]);
$count_turns = intval($matches[2]);
$players = array_fill(0, $count_players, 0);
$board = new Board;

for ($i = 0; $i < $count_turns; $i++) {
    $player = $i % $count_players;
    // echo 'Player ' . $player . '...' . ($i + 1) . PHP_EOL;
    if (($i + 1) % 23 === 0) {
        $board->rotate(7);
        $players[$player] += $i + 1;
        $players[$player] += $board->popCurrent();
        $board->rotate(-1);
    } else {
        $board->rotate(-1);
        $board->place($i + 1);
    }
    // $board->print();
    echo $i . ': ' . number_format($i / $count_turns, 2) . PHP_EOL;
}

var_dump(max($players));

class Board {
    private $marbles = [0];

    public function rotate($number) {
        $fnRemove = $number > 0 ? 'array_shift' : 'array_pop';
        $fnAdd = $number > 0 ? 'array_push' : 'array_unshift';
        for ($i = 0, $c = abs($number); $i < $c; $i++) {
            $tmp = $fnRemove($this->marbles);
            $fnAdd($this->marbles, $tmp);
        }
    }

    public function place($value) {
        array_unshift($this->marbles, $value);
    }

    public function popCurrent() {
        return array_shift($this->marbles);
    }

    public function print() {
        foreach ($this->marbles as $idx => $marble) {
            if ($idx === 0) {
                echo '*' . $marble . '*';
            } else {
                echo ' ' . $marble . ' ';
            }
        }
        echo PHP_EOL;
    }
}
