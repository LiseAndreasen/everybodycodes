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
	// flip
	$begin = 0;
	$size_of_line = sizeof($line);
	$end = $size_of_line - 1;
	$flips = 0;
	$flip_lengths = 0;

	while(true) {
		$shade1 = $line[$begin];
		// proceed to end of this group
		while($begin < $size_of_line - 1 && $line[$begin+1] == $shade1) {
			$begin++;
		}
		if($begin == $size_of_line - 1) {
			// we are done
			break;
		}
		// find last occurrence of this shade
		while($shade1 != $line[$end]) {
			$end--;
		}
		if($begin == $end) {
			// this group is done
			if($begin < $size_of_line - 1) {
				$begin++;
				$end = $size_of_line - 1;
				continue;
			} else {
				// all flips done
				break;
			}
		}

		// now both begin and end point to the same shade
		// test whether there's anything else in that interval
		$shade_test = array_slice($line, $begin, $end - $begin + 1);
		$counts = array_count_values($shade_test);
		if($counts[$shade1] != sizeof($shade_test)) {
			// move begin 1 more step
			$begin++;
			// flip
			$arr1 = array_slice($line, 0, $begin);
			$arr2 = array_slice($line, $begin, $end - $begin + 1);
			$arr3 = array_slice($line, $end + 1);
			$arr2 = array_reverse($arr2);
			$line = array_merge($arr1, $arr2, $arr3);
			$flips++;
			$flip_lengths += sizeof($arr2);
		} else {
			// this group is done, move to next shade
			while($shade1 == $line[$begin+1] && $begin < $size_of_line) {
				$begin++;
			}
			if($begin == $size_of_line - 1) {
				// all flips done
				break;
			}
		}
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
