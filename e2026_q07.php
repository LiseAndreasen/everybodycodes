<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "07";
$ID = 0;
$ENERGY = 1;
$CONNECTIONS = 2;

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			$line2 = explode(", ", $line);
			$data2 = [];
			foreach($line2 as $l) {
				$line3 = explode("=", $l);
				if(strcmp($line3[0], "connections") == 0) {
					$data2[] = explode(",", $line3[1]);
				} else {
					$data2[] = $line3[1];
				}
			}
			$id = $data2[0];
			$data[$id] = $data2;
		}
	}
	return $data;
}

function energy_goes($data, $impulse) {
	global $CONNECTIONS;
	// begin with the energy each cloud has at the beginning
	$total_energy = [];
	// find the cloud with the most outgoing connections
	$i_most = -1;
	$c_most = 0;
	foreach($data as $cloud) {
		[$i, $e, $c] = $cloud;
		$total_energy[$i] = $e;
		if($c_most < $c) {
			$i_most = $i;
			$c_most = $c;
		}
	}
	// add to the most-cloud
	$q = [];
	$q[] = [$i_most, $impulse];
	// let the energy propagate
	$e_old = $impulse + 1;
	while(0 < sizeof($q)) {
		$pq = sizeof($q);
		[$i, $e] = array_shift($q);
		if($e < $e_old) {
			$e_old = $e;
		}
		if($e == 0) {
			continue;
		}
		$total_energy[$i] += $e;
		$children = $data[$i][$CONNECTIONS];
		foreach($children as $c) {
			$q[] = [$c, $e - 1];
		}
	}
	
	// sum of final energies
	return array_sum($total_energy);
}

function energy_goes2($data, $impulse) {
	global $CONNECTIONS;
	// begin with the energy each cloud has at the beginning
	$total_energy = [];
	// find the cloud with the most outgoing connections
	$i_most = -1;
	$c_most = 0;
	// prepare a structure for travelling pulses
	// pulses[id] = no of pulses
	$pulses = [];
	foreach($data as $cloud) {
		[$i, $e, $c] = $cloud;
		$total_energy[$i] = $e;
		if($c_most < $c) {
			$i_most = $i;
			$c_most = $c;
		}
	}
	// initiate 1st pulse
	$pulses[$i_most][$impulse] = 1;
	$size = 1;
	// let the energy propagate
	while(0 < $size) {
		$new_pulses = [];
		foreach($pulses as $i => $pulse) {
			foreach($pulse as $e => $no) {
				// register energy of current pulses
				$total_energy[$i] += $no * $e;
			}
		}
		// make the next generation of pulses
		foreach($pulses as $i => $pulse) {
			foreach($pulse as $e => $no) {
				if($e == 1) {
					continue;
				}
				foreach($data[$i][$CONNECTIONS] as $c) {
					if(isset($new_pulses[$c][$e-1])) {
						$new_pulses[$c][$e-1] += $no;
					} else {
						$new_pulses[$c][$e-1] = $no;
					}
				}
			}
		}
		$pulses = $new_pulses;
		$size = array_sum(array_merge(...$pulses));
	}
	
	// sum of final energies
	return array_sum($total_energy);
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$SINGLE_IMPULSE = 5;

$sum = energy_goes($data, $SINGLE_IMPULSE);

printf("Result 1: %d\n", $sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$SINGLE_IMPULSE = 10;		// 10

$sum = energy_goes2($data, $SINGLE_IMPULSE);

printf("Result 2: %d\n", $sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
