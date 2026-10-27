<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "01";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$data[] = explode(",", $line);
		}
	}
	return $data;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$missing_sum = 0;
foreach($data[0] as $n) {
	// take square root, round up, take square
	$max_n = pow(ceil(pow($n, 0.5)), 2);
	$missing_sum += $max_n - $n;
}
printf("Result 1: %d\n", $missing_sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$missing_sum = 0;
foreach($data[0] as $n) {
	// divide by 4, round up, multuply by 4
	// should be minimum 8
	$max_n = max(4 * ceil($n / 4), 8);
	$missing_sum += $max_n - $n;
}
printf("Result 2: %d\n", $missing_sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);
$missing_sum = 0;
foreach($data[0] as $n) {
	// potentially fill in blank cell in the middle
	// take square root and round up
	$side = ceil(pow($n + 1, 0.5));
	// "round up" to odd
	if($side % 2 == 0) {
		$side++;
	}
	// should be minimum 3
	if($side < 3) {
		$side = 3;
	}
	// take square, subtract blank cell in the middle
	$max_n = pow($side, 2) - 1;
	$missing_sum += $max_n - $n;
}
printf("Result 3: %d\n", $missing_sum);

?>
