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
	$SOLID = "#";
	$EMPTY = ".";
	$EXTRA = "=";

	$grid = [];
	$x_all = [];
	$y_all = [];
	// fill in stacks
	foreach($data as $id => $d) {
		$x = $d["x"];
		$y = $d["y"];
		$x_all[$id] = $x;
		$y_all[$id] = $y;
		for($j=1;$j<=$y;$j++) {
			$grid[$x][$j] = $SOLID;
		}
	}
	// fill in the rest of the grid, first with empty space
	$x_min = min($x_all);
	$x_max = max($x_all);
	$y_min = 1;
	$y_max = max($y_all);
	for($i=$x_min;$i<=$x_max;$i++) {
		for($j=$y_min;$j<=$y_max;$j++) {
			if(!isset($grid[$i][$j])) {
				$grid[$i][$j] = $EMPTY;
			}
		}
	}
	// then fill in between the stacks
	for($j=$y_min;$j<=$y_max;$j++) {
		$x_left = $x_min;
		$x_right = $x_max;
		while($grid[$x_left][$j] != $SOLID) {
			$x_left++;
		}
		while($grid[$x_right][$j] != $SOLID) {
			$x_right--;
		}
		for($i=$x_left;$i<=$x_right;$i++) {
			if($grid[$i][$j] != $SOLID) {
				$grid[$i][$j] = $EXTRA;
			}
		}
	}
	// count filled squares
	$string = implode("", array_merge(...$grid));
	$counts = count_chars($string);
	$hash_value = ord($EMPTY);
	$all_filled_space = array_sum($counts) - $counts[$hash_value];

	return $all_filled_space;
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

$file2 = './everybody_codes_e2026_q' . $quest . '_p2_ex1.txt';
//$input = file_get_contents($file2, true);
//$data = get_input($input);
//printf("Result 2: %d\n", $hits);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
