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

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2025_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$data = pivot($data);
$map_w = sizeof($data);
$map_h = sizeof($data[0]);

$r = 10;

// find S
for($i=0;$i<$map_w;$i++) {
	$j = array_search("@", $data[$i]);
	if($j !== false) {
		$S_x = $i;
		$S_y = $j;
		$data[$i][$j] = 0;
	}
}

// (Xv - Xc) * (Xv - Xc) + (Yv - Yc) * (Yv - Yc) <= R * R
// calculate distances and change map
for($i=0;$i<$map_w;$i++) {
	for($j=0;$j<$map_h;$j++) {
		$dist2 = ($i - $S_x) * ($i - $S_x) + ($j - $S_y) * ($j - $S_y);
		if($r * $r < $dist2) {
			$data[$i][$j] = 0;
		}
	}
}

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

// find S
for($i=0;$i<$map_w;$i++) {
	$j = array_search("@", $data[$i]);
	if($j !== false) {
		$S_x = $i;
		$S_y = $j;
		$data[$i][$j] = 0;
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

$file3 = './everybody_codes_e2025_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
