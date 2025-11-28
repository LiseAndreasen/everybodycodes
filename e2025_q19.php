<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "19";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	$line_max_x = 0;
	$line_max_y = 0;
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$line_arr = explode(",", $line);
			if($line_max_x < $line_arr[0]) {
				$line_max_x = $line_arr[0];
			}
			if($line_max_y < $line_arr[1] + $line_arr[2] + 1) {
				$line_max_y = $line_arr[1] + $line_arr[2] + 1;
			}
			$data[] = $line_arr;
		}
	}
	return array($data, $line_max_x, $line_max_y);
}

function print_map($map) {
	global $map_w, $map_h;
	for($j=$map_h-1;$j>=0;$j--) {
		for($i=0;$i<$map_w;$i++) {
			echo $map[$i][$j];
		}
		echo "\n";
	}
	for($i=0;$i<$map_w;$i++) {
		echo "=";
	}
	echo "\n";
}

function fly_map($map, $x, $y, $flaps, $sofar) {
	global $points_seen;
	if(isset($points_seen[$x][$y])) {
		return $points_seen[$x][$y];
	}

	global $map_w, $map_h;
	global $best_route, $best_route_flaps;
	
	if($map_w <= $x || $y < 0 || $map_h <= $y) {
		// invalid route
		$points_seen[$x][$y] = -1;
		return -1;
	}
	
	if($map[$x][$y] == "#") {
		// invalid route
		$points_seen[$x][$y] = -1;
		return -1;
	}
	
	if($x == $map_w - 1) {
		// valid route
		if($flaps < $best_route_flaps) {
			$best_route_flaps = $flaps;
			$best_route = $sofar;
		}
		// how many more flaps are required?
		$points_seen[$x][$y] = 0;
		return 0;
	}
	
	$fly_unflap = fly_map($map, $x + 1, $y - 1, $flaps, $sofar . "d");
	$fly_flap = fly_map($map, $x + 1, $y + 1, $flaps + 1, $sofar . "u");
	if($fly_unflap == -1) {
		if($fly_flap == -1) {
			// it wasn't possible from this point
			$points_seen[$x][$y] = -1;
			return -1;
		} else {
			// it was only possible with flap
			$points_seen[$x][$y] = $fly_flap + 1;
			return $fly_flap + 1;
		}
	} else {
		if($fly_flap == -1) {
			// it was only possible without flap
			$points_seen[$x][$y] = $fly_unflap;
			return $fly_unflap;
		} else {
			// it was possible both with and without flap
			$points_seen[$x][$y] = min($fly_unflap, $fly_flap + 1);
			return min($fly_unflap, $fly_flap + 1);
		}
	}
}

function prepare_flight_old($data_big) {
	global $points_seen;
	global $map_w, $map_h;
	global $best_route, $best_route_flaps;

	$data = $data_big[0];
	$map_w = $data_big[1] + 1;
	$map_h = $data_big[2] + 1;

	// make map
	// first make empty map
	for($i=0;$i<$map_w;$i++) {
		$map[$i] = array_fill(0, $map_h, ".");
	}

	// add own position
	$map[0][0] = "S";

	// add walls
	foreach($data as $wall) {
		$x = $wall[0];
		if($map[$x][$map_h - 1] == ".") {
			// first create a solid wall
			for($j=0;$j<=$map_h;$j++) {
				$map[$x][$j] = "#";
			}
		}

		// add the hole	
		$y_begin = $wall[1];
		$y_end = $wall[1] + $wall[2];
		for($j=$y_begin;$j<$y_end;$j++) {
			$map[$x][$j] = ".";
		}
	}

	$best_route_flaps = 100000;
	$best_route = "";
	$points_seen = array();
	$tmp = fly_map($map, 0, 0, 0, "");
	
	return $tmp;
}

function prepare_flight($data_big) {
	$data = $data_big[0];
	$map_w = $data_big[1] + 1;
	$map_h = $data_big[2] + 1;

	// make list of interesting points
	// these are points in the holes
	// the lowest and highest possible points
	// begin with the starting point
	$points[0][0] = 1;
	foreach($data as $hole) {
		$x = $hole[0];
		$y1 = $hole[1];
		if(($x + $y1) % 2 == 1) {
			$y1++;
		}
		$points[$x][$y1] = 1;
		$y2 = $hole[1] + $hole[2] - 1;
		if(($x + $y2) % 2 == 1) {
			$y2--;
		}
		for($y=$y1+2;$y<$y2;$y+=2) {
			$points[$x][$y] = 1;
		}
		$points[$x][$y2] = 1;
	}

	// measure routes from every column of points
	// to every next column of points

	foreach($points as $x2 => $column) {
		if($x2 == 0) {
			$x1 = $x2;
			continue;
		}
		
		foreach($column as $y2 => $point2) {
			foreach($points[$x1] as $y1 => $prev_column) {
				$x_diff = $x2 - $x1;
				$y_diff = $y2 - $y1;
				if($x_diff < abs($y_diff)) {
					// this is impossible
					$distance[$x1][$y1][$x2][$y2] = -1;
					continue;
				}
				if(0 <= $y_diff) {
					$flaps = $y_diff + ($x_diff - $y_diff) / 2;
					$distance[$x1][$y1][$x2][$y2] = $flaps;
				} else {
					$flaps = ($x_diff + $y_diff) / 2;
					$distance[$x1][$y1][$x2][$y2] = $flaps;
				}
				
			}
		}
		
		$x1 = $x2;
	}

	// find shortest route
	foreach($points as $x2 => $column) {
		if($x2 == 0) {
			$all_dist[0][0] = 0;
			$x1 = $x2;
			continue;
		}
		
		foreach($column as $y2 => $point2) {
			$min_dist = 10000000000000;
			foreach($points[$x1] as $y1 => $prev_column) {
				$dist_sofar = $all_dist[$x1][$y1];
				$next_dist = $distance[$x1][$y1][$x2][$y2];
				if($next_dist == -1) {
					continue;
				}
				$poss_new_dist = $dist_sofar + $next_dist;
				if($poss_new_dist < $min_dist) {
					$min_dist = $poss_new_dist;
				}
			}
			$all_dist[$x2][$y2] = $min_dist;
		}
		$x1 = $x2;
	}

	$tmp = min($all_dist[$x1]);
	return $tmp;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2025_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data_big = get_input($input);

$tmp = prepare_flight($data_big);

printf("Result 1: %d\n", $tmp);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2025_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data_big = get_input($input);

$tmp = prepare_flight($data_big);

printf("Result 2: %d\n", $tmp);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2025_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data_big = get_input($input);

$tmp = prepare_flight($data_big);

printf("Result 3: %d\n", $tmp);

?>
