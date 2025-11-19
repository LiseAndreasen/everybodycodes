<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "12";

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
	// assume pivot called first
	$map_width = sizeof($map);
	$map_height = sizeof($map[0]);
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

function burn_barrels($data, $data2) {
	global $map_height, $map_width;

	// count barrels on fire
	$counts = array_count_values(array_merge(...$data2));
	$barrels = $counts["*"];
	$barrels_delta = $barrels;

	while($barrels_delta > 0) {
		// let fire grow
		for($j=0;$j<$map_height;$j++) {
			for($i=0;$i<$map_width;$i++) {
				if($data2[$i][$j] == "*") {
					// check those adjacent
					if(isset($data[$i-1][$j]) && $data[$i-1][$j] <= $data[$i][$j]) {
						$data2[$i-1][$j] = "*";
					}
					if(isset($data[$i+1][$j]) && $data[$i+1][$j] <= $data[$i][$j]) {
						$data2[$i+1][$j] = "*";
					}
					if(isset($data[$i][$j-1]) && $data[$i][$j-1] <= $data[$i][$j]) {
						$data2[$i][$j-1] = "*";
					}
					if(isset($data[$i][$j+1]) && $data[$i][$j+1] <= $data[$i][$j]) {
						$data2[$i][$j+1] = "*";
					}
				}
			}
		}

		$counts = array_count_values(array_merge(...$data2));
		$barrels_new = $counts["*"];
		$barrels_delta = $barrels_new - $barrels;
		$barrels = $barrels_new;
	}
	
	return $barrels;
}

function burn_barrels_best($data, $data2, $fires) {
	global $map_height, $map_width;

	$barrels_max = 0;
	$progress = 0;
	// start all possible fires, choose the best one
	for($jj=0;$jj<$map_height;$jj++) {
		for($ii=0;$ii<$map_width;$ii++) {
			$progress++;
			if($progress % 100 == 0) {
				print(".");
			}
			$data2 = array();
			// create parallel map of fires
			for($j=0;$j<$map_height;$j++) {
				for($i=0;$i<$map_width;$i++) {
					$data2[$i][$j] = ".";
				}
			}
			
			// pre-started fires
			foreach($fires as $fire) {
				$data2[$fire[0]][$fire[1]] = "*";				
			}

			// start fire
			$data2[$ii][$jj] = "*";

			$barrels = burn_barrels($data, $data2);
			if($barrels_max < $barrels) {
				$barrels_max = $barrels;
				$best_ii1 = $ii;
				$best_jj1 = $jj;
			}
		}
	}
	
	return array($best_ii1, $best_jj1, $barrels_max);
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2025_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$data = pivot($data);

$map_width = sizeof($data);
$map_height = sizeof($data[0]);

// create parallel map of fires
for($j=0;$j<$map_height;$j++) {
	for($i=0;$i<$map_width;$i++) {
		$data2[$i][$j] = ".";
	}
}

// start fire
$data2[0][0] = "*";

$barrels = burn_barrels($data, $data2);

printf("Result 1: %d\n", $barrels);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2025_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$data = pivot($data);

$map_width = sizeof($data);
$map_height = sizeof($data[0]);

$data2 = array();
// create parallel map of fires
for($j=0;$j<$map_height;$j++) {
	for($i=0;$i<$map_width;$i++) {
		$data2[$i][$j] = ".";
	}
}

// start fire
$data2[0][0] = "*";
$data2[$map_width - 1][$map_height - 1] = "*";

$barrels = burn_barrels($data, $data2);

printf("Result 2: %d\n", $barrels);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2025_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);
$data = pivot($data);

$map_width = sizeof($data);
$map_height = sizeof($data[0]);

$fires = array();
$best_b1 = burn_barrels_best($data, $data2, $fires);
$fires[] = $best_b1;

$best_b2 = burn_barrels_best($data, $data2, $fires);
$fires[] = $best_b2;

$best_b3 = burn_barrels_best($data, $data2, $fires);

printf("Result 3: %d\n", $best_b3[2]);

?>
