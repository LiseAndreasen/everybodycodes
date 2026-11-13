<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "08";
$EMPTY = ".";

// directions
$LEFT = 0;
$LEFTUP = 1;
$RIGHTUP = 2;
$RIGHT = 3;
$RIGHTDOWN = 4;
$LEFTDOWN = 5;
$NO_OF_DIRECTIONS = 6;
$HEIGHT = "H";
$CORNERS = 0;
$NO_UPDATE = false;
$UPDATE = true;
$TREE_BECOMES_WARRIOR = 10;
$SEED = -3;
$TREE_HEIGHT_MINIMUM = 1;
$WARRIORS_REQUIRED = 100000;
$DIR_DICT = ["SE", "SW", "W", "NW", "NE", "E"];	// test

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

function tree_id($x, $y) {
	return ($x + 1) * 1000 + $y + 1;
}

function build_graph($map) {
	global $EMPTY, $LEFT, $LEFTUP, $RIGHTUP, $RIGHT, $RIGHTDOWN, $LEFTDOWN,
		$HEIGHT, $CORNERS;

	$graph = [];
	$w = sizeof($map);
	$h = sizeof($map[0]);
	for($x=0;$x<$w;$x++) {
		for($y=0;$y<$h;$y++) {
			if($map[$x][$y] != $EMPTY) {
				$cell = [];
				$cell[$HEIGHT] = $map[$x][$y];
				$id = tree_id($x, $y);
				// look left
				if(isset($map[$x-2][$y]) && $map[$x-2][$y] != $EMPTY) {
					$cell[$LEFT] = tree_id($x-2, $y);
				}
				// look left and up
				if(isset($map[$x-1][$y-1]) && $map[$x-1][$y-1] != $EMPTY) {
					$cell[$LEFTUP] = tree_id($x-1, $y-1);
				}
				// look right and up
				if(isset($map[$x+1][$y-1]) && $map[$x+1][$y-1] != $EMPTY) {
					$cell[$RIGHTUP] = tree_id($x+1, $y-1);
				}
				// look right
				if(isset($map[$x+2][$y]) && $map[$x+2][$y] != $EMPTY) {
					$cell[$RIGHT] = tree_id($x+2, $y);
				}
				// look right and down
				if(isset($map[$x+1][$y+1]) && $map[$x+1][$y+1] != $EMPTY) {
					$cell[$RIGHTDOWN] = tree_id($x+1, $y+1);
				}
				// look left and down
				if(isset($map[$x-1][$y+1]) && $map[$x-1][$y+1] != $EMPTY) {
					$cell[$LEFTDOWN] = tree_id($x-1, $y+1);
				}
				$graph[$id] = $cell;
			}
		}
	}
	$here = min(array_keys($graph));
	$graph[$CORNERS][$LEFT] = $here;
	while(isset($graph[$here][$RIGHTUP])) {
		$here = $graph[$here][$RIGHTUP];
	}
	$graph[$CORNERS][$LEFTUP] = $here;
	while(isset($graph[$here][$RIGHT])) {
		$here = $graph[$here][$RIGHT];
	}
	$graph[$CORNERS][$RIGHTUP] = $here;
	while(isset($graph[$here][$RIGHTDOWN])) {
		$here = $graph[$here][$RIGHTDOWN];
	}
	$graph[$CORNERS][$RIGHT] = $here;
	while(isset($graph[$here][$LEFTDOWN])) {
		$here = $graph[$here][$LEFTDOWN];
	}
	$graph[$CORNERS][$RIGHTDOWN] = $here;
	while(isset($graph[$here][$LEFT])) {
		$here = $graph[$here][$LEFT];
	}
	$graph[$CORNERS][$LEFTDOWN] = $here;
	return $graph;
}

