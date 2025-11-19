<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "13";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>0) {
			$data[] = $line;
		}
	}
	return $data;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2025_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

// sort data as 1st in the middle, then to the right, then to the left etc.
$data_sz = sizeof($data);
$i_min = $data_sz;
$i_max = 0;
$dir = "r";
foreach($data as $no) {
	if($i_max == 0) {
		// this is the first number
		$data2[$i_max] = 1;
		$i_max++;
	}
	if($dir == "r") {
		$data2[$i_max] = $no;
		$i_max++;
		$dir = "l";
	} else {
		$data2[$i_min] = $no;
		$i_min--;
		$dir = "r";
	}
}

// turn the dial 2025 positions clockwise
$turns = 2025 % ($data_sz + 1);

printf("Result 1: %10d\n", $data2[$turns]);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2025_q' . $quest . '_p2_ex1.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

// sort data as 1st in the middle, then to the right, then to the left etc.
$data_sz = 0;
foreach($data as $range) {
	$range2 = explode("-", $range);
	$data3[] = $range2;
	$data_sz += $range2[1] - $range2[0] + 1;
}
$i_min = $data_sz;
$i_max = 0;
$dir = "r";
$data2 = array();
foreach($data3 as $range) {
	if($i_max == 0) {
		// this is the first number
		$data2[$i_max] = 1;
		$i_max++;
	}
	if($dir == "r") {
		for($i=$range[0];$i<=$range[1];$i++) {
			$data2[$i_max] = $i;
			$i_max++;
		}
		$dir = "l";
	} else {
		for($i=$range[0];$i<=$range[1];$i++) {
			$data2[$i_min] = $i;
			$i_min--;
		}
		$dir = "r";
	}
}

// turns of dial 20252025
$turns = 20252025 % ($data_sz + 1);

printf("Result 2: %10d\n", $data2[$turns]);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2025_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$data_sz = 0;
$data3 = array();
foreach($data as $range) {
	$range2 = explode("-", $range);
	$data3[] = $range2;
	$data_sz += $range2[1] - $range2[0] + 1;
}
$i_min = $data_sz;
$i_max = 0;
$dir = "r";
$data2 = array();
foreach($data3 as $range) {
	if($i_max == 0) {
		// this is the first number
		$data2[$i_max] = 1;
		$i_max++;
	}
	if($dir == "r") {
		$data2[$i_max] = $range[0];
		$range_diff = $range[1] - $range[0];
		$i_max += $range_diff;
		$data2[$i_max] = $range[1];
		$i_max++;
		$dir = "l";
	} else {
		$data2[$i_min] = $range[0];
		$range_diff = $range[1] - $range[0];
		$i_min -= $range_diff;
		$data2[$i_min] = $range[1];
		$i_min--;
		$dir = "r";
	}
}

ksort($data2);

// turns of dial 202520252025
$turns = 202520252025 % ($data_sz + 1);
foreach($data2 as $key => $no) {
	if(!isset($old_key)) {
		$old_key = $key;
		$old_no = $no;
		continue;
	}
	if($key == $turns) {
		// we hit exactly right!
		$hit = $no;
	}
	if($key < $turns) {
		// we haven't gone far enough
		$old_key = $key;
		$old_no = $no;
		continue;
	} else {
		// we need to hit the number between old_no and no
		// the fictitious $data2[$turns] would be
		$hit = $data2[$key] + $key - $turns;
		break;
	}
}

printf("Result 3: %10d\n", $hit);

?>
