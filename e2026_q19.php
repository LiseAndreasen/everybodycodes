<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "19";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	$data2 = [];
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert string to array
			$data2[] = str_split($line);
		} else {
			$data[] = $data2;
			$data2 = [];
		}
	}
	if(0 < sizeof($data2)) {
		$data[] = $data2;
	}
	return $data;
}

function count_crystals($data, $colors1, $colors2) {
	// todo: implement conversion from 1234 to CMYK
	$result = [];
	foreach($data[0] as $i => $col) {
		foreach($col as $j => $cell) {
			$color1 = $data[0][$i][$j] . $data[1][$i][$j];
			switch($color1) {
				case "KK":
				case "KC":
				case "KM":
				case "KY":
				case "CK":
				case "MK":
				case "YK":
					$result[$i][$j] = "K";
					break;
				case "CM":
				case "MC":
					$result[$i][$j] = "B";
					break;
				case "CY":
				case "YC":
					$result[$i][$j] = "G";
					break;
				case "MY":
				case "YM":
					$result[$i][$j] = "R";
					break;
				case "CC":
					$result[$i][$j] = "C";
					break;
				case "MM":
					$result[$i][$j] = "M";
					break;
				case "YY":
					$result[$i][$j] = "Y";
					break;
			}
		}
	}

	// count occurrences in 2d array
	$arr2 = array_merge(...$result);
	$arr3 = array_count_values($arr2);
	rsort($arr3);

	$product = 1;
	for($i=0;$i<=2;$i++) {
		$product *= $arr3[$i];
	}

	return $product;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$product = count_crystals($data, "CMYK", "CMYK");

printf("Result 1: %d\n", $product);

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
