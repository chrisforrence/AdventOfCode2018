<?php

ini_set('memory_limit', '-1');
$data = explode(' ', trim(file_get_contents('./day_8_input.txt')));

array_walk($data, function(&$value) {
    $value = intval($value);
});
// 2 3 0 3 10 11 12 1 1 0 1 99 2 1 1 2
// 2 3 0 3 10 11 12 1 1 1 1 0 4 38 11 22 11 99 2 1 1 2
// A--------------------------------------------------
//     B----------- C---------------------------
//                      D-----------------------
//                          E--------------
list($root, $data) = populateNode($data);

var_dump(getSumNodeMetadata($root));
var_dump($root->getValue());

function getSumNodeMetadata(Node $node) {
    $sum = $node->sumMetadata();
    foreach ($node->getChildren() as $child) {
        $sum += getSumNodeMetadata($child);
    }
    return $sum;
}

function populateNode($data) {
    $node = new Node;
    // echo 'Node: ' . json_encode($data) . PHP_EOL;
    $count_children = array_shift($data);
    $count_metadata = array_shift($data);
    $node->setHeader(['children' => $count_children, 'metadata' => $count_metadata]);
    // echo $count_children . '/' . $count_metadata . ': ' . json_encode($data) . PHP_EOL;

    for ($i = 0; $i < $count_children; $i++) {
        list($child, $data) = populateNode($data);
        $node->addChild($child);
    }
    for ($i = 0; $i < $count_metadata; $i++) {
        $node->addMetadata(array_shift($data));
    }

    return [$node, $data];
}



class Node {
    private $header   = ['children' => 0, 'metadata' => 0];
    private $children = [];
    private $metadata = [];

    public function setHeader($header) {
        $this->header['children'] = $header['children'];
        $this->header['metadata'] = $header['metadata'];
    }

    public function addChild(Node $node) {
        $this->children[] = $node;
    }

    public function getChildren() {
        return $this->children;
    }

    public function addMetadata($value) {
        $this->metadata[] = $value;
    }

    public function addMetadatas(array $values) {
        $this->metadata = array_merge($this->metadata, $values);
    }

    public function getMetadata() {
        return $this->metadata;
    }

    public function sumMetadata() {
        return array_reduce($this->metadata, function($carry, $data) {
            $carry += $data;
            return $carry;
        }, 0);
    }

    public function getValue() {
        $indexes = $this->getMetadata();
        $children = $this->getChildren();
        if (count($children) === 0) {
            return $this->sumMetadata();
        }
        $sum = 0;
        foreach ($indexes as $index) {
            if (!isset($children[$index - 1])) {
                continue;
            }
            $sum += $children[$index - 1]->getValue();
        }
        return $sum;
    }
}
