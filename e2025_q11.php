<?php

///////////////////////////////////////////////////////////////////////////
// constants

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		if(strlen($line)>0) {		
			$data[] = $line;
		}
	}
	return $data;
}

function print_ducks($data, $rounds_used) {
	$columns = sizeof($data);
	$check_sum = 0;
	printf("Round %5d: ", $rounds_used);
	for($i=0;$i<$columns;$i++) {
		printf("%4d ", $data[$i]);
		$check_sum += ($i + 1) * $data[$i];
	}
	printf("\nCheck sum: %6d\n\n", $check_sum);
}

function move_ducks($dir, $max, $rounds, $rounds_used) {
	global $data;
	$columns = sizeof($data);
	$ducks_moved = 1;
	while($ducks_moved == 1 && ($rounds_used < $rounds || $max == "n")) {
		$ducks_moved = 0;
		for($i=0;$i<$columns-1;$i++) {
			if($data[$i+1] < $data[$i] && $dir == "r") {
				$data[$i]--;
				$data[$i+1]++;
				$ducks_moved = 1;
			}
			if($data[$i] < $data[$i+1] && $dir == "l") {
				$data[$i]++;
				$data[$i+1]--;
				$ducks_moved = 1;
			}
		}
		if($ducks_moved == 1) {
			$rounds_used++;
		}
	}
	return $rounds_used;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2025_q11_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$columns = sizeof($data);
$rounds = 10;

$rounds_used = 0;

// first move right
$rounds_used = move_ducks("r", "y", $rounds, $rounds_used);

// then move left
$rounds_used = move_ducks("l", "y", $rounds, $rounds_used);

print_ducks($data, $rounds_used);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2025_q11_p2_ex2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$columns = sizeof($data);

$rounds_used = 0;

// first move right
$rounds_used = move_ducks("r", "n", 0, $rounds_used);

// then move left
$rounds_used = move_ducks("l", "n", 0, $rounds_used);

print_ducks($data, $rounds_used);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2025_q11_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
