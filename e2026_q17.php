<?php

include "dijkstra.php";

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "17";
$SOLID = "#";
$GAP = ".";

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

function convert_to_id($x, $y) {
    global $width;
    return $x + $width * $y;
}

function create_graph($map) {
	global $width, $height, $verticesCount, $SOLID, $GAP;

	$graph_sub = array_fill(0, $verticesCount, 0);
	$graph = array_fill(0, $verticesCount, $graph_sub);

	for($i=0;$i<$width;$i++) {
		for($j=0;$j<$height;$j++) {
			if($map[$i][$j] == $GAP) {
				continue;
			}
			$id_me = convert_to_id($i, $j);
			if(isset($map[$i+1][$j]) && $map[$i+1][$j] == $SOLID) {
				$id_right = convert_to_id($i+1, $j);
				$graph[$id_me][$id_right] = 1;
				$graph[$id_right][$id_me] = 1;
			}
			if(isset($map[$i][$j+1]) && $map[$i][$j+1] == $SOLID) {
				$id_down = convert_to_id($i, $j+1);
				$graph[$id_me][$id_down] = 1;
				$graph[$id_down][$id_me] = 1;
			}
		}
	}
	
	return $graph;
}

// a crust is between something solid and a gap
// just count all of these
function count_crust($map) {
	global $width, $height, $verticesCount, $SOLID, $GAP;

	$crusts = 0;
	for($i=-1;$i<$width;$i++) {
		for($j=-1;$j<$height;$j++) {
			// check right
			if($map[$i][$j] != $map[$i+1][$j]) {
				$crusts++;
			}
			// check down
			if($map[$i][$j] != $map[$i][$j+1]) {
				$crusts++;
			}
		}
	}
	return $crusts;
}

// add extra space around the map
function fillup($map) {
	global $width, $height;
	for($i=-1;$i<=$width;$i++) {
		$map[$i][-1] = ".";
		$map[$i][$height] = ".";
	}
	for($j=-1;$j<=$height;$j++) {
		$map[-1][$j] = ".";
		$map[$width][$j] = ".";
	}
	return $map;
}

// the idea here is to fill the map and then count edges of the fill
function flood_fill($map) {
	global $height, $width, $GAP, $SOLID;

	$LEFT_LOWER = "L";
	$UPPER_RIGHT = "U";

	// preemptively fill the corners of the map
	$map[-1][-1] = $SOLID;
	$map[-1][0] = $SOLID;
	$map[0][-1] = $SOLID;
	$map[$width][$height] = $SOLID;
	$map[$width][$height-1] = $SOLID;
	$map[$width-1][$height] = $SOLID;

	$q = [];
	// fill left and lower part of map
	for($i=1;$i<$width-1;$i++) {
		$map[$i][$height] = $LEFT_LOWER;
		$q[] = [$i, $height];
	}
	for($j=1;$j<$height-1;$j++) {
		$map[-1][$j] = $LEFT_LOWER;
		$q[] = [-1, $j];
	}
	// fill more of the map
	while(0 < sizeof($q)) {
		[$x, $y] = array_shift($q);
		for($i=$x-1;$i<=$x+1;$i++) {
			for($j=$y-1;$j<=$y+1;$j++) {
				if(!isset($map[$i][$j])) {
					continue;
				}
				if($map[$i][$j] == $GAP) {
					$map[$i][$j] = $LEFT_LOWER;
					$q[] = [$i, $j];
				}
			}
		}
	}

	// fill upper and right part of map
	for($i=1;$i<$width-1;$i++) {
		$map[$i][-1] = $UPPER_RIGHT;
		$q[] = [$i, -1];
	}
	for($j=1;$j<$height-1;$j++) {
		$map[$width][$j] = $UPPER_RIGHT;
		$q[] = [$width, $j];
	}
	// fill more of the map
	while(0 < sizeof($q)) {
		[$x, $y] = array_shift($q);
		for($i=$x-1;$i<=$x+1;$i++) {
			for($j=$y-1;$j<=$y+1;$j++) {
				if(!isset($map[$i][$j])) {
					continue;
				}
				if($map[$i][$j] == $GAP) {
					$map[$i][$j] = $UPPER_RIGHT;
					$q[] = [$i, $j];
				}
			}
		}
	}

	// fill close to the corners back in
	$map[-1][0] = $LEFT_LOWER;
	$map[0][-1] = $UPPER_RIGHT;
	$map[$width][$height-1] = $UPPER_RIGHT;
	$map[$width-1][$height] = $LEFT_LOWER;

	// now count
	$left_lower_count = 0;
	$upper_right_count = 0;
	for($i=-1;$i<$width;$i++) {
		for($j=-1;$j<$height;$j++) {
			// don't count the corners
			if($i == -1 & $i == -1) {
				continue;
			}
			// check right
			if($map[$i][$j] != $map[$i+1][$j]) {
				if($map[$i][$j] == $LEFT_LOWER || $map[$i+1][$j] == $LEFT_LOWER) {
					$left_lower_count++;
				} else {
					$upper_right_count++;
				}
			}
			// check down
			if($map[$i][$j] != $map[$i][$j+1]) {
				if($map[$i][$j] == $LEFT_LOWER || $map[$i][$j+1] == $LEFT_LOWER) {
					$left_lower_count++;
				} else {
					$upper_right_count++;
				}
			}
		}
	}

	return min($left_lower_count, $upper_right_count);
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

// note, i don't pivot
$width = sizeof($data);
$height = sizeof($data[0]);
$verticesCount = $width * $height;

$graph = create_graph($data);

$id_begin = convert_to_id(0, 0);
$id_end = convert_to_id($width - 1, $height - 1);
$distance = Dijkstra($graph, $id_begin, $verticesCount);

printf("Result 1: %d\n", $distance[$id_end]);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$width = sizeof($data);
$height = sizeof($data[0]);
// add gaps around the map
$data = fillup($data);

$crusts = count_crust($data);

printf("Result 2: %d\n", $crusts);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$width = sizeof($data);
$height = sizeof($data[0]);
// add gaps around the map
$data = fillup($data);

$route = flood_fill($data);

printf("Result 3: %d\n", $route);

?>
