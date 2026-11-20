<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "19";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	$data2 = [];
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert string to array
			$data2[] = str_split($line);
		} else {
			$data[] = $data2;
			$data2 = [];
		}
	}
	if(0 < sizeof($data2)) {
		$data[] = $data2;
	}
	return $data;
}

// https://stackoverflow.com/questions/5506888/permutations-all-possible-sets-of-numbers
// create permutations
function pc_permute($items, $perms = array( )) {
	global $perms1;
    if (empty($items)) { 
    	$perms1[] = implode("", $perms);
    }  else {
        for ($i = count($items) - 1; $i >= 0; --$i) {
             $newitems = $items;
             $newperms = $perms;
             list($foo) = array_splice($newitems, $i, 1);
             array_unshift($newperms, $foo);
             pc_permute($newitems, $newperms);
         }
    }
}

function count_crystals($data, $colors1, $colors2) {
	$colors2_arr = str_split($colors2);
	if(in_array(1, $colors2_arr)) {
		// convert from 1234 to CMYK
		$new_colors1 = str_split("CMYK");
		$new_colors2 = [];
		foreach($colors2_arr as $i => $c) {
			$new_colors2[] = $new_colors1[(int)$c - 1];
		}
		foreach($data[1] as $i => $col) {
			foreach($col as $j => $cell) {
				$data[1][$i][$j] = $new_colors2[$cell - 1];
			}
		}
	}

	$result = [];
	foreach($data[0] as $i => $col) {
		foreach($col as $j => $cell) {
			$color1 = $data[0][$i][$j] . $data[1][$i][$j];
			switch($color1) {
				case "KK":
				case "KC":
				case "KM":
				case "KY":
				case "CK":
				case "MK":
				case "YK":
					$result[$i][$j] = "K";
					break;
				case "CM":
				case "MC":
					$result[$i][$j] = "B";
					break;
				case "CY":
				case "YC":
					$result[$i][$j] = "G";
					break;
				case "MY":
				case "YM":
					$result[$i][$j] = "R";
					break;
				case "CC":
					$result[$i][$j] = "C";
					break;
				case "MM":
					$result[$i][$j] = "M";
					break;
				case "YY":
					$result[$i][$j] = "Y";
					break;
			}
		}
	}

	// count occurrences in 2d array
	$arr2 = array_merge(...$result);
	$arr3 = array_count_values($arr2);
	rsort($arr3);

	$product = 1;
	for($i=0;$i<=2;$i++) {
		$product *= $arr3[$i];
	}

	return $product;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$product = count_crystals($data, "CMYK", "CMYK");

printf("Result 1: %d\n", $product);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

// construct all possible permutations
$letters2 = str_split("1234");
// result of permute ends up in perms1
$perms1 = array();
pc_permute($letters2);

$maximum = 0;
foreach($perms1 as $perm) {
	$product = count_crystals($data, "CMYK", $perm);
	if($maximum < $product) {
		$maximum = $product;
	}
}

printf("Result 2: %d\n", $maximum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

print("\n");

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

// analyze input
$arr2 = array_merge(...$data[0]);
$arr3 = array_count_values($arr2);

$no_of_black_in_result = $arr3["K"];
$no_of_squares = array_sum($arr3);
printf("No. of black in result: %15d\n", $no_of_black_in_result);
printf("No. of squares........: %15d\n", $no_of_squares);

printf("Cyan..................: %15d\n", $arr3["C"]);
printf("Magenta...............: %15d\n", $arr3["M"]);
printf("Yellow................: %15d\n", $arr3["Y"]);
printf("Black.................: %15d\n", $arr3["K"]);

// the best product will include the black sqaures
// but this isn't possible if there are too few black
// including black means the number of black being about a third of the total
// and allocating 2 mixed colors a third each
if(floor($no_of_squares / 3) <= $no_of_black_in_result) {
	print("Optimal solution using black!\n");
	printf("Squares to allocate...: %15d\n", $no_of_squares);
	$not_black = $no_of_squares - $no_of_black_in_result;
	$a = floor($not_black / 2);
	$b = ceil($not_black / 2);
	$c = $no_of_black_in_result;
	printf("Check this solution...:\n");
	printf("......................: %15d *\n", $a);
	printf("......................: %15d *\n", $b);
	printf("......................: %15d =\n", $c);
	printf("......................: %15d\n", $a * $b * $c);
} else {
	// this solution allocates the 3 mixed colors 1 third each
	print("Black will be wasted.\n");
	printf("Squares to allocate...: %15d\n", $no_of_squares - $no_of_black_in_result);
	$not_black = $no_of_squares - $no_of_black_in_result;
	$a = floor($not_black / 3);
	$c = ceil($not_black / 3);
	$b = $not_black - $a - $c;
	printf("Check this solution...:\n");
	printf("......................: %15d *\n", $a);
	printf("......................: %15d *\n", $b);
	printf("......................: %15d =\n", $c);
	printf("......................: %15d\n", $a * $b * $c);
}

//printf("Result 3: %d\n", $hits);

?>
