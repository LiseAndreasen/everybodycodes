<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "04";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// id=1 quest=1 part=1 time=187
			// convert csv to array
			$line2 = explode(" ", $line);
			$data2 = [];
			foreach($line2 as $l) {
				$line3 = explode("=", $l);
				$data2[$line3[0]] = $line3[1];
			}
			$data[] = $data2;
		}
	}
	return $data;
}

// sort according to own rule
function cmp($a, $b) {
	$comp1 = $b["part"] - $a["part"];
	$comp2 = $b["time"] - $a["time"];
	switch(true) {
		case $comp1 < 0:
			return 1;
			break;
		case 0 < $comp1:
			return -1;
			break;
		case $comp1 == 0:
			switch(true) {
				case $comp2 < 0:
					return -1;
					break;
				case 0 < $comp2:
					return 1;
					break;
				case $comp2 == 0;
					return 0;
					break;
			}
			break;
	}
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

// go through all lines, add times together based on id
$times = [];
foreach($data as $line) {
	$id = $line["id"];
	if(isset($times[$id])) {
		$times[$id] += $line["time"];
	} else {
		$times[$id] = $line["time"];
	}
}

// flip key and value (i.e. id and time)
$times = array_flip($times);
// sort on time
ksort($times);

$i = 1;
$checksum = 0;
foreach($times as $time => $id) {
	$checksum += $i * $time;
	$i++;
}

printf("Result 1: %d\n", $checksum);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

// count parts solved and add up times, based on id
$times = [];
foreach($data as $line) {
	$id = $line["id"];
	if(isset($times[$id])) {
		$times[$id]["time"] += $line["time"];
		$times[$id]["part"]++;
	} else {
		$times[$id]["id"] = $line["id"];
		$times[$id]["time"] = $line["time"];
		$times[$id]["part"] = 1;
	}
}

// sort on parts 1st, times second
usort($times, "cmp");
// reverse order
$times = array_reverse($times);

$i = 1;
$checksum = 0;
foreach($times as $time) {
	$checksum += $i * $time["time"];
	$i++;
}

printf("Result 2: %d\n", $checksum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
