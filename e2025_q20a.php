<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "20";

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
	$map_h = sizeof($map[0]);
	$map_w = sizeof($map);
	for($i=0;$i<$map_w;$i++) {
		for($j=0;$j<$map_h;$j++) {
			echo $map[$i][$j];
		}
		echo "\n";
	}
	for($i=0;$i<$map_h;$i++) {
		echo "=";
	}
	echo "\n";
}

function print_map_coord($map) {
	$map_w = sizeof($map);
	$map_h = sizeof($map[$map_w/2]);
	for($j=0;$j<$map_h;$j++) {
		for($i=0;$i<$map_w;$i++) {
			if(isset($map[$i][$j])) {
				printf("(%2d,%2d) ", $map[$i][$j][0], $map[$i][$j][1]);
			} else {
				print("        ");
			}
		}
		echo "\n";
	}
	for($i=0;$i<$map_h;$i++) {
		echo "=";
	}
	echo "\n";
}

function dijkstra($map, $rotation) {
	global $orgx, $orgy, $maphei, $mapwid;
	global $jump_table;
	
	$unvis[$orgx][$orgy] = array(0, array());

	// Create a set of all unvisited nodes: the unvisited set.
	// Assign to every node a distance from start.
	for($x=0;$x<$mapwid;$x++) {
		for($y=0;$y<$maphei;$y++) {
			if($map[$y][$x] == "T" || $map[$y][$x] == "E") {
				$unvis[$x][$y] = array(1000000, array());
			}
		}
	}

	$wearedone = 0;
	while($wearedone == 0) {

		//From the unvisited set, select the current node to be the one with the smallest (finite) distance
		$dist = 1000000;
		$new_node_found = 0;
		foreach($unvis as $newx => $column) {
			foreach($column as $newy => $cell) {
				if($cell[0] < $dist) {
					$dist = $cell[0];
					$x = $newx;
					$y = $newy;
					$new_node_found = 1;
				}
			}
		}
		if($new_node_found == 0) {
			print("Ended without solution.\n");
			return -1;
		}
		
		$old_x = $x;
		$old_y = $y;

		if($rotation == "r") {
			// this is where the jump occurs
			$new = $jump_table[$x][$y];
			$x = $new[0];
			$y = $new[1];
		}

		// For the current node, consider all of its unvisited neighbors and update their distances through the current node

		$newdist = $dist + 1;
		
		if(isset($unvis[$x+1][$y])) {
			// neighb to the right
			if($newdist < $unvis[$x+1][$y][0]) {
				$unvis[$x+1][$y][0] = $newdist;
				$unvis[$x+1][$y][1] = array($old_x, $old_y);
			}
		}
		if(isset($unvis[$x-1][$y])) {
			// neighb to the left
			if($newdist < $unvis[$x-1][$y][0]) {
				$unvis[$x-1][$y][0] = $newdist;
				$unvis[$x-1][$y][1] = array($old_x, $old_y);
			}
		}
		if(($x + $y) % 2 == 1 && isset($unvis[$x][$y+1])) {
			// neighb below
			if($newdist < $unvis[$x][$y+1][0]) {
				$unvis[$x][$y+1][0] = $newdist;
				$unvis[$x][$y+1][1] = array($old_x, $old_y);
			}
		}
		if(($x + $y) % 2 == 0 && isset($unvis[$x][$y-1])) {
			// neighb above
			if($newdist < $unvis[$x][$y-1][0]) {
				$unvis[$x][$y-1][0] = $newdist;
				$unvis[$x][$y-1][1] = array($old_x, $old_y);
			}
		}
	
		// the current node is removed from the unvisited set
		$visit[$old_x][$old_y] = $unvis[$old_x][$old_y];
		unset($unvis[$old_x][$old_y]);
		if($map[$y][$x] == "E") {
			return $dist;
		}
	}
}

function find($map, $char) {
	global $mapwid, $maphei;
	for($x=0;$x<$mapwid;$x++) {
		for($y=0;$y<$maphei;$y++) {
			if($map[$y][$x] == $char) {
				return array($x,$y);
			}
		}
	}
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2025_q' . $quest . '_p1_ex1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

// count trampoline pairs
$pairs = 0;
foreach($data as $y => $line) {
	foreach($line as $x => $cell) {
		if($cell == "T" && isset($line[$x+1]) && $line[$x+1] == "T") {
			// the cell to the right also has a trampoline
			$pairs++;
		}
		if($cell == "T" && ($x + $y) % 2 == 1 && isset($data[$y+1]) && $data[$y+1][$x] == "T") {
			// the cell below also has a trampoline
			$pairs++;
		}
	}
}

printf("Result 1: %d\n", $pairs);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2025_q' . $quest . '_p2_ex1.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$maphei = sizeof($data);
$mapwid = sizeof($data[0]);

$coords = find($data, "S");
$orgx = $coords[0];
$orgy = $coords[1];

$best = dijkstra($data, "s");

printf("Result 2: %d\n", $best);

?>
