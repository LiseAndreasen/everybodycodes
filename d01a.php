<?php

///////////////////////////////////////////////////////////////////////////
// constants

$input1 = file_get_contents('./everybody_codes_e2025_q01_p1.txt', true);
$input2 = file_get_contents('./everybody_codes_e2025_q01_p2.txt', true);
$input3 = file_get_contents('./everybody_codes_e2025_q01_p3.txt', true);

///////////////////////////////////////////////////////////////////////////
// functions

///////////////////////////////////////////////////////////////////////////
// main program

// absorb input file, line by line
foreach(preg_split("/((\r?\n)|(\r\n?))/", $input1) as $line) {
	if(strlen($line)>2) {
		$lists[] = explode(",", $line);
	}
}

$pos = 0;
$end_pos = sizeof($lists[0]) - 1;

foreach($lists[1] as $ins) {
	$dir = $ins[0];
	$steps = $ins[1];
	if($dir == "L") {
		$pos -= $steps;
		if($pos < 0) {
			$pos = 0;
		}
	} else {
		$pos += $steps;
		if($pos > $end_pos) {
			$pos = $end_pos;
		}
	}
}

printf("My name................: %s\n", $lists[0][$pos]);

$lists = array();
// absorb input file, line by line
foreach(preg_split("/((\r?\n)|(\r\n?))/", $input2) as $line) {
	if(strlen($line)>2) {
		$lists[] = explode(",", $line);
	}
}

$pos = 0;
$no_pos = sizeof($lists[0]);

foreach($lists[1] as $ins) {
	$dir = $ins[0];
	$steps = substr($ins, 1);
	if($dir == "L") {
		$pos += $no_pos - $steps;
	} else {
		$pos += $steps;
	}
}
$pos = $pos % $no_pos;

printf("My first parent's name.: %s\n", $lists[0][$pos]);

$lists = array();
// absorb input file, line by line
foreach(preg_split("/((\r?\n)|(\r\n?))/", $input3) as $line) {
	if(strlen($line)>2) {
		$lists[] = explode(",", $line);
	}
}

$no_pos = sizeof($lists[0]);

foreach($lists[1] as $ins) {
	$dir = $ins[0];
	$steps = substr($ins, 1);
	if($dir == "L") {
		// at most 2 times around the circle
		$other_pos = (2 * $no_pos - $steps) % $no_pos;
	} else {
		$other_pos = $steps % $no_pos;
	}
	$swap = $lists[0][0];
	$lists[0][0] = $lists[0][$other_pos];
	$lists[0][$other_pos] = $swap;
}

printf("My second parent's name: %s\n", $lists[0][0]);

?>
