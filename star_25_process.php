<?php

ini_set('memory_limit', '-1');

$input = file_get_contents('./day_13_input.txt');

$board = new Board($input);

for ($i = 0; $i < 300; $i++) {
    echo 'Iteration #' . $i . ': ' . PHP_EOL;
    $tick = $board->tick();
    if ($tick !== true) {
        echo ' CRASH AT ' . json_encode($tick) . PHP_EOL;
        break;
    }
}


class Board {
    public $map = null;
    public $size;
    public $carts = [];

    public function __construct($map) {
        $boardArray = [];
        $map = explode(PHP_EOL, $map);
        foreach ($map as $y => $row) {
            if (!isset($boardArray[$y])) {
                $boardArray[$y] = [];
            }
            $xes = str_split($row);
            foreach ($xes as $x => $cell) {
                $boardArray[$y][$x] = $cell;
            }
        }

        $this->map = $boardArray;

        $this->carts = $this->determineCarts();
    }

    private function determineCarts() {
        $response = [];
        foreach ($this->map as $rowIdx => $row) {
            foreach ($row as $colIdx => $cell) {
                if ($cell === '<') {
                    $response[] = new Cart($rowIdx, $colIdx, 'W');
                } elseif ($cell === 'v') {
                    $response[] = new Cart($rowIdx, $colIdx, 'S');
                } elseif ($cell === '^') {
                    $response[] = new Cart($rowIdx, $colIdx, 'N');
                } elseif ($cell === '>') {
                    $response[] = new Cart($rowIdx, $colIdx, 'E');
                }
            }
        }

        return $response;
    }

    public function tick() {
        foreach ($this->carts as $idx => &$cart) {
            $cart = $this->move($cart, $idx);
            if ($collision = $this->getCollisions()) {
                return $collision;
            }
        }
        return true;
    }

    private function getCollisions() {
        $coordinates = [];
        foreach ($this->carts as $cart) {
            if (!isset($coordinates[$cart->getCoordinatesAsString()])) {
                $coordinates[$cart->getCoordinatesAsString()] = 0;
            }
            $coordinates[$cart->getCoordinatesAsString()]++;
        }
        $collisions = array_filter($coordinates, function ($value) {
            return $value > 1;
        });
        return empty($collisions) ? false : $collisions;
    }

    private function move(Cart $cart, $doLogIdx = null) {
        list($direction, $coordinates) = [$cart->getDirection(), $cart->getCoordinates()];
        list($rowIdx, $colIdx) = $coordinates;

        if ($doLogIdx !== null) {
            echo '...Cart ' . $doLogIdx . ' moved from ' . json_encode([$cart->getDirection(), $cart->getCoordinates()]);
        }

        switch ($direction) {
            case 'N': $rowIdx--; break;
            case 'E': $colIdx++; break;
            case 'W': $colIdx--; break;
            case 'S': $rowIdx++; break;
            default: throw new Exception('Tried to move ' . $direction);
        }
        $cart->setCoordinates($rowIdx, $colIdx);

        if ($doLogIdx !== null) {
            echo ' to ' . json_encode([$cart->getDirection(), $cart->getCoordinates(), $this->map[$rowIdx][$colIdx] ?? 'NOWHERE']) . PHP_EOL;
        }

        if (!isset($this->map[$rowIdx][$colIdx])) {
            if ($doLogIdx !== null) {
                echo '...Cart ' . $doLogIdx
                . ' is off the grid!!' . PHP_EOL;
                exit;
            }
        }

        switch($this->map[$rowIdx][$colIdx]) {
            case ' ':
                if ($doLogIdx !== null) {
                    echo '...Cart ' . $doLogIdx
                    . ' is off the road!!' . PHP_EOL;
                    exit;
                }
            case '+':
                $cart->changeDirection();
                if ($doLogIdx !== null) {
                    echo '...Cart ' . $doLogIdx
                    . ' turned ' . $cart->turnDirections[($cart->turnDirectionIdx + 2) % 3]
                    . ' to ' . json_encode([$cart->getDirection()]) . PHP_EOL;
                }
                break;
            case '/':
            case '\\':
                $cart->followRoad($this->map[$rowIdx][$colIdx]);
                if ($doLogIdx !== null) {
                    echo '...Cart ' . $doLogIdx
                    . ' followed the road'
                    . ' to ' . json_encode([$cart->getDirection()]) . PHP_EOL;
                }
                break;
            default: break;
        }

        return $cart;
    }

}

class Cart {
    private $turns = [
        'left' => ['N' => 'W', 'W' => 'S', 'S' => 'E', 'E' => 'N'],
        'straight' => ['N' => 'N', 'W' => 'W', 'S' => 'S', 'E' => 'E'],
        'right' => ['N' => 'E', 'E' => 'S', 'S' => 'W', 'W' => 'N'],
    ];

    public $turnDirections = ['left', 'straight', 'right'];
    public $turnDirectionIdx = 0;

    public function changeDirection() {
        $this->direction = $this->turns[$this->turnDirections[$this->turnDirectionIdx]][$this->direction];
        $this->turnDirectionIdx = ($this->turnDirectionIdx + 1) % 3;
        return $this;
    }

    public function followRoad($curveOfRoad) {
        switch ($curveOfRoad) {
            case '/':
                switch ($this->direction) {
                    case 'N': $this->direction = 'E'; break;
                    case 'E': $this->direction = 'N'; break;
                    case 'S': $this->direction = 'W'; break;
                    case 'W': $this->direction = 'S'; break;
                }
                break;
            case '\\':
                switch ($this->direction) {
                    case 'N': $this->direction = 'W'; break;
                    case 'W': $this->direction = 'N'; break;
                    case 'S': $this->direction = 'E'; break;
                    case 'E': $this->direction = 'S'; break;
                }
                break;
        }
    }

    private $rowIdx;
    private $colIdx;
    private $direction;

    public function __construct($rowIdx, $colIdx, $direction) {
        $this->rowIdx = $rowIdx;
        $this->colIdx = $colIdx;
        $this->direction = $direction;
    }

    public function getCoordinates() {
        return [$this->rowIdx, $this->colIdx];
    }
    public function getCoordinatesAsString() {
        return "{$this->colIdx}, {$this->rowIdx}";
    }

    public function setCoordinates($rowIdx, $colIdx) {
        $this->rowIdx = $rowIdx;
        $this->colIdx = $colIdx;
    }

    public function getDirection() {
        return $this->direction;
    }

}
