<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "18";

$thickness = 0;
$branch = 1;
$free = -1;
$mask_data = -1;

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	global $thickness, $branch, $free, $mask_data;
	
	$data = array();
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		if(strlen($line)>2) {
			if(preg_match('/Plant (\d+) with thickness (\d+):/', $line, $matches)) {
				$plant_no = $matches[1];
				$data[$matches[1]][$thickness] = $matches[2];
				continue;
			}
			if(preg_match('/free branch with thickness (-*\d+)/', $line, $matches)) {
				$data[$plant_no][$free][$thickness] = $matches[1];
				continue;
			}
			if(preg_match('/branch to Plant (\d+) with thickness (-*\d+)/', $line, $matches)) {
				$data[$plant_no][$matches[1]][$thickness] = $matches[2];
				continue;
			}
			preg_match_all('/\d+/', $line, $matches);
			$data[$mask_data][] = $matches[0];
		}
	}
	return $data;
}

function calc_energies($data, $mask) {
	global $thickness, $branch, $free, $mask_data;

	$data_sz = sizeof($data);
	$energy = array_fill(1, $data_sz, -1);

	// assuming all incoming branches have lower numbers
	// and have already been calculated
	foreach($data as $plant_no => $description) {
		if(isset($description[$free])) {
			if($mask[$plant_no - 1] == 1) {
				$energy[$plant_no] = 1;
			} else {
				$energy[$plant_no] = 0;
			}
			continue;
		}
		
		if($plant_no == $mask_data) {
			unset($energy[$data_sz]);
			continue;
		}

		$all_incoming = 0;
		foreach($description as $no => $incoming) {
			if($no == $free || $no == $thickness) {
				continue;
			}
			$all_incoming += $energy[$no] * $incoming[$thickness];
		}

		if($description[$thickness] <= $all_incoming) {
			$energy[$plant_no] = $all_incoming;
		} else {
			$energy[$plant_no] = 0;
		}
	}
	
	return $energy;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2025_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$data_sz = sizeof($data);

$mask = array_fill(0, $data_sz, 1);
$energy = calc_energies($data, $mask);

printf("Result 1: %d\n", $energy[$data_sz]);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2025_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$data_sz = sizeof($data) - 1;

$energy_sum = 0;
foreach($data[$mask_data] as $mask) {
	$energy = calc_energies($data, $mask);
	$energy_sum += $energy[$data_sz];
}

printf("Result 2: %d\n", $energy_sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2025_q' . $quest . '_p3_ex1.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);
$data_sz = sizeof($data) - 1;

$free_branches = 0;
foreach($data as $plant) {
	if(isset($plant[$free])) {
		$free_branches++;
	}
}
printf("Free branches: %d\n", $free_branches);

// hack: assume free branches = 4 - example
// hack: assume free branches = 81 - actual

$max_energy = 0;
for($k=0;$k<pow(2, $free_branches);$k++) {
	$bin_no = decbin($k);
	$bin_pad = sprintf('%4d', $bin_no); // free branches hard coded
	$bin_arr = array_map('intval', str_split($bin_pad));
	$energy = calc_energies($data, $bin_arr);
	if($max_energy < $energy[$data_sz]) {
		$max_energy = $energy[$data_sz];
	}
}
printf("Max energy...: %d\n", $max_energy);

$energy_diff = 0;
foreach($data[$mask_data] as $mask) {
	$energy = calc_energies($data, $mask);
	if(0 < $energy[$data_sz]) {
		$energy_diff += $max_energy - $energy[$data_sz];
	}
}

printf("Result 3: %d\n", $energy_diff);

?>
