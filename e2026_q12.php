<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "12";
$EMPTY = ".";
$SPELL = "#";

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

function print_map($map) {
	foreach($map[0] as $j => $cell) {
		foreach($map as $i => $col) {
			echo $map[$i][$j];
		}
		echo "\n";
	}
	for($i=0;$i<sizeof($map);$i++) {
		echo "=";
	}
	echo "\n";
}

/*
###			3x2
###

^ ### ^
| ### n
m ### v
| #
v #

###			m > n
#

###			m < n
  #

#			m = n, but more on the right
###

  #			m = n, but more on the left
###
*/
function destroy_block($map, $a, $b) {
	global $SPELL, $EMPTY;

	$spells_needed = 0;

	// a goes left right
	// b goes up down

	$a_left = $a;
	$b_top = $b;

	// find the right end of the top edge
	$a_right = $a_left;
	while(isset($map[$a_right+1][$b_top]) && $map[$a_right+1][$b_top] == $SPELL) {
		$a_right++;
	}
	// find the lower end of the left edge
	$b_left_low = $b_top;
	while(isset($map[$a_left][$b_left_low+1]) && $map[$a_left][$b_left_low+1] == $SPELL) {
		$b_left_low++;
	}
	// same on right edge
	$b_right_low = $b_top;
	while(isset($map[$a_right][$b_right_low+1]) && $map[$a_right][$b_right_low+1] == $SPELL) {
		$b_right_low++;
	}

	if($b_left_low != $b_right_low) {
		// L with arm hanging down
		if($b_left_low < $b_right_low) {
			// arm hanging down on the right
			$type = "l-hang-right";
		} else {
			$type = "l-hang-left";
		}
	} else {
		if(isset($map[$a_left-1][$b_left_low]) && $map[$a_left-1][$b_left_low] == $SPELL) {
			// L with arm shooting up on the right
			$type = "l-shoot-right";
		} else {
			if(isset($map[$a_right+1][$b_right_low]) && $map[$a_right+1][$b_right_low] == $SPELL) {
				// shooting up on the left
				$type = "l-shoot-left";
			} else {
				// rectangle
				$type = "rectangle-solid";
			}
		}
	}

//	printf("Block of type %s from %d %d to %d %d\n", $type, $a_left, $b_top, $a_right, $b_right_low);

	switch($type) {
		case "rectangle-solid":
			$a_span = $a_right - $a_left + 1;
			$b_span = $b_left_low - $b_top + 1;
			if($a_span < $b_span) {
				$spells_needed = $a_span;
//				printf("%d spells required\n", $a_span);
			} else {
				$spells_needed = $b_span;
//				printf("%d spells required\n", $b_span);
			}
//			printf("Rectangle with length %d and %d detected\n", $a_span, $b_span);

			// delete
			for($a_spell=$a_left;$a_spell<=$a_right;$a_spell++) {
				for($b_spell=$b_top;$b_spell<=$b_left_low;$b_spell++) {
					$map[$a_spell][$b_spell] = $EMPTY;
				}
			}
			break;
		case "l-hang-left":
			$a_center = $a_left;
			while(isset($map[$a_center+1][$b_left_low]) && $map[$a_center+1][$b_left_low] == $SPELL) {
				$a_center++;
			}
			$a_span_big = $a_right - $a_left + 1;
			$b_span_big = $b_left_low - $b_top + 1;
			$a_span_small = $a_center - $a_left + 1;
			$b_span_small = $b_right_low - $b_top + 1;
//			printf("L with arms of length %d (%d) and %d (%d) detected\n", $a_span_big, $a_span_small, $b_span_big, $b_span_small);

			// delete each arm separately?
			$delete1 = $a_span_small + $b_span_small;
			$delete2 = min($a_span_big, $b_span_big);
			// delete with only 1 kind of spell?
			$delete = min($delete1, $delete2);
//			printf("%d spells required\n", $delete);

			$spells_needed += $delete;

			// delete
			for($a_spell=$a_left;$a_spell<=$a_right;$a_spell++) {
				for($b_spell=$b_top;$b_spell<=$b_right_low;$b_spell++) {
					$map[$a_spell][$b_spell] = $EMPTY;
				}
			}
			for($a_spell=$a_left;$a_spell<=$a_center;$a_spell++) {
				for($b_spell=$b_top;$b_spell<=$b_left_low;$b_spell++) {
					$map[$a_spell][$b_spell] = $EMPTY;
				}
			}
			break;
		case "l-hang-right":
			$a_center = $a_right;
			while(isset($map[$a_center-1][$b_right_low]) && $map[$a_center-1][$b_right_low] == $SPELL) {
				$a_center--;
			}
			$a_span_big = $a_right - $a_left + 1;
			$b_span_big = $b_right_low - $b_top + 1;
			$a_span_small = $a_right - $a_center + 1;
			$b_span_small = $b_left_low - $b_top + 1;
//			printf("L with arms of length %d (%d) and %d (%d) detected\n", $a_span_big, $a_span_small, $b_span_big, $b_span_small);

			// delete each arm separately?
			$delete1 = $a_span_small + $b_span_small;
			$delete2 = min($a_span_big, $b_span_big);
			// delete with only 1 kind of spell?
			$delete = min($delete1, $delete2);
//			printf("%d spells required\n", $delete);

			$spells_needed += $delete;

			// delete
			for($a_spell=$a_left;$a_spell<=$a_right;$a_spell++) {
				for($b_spell=$b_top;$b_spell<=$b_left_low;$b_spell++) {
					$map[$a_spell][$b_spell] = $EMPTY;
				}
			}
			for($a_spell=$a_center;$a_spell<=$a_right;$a_spell++) {
				for($b_spell=$b_top;$b_spell<=$b_right_low;$b_spell++) {
					$map[$a_spell][$b_spell] = $EMPTY;
				}
			}
			break;
		case "l-shoot-left":
			$a_center = $a_right;
			while(isset($map[$a_right+1][$b_left_low]) && $map[$a_right+1][$b_left_low] == $SPELL) {
				$a_right++;
			}
			$b_center = $b_right_low;
			while(isset($map[$a_right][$b_center-1]) && $map[$a_right][$b_center-1] == $SPELL) {
				$b_center--;
			}
			$a_span_big = $a_right - $a_left + 1;
			$b_span_big = $b_left_low - $b_top + 1;
			$a_span_small = $a_center - $a_left + 1;
			$b_span_small = $b_right_low - $b_center + 1;
//			printf("L with arms of length %d (%d) and %d (%d) detected\n", $a_span_big, $a_span_small, $b_span_big, $b_span_small);

			// delete each arm separately?
			$delete1 = $a_span_small + $b_span_small;
			$delete2 = min($a_span_big, $b_span_big);
			// delete with only 1 kind of spell?
			$delete = min($delete1, $delete2);
//			printf("%d spells required\n", $delete);

			$spells_needed += $delete;

			// delete
			for($a_spell=$a_left;$a_spell<=$a_center;$a_spell++) {
				for($b_spell=$b_top;$b_spell<=$b_left_low;$b_spell++) {
					$map[$a_spell][$b_spell] = $EMPTY;
				}
			}
			for($a_spell=$a_left;$a_spell<=$a_right;$a_spell++) {
				for($b_spell=$b_center;$b_spell<=$b_left_low;$b_spell++) {
					$map[$a_spell][$b_spell] = $EMPTY;
				}
			}
			break;
		case "l-shoot-right":
			$a_center = $a_left;
			while(isset($map[$a_left-1][$b_left_low]) && $map[$a_left-1][$b_left_low] == $SPELL) {
				$a_left--;
			}
			$b_center = $b_right_low;
			while(isset($map[$a_left][$b_center-1]) && $map[$a_left][$b_center-1] == $SPELL) {
				$b_center--;
			}
			$a_span_big = $a_right - $a_left + 1;
			$b_span_big = $b_left_low - $b_top + 1;
			$a_span_small = $a_right - $a_center + 1;
			$b_span_small = $b_left_low - $b_center + 1;
//			printf("L with arms of length %d (%d) and %d (%d) detected\n", $a_span_big, $a_span_small, $b_span_big, $b_span_small);

			// delete each arm separately?
			$delete1 = $a_span_small + $b_span_small;
			$delete2 = min($a_span_big, $b_span_big);
			// delete with only 1 kind of spell?
			$delete = min($delete1, $delete2);
//			printf("%d spells required\n", $delete);

			$spells_needed += $delete;

			// delete
			for($a_spell=$a_center;$a_spell<=$a_right;$a_spell++) {
				for($b_spell=$b_top;$b_spell<=$b_left_low;$b_spell++) {
					$map[$a_spell][$b_spell] = $EMPTY;
				}
			}
			for($a_spell=$a_left;$a_spell<=$a_right;$a_spell++) {
				for($b_spell=$b_center;$b_spell<=$b_left_low;$b_spell++) {
					$map[$a_spell][$b_spell] = $EMPTY;
				}
			}
			break;
	}

	return [$map, $spells_needed];
}

function find_and_destroy_blocks($map) {
	global $EMPTY, $PART;

	if($PART == 3) {
		print_map($map);
	}

	$spells = 0;
	$a_max = sizeof($map);
	$b_max = sizeof($map[0]);
	for($b=0;$b<$b_max;$b++) {
		for($a=0;$a<$a_max;$a++) {
			if($map[$a][$b] == $EMPTY) {
				continue;
			}
			[$map, $s] = destroy_block($map, $a, $b);
			$spells += $s;
			if($PART == 3) {
				print_map($map);
			}
		}
	}
	return $spells;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$PART = 1;

$spells = find_and_destroy_blocks($data);

printf("Result 1: %d\n", $spells);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$PART = 2;

$spells = find_and_destroy_blocks($data);

printf("Result 2: %d\n", $spells);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3_ex1.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);
$PART = 3;

$spells = find_and_destroy_blocks($data);

printf("Result 3: %d\n", $spells);

?>
