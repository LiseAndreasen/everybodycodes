<?php

///////////////////////////////////////////////////////////////////////////
// constants

// file names
$file1 = './everybody_codes_e2025_q10_p1.txt';
$dragon_steps1 = 3; // 3 for example, otherwise 4

$file2 = './everybody_codes_e2025_q10_p2.txt';
$dragon_steps2 = 20; // 3 for example, otherwise 20

$file3 = './everybody_codes_e2025_q10_p3.txt';

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
	global $map_width, $map_height;
	for($j=0;$j<$map_height;$j++) {
		for($i=0;$i<$map_width;$i++) {
			echo $map[$i][$j];
		}
		echo "\n";
	}
	for($i=0;$i<$map_width;$i++) {
		echo "=";
	}
	echo "\n";
}

function print_dragon_and_sheep($dragon, $sheep) {
	global $map_width, $map_height;
	for($j=0;$j<$map_height;$j++) {
		for($i=0;$i<$map_width;$i++) {
			echo $dragon[$i][$j];
		}
		print("     ");
		for($i=0;$i<$map_width;$i++) {
			echo $sheep[$i][$j];
		}
		echo "\n";
	}
	for($i=0;$i<$map_width*2+5;$i++) {
		echo "=";
	}
	echo "\n";
}

function init_maps() {
	global $map_width, $map_height;
	global $dragon_squares, $data, $data_dragon, $data_sheep;

	// first prepare empty map
	for($j=0;$j<$map_height;$j++) {
		for($i=0;$i<$map_width;$i++) {
			$data_dragon[$i][$j] = ".";
		}
	}

	// find D
	for($j=0;$j<$map_height;$j++) {
		$i = array_search("D", $data[$j]);
		if($i !== false) {
			$dragon_squares[] = array($i, $j);
		}
	}
	
	$data_sheep = $data;
	for($j=0;$j<$map_height;$j++) {
		for($i=0;$i<$map_width;$i++) {
			if($data_sheep[$i][$j] == "#" || $data_sheep[$i][$j] == "D") {
				$data_sheep[$i][$j] = ".";
			}
		}
	}
}

function dragon_moves() {
	global $dragon_squares, $data_dragon;
	global $map_width, $map_height;

	// prepare new, empty map
	$data_dragon2 = array();
	for($j=0;$j<$map_height;$j++) {
		for($i=0;$i<$map_width;$i++) {
			$data_dragon2[$i][$j] = ".";
		}
	}

	foreach($dragon_squares as $key => $ds) {
		$x = $ds[0];
		$y = $ds[1];
		unset($dragon_squares[$key]);
		
		// is this square on the map?
		if($x < 0 || $x >= $map_width || $y < 0 || $y >= $map_height) {
			continue;
		}

		// mark dragon
		if($data_dragon2[$x][$y] == ".") {
			$data_dragon2[$x][$y] = "X";
		} else {
			// only look at this square once
			continue;
		}
		
		// add all 8 new squares to the q
		// this only turns into marks on the map
		// if the loop runs 1 more time
		$dragon_squares[] = array($x + 1, $y + 2);
		$dragon_squares[] = array($x - 1, $y + 2);
		$dragon_squares[] = array($x + 1, $y - 2);
		$dragon_squares[] = array($x - 1, $y - 2);
		$dragon_squares[] = array($x + 2, $y + 1);
		$dragon_squares[] = array($x - 2, $y + 1);
		$dragon_squares[] = array($x + 2, $y - 1);
		$dragon_squares[] = array($x - 2, $y - 1);
	}
	
	$data_dragon = $data_dragon2;
}

