<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "20";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$data[] = $line;
		}
	}
	return $data;
}

function do_math($line) {
	$a1 = 0;
	$a2 = 0;
	$symbol = "";
	$before = true;			// before symbol
	foreach(str_split($line) as $char) {
		if(strpos("+-=", $char) !== false) {
			$symbol = $char;
			$before = false;
		} else {
			if($char == "A") {
				if($before) {
					$a1 = $a1 * 11 + 10;
				} else {
					$a2 = $a2 * 11 + 10;
				}
			} else {
				if($before) {
					$a1 = $a1 * 11 + (int) $char;
				} else {
					$a2 = $a2 * 11 + (int) $char;
				}
			}
		}
	}
	// do math
	switch($symbol) {
		case "+":
			$a = $a1 + $a2;
			break;
		case "-":
			$a = $a1 - $a2;
			break;
	}
	return $a;
}
///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$segment_count = 0;
foreach($data as $line) {
	$math = explode(" = ", $line);
	$a = eval('return '.$math[0].';');
	$b = eval('return '.$math[1].';');
	if($a == $b) {
		$line = str_replace(" ", "", $line);
		// convert character to number of segments
		$segments = strtr($line, "0123456789+-=", "6255456376212");
		$c = array_sum(str_split($segments));
		$segment_count += $c;
	}
}

printf("Result 1: %d\n", $segment_count);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$segment_count = 0;
foreach($data as $line) {
	$line = str_replace(" ", "", $line);
	$math = explode("=", $line);
	$a = do_math($math[0]);
	$b = do_math($math[1]);
	if($a == $b) {
		// convert character to number of segments
		$segments = strtr($line, "0123456789A+-=", "62554563766212");
		$c = array_sum(str_split($segments));
		$segment_count += $c;
	}
}

// show in base 11
$segment_count = base_convert($segment_count, 10, 11);

printf("Result 2: %s\n", $segment_count);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
