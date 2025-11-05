<?php

///////////////////////////////////////////////////////////////////////////
// constants

$input1 = file_get_contents('./everybody_codes_e2025_q02_p1.txt', true);
$input2 = file_get_contents('./everybody_codes_e2025_q02_p2.txt', true);
$input3 = file_get_contents('./everybody_codes_e2025_q02_p3.txt', true);

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		if(strlen($line)>2) {
			if(preg_match_all('/(-?\d+)/', $line, $matches)) {
				$a["x"] = $matches[0][0];
				$a["y"] = $matches[0][1];
			}
		}
	}
	return $a;
}

function add_complex($a, $b) {
	$c["x"] = $a["x"] + $b["x"];
	$c["y"] = $a["y"] + $b["y"];
	return $c;
}

// [X1,Y1] * [X2,Y2] = [X1 * X2 - Y1 * Y2, X1 * Y2 + Y1 * X2]
function mult_complex($a, $b) {
	$c["x"] = $a["x"] * $b["x"] - $a["y"] * $b["y"];
	$c["y"] = $a["x"] * $b["y"] + $a["y"] * $b["x"];
	return $c;
}

// [X1,Y1] / [X2,Y2] = [X1 / X2, Y1 / Y2]
function div_complex($a, $b) {
	$c["x"] = (int) ($a["x"] / $b["x"]);
	$c["y"] = (int) ($a["y"] / $b["y"]);
	return $c;
}

///////////////////////////////////////////////////////////////////////////
// main program

$a = get_input($input1);

$r["x"] = 0;
$r["y"] = 0;
$ten["x"] = 10;
$ten["y"] = 10;

// complete three cycles of the following operations:
//    Multiply the result by itself.
//    Divide the result by [10,10].
//    Add A to the result.

for($i=1;$i<=3;$i++) {
	$r = mult_complex($r, $r);
	$r = div_complex($r, $ten);
	$r = add_complex($r, $a);
}

printf("Result 1: [%d,%d]\n", $r["x"], $r["y"]);

// top left corner
$a = get_input($input2);

$thousand["x"] = 1000;
$thousand["y"] = 1000;

// bottom right corner
$b = add_complex($a, $thousand);

// divide into 101 points on each axis
// that's a distance of 10 between the points
// for each point:
// Initialise the check result as [0,0].
// perform 100 cycles of the following steps:
//    Multiply the result by itself.
//    Divide the result by [100000,100000].
//    Add the coordinates of the point under examination.
// If at the end of any of the 100 cycles the X or Y coordinate 
// of the result exceeds 1000000 or falls below -1000000, 
// that point will not be engraved.
// count the engraved numbers

$c100000["x"] = 100000;
$c100000["y"] = 100000;
$engraved_count = 0;

for($i=$a["x"];$i<=$b["x"];$i+=10) {
	for($j=$a["y"];$j<=$b["y"];$j+=10) {
		$p["x"] = $i;
		$p["y"] = $j;
		$engraving = 1;
		$r["x"] = 0;
		$r["y"] = 0;
		for($k=1;$k<=100;$k++) {
			$r = mult_complex($r, $r);
			$r = div_complex($r, $c100000);
			$r = add_complex($r, $p);
			if(abs($r["x"]) > 1000000 || abs($r["y"]) > 1000000) {
				$engraving = 0;
				break;
			}
		}
		$engraved_count += $engraving;
	}
}

printf("Result 2: %d\n", $engraved_count);

// do the same, but with distance 1

// top left corner
$a = get_input($input3);

$thousand["x"] = 1000;
$thousand["y"] = 1000;

// bottom right corner
$b = add_complex($a, $thousand);

$c100000["x"] = 100000;
$c100000["y"] = 100000;
$engraved_count = 0;

for($i=$a["x"];$i<=$b["x"];$i+=1) {
	// progress
	if($i % 100 == 0) {
		print("*");
	}
	for($j=$a["y"];$j<=$b["y"];$j+=1) {
		$p["x"] = $i;
		$p["y"] = $j;
		$engraving = 1;
		$r["x"] = 0;
		$r["y"] = 0;
		for($k=1;$k<=100;$k++) {
			$r = mult_complex($r, $r);
			$r = div_complex($r, $c100000);
			$r = add_complex($r, $p);
			if(abs($r["x"]) > 1000000 || abs($r["y"]) > 1000000) {
				$engraving = 0;
				break;
			}
		}
		$engraved_count += $engraving;
	}
}
print("\n");

printf("Result 3: %d\n", $engraved_count);

?>
