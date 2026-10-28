<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "03";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		// line: ISO-3210 7654
		if(strlen($line)>2) {
			$line2 = explode(" ", $line);
			$line3 = explode("-", $line2[0]);
			$data[] = [$line3[1], $line2[1]];
		}
	}
	return $data;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$vote_sum = 0;
foreach($data as $vote) {
	$vote_num = 0;
	$vote1 = array_reverse(str_split($vote[0]));
	$vote2 = array_reverse(str_split($vote[1]));
	$sz = sizeof($vote1);
	// systematically build the vote number in decimal
	for($v=0;$v<$sz;$v++) {
		$vote_num += $vote2[$v] * pow($vote1[$v], $v);
	}
	$vote_sum += $vote_num;
}

printf("Result 1: %d\n", $vote_sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$vote_sum = 0;
foreach($data as $vote) {
	$vote_num = 0;
	$vote1 = array_reverse(str_split($vote[0]));
	$vote2 = array_reverse(str_split($vote[1]));
	// if vote1 is too short, repeat it
	while(sizeof($vote1) < sizeof($vote2)) {
		foreach($vote1 as $v) {
			$vote1[] = $v;
		}
	}
	$sz = sizeof($vote2);
	// systematically build the vote number in decimal
	for($v=0;$v<$sz;$v++) {
		$vote_num += $vote2[$v] * pow($vote1[$v], $v);
	}
	$vote_sum += $vote_num;
}

printf("Result 2: %d\n", $vote_sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3_ex1.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$vote_sum = 0;
foreach($data as $vote) {
	$vote_num = 0;
	$vote1 = array_reverse(str_split($vote[0]));
	$vote2 = array_reverse(str_split($vote[1]));
	// if vote1 is too short, repeat it
	while(sizeof($vote1) < sizeof($vote2)) {
		foreach($vote1 as $v) {
			$vote1[] = $v;
		}
	}
	$sz = sizeof($vote2);
	// systematically build the vote number in decimal
	for($v=0;$v<$sz;$v++) {
		$vote_num += $vote2[$v] * pow($vote1[$v], $v);
	}
	
	// is it possible to reach this vote_num with a smaller vote2?
	
	$vote_sum += $vote_num;
}

printf("Result 3: %d\n", $vote_sum);

?>
