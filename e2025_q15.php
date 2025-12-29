<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "15";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$data = explode(",", $line);
			foreach($data as $dir) {
				$data2[] = array($dir[0], substr($dir, 1));
			}
		}
	}
	return $data2;
}

function measure_map($map) {
	$map_x_min = 0;
	$map_x_max = 0;
	$map_y_min = 0;
	$map_y_max = 0;
	
	foreach($map as $row => $col) {
		if($row < $map_x_min) {
			$map_x_min = $row;
		}
		if($map_x_max < $row) {
			$map_x_max = $row;
		}
		if(min(array_keys($col)) < $map_y_min) {
			$map_y_min = min(array_keys($col));
		}
		if($map_y_max < max(array_keys($col))) {
			$map_y_max = max(array_keys($col));
		}
	}
	return array($map_x_min, $map_x_max, $map_y_min, $map_y_max);
}

function fill_map($map, $char, $degree) {
	global $data_x, $data_y;
	
	$map_measured = measure_map($map);
	$map_x_min = $map_measured[0];
	$map_x_max = $map_measured[1];
	$map_y_min = $map_measured[2];
	$map_y_max = $map_measured[3];
	
	if(strcmp($degree, "full") == 0) {
		for($j=$map_y_min;$j<=$map_y_max;$j++) {
			for($i=$map_x_min;$i<=$map_x_max;$i++) {
				if(!isset($map[$i][$j])) {
					$map[$i][$j] = $char;
				}
			}
		}
	} else { // sparse
		foreach($data_x as $i) {
			foreach($data_y as $j) {
				if(!isset($map[$i][$j])) {
					$map[$i][$j] = $char;
				}
			}
		}
	}
	return $map;
}

function print_map($map) {
	$map_measured = measure_map($map);
	$map_x_min = $map_measured[0];
	$map_x_max = $map_measured[1];
	$map_y_min = $map_measured[2];
	$map_y_max = $map_measured[3];
	
	printf("Filling map\n");

	for($j=$map_y_min;$j<=$map_y_max;$j++) {
		for($i=$map_x_min;$i<=$map_x_max;$i++) {
			echo $map[$i][$j];
		}
		echo "\n";
	}
	for($i=$map_x_min;$i<=$map_x_max;$i++) {
		echo "=";
	}
	echo "\n";
}

function create_map($data, $degree) {
	global $data_x, $data_y;
	$x_begin = 0;
	$y_begin = 0;
	$x = $x_begin;
	$y = $y_begin;
	$map[$x][$y] = "S";
	$progress = 0;
	
	printf("Creating map - %s\n", $degree);

	$my_dir_arr = array("u", "r", "d", "l");
	$my_dir_no = 0;
	$my_dir = $my_dir_arr[$my_dir_no];
	foreach($data as $dir) {
		if($dir[0] == "L") {
			$my_dir_no = ($my_dir_no + 4 - 1) % 4;
			$my_dir = $my_dir_arr[$my_dir_no];
		} else {
			$my_dir_no = ($my_dir_no + 1) % 4;
			$my_dir = $my_dir_arr[$my_dir_no];
		}
		
		if(strcmp($degree, "full") == 0) {
			// corners and edges
			switch($my_dir) {
				case "u":
					for($i=0;$i<$dir[1];$i++) {
						$y--;
						$map[$x][$y] = "#";
					}
					break;
				case "d":
					for($i=0;$i<$dir[1];$i++) {
						$y++;
						$map[$x][$y] = "#";
					}
					break;
				case "l":
					for($i=0;$i<$dir[1];$i++) {
						$x--;
						$map[$x][$y] = "#";
					}
					break;
				case "r":
					for($i=0;$i<$dir[1];$i++) {
						$x++;
						$map[$x][$y] = "#";
					}
					break;
			}
		} else {
			if(strcmp($degree, "sparse") == 0) {
				// only the corners
				switch($my_dir) {
					case "u":
						$y -= $dir[1];
						$map[$x][$y] = "#";
						break;
					case "d":
						$y += $dir[1];
						$map[$x][$y] = "#";
						break;
					case "l":
						$x -= $dir[1];
						$map[$x][$y] = "#";
						break;
					case "r":
						$x += $dir[1];
						$map[$x][$y] = "#";
						break;
				}
			} else { // semisparse
				// corners and some of the edges
				switch($my_dir) {
					case "u":
						$old_y = $y;
						$key_of_y = array_search($y, $data_y);
						while($old_y - $dir[1] < $y) {
							$key_of_up = $key_of_y - 1;
							$y = $data_y[$key_of_up];
							$map[$x][$y] = "#";
							$key_of_y = array_search($y, $data_y);
						}
						break;
					case "d":
						$old_y = $y;
						$key_of_y = array_search($y, $data_y);
						while($y < $old_y + $dir[1]) {
							$key_of_down = $key_of_y + 1;
							$y = $data_y[$key_of_down];
							$map[$x][$y] = "#";
							$key_of_y = array_search($y, $data_y);
						}
						break;
					case "l":
						$old_x = $x;
						$key_of_x = array_search($x, $data_x);
						while($old_x - $dir[1] < $x) {
							$key_of_left = $key_of_x - 1;
							$x = $data_x[$key_of_left];
							$map[$x][$y] = "#";
							$key_of_x = array_search($x, $data_x);
						}
						break;
					case "r":
						$old_x = $x;
						$key_of_x = array_search($x, $data_x);
						while($x < $old_x + $dir[1]) {
							$key_of_right = $key_of_x + 1;
							$x = $data_x[$key_of_right];
							$map[$x][$y] = "#";
							$key_of_x = array_search($x, $data_x);
						}
						break;
				}
			}
		}
	}
	$map[$x][$y] = "E";
	return $map;
}

