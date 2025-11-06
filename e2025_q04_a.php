<?php

///////////////////////////////////////////////////////////////////////////
// constants

$input1 = file_get_contents('./everybody_codes_e2025_q04_p1.txt', true);
$input2 = file_get_contents('./everybody_codes_e2025_q04_p2.txt', true);
$input3 = file_get_contents('./everybody_codes_e2025_q04_p3.txt', true);

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		if(strlen($line)>0) {
			$list[] = $line;
		}
	}
	return $list;
}

///////////////////////////////////////////////////////////////////////////
// main program

$list = get_input($input1);

// calculate how many teeth went by
$teeth = 2025 * $list[0];

// calculate the turns of the last gear
$turns = $teeth / $list[sizeof($list)-1];
printf("Result 1: %20d\n", $turns);

$list = get_input($input2);

// turns of last gear
$turns_last = 10000000000000;

// calculate how many teeth went by
$teeth = $turns_last * $list[sizeof($list)-1];

// calculate turns for first gear
$turns_first = ceil($teeth / $list[0]);

printf("Result 2: %20d\n", $turns_first);

$list = get_input($input3);

$list_sz = sizeof($list);
$teeth_first = $list[0];
$all_teeth = 100 * $teeth_first;

for($i=1;$i<$list_sz-1;$i++) {
	$teeth = explode("|", $list[$i]);
	$turns_this = $all_teeth / $teeth[0];
	$all_teeth = $turns_this * $teeth[1];
}

$turns_last = $all_teeth / $list[$list_sz-1];
printf("Result 3: %20d\n", $turns_last);

?>
