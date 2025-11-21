<?php

///////////////////////////////////////////////////////////////////////////
// constants

$test = 0;

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

// https://stackoverflow.com/questions/34034730/how-to-enable-color-for-php-cli

// example
// output red text
// php -r 'echo "\033[31m some colored text \033[0m some white text \n";'
//Examples:
//formatPrint(['blue', 'bold', 'italic','strikethrough'], "Wohoo");

function formatPrint(array $format=[],string $text = '') {
	$codes=[
		'bold'=>1,
		'italic'=>3, 'underline'=>4, 'strikethrough'=>9,
		'black'=>30,   'red'=>31,   'green'=>32,   'yellow'=>33,   
		'blue'=>34,   'magenta'=>35,   'cyan'=>36,   'white'=>37,
		'blackbg'=>40, 'redbg'=>41, 'greenbg'=>42, 'yellowbg'=>43, 
		'bluebg'=>44, 'magentabg'=>45, 'cyanbg'=>46, 'lightgreybg'=>47
	];
	$formatMap = array_map(function ($v) use ($codes) {
		return $codes[$v]; 
		}, $format);
	return "\e[".implode(';',$formatMap).'m'.$text."\e[0m";
}

function print_ducks_change($old_data, $data) {
	$ducks = "";
	foreach($old_data as $key => $col) {
		$duck = sprintf("%3d ", $data[$key]);
		if($old_data[$key] != $data[$key]) {
			if($old_data[$key] < $data[$key]) {
				$ducks .= formatPrint(['greenbg'], $duck);
			} else {
				$ducks .= formatPrint(['redbg'], $duck);
			}
		} else {
			$ducks .= $duck;
		}
	}
	print("$ducks\n");
}

function move_ducks($dir, $max, $rounds, $rounds_used) {
	global $data, $test;
	$old_data = $data;
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
		if($dir == "l" && $test == 1) {
			print_ducks_change($old_data, $data);
		}
		$old_data = $data;
	}
	return $rounds_used;
}

function move_ducks2() {
	// this version uses the knowledge, that all the ducks missing
	// from the left part of the columns, all require 1 round
	global $data;
	
	$average = array_sum($data) / sizeof($data);
	
	printf("Average number of ducks in columns: %20d\n", $average);
	
	$rounds = 0;
	foreach($data as $column) {
		if($column < $average) {
			$rounds += $average - $column;
		}
	}
	
	return $rounds;
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
print_ducks_change($data, $data);

// then move left
$rounds_used = move_ducks("l", "y", $rounds, $rounds_used);

print_ducks($data, $rounds_used);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2025_q11_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$columns = sizeof($data);

$rounds_used = 0;

// first move right
$rounds_used = move_ducks("r", "n", 0, $rounds_used);
print_ducks_change($data, $data);

// then move left
$rounds_used = move_ducks("l", "n", 0, $rounds_used);

print_ducks($data, $rounds_used);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

// https://www.reddit.com/r/everybodycodes/comments/1ozvbnq/comment/npekmd0/
// Key insight 2 is that each round in a phase
//    (phase 2)
// effectively only moves one duck from one column to another. 
// i can't quite prove that this is the case

$file3 = './everybody_codes_e2025_q11_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);
$columns = sizeof($data);

$rounds_used = 0;

print("Ducks ready for phase 1.\n");
// first move right
$rounds_used = move_ducks("r", "n", 0, $rounds_used);
//print_ducks($data, $rounds_used);
printf("Rounds used.......................: %20d\n", $rounds_used);
// note: no rounds!

print("And now phase 2.\n");

// then move left
$rounds_used = move_ducks2();
printf("Rounds used.......................: %20d\n", $rounds_used);

?>
