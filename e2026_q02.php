<?php

include "topoSort.php";

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "02";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$pairs = explode(" ", $line);
			foreach($pairs as $p => $pair) {
				[$p0, $p1, $p2] = str_split($pair);
				if($p1 == ">") {
					$pairs[$p] = $p2 . "<" . $p0;
				}
			}
			$data[] = $pairs;
		}
	}
	return $data;
}

// sort according to own rule
function cmp($a, $b) {
	global $pairs_order;
	
    if ($a == $b) {
        return 0;
    }
	$tst_str = $a . "<" . $b;
	if(in_array($tst_str, $pairs_order)) {
		return -1;
	} else {
		return 1;
	}
}

function incomplete_sort($list, $pairs) {
	global $pairs_order;
	
	// analyze order
	$alloys_sz = sizeof($list);
	// 0/1, soft/hard
	$alloys[0] = array_fill(1, $alloys_sz, 0);
	$alloys[1] = array_fill(1, $alloys_sz, 0);
	foreach($pairs as $pair) {
		[$p0, $p1, $p2] = str_split($pair);
		$alloys[0][$p0]++;
		$alloys[1][$p2]++;
	}
	// after this sweep, 2 alloys will have a sum of soft and hard
	// of alloys_sz - 2, the rest alloys_sz - 1
	
	$undef_alloys = [];
	foreach($list as $a) {
		if($alloys[0][$a] + $alloys[1][$a] == $alloys_sz - 2) {
			$undef_alloys[] = $a;
		}
	}
	[$candidate0, $candidate1] = $undef_alloys;
	// the softest candidate will have a high value for the soft alloy count
	// add this pair to the list of pairs
	if($alloys[0][$candidate1] < $alloys[0][$candidate0]) {
		$pairs[] = $candidate0 . "<" . $candidate1;
	} else {
		$pairs[] = $candidate1 . "<" . $candidate0;
	}
	
	$pairs_order = $pairs;
	usort($list, "cmp");
	return $list;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$checksum = 0;

foreach($data as $p => $pairs) {
	// construct unsorted list
	if(sizeof($pairs) == 10) {
		$sz = 5;		// 10 = 5 * 4 / 2
	}
	$list = range(1, $sz);
	
	// sort
	$pairs_order = $pairs;		// appears to be needed
	usort($list, "cmp");
	
	// reverse order
	$list = array_reverse($list);
	
	// turn list into number
	$no = implode($list);
	
	// add to checksum
	$checksum += $no;
}

printf("Result 1: %d\n", $checksum);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$checksum = 0;

foreach($data as $p => $pairs) {
	// construct unsorted list
	if(sizeof($pairs) == 9) {
		$sz = 5;		// 9 = 5 * 4 / 2 - 1
	}
	if(sizeof($pairs) == 35) {
		$sz = 9;		// 35 = 9 * 8 - 1
	}
	$list = range(1, $sz);
	
	// sort
	$list = incomplete_sort($list, $pairs);
	
	// reverse order
	$list = array_reverse($list);
	
	// turn list into number
	$no = implode($list);
	
	// add to checksum
	$checksum += $no;
}

printf("Result 2: %d\n", $checksum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$checksum = 0;

foreach($data as $pairs) {
	// order list of alloys topologically
	$nodes = [];
	$edges = [];
	foreach($pairs as $pair) {
		[$p0, $p1, $p2] = str_split($pair);
		$nodes[$p0] = $p0;
		$nodes[$p2] = $p2;
		$edges[] = [$p0, $p2];
	}
	$ordered_list = topoSort($nodes, $edges);
	
	// reverse order
	$list = array_reverse($ordered_list);

	// turn list into number
	$no = implode($list);

	// add to checksum
	$checksum += $no;
}

printf("Result 3: %d\n", $checksum);

?>
