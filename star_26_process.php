<?php

ini_set('memory_limit', '-1');

$input = file_get_contents('./day_13_input.txt');

$board = new Board($input);
$i = 0;
do {
    $i++;
    echo 'Iteration #' . $i . ': ' . (sizeof($board->carts)) . ' carts remaining...' . PHP_EOL;
    $tick = $board->tick();
    if ($tick !== true) {
        echo '...CRASH AT ' . json_encode($tick) . PHP_EOL;
        foreach ($tick as $cartsIdx) {
            foreach ($cartsIdx as $idx) {
                $board->removeCart($idx);
            }
        }
        $board->normalizeCarts();
    }
} while (sizeof($board->carts) > 1);

echo($board->carts[0]) . PHP_EOL;

$board->tick();

echo($board->carts[0]) . PHP_EOL;


class Board {
    public $map = null;
    public $size;
    public $carts = [];

    public function removeCart($idx) {
        unset($this->carts[$idx]);
    }

    public function normalizeCarts() {
        $this->carts = array_values($this->carts);
    }

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
        foreach ($this->carts as $idx => $cart) {
            if (!isset($coordinates[$cart->getCoordinatesAsString()])) {
                $coordinates[$cart->getCoordinatesAsString()] = [];
            }
            $coordinates[$cart->getCoordinatesAsString()][] = $idx;
        }
        $collisions = array_filter($coordinates, function ($value) {
            return count($value) > 1;
        });
        return empty($collisions) ? false : $collisions;
    }

    private function move(Cart $cart, $doLogIdx = null) {
        list($direction, $coordinates) = [$cart->getDirection(), $cart->getCoordinates()];
        list($rowIdx, $colIdx) = $coordinates;


        switch ($direction) {
            case 'N': $rowIdx--; break;
            case 'E': $colIdx++; break;
            case 'W': $colIdx--; break;
            case 'S': $rowIdx++; break;
            default: throw new Exception('Tried to move ' . $direction);
        }
        $cart->setCoordinates($rowIdx, $colIdx);


        if (!isset($this->map[$rowIdx][$colIdx])) {
                echo '...Cart '
                . ' is off the grid!!' . PHP_EOL;
                exit;
        }

        switch($this->map[$rowIdx][$colIdx]) {
            case ' ':
                echo '...Cart '
                . ' is off the road!!' . PHP_EOL;
                exit;
            case '+':
                $cart->changeDirection();
                break;
            case '/':
            case '\\':
                $cart->followRoad($this->map[$rowIdx][$colIdx]);
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
        return "{$this->colIdx},{$this->rowIdx}";
    }

    public function setCoordinates($rowIdx, $colIdx) {
        $this->rowIdx = $rowIdx;
        $this->colIdx = $colIdx;
    }

    public function getDirection() {
        return $this->direction;
    }

    public function __toString() {
        return '[' . $this->getCoordinatesAsString() . '], heading ' . $this->getDirection();
    }

}
