<?php

///////////////////////////////////////////////////////////////////////////
// constants

$input1 = file_get_contents('./everybody_codes_e2025_q07_p1.txt', true);
$input2 = file_get_contents('./everybody_codes_e2025_q07_p2.txt', true);
$input3 = file_get_contents('./everybody_codes_e2025_q07_p3_ex2.txt', true);

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	$names = 1;
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		if(strlen($line)>2) {
			if($names == 1) {
				$map[0] = explode(",", $line);
				$names = 0;
			} else {
				$letters = explode(" > ", $line);
				$letter1 = $letters[0];
				$letter2 = explode(",", $letters[1]);
				$map[1][$letter1] = $letter2;
			}
		}
	}
	return $map;
}

function check_this_name($name, $rules) {
	$name_good = 1;
	$name_arr = str_split($name);
	$name_lgt = sizeof($name_arr);
	foreach($name_arr as $key => $letter) {
		if($key == $name_lgt - 1) {
			// we are done
			break;
		}
		$next_letter = $name_arr[$key + 1];
		if(!isset($rules[$letter])) {
			$name_good = 0;
			break;
		} else {
			$rule_good = array_search($next_letter, $rules[$letter]);
			if($rule_good === false) {
				$name_good = 0;
				break;
			}
		}
	}
	return $name_good;
}

function check_names($map, $print) {
	$names = $map[0];
	$rules = $map[1];
	$key_sum = 0;

	foreach($names as $key2 => $name) {
		$name_good = check_this_name($name, $rules);
		if($name_good == 1) {
			switch($print) {
				case 1:
					printf("Name good..: %s\n", $name);
					break;
				case 2:
					$key_sum += $key2 + 1;
					break;
			}
		}
	}
	if($print == 2) {
		printf("Sum of keys: %d\n", $key_sum);
	}
}

function permute_names($prefix, $rules) {
	$good_names = 0;
	$name_lgt = strlen($prefix);
	if(7 <= $name_lgt && $name_lgt <= 11) {
		// this is actually a good name too
		$good_names += 1;
	}
	if($name_lgt == 11) {
		// no further permutations possible
		return $good_names;
	}
	$last_letter = substr($prefix, -1, 1);
	if(isset($rules[$last_letter])) {
		foreach($rules[$last_letter] as $next_letter) {
			$next_prefix = $prefix . $next_letter;
			$good_names += permute_names($next_prefix, $rules);
		}
	}
	return $good_names;
}

///////////////////////////////////////////////////////////////////////////
// main program

$map = get_input($input1);
check_names($map, 1);

$map = get_input($input2);
check_names($map, 2);

$map = get_input($input3);
$prefixes = $map[0];
$rules = $map[1];
$good_names = 0;
foreach($prefixes as $prefix) {
	$prefix_good = check_this_name($prefix, $rules);
	if($prefix_good == 1) {
		$good_names += permute_names($prefix, $rules);
	}
}
printf("Good names.: %d\n", $good_names);

?>
