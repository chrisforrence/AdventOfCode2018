<?php

// position=< 9,  1> velocity=< 0,  2>
$rows = explode(PHP_EOL, trim(file_get_contents('./day_10_input.txt')));

$regex = '/position=<\W*?(\-?\d+),\W*?(\-?\d+)> velocity=<\W*?(\-?\d+),\W*?(\-?\d+)>/';

$inputs = [];
$gridDimensions = ['minX' => PHP_INT_MAX, 'maxX' => PHP_INT_MIN, 'minY' => PHP_INT_MAX, 'maxY' => PHP_INT_MIN];
foreach ($rows as $row) {
    preg_match($regex, $row, $matches);
    $inputs[] = [
        'position' => [
            $x = intval($matches[1]), $y = intval($matches[2])
        ],
        'step' => 0,
        'velocity' => [
            intval($matches[3]), intval($matches[4])
        ],
    ];
    $gridDimensions['minX'] = min($gridDimensions['minX'], $x);
    $gridDimensions['maxX'] = max($gridDimensions['maxX'], $x);
    $gridDimensions['minY'] = min($gridDimensions['minY'], $y);
    $gridDimensions['maxY'] = max($gridDimensions['maxY'], $y);
}


$gridBox = getArea($gridDimensions);
$second = 0;
while ($area < $gridBox) {
    $gridBox = getArea($gridDimensions);
    $second++;
    $gridDimensions = ['minX' => PHP_INT_MAX, 'maxX' => PHP_INT_MIN, 'minY' => PHP_INT_MAX, 'maxY' => PHP_INT_MIN];
    foreach ($inputs as $key => $input) {
        $input['step']++;
        $input['position'][0] += $input['velocity'][0];
        $input['position'][1] += $input['velocity'][1];
        $inputs[$key] = $input;
        $gridDimensions['minX'] = min($gridDimensions['minX'], $input['position'][0]);
        $gridDimensions['maxX'] = max($gridDimensions['maxX'], $input['position'][0]);
        $gridDimensions['minY'] = min($gridDimensions['minY'], $input['position'][1]);
        $gridDimensions['maxY'] = max($gridDimensions['maxY'], $input['position'][1]);
    }
    echo json_encode($inputs[0]) . PHP_EOL;
    $area = getArea($gridDimensions);
    echo 'Second #' . $second . ': ' . $area . ' vs ' . $gridBox . PHP_EOL;
}

echo $second; die;
$offsetX = abs($gridDimensions['minX']);
$offsetY = abs($gridDimensions['minY']);

function getArea($box) {
    return ($box['maxX'] - $box['minX']);
}
/*

$x = 200;
$y = 200;

$gd = imagecreatetruecolor($x, $y);

$corners[0] = array('x' => 100, 'y' =>  10);
$corners[1] = array('x' =>   0, 'y' => 190);
$corners[2] = array('x' => 200, 'y' => 190);

$red = imagecolorallocate($gd, 255, 0, 0);

for ($i = 0; $i < 100000; $i++) {
  imagesetpixel($gd, round($x),round($y), $red);
  $a = rand(0, 2);
  $x = ($x + $corners[$a]['x']) / 2;
  $y = ($y + $corners[$a]['y']) / 2;
}

header('Content-Type: image/png');
imagepng($gd);

?>
 */
$turn = 10932;
$gd = imagecreatetruecolor(200, 200);
$white = imagecolorallocate($gd, 255, 255, 255);
foreach ($inputs as $input) {
    imagesetpixel(
        $gd,
        $input['position'][0] + ($input['velocity'][0] * $turn),
        $input['position'][1] + ($input['velocity'][1] * $turn),
        $white
    );
}
imagepng($gd, './images/here.png');
