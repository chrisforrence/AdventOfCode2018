<?php

$input = trim(file_get_contents('./day_15_input.txt'));

$map = new Map($input);
$map->resetTurnOrder();
foreach($map->units as $unit) {
    echo $unit . ': ' . ($unit->isAdjacentToEnemy($map->layout) ? 'YES' : 'no') . PHP_EOL;
}
for ($i = 0; $i < 100; $i++) {
$map->processTurn();
}

class Map {
    public $layout = [];
    public $units = [];
    public $turns = 0;
    public $unitCounter = 0;


    public function __construct($map) {
        $this->layout = [];
        foreach (explode(PHP_EOL, $map) as $y => $line) {
            $this->layout[$y] = str_split($line);
            foreach ($this->layout[$y] as $x => $cell) {
                switch($cell) {
                    case '#': break; // Wall
                    case '.': break; // Ground
                    case 'E':
                        $unit = new Elf($x, $y, $this->unitCounter++);
                        $this->units[$unit->getID()] = $unit;
                        $this->layout[$y][$x] = '.';
                        break;
                    case 'G':
                        $unit = new Goblin($x, $y, $this->unitCounter++);
                        $this->units[$unit->getID()] = $unit;
                        $this->layout[$y][$x] = '.';
                        break;
                }
            }
        }
    }

    public function processTurn() {
        $that = $this;
        echo 'Starting Turn #' . ($this->turns + 1) . '...' . PHP_EOL;

        // Each unit takes its turn
        $units = $this->units;
        $layout = $this->layout;
        foreach ($units as $unit) {
            if (!isset($this->units[$unit->getID()])) {
                // Unit had died. :(
                continue;
            }
            // Attack if possible
            if ($unit->isAdjacentToEnemy($this->layout)) {
                // ATTACK!!!!
                $target = $this->getUnitTargetedEnemy($unit);

                if ($target === null) {
                    continue;
                }
                echo $unit->getRace() . ' ' . $unit->getID() . ' attacks ' . $target->getRace() . ' ' . $target->getID() . '!';
                $target->alterHP(-1 * $unit->getAttackPower());
                echo ' ' . $target->getHP() . 'hp remaining.' . PHP_EOL;
                $this->units[$target->getID()] = $target;

                // Check if the target got KO'd
                if (!$target->isAlive()) {
                    echo '...Target KO\'d!' . PHP_EOL;
                    unset($this->units[$target->getID()]);
                }
                continue;
            }
            // Otherwise, move towards its closest target
            $targets = $this->getUnitEnemies($unit);
            $targets_in_range = [];
            foreach($targets as $target) {
                $coordinates = $target->getCoordinates();
                $possibles = [
                    'N' => $unit->getCoordinates('N'),
                    'S' => $unit->getCoordinates('S'),
                    'E' => $unit->getCoordinates('E'),
                    'W' => $unit->getCoordinates('W'),
                ];
                foreach ($possibles as $direction => $possible) {
                    if ($unit->isAdjacentToUnitInDirection($that->units, $direction)) {
                        $possibles[$direction] = false;
                    }
                    else {
                        $coordinates = $unit->getCoordinates($direction);
                        if ($that->isWall($coordinates[0], $coordinates[1])) {
                            $possibles[$direction] = false;
                        }
                    }
                }
                $possibles = array_filter($possibles);
                foreach ($possibles as $coord) {
                    $targets_in_range[] = $coord;
                }
            }
            if (empty($targets_in_range)) {
                continue;
            }

            // Check if targets are actually reachable
            $target_reachable = [];
            foreach($targets_in_range as $coordinate_set) {
                $target_reachable[] = ['target' => $coordinate_set, 'distance' => 0];
            }

        }
        echo 'Completed Turn #' . (++$this->turns) . '!' . PHP_EOL;
    }

    public function isWall($coordinates_x, $coordinates_y) {
        return $this->layout[$coordinate_y][$coordinate_x] === '#';
    }

    public function getUnitEnemies($unit) {
        return array_filter($this->units, function ($possibleTarget) use ($unit) {
            return $unit->getEnemy() === $possibleTarget->getRace();
        });
    }

    public function getUnitTargetedEnemy($unit) {
        $enemies = $this->getUnitEnemies($unit);
        $target = null;
        $targets = $unit->getAdjacentUnits($enemies);

        if (count($targets) > 0) {
            $target = $targets[0];
        }

        return $target;
    }

    public function getUnit($id) {
        return $this->units[$id] ?? null;
    }

    public function resetTurnOrder() {
        uasort($this->units, function ($unitA, $unitB) {
            if ($cmp = $unitA->coordinate_y <=> $unitB->coordinate_y) {
                return $cmp;
            }
            return $unitA->coordinate_x <=> $unitB->coordinate_x;
        });
    }
}

abstract class Unit {
    public $current_hp;
    public $max_hp;
    public $atk;
    public $coordinate_x;
    public $coordinate_y;
    public $id;

