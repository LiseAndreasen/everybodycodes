<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "20";
$INT_MAX = 0x7FFFFFFF;

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
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
	$map_h = sizeof($map[0]);
	$map_w = sizeof($map);
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

function Dijkstra($graph, $source, $dest, $verticesCount)
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
		$u = MinimumDistance($distance, $shortestPathTreeSet, $verticesCount);
		if($u == $dest) {
			return $distance[$u];
		}
		
		$shortestPathTreeSet[$u] = true;

		for ($v = 0; $v < $verticesCount; ++$v) {
			if (!$shortestPathTreeSet[$v] && $graph[$u][$v] && 
			$distance[$u] != $INT_MAX && 
			$distance[$u] + $graph[$u][$v] < $distance[$v]) {
				$distance[$v] = $distance[$u] + $graph[$u][$v];
			}
		}
	}
}

function find($map, $char) {
	global $map_w, $map_h;
	for($x=0;$x<$map_w;$x++) {
		for($y=0;$y<$map_h;$y++) {
			if($map[$x][$y] == $char) {
				return array($x,$y);
			}
		}
	}
}

function coord_2_id($map_h, $x, $y) {
	return $map_h * $x + $y;
}

function jump_coord($x, $y) {
	global $M;
	
	// standing at corner (0,0) looking towards center
	// x corresponds to: go x steps from corner clockwise
	// y corresponds to: go y steps from that position counterclockwise
	
	// new position:
	// standing at corner (M-1,M-1) looking towards center
	// x corresponds to:
	//		x = m + n (even: m=n, odd: m=n+1)
	// y corresponds to:
	//		y = o + p		(x even: y even, o=p; y odd, o=p+1)
	//						(x odd: y even, o=p; y odd, o+1=p)
	// x: m even to odd: new y - 1
	// x: n odd to even: new x - 1
	// y: o even to odd: new x + 2, new y - 1
	// y: p odd to even: new x + 1
	// x = M-1     - n + 2o + p
	// y = M-1 - m     -  o
	
	$mn1 = floor($x / 2);
	$mn2 = $x % 2;
	$m = $mn1 + $mn2;
	$n = $mn1;
	
	$op1 = floor($y / 2);
	$op2 = $y % 2;
	if($mn2 == 0) {
		$o = $op1 + $op2;
		$p = $op1;
	} else {
		$p = $op1 + $op2;
		$o = $op1;
	}
	
	$new_x = $M - 1 - $n + 2 * $o + $p;
	$new_y = $M - 1 - $m - $o;
	return [$new_x, $new_y];
}

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2025_q' . $quest . '_p3_ex1.txt';
$input = file_get_contents($file3, true);
$data = pivot(get_input($input));

$map_w = sizeof($data);
$map_h = sizeof($data[0]);

$coords = find($data, "S");
$orgx = $coords[0];
$orgy = $coords[1];

$no_of_cells = $map_w * $map_h;
$graph_sub = array_fill(0, $no_of_cells, $INT_MAX);
$graph = array_fill(0, $no_of_cells, $graph_sub);

// triangle is M high and 2M wide
$M = $map_h;

for($j=0;$j<$map_h;$j++) {
	for($i=0;$i<$map_w;$i++) {
		if($data[$i][$j] == "T" || $data[$i][$j] == "S") {
			$ij_id = coord_2_id($map_h, $i, $j);
			[$new_i, $new_j] = jump_coord($i, $j);
			if($data[$new_i][$new_j] == "T" || $data[$new_i][$new_j] == "S" || $data[$new_i][$new_j] == "E") {
				// simply jump in place
				$this_id = coord_2_id($map_h, $new_i, $new_j);
				$graph[$ij_id][$this_id] = 1;
			}
			if(0 < $new_i) {
				// neighb on the left
				if($data[$new_i-1][$new_j] == "T" || $data[$new_i-1][$new_j] == "S" || $data[$new_i-1][$new_j] == "E") {
					$left_id = coord_2_id($map_h, $new_i - 1, $new_j);
					$graph[$ij_id][$left_id] = 1;
				}
			}
			if($new_i < $map_w - 1) {
				// neighb on the left
				if($data[$new_i+1][$new_j] == "T" || $data[$new_i+1][$new_j] == "S" || $data[$new_i+1][$new_j] == "E") {
					$right_id = coord_2_id($map_h, $new_i + 1, $new_j);
					$graph[$ij_id][$right_id] = 1;
				}
			}
			if(($new_i + $new_j) % 2 == 0) {
				// i am even and might have a neighb above
				if(0 < $new_j) {
					if($data[$new_i][$new_j-1] == "T" || $data[$new_i][$new_j-1] == "S" || $data[$new_i][$new_j-1] == "E") {
						$above_id = coord_2_id($map_h, $new_i, $new_j - 1);
						$graph[$ij_id][$above_id] = 1;
					}
				}
			} else {
				// i am odd and might have a neighb below
				if($new_j < $map_h - 1) {
					if($data[$new_i][$new_j+1] == "T" || $data[$new_i][$new_j+1] == "S" || $data[$new_i][$new_j+1] == "E") {
						$below_id = coord_2_id($map_h, $new_i, $new_j + 1);
						$graph[$ij_id][$below_id] = 1;
					}
				}
			}
		}
	}
}

[$source_x, $source_y] = find($data, "S");
$source = coord_2_id($map_h, $source_x, $source_y);
[$dest_x, $dest_y] = find($data, "E");
$dest = coord_2_id($map_h, $dest_x, $dest_y);

$best = Dijkstra($graph, $source, $dest, $no_of_cells);

printf("Result 3: %d\n", $best);

?>
