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
$CYCLE_PHASE_BEGIN = 0;
$CYCLE_PHASE_RECORD = 1;

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

function store_state($ghosts, $round) {
	global $all_states_seen;
	
	// ignore collisions left
	$state_arr = [];
	foreach($ghosts as $ghost) {
		[$x, $y, $cell, $col] = $ghost;
		$state_arr[] = $x . "-" . $y . "-" . $cell;
	}
	sort($state_arr);
	$state = implode("*", $state_arr);
	$pos = array_search($state, $all_states_seen);
	if($pos !== false) {
		return $pos;
	} else {
		$all_states_seen[$round] = $state;
		return -1;
	}
}

function walk_the_ghosts($data, $rounds, $collisions_allowed) {
	global $NOT_GHOST, $EMPTY_SPACE, $MOVE, $MOVES,
		$XPOS, $YPOS, $DIRPOS, $COLPOS, $all_states_seen,
		$CYCLE_PHASE_BEGIN, $CYCLE_PHASE_RECORD;
	
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
	store_state($ghosts, 0);
	
	// no cycle detected yet
	$cycle_phase = $CYCLE_PHASE_BEGIN;

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
					$cycle_phase = $CYCLE_PHASE_BEGIN;
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
							$cycle_phase = $CYCLE_PHASE_BEGIN;
						}
					}
				}
			}
		}
		if(sizeof($ghosts) == 0) {
			break;
		}
		$seen_before = store_state($ghosts, $r);
		if($seen_before != -1) {
			if($cycle_phase == $CYCLE_PHASE_BEGIN) {
				$length_of_cycle = $r - $seen_before;
				$cycle_phase = $CYCLE_PHASE_RECORD;
				// go one more cycle to count collisions, then leap
				$all_states_seen = [];
				store_state($ghosts, $r);
				$ghosts_cycle_recording = $ghosts;
			} else {
				$ghosts_cycle_collisions = [];
				// calculate the collisions for each ghost during 1 cycle
				foreach($ghosts as $g => $cell) {
					$coll_post_cycle = $cell[$COLPOS];
					$coll_pre_cycle = $ghosts_cycle_recording[$g][$COLPOS];
					$ghosts_cycle_collisions[$g] =
						$coll_pre_cycle - $coll_post_cycle;
				}
				// calculate the length of the leap ahead
				// the leap should stop before a ghost will die
				$leap = -1;
				foreach($ghosts as $g => $cell) {
					$coll_pre_leap = $cell[$COLPOS];
					$coll_pr_cycle = $ghosts_cycle_collisions[$g];
					// round down by 1 so that the leap doesn't end
					// where a ghost would have died
					$cycles_to_be_leaped =
						floor($coll_pre_leap / $coll_pr_cycle - 1);
					if($leap == -1 || $cycles_to_be_leaped < $leap) {
						$leap = $cycles_to_be_leaped;
					}
				}
				// leap
				foreach($ghosts as $g => $cell) {
					$ghosts[$g][$COLPOS] -=
						$leap * $ghosts_cycle_collisions[$g];
				}
				$r += $length_of_cycle * $leap;
				$all_states_seen = [];
				$cycle_phase = $CYCLE_PHASE_BEGIN;
			}
		}
	}
	
	return [$collisions, $r];
}

///////////////////////////////////////////////////////////////////////////

// global variable needed later
$all_states_seen = [];

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

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$data = pivot($data);

$rounds = 20000000000000;			// guess too high
$collisions_allowed = 1000000000000;
[$collisions, $rounds_used] = walk_the_ghosts($data, $rounds,
	$collisions_allowed);

printf("Result 3: %d\n", $rounds_used);

?>