/*
function move_sheep_and_dragon($data, $sheep_list, $dragon, $sofar) {

//printf("Sheep: %d Dragon (%d,%d)\n", sizeof($sheep_list), $dragon[0], $dragon[1]);

	global $map_width, $map_height;
	global $states_seen;
	
	$arr1 = $sheep_list;
	$arr1[] = $dragon;
	$arr2 = array_merge(...$arr1);
	$str3 = implode("*", $arr2);
	
	// avoid cycles
	if(isset($states_seen[$str3])) {
		if($states_seen[$str3] == -1) {
			// somebody else is already working on this, abandon
print("Do not duplicate work 1\n");
			return 0;
		} else {
print("Do not duplicate work 2\n");
			return $states_seen[$str3];
		}
	}
	
	// avoid checking the same configuration twice
	$states_seen[$str3] = -1;

	$x = $dragon[0];
	$y = $dragon[1];
	if($x < 0 || $x >= $map_width || $y < 0 || $y >= $map_height) {
		// this involved the dragon moving off the map, invalid
print("Dragon off map\n");
		return 0;
	}
	
	$perms = 0;

	// it is possible the dragon just moved close to a sheep
	// not in a shelter
	// if yes, eat sheep
	foreach($sheep_list as $key => $sheep) {
		if($sheep[0] == $x && $sheep[1] == $y && $data[$x][$y] != "#") {
print("Sheep eaten\n");
			unset($sheep_list[$key]);
			break;
		}
	}

	if(sizeof($sheep_list) == 0) {
		// all sheep are gone, valid permutation
printf("********************************************\nAll sheep eaten!\n%s\n", $sofar);
		return 1;
	}
print($sofar."\n");
	
	$local_sheep = $sheep_list;
	// first prepare to move a sheep
	foreach($local_sheep as $key => $sheep) {
		// sheep won't move into dragon
		if($sheep[0] == $dragon[0] && $sheep[1] == $dragon[1])  {
			unset($local_sheep[$key]);
			continue;
		}
		// sheep should not be allowed to move off map
		if($sheep[1] == $map_height - 1) {
			unset($local_sheep[$key]);
		}
	}
	
	// then prepare to move a dragon
	$dragon_list[] = array($x + 1, $y + 2);
	$dragon_list[] = array($x - 1, $y + 2);
//	$dragon_list[] = array($x + 1, $y - 2);
	$dragon_list[] = array($x - 1, $y - 2);
//	$dragon_list[] = array($x + 2, $y + 1);
	$dragon_list[] = array($x - 2, $y + 1);
	$dragon_list[] = array($x + 2, $y - 1);
//	$dragon_list[] = array($x - 2, $y - 1);
	
	// if there are some sheep
	foreach($local_sheep as $key => $sheep) {
		$new_sheep_list = $sheep_list;
		// move this sheep down
		$new_sheep_list[$key][1]++;
		foreach($dragon_list as $new_dragon) {
			$move = " S>" . chr($sheep[0]+65) . $sheep[1]+2 . " D>" . chr($new_dragon[0]+65) . $new_dragon[1]+1;
			$perms += move_sheep_and_dragon($data, $new_sheep_list, $new_dragon, $sofar . $move);
		}
	}
	
	// if there are no sheep, still move the dragon
	if(sizeof($local_sheep) == 0) {
		foreach($dragon_list as $new_dragon) {
			$move = " D>" . chr($new_dragon[0]+65) . $new_dragon[1]+1;
			$perms += move_sheep_and_dragon($data, $sheep_list, $new_dragon, $sofar . $move);
		}
	}
	
	$states_seen[$str3] = $perms;
	return $perms;
}
*/

// first time i am trying this!
function memoize($func) {
    return function() use ($func) {
        static $cache = [];
        $args = func_get_args();
        $key = serialize($args);
        if (!isset($cache[$key])) {
            $cache[$key] = call_user_func_array($func, $args);
        }
        return $cache[$key];
    };
}

$num_ways = memoize(function($dragon, $sheep, $hiding, $curr_move) use (&$num_ways) {
	global $numrows, $numcols, $delta;
	
	if(sizeof($sheep) == 0) {
		return 1;
	}
	foreach($sheep as $this_sheep) {
		if($this_sheep[1] >= $numrows) {
			return 0;
		}
	}
	
	if($curr_move == "S") {
		$can_move = $sheep;
		foreach($can_move as $key => $this_sheep) {
			$x = $this_sheep[0];
			$y = $this_sheep[1] + 1;
			foreach($hiding as $hplace) {
				if($x == $hplace[0] && $y == $hplace[1]) {
					continue 2;
				}
			}
			if($x == $dragon[0] && $y == $dragon[1]) {
				unset($can_move[$key]);
			}
		}
//print_r($can_move);		
		if(sizeof($can_move) > 0) {
			$answer = 0;
			foreach($can_move as $key => $this_sheep) {
				$new_sheep = $sheep;
				$new_sheep[$key][1]++;
				$answer += $num_ways($dragon, $new_sheep, $hiding, "D");
			}
			return $answer;	
		} else {
			return $num_ways($dragon, $sheep, $hiding, "D");
		}
	} else {
		$x = $dragon[0];
		$y = $dragon[1];
		$answer = 0;
		foreach($delta as $d) {
			$newx = $x + $d[0];
			$newy = $y + $d[1];
			$new_dragon = array($newx, $newy);
			// did the dragon go off the map?
			if($newx < 0 || $newx >= $numcols || $newy < 0 || $newy >= $numrows) {
				continue;
			}
			// did the dragon land on a sheep?
			foreach($sheep as $key => $this_sheep) {
				if($this_sheep[0] == $newx && $this_sheep[1] == $newy) {
					// but the sheep was in a hiding place?
					foreach($hiding as $hplace) {
						if($hplace[0] == $newx && $hplace[1] == $newy) {
							$answer += $num_ways($new_dragon, $sheep, $hiding, "S");
							continue 3;
						}
					}
					// the sheep was not in a hiding place, eat it
					$new_sheep = $sheep;
					unset($new_sheep[$key]);
					$answer += $num_ways($new_dragon, $new_sheep, $hiding, "S");
					continue 2;
				}
			}
			// the dragon did not land on a sheep
			$answer += $num_ways($new_dragon, $sheep, $hiding, "S");			
		}
		return $answer;	
	}
	
	return 7;
});

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$input = file_get_contents($file1, true);
$data = get_input($input);
$data = pivot($data);
$map_height = sizeof($data);
$map_width = sizeof($data[0]);

