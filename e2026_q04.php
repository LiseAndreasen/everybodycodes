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
function cmp2($a, $b) {
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

function cmp3($a, $b) {
	$comp = $b["time"] - $a["time"];
	switch(true) {
		case $comp < 0:
			return 1;
			break;
		case 0 < $comp:
			return -1;
			break;
		case $comp == 0;
			return 0;
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
usort($times, "cmp2");
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

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

// sort data by quest and part
$times = [];
foreach($data as $line) {
	$quest = $line["quest"];
	$part = $line["part"];
	$id = $line["id"];
	$time = $line["time"];
	$times[$quest][$part][$id]["id"] = $id;
	$times[$quest][$part][$id]["time"] = $time;
}

// sort times to make sure the quests come in the correct order
ksort($times);

// sort times to make sure the parts come in the correct order
foreach($times as $q => $quest) {
	ksort($times[$q]);
}

// for part 2 and 3, add previous times in
foreach($times as $q => $quest) {
	foreach($quest as $p => $part) {
		if($p == 1) {
			continue;
		}
		foreach($part as $t => $id_time) {
			$id = $id_time["id"];
			$time = $id_time["time"];
			$times[$q][$p][$t]["time"] += $times[$q][$p-1][$t]["time"];
		}
	}
}

// sort each quest-part by time
foreach($times as $q => $quest) {
	foreach($quest as $p => $part) {
		usort($times[$q][$p], "cmp3");
	}
}

// add up points for each id
$points = [];
foreach($times as $q => $quest) {
	foreach($quest as $p => $part) {
		$po = $p * 50;
		$prev_time = 0;			// there was no previous for the 1st time
		foreach($part as $id_time) {
			$id = $id_time["id"];
			$time = $id_time["time"];
			if($prev_time == 0) {
				$this_po = $po;
			} else {
				if($time == $prev_time) {
					$this_po = $prev_po;
				} else {
					$this_po = $po;
				}
			}
			if(isset($points[$id])) {
				$points[$id] += $this_po;
			} else {
				$points[$id] = $this_po;
			}
			$prev_time = $time;
			$prev_po = $this_po;
			if(0 < $po) {
				$po--;
			}
		}
	}
}

// sort points
rsort($points);

// calculate checksum
$i = 1;
$prev_i = 1;		// in case of ties
$prev_po = 0;		// check for tie
$checksum = 0;
foreach($points as $p) {
	if($p != $prev_po) {
		$prev_i = $i;
	}
	$checksum += $prev_i * $p;
	$prev_po = $p;
	$i++;
}

printf("Result 3: %d\n", $checksum);

?>
