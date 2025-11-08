<?php

///////////////////////////////////////////////////////////////////////////
// constants

$input1 = file_get_contents('./everybody_codes_e2025_q05_p1.txt', true);
$input2 = file_get_contents('./everybody_codes_e2025_q05_p2.txt', true);
$input3 = file_get_contents('./everybody_codes_e2025_q05_p3.txt', true);

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		if(strlen($line)>2) {
			$parts = explode(":", $line);
			$id = $parts[0];
			$numbers = explode(",", $parts[1]);
			$map[] = array($id, $numbers);
		}
	}
	return $map;
}

function fishbone($numbers) {
	// construct fishbone
	$fb = array();
	foreach($numbers as $no) {
		$spine_no = 0;
		$going = isset($fb[$spine_no][0]);
		while($going === true) {
			$this_val = $fb[$spine_no][0];
			if($this_val < $no && !isset($fb[$spine_no][1])) {
				// right part of fishbone
				$fb[$spine_no][1] = $no;
				continue 2;
			}
			if($no < $this_val && !isset($fb[$spine_no][-1])) {
				// left part of fishbone
				$fb[$spine_no][-1] = $no;
				continue 2;
			}
			$spine_no++;
			$going = isset($fb[$spine_no][0]);
		}
		if($going === false) {
			// middle part of fishbone
			$fb[$spine_no][0] = $no;
			continue;
		}
	}
	return $fb;
}

function get_quality($fb) {
	$quality = "";
	foreach($fb as $segment) {
		$quality .= $segment[0];
	}
	return $quality;
}

function sword_sort($sword1, $sword2) {
	// first quality
	if($sword1[3] < $sword2[3]) {
		return -1;
	}
	if($sword1[3] > $sword2[3]) {
		return 1;
	}
	
	// then fishbone
	foreach($sword1[2] as $key => $line) {
		sort($sword1[2][$key]);
		$line1 = implode("", $sword1[2][$key]);
		sort($sword2[2][$key]);
		$line2 = implode("", $sword2[2][$key]);
		if($line1 < $line2) {
			return -1;
		}
		if($line1 > $line2) {
			return 1;
		}
	}
	
	// finally id
	if($sword1[0] < $sword2[0]) {
		return -1;
	}
	if($sword1[0] > $sword2[0]) {
		return 1;
	}
	
	// this should never happen!
	return 0;
}

///////////////////////////////////////////////////////////////////////////
// main program

$map = get_input($input1);

$fb = fishbone($map[0][1]);
$quality = get_quality($fb);

print("Result 1: $quality\n");

$map = get_input($input2);

foreach($map as $sword) {
	$fb = fishbone($sword[1]);
	$qualities[] = get_quality($fb);
}
printf("Result 2: %d\n", max($qualities) - min($qualities));

$map = get_input($input3);
foreach($map as $key => $sword) {
	$fb = fishbone($sword[1]);
	$map[$key][2] = $fb;
	$map[$key][3] = get_quality($fb);
}

usort($map, "sword_sort");
$map = array_reverse($map);

$i = 1;
$sword_sum = 0;
foreach($map as $sword) {
	$sword_sum += $i * $sword[0];
	$i++;
}
printf("Result 3: %d\n", $sword_sum);

?>
