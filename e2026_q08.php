<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "08";
$EMPTY = ".";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert string to array
			$data[] = str_split($line);
		}
	}
	return $data;
}

// change (y, x) map to (x, y) map
function pivot($map) {
	$map_height = sizeof($map);
	$map_width = sizeof($map[0]);
	for($j=0;$j<$map_height;$j++) {
		for($i=0;$i<$map_width;$i++) {
			$map2[$i][$j] = $map[$j][$i];
		}
	}
	return $map2;
}

function print_map($map) {
	foreach($map[0] as $j => $cell) {
		foreach($map as $i => $col) {
			echo $map[$i][$j];
		}
		echo "\n";
	}
	for($i=0;$i<sizeof($map);$i++) {
		echo "=";
	}
	echo "\n";
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$data = pivot($data);

$map = $data;
$w = sizeof($map);
$h = sizeof($map[0]);
// begin at middle of left edge
// then traverse upper left edge of hexagon
// then finally upper edge of hexagon
$j = ($h - 1) / 2;
for($i=0;$i<=($h-1)/2;$i++) {
	$tree = $map[$i][$j];
	//	print("(i,j) = ($i,$j) = $tree\n");
	$i_next = $i;
	$j_next = $j;
	$shadow = $tree - 1;
	$i_next++;
	$j_next++;
	while(isset($map[$i_next][$j_next])) {
		$tree_next = $map[$i_next][$j_next];
		if($tree_next == $EMPTY) {
			break;
		}
		if($shadow < $tree_next) {
			$shadow = $tree_next;
		} else {
			$map[$i_next][$j_next] = $EMPTY;
		}
		$shadow--;
		$i_next++;
		$j_next++;
	}
	$j--;
}
$j++;		// get back on map
$i++;
while($map[$i][$j] != $EMPTY) {
	$tree = $map[$i][$j];
	$i_next = $i;
	$j_next = $j;
	$shadow = $tree - 1;
	$i_next++;
	$j_next++;
	while(isset($map[$i_next][$j_next])) {
		$tree_next = $map[$i_next][$j_next];
		if($tree_next == $EMPTY) {
			break;
		}
		if($shadow < $tree_next) {
			$shadow = $tree_next;
		} else {
			$map[$i_next][$j_next] = $EMPTY;
		}
		$shadow--;
		$i_next++;
		$j_next++;
	}
	$i += 2;
}

// count still alive
$no = 0;
foreach($map as $col) {
	foreach($col as $cell) {
		if($cell != $EMPTY) {
			$no++;
		}
	}
}

printf("Result 1: %d\n", $no);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2_ex1.txt';
//$input = file_get_contents($file2, true);
//$data = get_input($input);
//printf("Result 2: %d\n", $hits);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
