<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "18";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$data2 = explode(" ", $line);
			foreach($data2 as $d) {
				$data3 = explode("=", $d);
				$data4[$data3[0]] = $data3[1];
			}
			$data[] = $data4;
		}
	}
	return $data;
}

function print_map($map) {
	ksort($map);
	ksort($map[0]);
	foreach($map[0] as $j => $cell) {
		foreach($map as $i => $col) {
			echo $map[$i][$j];
		}
		echo "\n";
	}
	for($i=0;$i<sizeof($map);$i++) {
		echo "=";
	}
	echo "\n";
}

function fill_and_count($data) {
	// these will keep track of the upper corners
	$x_all = [];
	$y_all = [];
	foreach($data as $d) {
		$x = $d["x"];
		$y = $d["y"];
		if(isset($d["width"])) {
			$width = $d["width"];
		} else {
			$width = 1;
		}
		// corner 1
		$x_all[] = $x;
		$y_all[] = $y;
		// corner 2
		if(1 < $width) {
			$x_all[] = $x + $width - 1;
			$y_all[] = $y;
		}
	}

	// the key will keep rising on either side
	// getting higher and higher
	// until the bit with the maximum height
	$y_max = max($y_all);
	$sum = 0;
	// handle left part, where y rises
	$id = 0;
	$x_left = $x_all[$id];
	$y_left = $y_all[$id];
	while($y_left < $y_max) {
		$id++;
		$x_next = $x_all[$id];
		$y_next = $y_all[$id];
		if($y_next < $y_left) {
			// ignore this corner
			continue;
		}
		// add the rectangle between
		// x_left, 1
		// x_next - 1, y_left
		$sum += ($x_next - $x_left) * $y_left;
		$x_left = $x_next;
		$y_left = $y_next;
	}
	// then handle right bit
	$id = sizeof($x_all) - 1;
	$x_right = $x_all[$id];
	$y_right = $y_all[$id];
	while($y_right < $y_max) {
		$id--;
		$x_next = $x_all[$id];
		$y_next = $y_all[$id];
		if($y_next < $y_right) {
			// ignore this corner
			continue;
		}
		// add the rectangle between
		// x_right, 1
		// x_next + 1, y_right
		$sum += ($x_right - $x_next) * $y_right;
		$x_right = $x_next;
		$y_right = $y_next;
	}
	// finally do the middle bit
	$sum += ($x_right - $x_left + 1) * $y_max;

	return $sum;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$all_filled_space = fill_and_count($data);

printf("Result 1: %d\n", $all_filled_space);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$all_filled_space = fill_and_count($data);

printf("Result 2: %d\n", $all_filled_space);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$all_filled_space = fill_and_count($data);

printf("Result 3: %d\n", $all_filled_space);

?>
