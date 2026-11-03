<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "06";
$beginning_point = "NULL";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			$line2 = explode(":", $line);
			$line3 = explode(",", $line2[1]);
			$data[$line2[0]] = $line3;
		}
	}
	return $data;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

// queue of ingredients to work through
// begin with the ingredients of the beginning point
$queue = $data[$beginning_point];
// keep a list of ingredients used
$used = [];
while(0 < sizeof($queue)) {
	$ingredient = array_shift($queue);
	// if this particular ingredient isn't simply the beginning point
	if(strcmp($ingredient, $beginning_point) != 0) {
		// if this ingredient isn't already in used list
		if(!isset($used[$ingredient])) {
			// add ingredient to used
			$used[$ingredient] = $ingredient;
			// if this ingredient has further ingredients
			if(isset($data[$ingredient])) {
				// add them to the queue
				foreach($data[$ingredient] as $i) {
					$queue[] = $i;
				}
			}
		}
	}
}

printf("Result 1: %d\n", sizeof($used));

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

// create the reverse table - this ingredient is used by this item
foreach($data as $item => $ingredients) {
	$all_ingredients[$item] = $item;
	foreach($ingredients as $i) {
		$all_ingredients[$i] = $i;
	}
}
$used_by = [];
foreach($all_ingredients as $i) {
	$used_by[$i] = [];
}
foreach($all_ingredients as $ingredient) {
	if(isset($data[$ingredient])) {
		foreach($data[$ingredient] as $i) {
			$used_by[$i][$ingredient] = $ingredient;
		}
	}
}

// same as part 1, just the other direction
$queue = $used_by[$beginning_point];
$uses = [];
while(0 < sizeof($queue)) {
	$ingredient = array_shift($queue);
	// if this ingredient isn't already in uses list
	if(!isset($uses[$ingredient])) {
		// add ingredient to used
		$uses[$ingredient] = $ingredient;
		// if this ingredient is used by further ingredients
		// add them to the queue
		foreach($used_by[$ingredient] as $i) {
			// if this particular ingredient isn't simply the beginning point
			if(strcmp($i, $beginning_point) != 0) {
				$queue[] = $i;
			}
		}
	}
}

printf("Result 2: %d\n", sizeof($uses));

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

// create list of all ingredients
$all_ingredients = [];
foreach($data as $item => $ingredients) {
	$all_ingredients[$item] = $item;
	foreach($ingredients as $i) {
		$all_ingredients[$i] = $i;
	}
}

$used = [];
foreach($all_ingredients as $beginning_point2) {
	// queue of ingredients to work through
	// begin with the ingredients of the beginning point
	$used[$beginning_point2] = [];
	if(!isset($data[$beginning_point2])) {
		continue;
	}
	$queue = $data[$beginning_point2];
	// keep a list of ingredients used
	while(0 < sizeof($queue)) {
		$ingredient = array_shift($queue);
		// if this particular ingredient isn't simply the beginning point
		if(strcmp($ingredient, $beginning_point2) != 0) {
			// if this ingredient isn't already in used list
			if(!isset($used[$beginning_point2][$ingredient])) {
				// add ingredient to used
				$used[$beginning_point2][$ingredient] = $ingredient;
				// if this ingredient has further ingredients
				if(isset($data[$ingredient])) {
					// add them to the queue
					foreach($data[$ingredient] as $i) {
						$queue[] = $i;
					}
				}
			}
		}
	}
}

$null_counter1 = 0;
foreach($used as $i0 => $i1) {
	$null_counter2 = 0;
	foreach($i1 as $i2) {
		$pos = strpos($i2, $beginning_point);
		if($pos !== false) {
			$null_counter2++;
		}
	}
	if(2 <= $null_counter2) {
		$null_counter1++;
	}
}

printf("Result 3: %d\n", $null_counter1);

?>
