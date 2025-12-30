<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "17";

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
	global $map_w, $map_h;
	for($j=0;$j<$map_h;$j++) {
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

// https://www.programmingalgorithms.com/algorithm/dijkstra's-algorithm/php/

$INT_MAX = 0x7FFFFFFF;

function MinimumDistance($distance, $shortestPathTreeSet, $verticesCount)
{
	global $INT_MAX;
	$min = $INT_MAX;
	$minIndex = 0;

	for ($v = 0; $v < $verticesCount; ++$v)
	{
		if ($shortestPathTreeSet[$v] == false && $distance[$v] <= $min)
		{
			$min = $distance[$v];
			$minIndex = $v;
		}
	}

	return $minIndex;
}

function Dijkstra($graph, $source, $verticesCount)
{
	global $INT_MAX;
	$distance = array();
	$shortestPathTreeSet = array();

	for ($i = 0; $i < $verticesCount; ++$i)
	{
		$distance[$i] = $INT_MAX;
		$shortestPathTreeSet[$i] = false;
	}

	$distance[$source] = 0;

	for ($count = 0; $count < $verticesCount - 1; ++$count)
	{

		if($count % 1000 == 0) { print("."); }

		$u = MinimumDistance($distance, $shortestPathTreeSet, $verticesCount);
		$shortestPathTreeSet[$u] = true;

		for ($v = 0; $v < $verticesCount; ++$v)
			if (!$shortestPathTreeSet[$v] && $graph[$u][$v] && $distance[$u]
			!= $INT_MAX && $distance[$u] + $graph[$u][$v] < $distance[$v])
				$distance[$v] = $distance[$u] + $graph[$u][$v];
	}

	return $distance;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2025_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$data = pivot($data);
$map_w = sizeof($data);
$map_h = sizeof($data[0]);

$r = 10; // the radius the volcano reaches

// find @
for($i=0;$i<$map_w;$i++) {
	$j = array_search("@", $data[$i]);
	if($j !== false) {
		$S_x = $i;
		$S_y = $j;
		$data[$i][$j] = 0;
		break;
	}
}

// (Xv - Xc) * (Xv - Xc) + (Yv - Yc) * (Yv - Yc) <= R * R
// calculate distances and change map
for($i=0;$i<$map_w;$i++) {
	for($j=0;$j<$map_h;$j++) {
		$dist2 = ($i - $S_x) * ($i - $S_x) + ($j - $S_y) * ($j - $S_y);
		if($r * $r < $dist2) {
			$data[$i][$j] = 0; // cell removed
		}
	}
}

// sum of cells destroyed
$result = array_sum(array_merge(...$data));

printf("Result 1: %d\n", $result);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2025_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$data = pivot($data);
$map_w = sizeof($data);
$map_h = sizeof($data[0]);

// find @
for($i=0;$i<$map_w;$i++) {
	$j = array_search("@", $data[$i]);
	if($j !== false) {
		$S_x = $i;
		$S_y = $j;
		$data[$i][$j] = 0;
		break;
	}
}

$max_r = min($S_x, $S_y);
$steps = array_fill(0, $max_r + 1, 0);

// (Xv - Xc) * (Xv - Xc) + (Yv - Yc) * (Yv - Yc) <= R * R
// calculate distances
for($i=0;$i<$map_w;$i++) {
	for($j=0;$j<$map_h;$j++) {
		$dist2 = ($i - $S_x) * ($i - $S_x) + ($j - $S_y) * ($j - $S_y);
		$r = ceil(pow($dist2, 0.5));
		if($r <= $max_r) {
			// sum of cell values for this r
			$steps[$r] += $data[$i][$j];
		}
	}
}

// find most destructive distance
$dest_dist = array_search(max($steps), $steps);
$result = $dest_dist * $steps[$dest_dist];

printf("Result 2: %d\n", $result);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2025_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = pivot(get_input($input));
$map_w = sizeof($data);
$map_h = sizeof($data[0]);

// find S
for($i=0;$i<$map_w;$i++) {
	$j = array_search("S", $data[$i]);
	if($j !== false) {
		$S_x = $i;
		$S_y = $j;
		break;
	}
}

// find @
for($i=0;$i<$map_w;$i++) {
	$j = array_search("@", $data[$i]);
	if($j !== false) {
		$at_x = $i;
		$at_y = $j;
		break;
	}
}

///////////////////////////////////////////////////////////////////////////
// thoughts
// S is above the volcano
// below the volcano are a line consisting of a, b, c, ...
// find 2 shortest paths clockwise (*) and counterclockwise from S to a
// and S to b
// and S to c
// (*) clockwise: can't go through line between volcano and left edge
// what is the smallest sum?
// is this sum small enough?
// if not, then grow the volcano and repeat
///////////////////////////////////////////////////////////////////////////

// after each 30 seconds, the volcano will grow
$seconds = 0;
$radius = 0;
$big_number = 1000000;

$min_dist = $big_number;
while($seconds <= $min_dist) {
	// at this second, the radius of the volcano will grow
	$seconds = ($radius + 1) * 30;

	print("Seconds = $seconds\n");

	print("Volcano grows\n");

	// let volcano grow
	// (Xv - Xc) * (Xv - Xc) + (Yv - Yc) * (Yv - Yc) <= R * R
	// calculate distances and change map
	for($i=0;$i<$map_w;$i++) {
		for($j=0;$j<$map_h;$j++) {
			$dist2 = ($i - $at_x) * ($i - $at_x) + ($j - $at_y) * ($j - $at_y);
			if($dist2 <= $radius * $radius) {
				$data[$i][$j] = "@"; // more volcano
			}
		}
	}

	print("Map converts\n");

	// convert map to Dijkstra format
	// how many cells?
	$no_of_cells = $map_w * $map_h;
	// id for cell: $map_h * x + y
	$graph_sub = array_fill(0, $no_of_cells, $big_number);
	$graph = array_fill(0, $no_of_cells, $graph_sub);
	for($i=0;$i<$map_w;$i++) {
		if($i % 10 == 0) { print("*"); }
		for($j=0;$j<$map_h;$j++) {
			$my_value = $data[$i][$j];
			if($my_value == "S" || $my_value == "@") {
				continue;
			}
			$my_id = $map_h * $i + $j;
			if(0 < $i) {
				$neighb_id = $map_h * ($i - 1) + $j;
				$graph[$neighb_id][$my_id] = $my_value;
			}
			if($i < $map_w - 1) {
				$neighb_id = $map_h * ($i + 1) + $j;
				$graph[$neighb_id][$my_id] = $my_value;
			}
			if(0 < $j) {
				$neighb_id = $map_h * $i + $j - 1;
				$graph[$neighb_id][$my_id] = $my_value;
			}
			if($j < $map_h - 1) {
				$neighb_id = $map_h * $i + $j + 1;
				$graph[$neighb_id][$my_id] = $my_value;
			}
		}
	}
	
	$S_id = $map_h * $S_x + $S_y;
	
	// force the paths to go clockwise by removing a bit of the left
	$graph_clock = $graph;
	$j = $at_y;
	for($i=0;$i<$at_x;$i++) {
		$my_id = $map_h * $i + $j;
		if(0 < $i) {
			$neighb_id = $map_h * ($i - 1) + $j;
			$graph_clock[$neighb_id][$my_id] = $big_number;
		}
		if($i < $map_w - 1) {
			$neighb_id = $map_h * ($i + 1) + $j;
			$graph_clock[$neighb_id][$my_id] = $big_number;
		}
		if(0 < $j) {
			$neighb_id = $map_h * $i + $j - 1;
			$graph_clock[$neighb_id][$my_id] = $big_number;
		}
		if($j < $map_h - 1) {
			$neighb_id = $map_h * $i + $j + 1;
			$graph_clock[$neighb_id][$my_id] = $big_number;
		}
	}
	print("\nDijkstra clock\n");
	$distance_clock = Dijkstra($graph_clock, $S_id, $no_of_cells);
	$graph_clock = [];

	// force the paths to go clockwise by removing a bit of the right
	$graph_counter = $graph;
	$j = $at_y;
	for($i=$at_x+1;$i<$map_w;$i++) {
		$my_id = $map_h * $i + $j;
		if(0 < $i) {
			$neighb_id = $map_h * ($i - 1) + $j;
			$graph_counter[$neighb_id][$my_id] = $big_number;
		}
		if($i < $map_w - 1) {
			$neighb_id = $map_h * ($i + 1) + $j;
			$graph_counter[$neighb_id][$my_id] = $big_number;
		}
		if(0 < $j) {
			$neighb_id = $map_h * $i + $j - 1;
			$graph_counter[$neighb_id][$my_id] = $big_number;
		}
		if($j < $map_h - 1) {
			$neighb_id = $map_h * $i + $j + 1;
			$graph_counter[$neighb_id][$my_id] = $big_number;
		}
	}
	print("\nDijkstra counter clock\n");
	$distance_counter = Dijkstra($graph_counter, $S_id, $no_of_cells);
	$graph_counter = [];
	$graph = [];
	
	$i = $at_x;
	$min_dist = $big_number;
	for($j=$at_y+1;$j<$map_h;$j++) {
		if($data[$i][$j] == "@") {
			continue;
		}
		$id = $map_h * $i + $j;
		// distance: from S to id, 2 ways, not counting id twice
		$dist = $distance_clock[$id] + $distance_counter[$id] - $data[$i][$j];
		if($dist < $min_dist) {
			$min_dist = $dist;
		}
	}
	printf("\nSeconds: %3d -- Lowest distance: %3d\n", $seconds, $min_dist);
	$distance_clock = [];
	$distance_counter = [];
	if($min_dist < $seconds) {
		break;
	}
	
	$radius = floor($min_dist / 30);
}

printf("Result 3: %d\n", $radius * $min_dist);

?>