function count_trees_sub($here, $graph, $shadow_direction,
$walking_direction, $update_yes) {
	global $HEIGHT, $TREE_HEIGHT_MINIMUM, $TREE_BECOMES_WARRIOR, $SEED;

	$trees_without_shadow = 0;
	$energy_absorbed = 0;
	$left_the_field = 0;
	while(true) {
		$there = $here;
		$shadow = 0;
		while(true) {
			if($shadow < $graph[$there][$HEIGHT]) {
				// tree absorbs energy
				// and potentially it grows
				// it also becomes the new shadow caster
				$trees_without_shadow++;
				if($TREE_HEIGHT_MINIMUM <= $graph[$there][$HEIGHT]) {
					$energy_absorbed++;
				}
				$shadow = $graph[$there][$HEIGHT];
				if($update_yes) {
					$graph[$there][$HEIGHT]++;
					if($graph[$there][$HEIGHT] == $TREE_BECOMES_WARRIOR) {
						$left_the_field++;
						$graph[$there][$HEIGHT] = $SEED;
					}
				}
			}
			if($graph[$there][$HEIGHT] < $TREE_HEIGHT_MINIMUM) {
					$graph[$there][$HEIGHT]++;
			}
			if(isset($graph[$there][$shadow_direction])) {
				$there = $graph[$there][$shadow_direction];
			} else {
				break;
			}
			$shadow--;
		}
		if(isset($graph[$here][$walking_direction])) {
			$here = $graph[$here][$walking_direction];
		} else {
			break;
		}
	}
	return [$graph, $trees_without_shadow, $energy_absorbed,
		$left_the_field, $here];
}

function count_trees($graph, $direction, $update_yes) {
	global $CORNERS, $NO_OF_DIRECTIONS,
		$TREE_BECOMES_WARRIOR, $SEED, $TREE_HEIGHT_MINIMUM;
	// always move clockwise around the edge of the trees
	$walking_direction = ($direction + 2) % $NO_OF_DIRECTIONS;
	// and to examine shadows, move direction + 4
	$shadow_direction = ($direction + 4) % $NO_OF_DIRECTIONS;
	$trees_without_shadow = 0;
	$energy_absorbed = 0;
	$left_the_field = 0;
	$here = $graph[$CORNERS][$direction];

	[$graph, $tws, $ea, $ltf, $h] = count_trees_sub($here, $graph,
		$shadow_direction, $walking_direction, $update_yes);
	$trees_without_shadow += $tws;
	$energy_absorbed += $ea;
	$left_the_field += $ltf;
	$here = $h;

	// now walk along the next edge
	$walking_direction = ($walking_direction + 1) % $NO_OF_DIRECTIONS;
	// we already did this corner, so move to the next tree
	$here = $graph[$here][$walking_direction];

	[$graph, $tws, $ea, $ltf, $h] = count_trees_sub($here, $graph,
		$shadow_direction, $walking_direction, $update_yes);
	$trees_without_shadow += $tws;
	$energy_absorbed += $ea;
	$left_the_field += $ltf;

	return [$graph, $trees_without_shadow, $energy_absorbed,
		$left_the_field];
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$data = pivot($data);

$graph = build_graph($data);
[$graph, $no, $energy_absorbed, $left_the_field] =
	count_trees($graph, $LEFT, $NO_UPDATE);

printf("Result 1: %d\n", $no);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$data = pivot($data);

$graph = build_graph($data);

$sum = 0;
for($dir=0;$dir<$NO_OF_DIRECTIONS;$dir++) {
	[$graph, $no, $energy_absorbed, $left_the_field] =
		count_trees($graph, $dir, $NO_UPDATE);
	$sum += $no;
}

printf("Result 2: %d\n", $sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3_ex1.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$data = pivot($data);

$graph = build_graph($data);

$sum_warriors = 0;
$sum_energy = 0;
$dir = $LEFT;
$round = 0;		// test
while($sum_warriors <= $WARRIORS_REQUIRED) {
	[$graph, $no, $energy_absorbed, $left_the_field] =
		count_trees($graph, $dir, $UPDATE);
	$sum_warriors += $left_the_field;
	$sum_energy += $energy_absorbed;
	$round++;		// test
	if($round <= 100 || $round == 1000 || $round % 10000 == 0 ||
	32000 <= $round) {
		printf("%5d %4s %7d %4d %7d %7d\n", $round, $DIR_DICT[$dir],
			$energy_absorbed, $left_the_field, $sum_warriors, $sum_energy);
	}
	$dir = ($dir + 1) % $NO_OF_DIRECTIONS;
}

printf("Result 3: %d\n", $sum_energy);

?>
