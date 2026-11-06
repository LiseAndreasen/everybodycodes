<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "09";
$type_point = 1;
$type_branch = 2;
$PLANTIDPOS = 0;
$LENGTHPOS = 1;
$PARENTPOS = 2;
$CHILDRENPOS = 3;
$ROOT = -1;
$NEW_BRANCH_LENGTH = 1;

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$line2 = explode(", ", $line);
			$data2 = [];
			foreach($line2 as $l) {
				$line3 = explode("=", $l);
				$data2[] = $line3[1];
			}
			$data[] = $data2;
		}
	}
	return $data;
}

function size_of_plant($plant, $part) {
	$size = 0;
	foreach($plant as $branch) {
		[$pid, $l, $pa, $c] = $branch;
		if($part == 1) {
			$size += $l;
			if(0 < sizeof($c)) {
				$size++;
			}
		}
		if($part == 2) {
			if(sizeof($c) == 0) {
				// leaves have 0 children
				$size++;
			}
		}
	}
	return $size;
}

function grow_plants($data, $part) {
	global $ROOT, $LENGTHPOS, $CHILDRENPOS, $NEW_BRANCH_LENGTH;
	
	// a plant is an array of arrays
	// 1 branch: [$plant_id, $length, $parent, [$child1, $child2]]
	// branches with children end at branching points not included in the length
	$seconds_sum = 0;
	foreach($data as $seed) {
		print("S");
		[$id, $growth, $limit] = $seed;
		$plant = [];
		$size = 0;
		$plant_id_counter = 0;
		$seconds = 0;
		while($size < $limit) {
			$seconds++;
			if($size == 0) {
				// this is the 1st round
				$plant[$plant_id_counter] = [$plant_id_counter, $NEW_BRANCH_LENGTH, $ROOT, []];
				$plant_id_counter++;
			} else {
				foreach($plant as $pid => $branch) {
					$plant[$pid][$LENGTHPOS]++;
					if($plant[$pid][$LENGTHPOS] == $growth) {
						// split branch around branching point, 2 branches added
						// 1: 1st half of old branch
						$new_length = ($plant[$pid][$LENGTHPOS] - 1) / 2;
						$plant[$pid][$LENGTHPOS] = $new_length;
						// 2: 2nd half of old branch, as a new branch
						$plant[$plant_id_counter] = [$plant_id_counter, $new_length, $pid, $plant[$pid][$CHILDRENPOS]];
						$plant_id_counter++;
						// 3: add new branch
						$plant[$plant_id_counter] = [$plant_id_counter, $NEW_BRANCH_LENGTH, $pid, []];
						$plant[$pid][$CHILDRENPOS] = [$plant_id_counter-1, $plant_id_counter];
						$plant_id_counter++;
					}
				}
			}
			$size = size_of_plant($plant, $part);
		}
		$seconds_sum += $seconds;
	}
	return $seconds_sum;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$part = 1;

$seconds_sum = grow_plants($data, $part);

printf("\nResult 1: %d\n", $seconds_sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$part = 2;

$seconds_sum = grow_plants($data, $part);

printf("\nResult 2: %d\n", $seconds_sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
