<?php

///////////////////////////////////////////////////////////////////////////
// constants

$input1 = file_get_contents('./everybody_codes_e2025_q03_p1.txt', true);
$input2 = file_get_contents('./everybody_codes_e2025_q03_p2.txt', true);
$input3 = file_get_contents('./everybody_codes_e2025_q03_p3.txt', true);

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			$crates = explode(",", $line);
		}
	}
	return $crates;
}

///////////////////////////////////////////////////////////////////////////
// main program

$crates = get_input($input1);

// a set can't have 2 crates of the same size - throw duplicates away
$crates = array_unique($crates);
// the remaining crates form a valid set
$set = array_sum($crates);
print("Result 1: $set\n");

$crates = get_input($input2);
// a set can't have 2 crates of the same size - throw duplicates away
$crates = array_unique($crates);
// we're interested in the smallest crates
sort($crates);
$sz = sizeof($crates);
for($i=20;$i<$sz;$i++) {
	// throw the big crates away
	unset($crates[$i]);
}
// this is our valid set
$set = array_sum($crates);
print("Result 2: $set\n");

$crates = get_input($input3);
// there has to be a set for every group of crates with the same size
// count the number of crates in each of these groups
$counts = array_count_values($crates);
// find the largest group
$sets = max($counts);
print("Result 3: $sets\n");

?>
