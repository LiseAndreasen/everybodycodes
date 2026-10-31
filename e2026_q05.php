<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "05";
$turn_into_land = 5;
$turn_into_sand = 5;
$turn_into_tree1 = 20;		// 5x5 - 1 middle - 4 corners
$turn_into_tree2 = 4;
$land = "#";
$land_or_dark = "#1";
$land_marker = "#";
$land_width = 1;
$water = ".";
$water_marker = ".";
$water_width = 1;
$sand = "=";
$sand_marker = "=";
$sand_width = 1;
$land_or_tree = "#@";
$tree = "@";
$tree_marker = "@";
$tree_width1 = 2;
$tree_width2 = 1;
$growth_steps = 2;

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

// change (y, x) map to (x, y) map
function pivot($map) {
	$map_height = sizeof($map);
	$map_width = sizeof($map[0]);
	for($j=0;$j<$map_height;$j++) {
		for($i=0;$i<$map_width;$i++) {
			$map2[$i][$j] = $map[$j][$i];
		}
	}
	return $map2;
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

// add extra space around the map
function fillup($map) {
	global $width, $height, $water_marker;
	for($i=-1;$i<=$width;$i++) {
		$map[$i][-1] = $water_marker;
		$map[$i][$height] = $water_marker;
	}
	for($j=-1;$j<=$height;$j++) {
		$map[-1][$j] = $water_marker;
		$map[$width][$j] = $water_marker;
	}
	// sort
	ksort($map);
	foreach($map as $m => $line) {
		ksort($map[$m]);
	}
	// reindex
	$map = array_values($map);
	$map = array_map("array_values", $map);
	return $map;
}

// count neighbors in the good list
function count_neigh($x, $y, $map, $good, $w) {
	global $width, $height;
	$neighs = 0;
	$good_arr = str_split($good);
	for($i=max(0, $x-$w);$i<=min($x+$w, $width-1);$i++) {
		for($j=max(0, $y-$w);$j<=min($y+$w, $height-1);$j++) {
			// don't look at mid cell
			if($i == $x && $j == $y) {
				continue;
			}
			// don't count the corners
			if(abs($x-$i) == 2 && abs($y-$j) == 2) {
				continue;
			}
			if(in_array($map[$i][$j], $good_arr)) {
				$neighs++;
			}
		}
	}
	return $neighs;
}

function grow_land($map) {
	global $width, $height,
		$land_marker, $land, $land_or_dark, $turn_into_land, $land_width;

	// find middle cell
	$x = ($width - 1) / 2;
	$y = ($height - 1) / 2;

	$map[$x][$y] = $land_marker;

	$p = 0;
	// assume map will always change a little
	$map_changed = true;
	while($map_changed) {
		if($p % 10 == 0) {
			print("L");					// progress
		}
		$p++;
		$new_map = $map;
		$map_changed = false;
		for($i=0;$i<$width;$i++) {
			for($j=0;$j<$height;$j++) {
				if($map[$i][$j] == $land_marker) {
					continue;
				}
				$land_locked = count_neigh($i, $j, $map, $land, $land_width);
				if(1 <= $land_locked) {
					$neighs = count_neigh($i, $j, $map, $land_or_dark, $land_width);
					if($turn_into_land <= $neighs) {
						$new_map[$i][$j] = $land_marker;
						$map_changed = true;
					}
				}
			}
		}
		$map = $new_map;
	}
	return $map;
}

function grow_sand($map) {
	global $width, $height, $land_marker,
		$water, $water_marker, $sand, $sand_marker, $turn_into_sand,
		$water_width, $sand_width;

	$p = 0;
	// change into water
	for($i=0;$i<$width;$i++) {
		for($j=0;$j<$height;$j++) {
			if($map[$i][$j] != $land_marker) {
				$map[$i][$j] = $water_marker;
			}
		}
	}
	
	// change edges into sand
	for($i=0;$i<$width;$i++) {
		for($j=0;$j<$height;$j++) {
			if($map[$i][$j] == $water_marker) {
				continue;
			}
			$edge = count_neigh($i, $j, $map, $water, $water_width);
			if(1 <= $edge) {
				$map[$i][$j] = $sand_marker;
			}
		}
	}

	// assume map will always change a little
	$map_changed = true;
	while($map_changed) {
		if($p % 10 == 0) {
			print("S");					// progress
		}
		$p++;
		$new_map = $map;
		$map_changed = false;
		for($i=0;$i<$width;$i++) {
			for($j=0;$j<$height;$j++) {
				if($map[$i][$j] == $sand_marker) {
					continue;
				}
				$sand_locked = count_neigh($i, $j, $map, $sand, $sand_width);
				if($turn_into_sand <= $sand_locked) {
					$new_map[$i][$j] = $sand_marker;
					$map_changed = true;
				}
			}
		}
		$map = $new_map;
	}
	return $map;
}

function grow_trees($map) {
	global $width, $height, $land_marker, $tree_width1, $land_or_tree,
		$tree_marker, $turn_into_tree1, $growth_steps, $tree_width2, $tree,
		$turn_into_tree2, $sand;

	$tw = $tree_width1;
	for($i=0+$tw;$i<$width-$tw;$i++) {
		for($j=0+$tw;$j<$height-$tw;$j++) {
			if($map[$i][$j] == $land_marker) {
				$lands = count_neigh($i, $j, $map, $land_or_tree, $tree_width1);
				if($lands == $turn_into_tree1) {
					$map[$i][$j] = $tree_marker;
				}
			}
		}
	}

	for($gs=0;$gs<$growth_steps;$gs++) {
		$new_map = $map;
		for($i=0;$i<$width;$i++) {
			for($j=0;$j<$height;$j++) {
				if($map[$i][$j] != $land_marker) {
					continue;
				}
				$trees_enough = count_neigh($i, $j, $map, $tree, $tree_width2);
				$no_sand = count_neigh($i, $j, $map, $sand, $tree_width2);
				if($turn_into_tree2 <= $trees_enough && 0 == $no_sand) {
					$new_map[$i][$j] = $tree_marker;
				}
			}
		}
		$map = $new_map;
	}

	return $map;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$data = pivot($data);

$width = sizeof($data);
$height = sizeof($data[0]);

$data = grow_land($data);

// count occurrences in 2d array
$data2 = array_merge(...$data);
$data3 = array_count_values($data2);
$no = $data3[$land_marker];

printf("\nResult 1: %d\n", $no);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$data = pivot($data);

$width = sizeof($data);
$height = sizeof($data[0]);

$data = grow_land($data);

$data = fillup($data);

$width = sizeof($data);
$height = sizeof($data[0]);

$data = grow_sand($data);

// count occurrences in 2d array
$data2 = array_merge(...$data);
$data3 = array_count_values($data2);
$no = $data3[$sand_marker];

printf("\nResult 2: %d\n", $no);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$data = pivot($data);

$width = sizeof($data);
$height = sizeof($data[0]);

$data = grow_land($data);

$data = fillup($data);

$width = sizeof($data);
$height = sizeof($data[0]);

$data = grow_sand($data);

$data = grow_trees($data);

// count occurrences in 2d array
$data2 = array_merge(...$data);
$data3 = array_count_values($data2);
$no = $data3[$tree_marker];

printf("\nResult 3: %d\n", $no);

?>
