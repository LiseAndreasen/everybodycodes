<?php

///////////////////////////////////////////////////////////////////////////
// constants

$input1 = file_get_contents('./everybody_codes_e2025_q08_p1_ex1.txt', true);
$input2 = file_get_contents('./everybody_codes_e2025_q08_p2_ex1.txt', true);
$input3 = file_get_contents('./everybody_codes_e2025_q08_p3_ex1.txt', true);

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			$map = explode(",", $line);
		}
	}
	return $map;
}

function count_knots1($map) {
	$nails = 256;
	$knots = 0;
	foreach($map as $key => $nail) {
		if($key == 0) {
			// because there's not a nail in front of this one
			continue;
		}
		$prev_nail = $map[$key - 1];
		$strings[] = array($prev_nail, $nail);
	}

	foreach($strings as $key1 => $string1) {
		foreach($strings as $key2 => $string2) {
			if($key1 <= $key2) {
				// don't double count
				// a string doesn't cross itself
				continue;
			}
			// 2 strings (a,b) and (c,d) cross if
			// min(a,b) < (exactly 1 of c,d) < max(a,b)
			$s1a = $string1[0];
			$s1b = $string1[1];
			$s2c = $string2[0];
			$s2d = $string2[1];
			$s1min = min($s1a, $s1b);
			$s1max = max($s1a, $s1b);
			if($s1a == $s2c || $s1a == $s2d || $s1b == $s2c || $s1b == $s2d) {
				// the 2 strings share a nail, no crossing
				continue;
			}
			$hits = 0;
			if($s1min < $s2c && $s2c < $s1max) {
				$hits++;
			}
			if($s1min < $s2d && $s2d < $s1max) {
				$hits++;
			}
			if($hits == 1) {
				$knots++;
			}
		}
	}
	return $knots;
}

function count_knots2($map) {
	$nails = 256;
	$knots = 0;
	$max_knots = 0;
	$max_string = "";
	foreach($map as $key => $nail) {
		if($key == 0) {
			// because there's not a nail in front of this one
			continue;
		}
		$prev_nail = $map[$key - 1];
		$strings1[] = array($prev_nail, $nail);
	}
	
	// construct all possible strings
	for($i=1;$i<=$nails;$i++) {
		for($j=$i+1;$j<=$nails;$j++) {
			$strings2[] = array($i, $j);
		}
	}

	foreach($strings2 as $string2) {
		$poss_hits = 0;
		foreach($strings1 as $string1) {
			// 2 strings (a,b) and (c,d) cross if
			// min(a,b) < (exactly 1 of c,d) < max(a,b)
			$s1a = $string1[0];
			$s1b = $string1[1];
			$s2c = $string2[0];
			$s2d = $string2[1];
			$s1min = min($s1a, $s1b);
			$s1max = max($s1a, $s1b);
			$s2min = min($s2c, $s2d);
			$s2max = max($s2c, $s2d);
			if($s1min == $s2min && $s1max == $s2max) {
				// the possible string coincides with an actual string
				// this counts as a hit
				$poss_hits++;
				continue;
			}
			if($s1a == $s2c || $s1a == $s2d || $s1b == $s2c || $s1b == $s2d) {
				// the 2 strings share a nail, no crossing
				continue;
			}
			$hits = 0;
			if($s1min < $s2c && $s2c < $s1max) {
				$hits++;
			}
			if($s1min < $s2d && $s2d < $s1max) {
				$hits++;
			}
			if($hits == 1) {
				$poss_hits++;
			}
		}
		if($max_knots < $poss_hits) {
			$max_knots = $poss_hits;
			$max_string = "($s2c,$s2d)";
		}
	}
	return array($max_knots, $max_string);
}

///////////////////////////////////////////////////////////////////////////
// main program

$map = get_input($input1);
//print_r($map);
$nails = 8;
$hits = 0;
foreach($map as $key => $nail) {
	if($key == 0) {
		// because there's not a nail in front of this one
		continue;
	}
	$prev_nail = $map[$key - 1];
	$nail_dist = abs($nail - $prev_nail);
	if($nail_dist == $nails / 2) {
		$hits++;
	}
}

printf("Result 1: %d\n", $hits);

$map = get_input($input2);
$knots = count_knots1($map);

printf("Result 2: %d\n", $knots);

$map = get_input($input3);
$knots = count_knots2($map);

printf("Result 3: %d %s\n", $knots[0], $knots[1]);

?>
