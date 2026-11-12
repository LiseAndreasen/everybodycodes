<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "13";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	$counter = 0;
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$data2 = explode(" ", $line);
			$data3 = explode(":", $data2[1]);
			$data[$counter][$data2[0]] = $data3;
		} else {
			$counter++;
		}
	}
	return $data;
}

function calculate_lcm($hands) {
	foreach($hands as $i => $h) {
		if($i == 0) {
			$lcm = $h;
		} else {
			$lcm = gmp_lcm($lcm, $h);
		}
	}
	return (int) $lcm;
}

function detect_meetings($current, $next, $lcm) {
	$meetings = 0;
	foreach($current as $i => $c) {
		// it makes more sense to replace 0 with lcm
		// when the other end of the interval is high
		if($current[$i] == 0 && $lcm / 2 < $next[$i]) {
			$current[$i] = $lcm;
		}
		if($next[$i] == 0 && $lcm / 2 < $current[$i]) {
			$next[$i] = $lcm;
		}
	}
	foreach($current as $i => $c) {
		$c_i = $current[$i];
		$n_i = $next[$i];
		foreach($current as $j => $d) {
			if($i <= $j) {
				continue;
			}
			$c_j = $current[$j];
			$n_j = $next[$j];
//			printf("Meeting occurring at %3d %3d\n.................... %3d %3d? ", $c_i, $c_j, $n_i, $n_j);
			// don't count both arms stopped here twice
			if($c_i == $c_j) {
//				print("\n");
				continue;
			}
			// meeting at time 0
			if($n_i % $lcm == 0 && $n_j % $lcm == 0) {
				$meetings++;
//				print("Yes!\n");
				continue;
			}
			// 2 lines crossing, ai x + bi = y
			// or rather (ni - ci) x + ci = y
			// we want the solution 0 < x <= 1
			if($n_i - $c_i == $n_j - $c_j) {
				if($c_i == $c_j) {
					$meetings++;
//					print("Yes!");
				}
//				print("\n");
				continue;
			}
			$x = ($c_j - $c_i) / (($n_i - $c_i) - ($n_j - $c_j));
			if(0 < $x && $x <= 1) {
				$meetings++;
//				print("Yes!\n");
				continue;
			}
//			print("\n");
		}
	}
	return $meetings;
}

function let_the_clock_run($data) {
	// calculate lcm
	$lcm = calculate_lcm($data["hands"]);
	// calculate leaps with this new scale
	foreach($data["hands"] as $h) {
		$leaps[] = $lcm / $h;
	}
	foreach($data["direction"] as $i => $d) {
		if(strcmp($d, "CCW") == 0) {
			$leaps[$i] *= -1;
		}
	}

	$current = $data["current"];
	foreach($current as $i => $c) {
		$current[$i] = $c * abs($leaps[$i]);
	}

	// let the clock run
	$seconds = 0;
	$meetings = 0;
	while($seconds <= max($data["move"])) {
//		print("Seconds: $seconds\n");
		$next = [];
		foreach($current as $i => $c) {
			if($seconds < $data["move"][$i]) {
				$next[] = ($c + $leaps[$i] + $lcm) % $lcm;
			} else {
				$next[] = $c;
			}
		}
		$meetings += detect_meetings($current, $next, $lcm);
		$current = $next;
		$seconds++;
	}
	return $meetings;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$meetings = let_the_clock_run($data[0]);

printf("Result 1: %d\n", $meetings);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$meetings = 0;
for($i=0;$i<sizeof($data);$i++) {
	print("=");
}
print("\n");
foreach($data as $clock) {
	print(".");
	$meetings += let_the_clock_run($clock);
}
print("\n");

printf("Result 2: %d\n", $meetings);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$meetings = 0;
for($i=0;$i<sizeof($data);$i++) {
	print("=");
}
print("\n");
foreach($data as $clock) {
	print(".");
//	$meetings += let_the_clock_run($clock);
}
print("\n");

printf("Result 3: %d\n", $meetings);

?>
