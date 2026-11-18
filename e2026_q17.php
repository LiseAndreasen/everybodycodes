<?php

include "dijkstra.php";

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "17";
$SOLID = "#";
$GAP = ".";

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

function convert_to_id($x, $y) {
    global $width;
    return $x + $width * $y;
}

function create_graph($map) {
	global $width, $height, $verticesCount, $SOLID, $GAP;

	$graph_sub = array_fill(0, $verticesCount, 0);
	$graph = array_fill(0, $verticesCount, $graph_sub);

	for($i=0;$i<$width;$i++) {
		for($j=0;$j<$height;$j++) {
			if($map[$i][$j] == $GAP) {
				continue;
			}
			$id_me = convert_to_id($i, $j);
			if(isset($map[$i+1][$j]) && $map[$i+1][$j] == $SOLID) {
				$id_right = convert_to_id($i+1, $j);
				$graph[$id_me][$id_right] = 1;
				$graph[$id_right][$id_me] = 1;
			}
			if(isset($map[$i][$j+1]) && $map[$i][$j+1] == $SOLID) {
				$id_down = convert_to_id($i, $j+1);
				$graph[$id_me][$id_down] = 1;
				$graph[$id_down][$id_me] = 1;
			}
		}
	}
	
	return $graph;
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

// note, i don't pivot
$width = sizeof($data);
$height = sizeof($data[0]);
$verticesCount = $width * $height;

$graph = create_graph($data);

$id_begin = convert_to_id(0, 0);
$id_end = convert_to_id($width - 1, $height - 1);
$distance = Dijkstra($graph, $id_begin, $verticesCount);

printf("Result 1: %d\n", $distance[$id_end]);

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