$data_dragon = array();
$data_sheep = array();
$dragon_squares = array();
init_maps();

// do knight moves

$dead_sheep = 0;

// make all possible knight moves, k times
for($k=-1;$k<$dragon_steps1;$k++) {
	dragon_moves();

	// count dead sheep
	for($j=0;$j<$map_height;$j++) {
		for($i=0;$i<$map_width;$i++) {
			if($data_dragon[$i][$j] == "X" && $data_sheep[$i][$j] == "S") {
				$dead_sheep++;
				// only eat the sheep once
				$data_sheep[$i][$j] = ".";
			}
		}
	}
}

printf("Result 1: %d\n", $dead_sheep);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$input = file_get_contents($file2, true);
$data = get_input($input);
$data = pivot($data);
$map_height = sizeof($data);
$map_width = sizeof($data[0]);

$data_dragon = array();
$data_sheep = array();
$dragon_squares = array();
init_maps();

$dead_sheep = 0;

// mark where the dragon is
dragon_moves();

//print_map($data_dragon);

for($k=0;$k<$dragon_steps2;$k++) {
	// move the dragon
	dragon_moves();

	// dragon eats sheep before they move
	for($j=0;$j<$map_height;$j++) {
		for($i=0;$i<$map_width;$i++) {
			if($data_dragon[$i][$j] == "X" && $data_sheep[$i][$j] == "S" && $data[$i][$j] != "#") {
				$dead_sheep++;
				$data_sheep[$i][$j] = ".";
			}
		}
	}

	// sheep move
	for($j=$map_height-1;$j>0;$j--) {
		for($i=0;$i<$map_width;$i++) {
			$data_sheep[$i][$j] = $data_sheep[$i][$j-1];
			$data_sheep[$i][$j-1] = ".";
		}
	}

	// dragon eats sheep after they move
	for($j=0;$j<$map_height;$j++) {
		for($i=0;$i<$map_width;$i++) {
			if($data_dragon[$i][$j] == "X" && $data_sheep[$i][$j] == "S" && $data[$i][$j] != "#") {
				$dead_sheep++;
				$data_sheep[$i][$j] = ".";
			}
		}
	}

//print_dragon_and_sheep($data_dragon, $data_sheep);

}

printf("Result 2: %d\n", $dead_sheep);

// i suspect my x/y, i/j, height/width are wrong some of the time

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$input = file_get_contents($file3, true);
$data = get_input($input);
$data = pivot($data);
$numcols = sizeof($data);
$numrows = sizeof($data[0]);

// trying to implement my own version of this
// https://www.reddit.com/r/everybodycodes/comments/1oxbehi/comment/noyrijn/

// find dragon, sheep and hiding places
for($i=0;$i<$numcols;$i++) {
	for($j=0;$j<$numrows;$j++) {
		if($data[$i][$j] == "D") {
			$dragon = array($i, $j);
		}
		if($data[$i][$j] == "S") {
			$sheep[] = array($i, $j);
		}
		if($data[$i][$j] == "#") {
			$hiding[] = array($i, $j);
		}
	}
}

// all the possible dragon moves
$delta = array(
	array( 1,  2),
	array( 1, -2),
	array(-1,  2),
	array(-1, -2),
	array( 2,  1),
	array( 2, -1),
	array(-2,  1),
	array(-2, -1)
);

//print_r($dragon);
//print_r($sheep);
//print_r($hiding);

$perms = $num_ways($dragon, $sheep, $hiding, "S");

/*
//print_r($sheep_list);

//print_r($dragon);

$perms = move_sheep_and_dragon($data, $sheep_list, $dragon, "");
*/

printf("Result 3: %d\n", $perms);

?>
