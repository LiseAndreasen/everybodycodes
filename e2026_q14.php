<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "14";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert string to array
			$data[] = str_split($line);
		}
	}
	return $data;
}

function count_bindings($map) {
	$w = sizeof($map);
	$h = sizeof($map[0]);

	$bindings = 0;

	// 1 binding for each letter-change
	// +1 at each end
	// width
	for($i=0;$i<$w;$i++) {
		for($j=0;$j<$h;$j++) {
			$letter = $map[$i][$j];
			if($j!=0) {
				if($letter_prev != $letter) {
					$bindings++;
				}
			}
			$letter_prev = $letter;
		}
		$bindings += 2;
	}
	// height
	for($j=0;$j<$h;$j++) {
		for($i=0;$i<$w;$i++) {
			$letter = $map[$i][$j];
			if($i!=0) {
				if($letter_prev != $letter) {
					$bindings++;
				}
			}
			$letter_prev = $letter;
		}
		$bindings += 2;
	}
	return $bindings;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$bindings = count_bindings($data);

printf("Result 1: %d\n", $bindings);

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