function find($map, $map_measured, $char) {
	$map_x_min = $map_measured[0];
	$map_x_max = $map_measured[1];
	$map_y_min = $map_measured[2];
	$map_y_max = $map_measured[3];

	foreach($map as $i => $col) {
		foreach($col as $j => $p) {
			if($p == $char) {
				return array($i,$j);
			}
		}
	}
}

function dijkstra($map, $degree) {
	global $data_x, $data_y;
	
	$really_big_number = 10000000000;
	
	$map_measured = measure_map($map);
	$map_x_min = $map_measured[0];
	$map_x_max = $map_measured[1];
	$map_y_min = $map_measured[2];
	$map_y_max = $map_measured[3];

	$org = find($map, $map_measured, "S");
	$orgx = $org[0];
	$orgy = $org[1];
	global $visit;

	print("\nSize of map:\n");
	printf("x, min/max, %3d/%3d - y, min/max, %3d/%3d\n", $map_x_min, $map_x_max, $map_y_min, $map_y_max);
	
	$unvis[] = array($orgx, $orgy, 0, array());

	// Create a set of all unvisited nodes: the unvisited set.
	// Assign to every node a distance from start.
	if(strcmp($degree, "full") == 0) {
		for($j=$map_y_min;$j<=$map_y_max;$j++) {
			for($i=$map_x_min;$i<=$map_x_max;$i++) {
				if($map[$i][$j] == "." || $map[$i][$j] == "E") {
					$unvis[] = array($i, $j, $really_big_number, array());
				}
			}
		}
	} else { // sparse
		foreach($data_x as $i) {
			foreach($data_y as $j) {
				if($map[$i][$j] == "." || $map[$i][$j] == "E") {
					$unvis[] = array($i, $j, $really_big_number, array());
				}
			}
		}
	}

	printf("No of nodes on map: %d\n", sizeof($unvis));

	$wearedone = 0;
	$progress = 0;
	while($wearedone == 0) {
		$progress++;
		if($progress % 100 == 0) {
			print(".");
		}
		//From the unvisited set, select the current node to be the one with the smallest (finite) distance
		$dist = $really_big_number;
		foreach($unvis as $key => $node) {
			if($node[2] < $dist) {
				$dist = $node[2];
				$x = $node[0];
				$y = $node[1];
				$mykey = $key;
			}
		}

		// For the current node, consider all of its unvisited neighbors and update their distances through the current node
		if(strcmp($degree, "full") == 0) {
			foreach($unvis as $key => $node) {
				$neighb = abs($x - $node[0]) + abs($y - $node[1]);
				if($neighb == 1) {
					if($x - $node[0] == 1) {
						// neighbour to the left
						$newdist = $dist + 1;
						if($newdist < $node[2]) {
							$unvis[$key][2] = $newdist;
							$unvis[$key][3] = array($x, $y);
						}
					}
					if($x - $node[0] == -1) {
						// neighbour to the right
						$newdist = $dist + 1;
						if($newdist < $node[2]) {
							$unvis[$key][2] = $newdist;
							$unvis[$key][3] = array($x, $y);
						}
					}
					if($y - $node[1] == 1) {
						// neighbour above
						$newdist = $dist + 1;
						if($newdist < $node[2]) {
							$unvis[$key][2] = $newdist;
							$unvis[$key][3] = array($x, $y);
						}
					}
					if($y - $node[1] == -1) {
						// neighbour below
						$newdist = $dist + 1;
						if($newdist < $node[2]) {
							$unvis[$key][2] = $newdist;
							$unvis[$key][3] = array($x, $y);
						}
					}
				}
			}
		} else { // sparse
			// i am at (x,y)
			$key_of_x = array_search($x, $data_x);
			if(0 < $key_of_x) {
				$key_of_left = $key_of_x - 1;
				$x_left = $data_x[$key_of_left];
				foreach($unvis as $key => $node) {
					if($node[0] == $x_left && $node[1] == $y) {
						$newdist = $dist + abs($x - $x_left);
						if($newdist < $node[2]) {
							$unvis[$key][2] = $newdist;
							$unvis[$key][3] = array($x, $y);
						}
						break;
					}
				}
			}
			if($key_of_x < sizeof($data_x) - 1) {
				$key_of_right = $key_of_x + 1;
				$x_right = $data_x[$key_of_right];
				foreach($unvis as $key => $node) {
					if($node[0] == $x_right && $node[1] == $y) {
						$newdist = $dist + abs($x - $x_right);
						if($newdist < $node[2]) {
							$unvis[$key][2] = $newdist;
							$unvis[$key][3] = array($x, $y);
						}
						break;
					}
				}
			}
			$key_of_y = array_search($y, $data_y);
			if(0 < $key_of_y) {
				$key_of_up = $key_of_y - 1;
				$y_up = $data_y[$key_of_up];
				foreach($unvis as $key => $node) {
					if($node[0] == $x && $node[1] == $y_up) {
						$newdist = $dist + abs($y - $y_up);
						if($newdist < $node[2]) {
							$unvis[$key][2] = $newdist;
							$unvis[$key][3] = array($x, $y);
						}
						break;
					}
				}
			}
			if($key_of_y < sizeof($data_y) - 1) {
				$key_of_down = $key_of_y + 1;
				$y_down = $data_y[$key_of_down];
				foreach($unvis as $key => $node) {
					if($node[0] == $x && $node[1] == $y_down) {
						$newdist = $dist + abs($y - $y_down);
						if($newdist < $node[2]) {
							$unvis[$key][2] = $newdist;
							$unvis[$key][3] = array($x, $y);
						}
						break;
					}
				}
			}
		}
		
		// the current node is removed from the unvisited set
		$visit[$x][$y][] = $unvis[$mykey];
		unset($unvis[$mykey]);
		if($map[$x][$y] == "E") {
			return $dist;
		}
	}
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

echo "The time is " . date("h:i:sa" . "\n");

$file1 = './everybody_codes_e2025_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$map = create_map($data, "full");
$map = fill_map($map, ".", "full");

$best = dijkstra($map, "full");

printf("\nResult 1: %d\n\n", $best);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

echo "The time is " . date("h:i:sa" . "\n");

$file2 = './everybody_codes_e2025_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$map = create_map($data, "sparse");

printf("Make list of x and y actually used, including neighbs\n");

// make list of x and y actually used, including neighbs
foreach($map as $x => $col) {
	foreach($col as $y => $p) {
		$data_x[$x-1] = $x-1;
		$data_x[$x] = $x;
		$data_x[$x+1] = $x+1;
		$data_y[$y-1] = $y-1;
		$data_y[$y] = $y;
		$data_y[$y+1] = $y+1;
	}
}
sort($data_x);
sort($data_y);

$map = create_map($data, "semisparse");
$map = fill_map($map, ".", "sparse");

$best = dijkstra($map, "sparse");

printf("\nResult 2: %d\n\n", $best);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

echo "The time is " . date("h:i:sa" . "\n");

$file3 = './everybody_codes_e2025_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);
printf("Number of corners: %d\n", sizeof($data));

$map = create_map($data, "sparse");

// make list of x and y actually used, including neighbs
$data_x = [];
$data_y = [];
foreach($map as $x => $col) {
	foreach($col as $y => $p) {
		$data_x[$x-1] = $x-1;
		$data_x[$x] = $x;
		$data_x[$x+1] = $x+1;
		$data_y[$y-1] = $y-1;
		$data_y[$y] = $y;
		$data_y[$y+1] = $y+1;
	}
}
sort($data_x);
sort($data_y);

$map = create_map($data, "semisparse");
$map = fill_map($map, ".", "sparse");

$best = dijkstra($map, "sparse");

printf("\nResult 3: %d\n\n", $best);

echo "The time is " . date("h:i:sa" . "\n");

?>
