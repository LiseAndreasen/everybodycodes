<?php

///////////////////////////////////////////////////////////////////////////
// constants

$input1 = file_get_contents('./everybody_codes_e2025_q06_p1.txt', true);
$input2 = file_get_contents('./everybody_codes_e2025_q06_p2.txt', true);
$input3 = file_get_contents('./everybody_codes_e2025_q06_p3.txt', true);

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			$map = str_split($line);
		}
	}
	return $map;
}

///////////////////////////////////////////////////////////////////////////
// main program

$map = get_input($input1);

$mentors = 0;
$ment_sum = 0;
foreach($map as $person) {
	if($person == "A") {
		$mentors++;
	} else {
		if($person == "a") {
			$ment_sum += $mentors;
		}
	}
}
printf("Result 1: %d\n", $ment_sum);

$map = get_input($input2);

$mentors = array();
$mentors["A"] = 0;
$mentors["B"] = 0;
$mentors["C"] = 0;
$ment_sum = 0;
foreach($map as $person) {
	if(ctype_upper($person)) {
		$mentors[$person]++;
	} else {
		$ment_sum += $mentors[strtoupper($person)];
	}
}
printf("Result 2: %d\n", $ment_sum);

$map = get_input($input3);
$map_str = implode("", $map);
$patt_lgt = sizeof($map);
$dlimit = 1000;
$repeat = 1000;

$map_str = $map_str . $map_str . $map_str;

// count mentors in the middle patterns
$ment_sum1 = 0;
for($i=$patt_lgt;$i<$patt_lgt*2;$i++) {
	// cut off the end of the string
	$range = substr($map_str, 0, $i + $dlimit + 1);
	// cut of the beginning
	$range = substr($range, max(0, $i - $dlimit));
	$person = $map_str[$i];
	if(!ctype_upper($person)) {
		$mentor = strtoupper($person);
		$ment_ord = ord($mentor);
		$counts = count_chars($range);
		$no_mentors = $counts[$ment_ord];
		$ment_sum1 += $no_mentors;
	}
}

// count mentors in the first pattern
$ment_sum2 = 0;
for($i=0;$i<$patt_lgt;$i++) {
	// cut off the end of the string
	$range = substr($map_str, 0, $i + $dlimit + 1);
	// cut of the beginning
	$range = substr($range, max(0, $i - $dlimit));
	$person = $map_str[$i];
	if(!ctype_upper($person)) {
		$mentor = strtoupper($person);
		$ment_ord = ord($mentor);
		$counts = count_chars($range);
		$no_mentors = $counts[$ment_ord];
		$ment_sum2 += $no_mentors;
	}
}

// count mentors in the last pattern
$ment_sum3 = 0;
for($i=$patt_lgt*2;$i<$patt_lgt*3;$i++) {
	// cut off the end of the string
	$range = substr($map_str, 0, $i + $dlimit + 1);
	// cut of the beginning
	$range = substr($range, max(0, $i - $dlimit));
	$person = $map_str[$i];
	if(!ctype_upper($person)) {
		$mentor = strtoupper($person);
		$ment_ord = ord($mentor);
		$counts = count_chars($range);
		$no_mentors = $counts[$ment_ord];
		$ment_sum3 += $no_mentors;
	}
}

printf("Result 3: %d\n", $ment_sum1 * ($repeat - 2) + $ment_sum2 + $ment_sum3);

?>
