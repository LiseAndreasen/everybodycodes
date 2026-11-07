<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "10";
$EMPTY_SPACE = ".";
$CAVE_WALL = "#";
$NOT_GHOST = [$EMPTY_SPACE, $CAVE_WALL];
$MOVE = [];
$MOVE[">"] = [ 1,  0];
$MOVE["v"] = [ 0,  1];
$MOVE["<"] = [-1,  0];
$MOVE["^"] = [ 0, -1];
$MOVES = ">v<^";
$XPOS = 0;
$YPOS = 1;
$DIRPOS = 2;
$COLPOS = 3;

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

function walk_the_ghosts($data, $rounds, $collisions_allowed) {
	global $NOT_GHOST, $EMPTY_SPACE, $MOVE, $MOVES,
		$XPOS, $YPOS, $DIRPOS, $COLPOS;
	
	// make list of ghosts and remove them from the map
	$ghosts = [];
	foreach($data as $x => $line) {
		foreach($line as $y => $cell) {
			if(in_array($cell, $NOT_GHOST)) {
				continue;
			}
			$ghosts[] = [$x, $y, $cell, $collisions_allowed];
			$data[$x][$y] = $EMPTY_SPACE;
		}
	}

	// let the ghosts walk
	$collisions = 0;
	for($r=1;$r<=$rounds;$r++) {
		$cells = [];
		// walking
		foreach($ghosts as $g => $cell) {
			[$x, $y, $dir, $coll] = $cell;
			[$dx, $dy] = $MOVE[$dir];
			$x_new = $x + $dx;
			$y_new = $y + $dy;
			if($data[$x_new][$y_new] == $EMPTY_SPACE) {
				$ghosts[$g][$XPOS] = $x_new;
				$ghosts[$g][$YPOS] = $y_new;
				$cells[$x_new][$y_new][] = $g;
			} else {
				$collisions++;
				// turn 90 clock
				$dir_no = strpos($MOVES, $dir);
				$dir_no = ($dir_no + 1) % 4;
				$dir = $MOVES[$dir_no];
				$ghosts[$g][$DIRPOS] = $dir;
				$ghosts[$g][$COLPOS]--;
				if($ghosts[$g][$COLPOS] == 0) {
					// ghost disappears
					unset($ghosts[$g]);
				} else {
					$cells[$x][$y][] = $g;
				}
			}
		}
		// resolve collisions
		foreach($cells as $x => $line) {
			foreach($line as $y => $ghosties) {
				// collision?
				if(1 < sizeof($ghosties)) {
					foreach($ghosties as $g) {
						$collisions++;
						// turn 180
						$dir = $ghosts[$g][$DIRPOS];
						$dir_no = strpos($MOVES, $dir);
						$dir_no = ($dir_no + 2) % 4;
						$dir = $MOVES[$dir_no];
						$ghosts[$g][$DIRPOS] = $dir;
						$ghosts[$g][$COLPOS]--;
						if($ghosts[$g][$COLPOS] == 0) {
							// ghost disappears
							unset($ghosts[$g]);
						}
					}
				}
			}
		}
		if(sizeof($ghosts) == 0) {
			break;
		}
	}
	
	return [$collisions, $r];
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$data = pivot($data);

$rounds = 10;
$collisions_allowed = 21;
[$collisions, $rounds_used] = walk_the_ghosts($data, $rounds,
	$collisions_allowed);

printf("Result 1: %d\n", $collisions);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$data = pivot($data);

$rounds = 200000;			// guess too high
$collisions_allowed = 10000;
[$collisions, $rounds_used] = walk_the_ghosts($data, $rounds,
	$collisions_allowed);

printf("Result 2: %d\n", $rounds_used);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
