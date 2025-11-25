<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "16";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$data = explode(",", $line);
		}
	}
	return $data;
}

function blocks_used($data, $columns) {
	$blocks = 0;
	foreach($data as $number) {
		$blocks += floor($columns / $number);
	}
	return $blocks;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2025_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$columns = 90;

$blocks = blocks_used($data, $columns);
printf("Result 1: %d\n", $blocks);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2025_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$data_lgt = sizeof($data);

// i assume the largest number in the list is <= the length of list
$building = array_fill(0, $data_lgt, 0);
$numbers = array();
for($i=1;$i<=$data_lgt;$i++) {
	if($building[$i-1] < $data[$i-1]) {
		// we found a number
		$numbers[] = $i;
		for($j=$i-1;$j<$data_lgt;$j+=$i) {
			$building[$j]++;
		}
	}
}

$number_prod = array_product($numbers);

printf("Result 2: %d\n", $number_prod);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2025_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$data_lgt = sizeof($data);

// i assume the largest number in the list is <= the length of list
$building = array_fill(0, $data_lgt, 0);
$numbers = array();
for($i=1;$i<=$data_lgt;$i++) {
	if($building[$i-1] < $data[$i-1]) {
		// we found a number
		$numbers[] = $i;
		for($j=$i-1;$j<$data_lgt;$j+=$i) {
			$building[$j]++;
		}
	}
}

// bisection

$all_blocks = 202520252025000;
$columns_min = 1;
$columns_max = $all_blocks;
$used_min = blocks_used($numbers, $columns_min); // should be less than all blocks
$used_max = blocks_used($numbers, $columns_max); // should be more than all blocks
while($columns_min < $columns_max - 1) {
	// examine the point in the middle
	$columns_new = floor(($columns_min + $columns_max) / 2);
	$used_new = blocks_used($numbers, $columns_new);
	if($used_new < $all_blocks) {
		// increase min
		$columns_min = $columns_new;
		$used_min = $used_new;
	} else {
		// decrease max
		$columns_max = $columns_new;
		$used_max = $used_new;
	}
}

printf("Result 3: %d\n", $columns_min);

?>
