<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "07";
$ID = 0;
$ENERGY = 1;
$CONNECTIONS = 2;
$BEGIN_MOST = -1;
$DONT_EXPLODE = -1;

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

function energy_goes2($data, $impulse, $beginning_point, $explosion) {
	global $CONNECTIONS, $BEGIN_MOST, $DONT_EXPLODE;
	// begin with the energy each cloud has at the beginning
	$total_energy = [];
	$not_exploded = [];
	foreach($data as $cloud) {
		[$i, $e, $c] = $cloud;
		$total_energy[$i] = $e;
		$not_exploded[$i] = true;
	}
	if($beginning_point == $BEGIN_MOST) {
		// find the cloud with the most outgoing connections
		$i_most = -1;
		$c_most = 0;
		// prepare a structure for travelling pulses
		// pulses[id] = no of pulses
		$pulses = [];
		foreach($data as $cloud) {
			[$i, $e, $c] = $cloud;
			if($c_most < $c) {
				$i_most = $i;
				$c_most = $c;
			}
		}
		$i_begin = $i_most;
	} else {
		$i_begin = $beginning_point;
	}
	// initiate 1st pulse
	$pulses[$i_begin][$impulse] = 1;
	$size = 1;
	// structure to remember exploded clouds
	$exploded = [];
	// let the energy propagate
	while(0 < $size) {
		$new_pulses = [];
		foreach($not_exploded as $i => $still_here) {
			if($still_here) {
				if(isset($pulses[$i])) {
					foreach($pulses[$i] as $e => $no) {
						// register energy of current pulses
						$total_energy[$i] += $no * $e;
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
			}
		}
		// some clouds might explode
		if($explosion != $DONT_EXPLODE) {
			foreach($not_exploded as $i => $still_here) {
				if($still_here) {
					if($explosion <= $total_energy[$i]) {
						// cloud explodes, remove it from list
						unset($new_pulses[$i]);
						$not_exploded[$i] = false;
						unset($total_energy[$i]);
						// generate pulses
						foreach($data[$i][$CONNECTIONS] as $c) {
							if($not_exploded[$c]) {
								if(isset($new_pulses[$c][$impulse])) {
									$new_pulses[$c][$impulse] += 1;
								} else {
									$new_pulses[$c][$impulse] = 1;
								}
							} else {
								$pos = array_search($c, $data[$i][$CONNECTIONS]);
								unset($data[$i][$CONNECTIONS][$pos]);
							}
						}
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

$sum = energy_goes2($data, $SINGLE_IMPULSE, $BEGIN_MOST, $DONT_EXPLODE);

printf("Result 2: %d\n", $sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$SINGLE_IMPULSE = 10;		// 10
$explosion = 1000000;

$best_result = 0;
foreach($data as $cloud) {
	$id = $cloud[$ID];
	$sum = energy_goes2($data, $SINGLE_IMPULSE, $id, $explosion);
	if($best_result < $sum) {
		$best_result = $sum;
	}
}

printf("Result 3: %d\n", $best_result);

?>
