<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "15";
$START = "S";
$END = "E";
$WALL = "#";
$EMPTY = ".";
$DEPOSIT = "@";
$ALL_DIR = [[-1, 0], [0, -1], [0, 1], [1, 0]];

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

// find a certain value in 2d array
function find_char($map, $char) {
	$map_width = sizeof($map);
	for($i=0;$i<$map_width;$i++) {
		$j = array_search($char, $map[$i]);
		if($j !== false) {
			$x = $i;
			$y = $j;
			break;
		}
	}
	return [$x, $y];
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$lines = get_input($input);

echo "The time is " . date("h:i:sa" . "\n");

// this next bit heavily based on
// https://github.com/pf981/advent-of-code-and-friends/blob/23f07717ca165380af2f7ec486d7280ca55de700/everybody-codes/2026/15_1.py

$nrows = sizeof($lines);
$ncols = sizeof($lines[0]);
$start = find_char($lines, $START);
$to_collect = [];
for($r=0;$r<$nrows;$r++) {
	for($c=0;$c<$ncols;$c++) {
		if($lines[$r][$c] == $DEPOSIT) {
			$to_collect[] = [$r, $c];
		}
	}
}
$seen = [];

// array of distance, row/column, previous row/column, seen sulphur
$heap = [[0, $start[0], $start[1], -1, -1, []]];
$old_d = 0;
// progress
for($i=0;$i<80;$i++) {
	print("=");
}
print("\n");
while(true) {
	[$d, $r, $c, $prev_r, $prev_c, $collected] = array_shift($heap);
	$collected_flat = implode("*", array_merge(...$collected));
	$seen_id = sprintf("%d-%d-%d-%d-%s", $r, $c, $prev_r, $prev_c,
		$collected_flat);
	if(isset($seen[$seen_id])) {
		// this configuration already seen
		continue;
	}
	$seen[$seen_id] = true;

	if($lines[$r][$c] == $END and sizeof($collected) == sizeof($to_collect)) {
		// the solution!
		break;
	}

	foreach($ALL_DIR as $drdc) {
		[$dr, $dc] = $drdc;
		$r2 = $r + $dr;
		$c2 = $c + $dc;

		if($r2 == $prev_r && $c2 == $prev_c) {
			// don't go backwards
			continue;
		}

		if($r2 < 0 || $nrows <= $r2 || $c2 < 0 || $ncols <= $c2) {
			// don't go beyond the grid
			continue;
		}

		if($lines[$r2][$c2] == $WALL) {
			// don't walk on walls
			continue;
		}

		$collected2 = $collected;
		if(in_array([$r2, $c2], $to_collect)) {
			if(!in_array([$r2, $c2], $collected2)) {
				// register seen sulphur
				$collected2[] = [$r2, $c2];
				// sort is necessary for later comparison
				sort($collected2);
			}
		}
		// add this new step to the heap
		$heap[] = [$d + 1, $r2, $c2, $r, $c, $collected2];
	}
	// progress
	$new_d = $heap[0][0];
	if($old_d != $new_d) {
		$old_d = $new_d;
		printf(".", $new_d);
		if($new_d % 80 == 0) {
			print("\n");
		}
	}
}
print("\n");		// progress

echo "The time is " . date("h:i:sa" . "\n");

printf("\n\nResult 1: %d\n", $d);

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