    public function __construct($coordinate_x, $coordinate_y, $id) {
        $this->id = $id;
        $this->current_hp = 200;
        $this->max_hp = 200;
        $this->atk = 3;
        $this->coordinate_x = $coordinate_x;
        $this->coordinate_y = $coordinate_y;
    }

    public function getID() {
        return $this->id;
    }

    public abstract function isAdjacentToEnemy($layout);

    public function getCoordinates($direction = null) {
        if ($direction === null) {
            return [$this->coordinate_x, $this->coordinate_y];
        }

        switch ($direction) {
            case 'N': $coordinates = [$this->coordinate_x, $this->coordinate_y - 1]; break;
            case 'W': $coordinates = [$this->coordinate_x - 1, $this->coordinate_y]; break;
            case 'E': $coordinates = [$this->coordinate_x + 1, $this->coordinate_y]; break;
            case 'S': $coordinates = [$this->coordinate_x, $this->coordinate_y + 1]; break;
            default: return false;
        }
    }

    public function isAlive() {
        return $this->current_hp > 0;
    }

    public function getAttackPower() {
        return $this->atk;
    }

    public function alterHP($value) {
        $this->current_hp += $value;
        return $this;
    }

    public function getHP() {
        return $this->current_hp;
    }

    public function getAdjacentUnits($units) {
        $source = $this;

        $targets = array_filter($units, function ($unit) use ($source) {
            return $source->getAdjacenyScore($unit, true) > 0;
        });
        usort($targets, function ($unitA, $unitB) use ($source) {
            if ($unitA->getHP() !== $unitB->getHP()) {
                return $unitA->getHP() <=> $unitB->getHP();
            }
            return -1 * ($source->getAdjacenyScore($unitA) <=> $source->getAdjacenyScore($unitB));
        });
        return $targets;
    }

    public function hasAdjacentSpace($units) {
        $source = $this;

        $targets = array_filter($units, function ($unit) use ($source) {
            return $source->getAdjacenyScore($unit, true) > 0;
        });
        usort($targets, function ($unitA, $unitB) use ($source) {
            if ($unitA->getHP() !== $unitB->getHP()) {
                return $unitA->getHP() <=> $unitB->getHP();
            }
            return -1 * ($source->getAdjacenyScore($unitA) <=> $source->getAdjacenyScore($unitB));
        });
        return $targets;
    }

    public function getAdjacenyScore($unit) {
        $source = $this->getCoordinates();
        $target = $unit->getCoordinates();

        // [3, 4] -> [3, 3]
        // [3, 4] -> [2, 4]
        // [3, 4] -> [4, 4]
        // [3, 4] -> [3, 5]
        $return = 0;
        if ($source[1] === $target[1] + 1 && $source[0] === $target[0]) { $return = 4;}
        if ($source[0] === $target[0] - 1 && $source[1] === $target[1]) { $return = 3;}
        if ($source[0] === $target[0] + 1 && $source[1] === $target[1]) { $return = 2;}
        if ($source[1] === $target[1] - 1 && $source[0] === $target[0]) { $return = 1;}
        return $return;
    }

    public function isAdjacentToUnitInDirection($units, $direction) {
        switch ($direction) {
            case 'N': $coordinates = [$this->coordinate_x, $this->coordinate_y - 1]; break;
            case 'W': $coordinates = [$this->coordinate_x - 1, $this->coordinate_y]; break;
            case 'E': $coordinates = [$this->coordinate_x + 1, $this->coordinate_y]; break;
            case 'S': $coordinates = [$this->coordinate_x, $this->coordinate_y + 1]; break;
            default: return false;
        }
        foreach ($units as $unit) {
            if ($unit->getCoordinates() == $coordinates) {
                return true;
            }
        }
        return false;
    }
    public function isAdjacentToEnemy($units) {
        foreach ($units as $unit) {
            if ($this->getEnemy() === $unit->getRace()
                && ($unit->getCoordinates() === [$this->coordinate_x, $this->coordinate_y - 1]
                || $unit->getCoordinates() === [$this->coordinate_x, $this->coordinate_y + 1]
                || $unit->getCoordinates() === [$this->coordinate_x - 1, $this->coordinate_y]
                || $unit->getCoordinates() === [$this->coordinate_x + 1, $this->coordinate_y])) {
                return true;
            }
        }
        return false;
    }
}

class Elf extends Unit {
    public function getRace() {
        return 'Elf';
    }

    public function getEnemy() {
        return 'Goblin';
    }

    public function __toString() {
        return 'Elf ' . $this->getID() . ': ' . $this->current_hp . '/' . $this->max_hp . ' [' . $this->coordinate_x . ', ' . $this->coordinate_y . ']';
    }

}


class Goblin extends Unit {
    public function getRace() {
        return 'Goblin';
    }

    public function getEnemy() {
        return 'Elf';
    }

    public function __toString() {
        return 'Goblin ' . $this->getID() . ': ' . $this->current_hp . '/' . $this->max_hp . ' [' . $this->coordinate_x . ', ' . $this->coordinate_y . ']';
    }
}

