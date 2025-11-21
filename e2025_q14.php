<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "14";

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

function tiles_round() {
	global $map_w, $map_h;
	global $data;
	
	for($j=0;$j<$map_h;$j++) {
		for($i=0;$i<$map_w;$i++) {
			$even_odd = 0;
			if($data[$i][$j] == "#") {
				$even_odd++;
			}
			if(isset($data[$i-1][$j-1]) && $data[$i-1][$j-1] == "#") {
				$even_odd++;
			}
			if(isset($data[$i-1][$j+1]) && $data[$i-1][$j+1] == "#") {
				$even_odd++;
			}
			if(isset($data[$i+1][$j-1]) && $data[$i+1][$j-1] == "#") {
				$even_odd++;
			}
			if(isset($data[$i+1][$j+1]) && $data[$i+1][$j+1] == "#") {
				$even_odd++;
			}
			if($even_odd % 2 == 0) {
				$data2[$i][$j] = "#";
			} else {
				$data2[$i][$j] = ".";
			}
		}
	}
	$data = $data2;
}

// change (y, x) map to (x, y) map
function pivot($map) {
	$map_height = sizeof($map);
	$map_width = sizeof($map[0]);
	for($j=0;$j<$map_height;$j++) {
		for($i=0;$i<$map_width;$i++) {
			$map2[$j][$i] = $map[$i][$j];
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

function match_pattern($data, $data2) {
	global $pattern_sz, $p_x, $p_y;
	
	// test whether this configuration of tiles contains the pattern
	$pattern_possible = 1;
	for($j=0;$j<$pattern_sz;$j++) {
		for($k=0;$k<$pattern_sz;$k++) {
			if($data2[$j][$k] != $data[$j+$p_x][$k+$p_y]) {
				$pattern_possible = 0;
				break 2;
			}
		}
	}
	
	return $pattern_possible;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2025_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$data = pivot($data);
$map_w = sizeof($data);
$map_h = sizeof($data[0]);

$tile_sum = 0;
for($i=0;$i<10;$i++) {
	tiles_round();
	$arr2 = array_merge(...$data);
	$arr3 = array_count_values($arr2);
	$tile_sum += $arr3["#"];
}

printf("Result 1: %d\n", $tile_sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2025_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$data = pivot($data);
$map_w = sizeof($data);
$map_h = sizeof($data[0]);

$tile_sum = 0;
for($i=0;$i<2025;$i++) {
	tiles_round();
	$arr2 = array_merge(...$data);
	$arr3 = array_count_values($arr2);
	$tile_sum += $arr3["#"];
}

printf("Result 2: %d\n\n", $tile_sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2025_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data2 = get_input($input);

// create empty map
$data = array();
$map_w = 34;
$map_h = 34;
for($i=0;$i<$map_w;$i++) {
	for($j=0;$j<$map_h;$j++) {
		$data[$i][$j] = ".";
	}
}

$i = 0;
// keep track of the round number for pattern matches
$pattern_matches = array();

$pattern_sz = sizeof($data2);
// where is the pattern located?
// assuming the pattern is square
$p_x = ($map_w - $pattern_sz) / 2;
$p_y = ($map_h - $pattern_sz) / 2;

// keep track of all configurations seen
$tiles_seen[$i] = implode("", array_merge(...$data));

$pattern_possible = match_pattern($data, $data2);
if($pattern_possible == 1) {
	$pattern_matches[] = $i;
}

// keep going until a cycle has occurred
$tiles_seen_before = 0;
while($tiles_seen_before == 0) {
	$i++;
	tiles_round();

	$pattern_possible = match_pattern($data, $data2);
	if($pattern_possible == 1) {
		$pattern_matches[] = $i;
	}

	$these_tiles = implode("", array_merge(...$data));
	if(in_array($these_tiles, $tiles_seen)) {
		$tiles_seen_before = 1;
	} else {
		$tiles_seen[$i] = $these_tiles;
	}
}
$i_old = array_search($these_tiles, $tiles_seen);
printf("%12d rounds and the cycle begins.\n", $i_old);
printf("%12d rounds is the length of the cycle.\n", $i - $i_old);
$cycle_length = $i - $i_old;

$all_rounds = 1000000000;
// all = a + b * cyclelength + c
// the first a rounds are unique
$a = $i_old;
$b = floor(($all_rounds - $a) / $cycle_length);
$c = $all_rounds - $a - $b * $cycle_length;

printf("%12d rounds should be the total run.\n", $all_rounds);
printf("%12d = %d + %d * %d + %d\n\n", $all_rounds, $a, $b, $cycle_length, $c);

//print_r($pattern_matches);

$tile_sum = 0;
// how many matches from 1 to a - 1?
foreach($pattern_matches as $match) {
	if(0 < $match && $match < $a) {
		$counts = count_chars($tiles_seen[$match]);
		$hash_value = ord("#");
		$tile_sum += $counts[$hash_value];
	}
}

// how many matches from a to a + b * cyclelength - 1?
$tmp_sum = 0;
foreach($pattern_matches as $match) {
	$counts = count_chars($tiles_seen[$match]);
	$hash_value = ord("#");
	$tmp_sum += $counts[$hash_value];
}
$tile_sum += $tmp_sum * $b;

// how many matches from a + b * cyclelength to end?
foreach($pattern_matches as $match) {
	if($a <= $match && $match < $a + $c) {
		$counts = count_chars($tiles_seen[$match]);
		$hash_value = ord("#");
		$tile_sum += $counts[$hash_value];
	}
}

printf("Result 3: %d\n", $tile_sum);

?>
