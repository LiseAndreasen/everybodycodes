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
			// convert string to array
			$data[] = str_split($line);
		}
	}
	return $data;
}

function count_flips($line) {
	$begin = 0;
	$size_of_line = sizeof($line);
	$end = $size_of_line - 1;
	$flips = 0;
	$flip_lengths = 0;
	$end_new = [];
	$beginning_phase = true;
	$reversed = false;
	$shade = $line[0];

	while(true) {
		// progress
		printf("Chasing the color %s, outer loop\n", $shade);
		while($begin <= $end) {
			if($beginning_phase & !$reversed) {
				// may be at end of line
				if(!isset($line[$begin+1])) {
					break;
				}
				if($line[$begin] != $shade) {
					// go directly to next phase
					$beginning_phase = false;
					continue;
				}
				if($line[$begin+1] == $shade) {
					// handle shade at beginning of line
					// i.e. throw them away
				} else {
					// done with shade
					$beginning_phase = false;
				}
				$begin++;
				continue;
			}
			if($beginning_phase & $reversed) {
				if($line[$end] != $shade) {
					// go directly to next phase
					$beginning_phase = false;
					continue;
				}
				if($line[$end-1] == $shade) {
					// handle shade
				} else {
					$beginning_phase = false;
				}
				$end--;
				continue;
			}
			If(!$beginning_phase & !$reversed) {
				// look for shade at end of line
				if($line[$end] != $shade) {
					$end_new[] = $line[$end];
				} else {
					// reverse!
					$reversed = true;
					$beginning_phase = true;
					$flips++;
					$flip_lengths += $end - $begin + 1;
				}
				$end--;
				continue;
			}
			if(!$beginning_phase & $reversed) {
				// look for shade at beginning of line
				if($line[$begin] != $shade) {
					$end_new[] = $line[$begin];
				} else {
					// reverse!
					$reversed = false;
					$beginning_phase = true;
					$flips++;
					$flip_lengths += $end - $begin + 1;
				}
				$begin++;
				continue;
			}
		}
		// done with this shade
		if(sizeof($end_new) == 0) {
			// done altogether
			break;
		}
		$line = array_reverse($end_new);
		$begin = 0;
		$size_of_line = sizeof($line);
		$end = $size_of_line - 1;
		$end_new = [];
		$beginning_phase = true;
		$reversed = false;
		$shade = $line[0];
	}
	return [$flips, $flip_lengths];
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$line = $data[0];

[$flips, $flip_lengths] = count_flips($line);

printf("Result 1: %d\n", $flips);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$line = $data[0];
$SHELF_REPEATS = 10;

$repeated_shelf = [];
for($l=0;$l<$SHELF_REPEATS;$l++) {
	print(".");		// progress
	$repeated_shelf = array_merge($repeated_shelf, $line);
}
print("\n");		// progress

[$flips, $flip_lengths] = count_flips($repeated_shelf);

printf("Result 2: %d\n", $flip_lengths);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);
$line = $data[0];
$SHELF_REPEATS = 100;

$repeated_shelf = [];
for($l=0;$l<$SHELF_REPEATS;$l++) {
	print(".");		// progress
	$repeated_shelf = array_merge($repeated_shelf, $line);
}
print("\n");		// progress

[$flips, $flip_lengths] = count_flips($repeated_shelf);

printf("Result 3: %d\n", $flip_lengths);

?>
