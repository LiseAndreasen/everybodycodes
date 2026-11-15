<?php

include "prim_mst.php";

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "14";
$FRAME_LETTER = ",";

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

function count_bindings($map) {
	global $FRAME_LETTER;

	$w = sizeof($map);
	$h = sizeof($map[0]);

	$edges = [];

	$bindings = 0;

	// 1 binding for each letter-change
	// +1 at each end
	// width
	for($i=0;$i<$w;$i++) {
		for($j=0;$j<$h;$j++) {
			$letter = $map[$i][$j];
			if($j == 0) {
				if(isset($edges[$FRAME_LETTER][$letter])) {
					$edges[$FRAME_LETTER][$letter]++;
					$edges[$letter][$FRAME_LETTER]++;
				} else {
					$edges[$FRAME_LETTER][$letter] = 1;
					$edges[$letter][$FRAME_LETTER] = 1;
				}
			}
			if($j != 0) {
				if($letter_prev != $letter) {
					$bindings++;
					if(isset($edges[$letter][$letter_prev])) {
						$edges[$letter][$letter_prev]++;
						$edges[$letter_prev][$letter]++;
					} else {
						$edges[$letter][$letter_prev] = 1;
						$edges[$letter_prev][$letter] = 1;
					}
				}
			}
			if($j + 1 == $h) {
				if(isset($edges[$FRAME_LETTER][$letter])) {
					$edges[$FRAME_LETTER][$letter]++;
					$edges[$letter][$FRAME_LETTER]++;
				} else {
					$edges[$FRAME_LETTER][$letter] = 1;
					$edges[$letter][$FRAME_LETTER] = 1;
				}
			}
			$letter_prev = $letter;
		}
		$bindings += 2;
	}
	// height
	for($j=0;$j<$h;$j++) {
		for($i=0;$i<$w;$i++) {
			$letter = $map[$i][$j];
			if($i == 0) {
				if(isset($edges[$FRAME_LETTER][$letter])) {
					$edges[$FRAME_LETTER][$letter]++;
					$edges[$letter][$FRAME_LETTER]++;
				} else {
					$edges[$FRAME_LETTER][$letter] = 1;
					$edges[$letter][$FRAME_LETTER] = 1;
				}
			}
			if($i != 0) {
				if($letter_prev != $letter) {
					$bindings++;
					if(isset($edges[$letter][$letter_prev])) {
						$edges[$letter][$letter_prev]++;
						$edges[$letter_prev][$letter]++;
					} else {
						$edges[$letter][$letter_prev] = 1;
						$edges[$letter_prev][$letter] = 1;
					}
				}
			}
			if($i + 1 == $w) {
				if(isset($edges[$FRAME_LETTER][$letter])) {
					$edges[$FRAME_LETTER][$letter]++;
					$edges[$letter][$FRAME_LETTER]++;
				} else {
					$edges[$FRAME_LETTER][$letter] = 1;
					$edges[$letter][$FRAME_LETTER] = 1;
				}
			}
			$letter_prev = $letter;
		}
		$bindings += 2;
	}
	// add 0s to edges
	foreach($edges as $a => $ohwell) {
		foreach($edges as $b => $celliguess) {
			if(!isset($edges[$a][$b])) {
				$edges[$a][$b] = 0;
			}
		}
	}

	// sort, throw away letters
	ksort($edges);
	$edges = array_values($edges);
	foreach($edges as $e => $nevermind) {
		ksort($edges[$e]);
		$edges[$e] = array_values($edges[$e]);
	}

	return [$bindings, $edges];
}

function create_graph_e2026_q14($weights, $num_vertices)
{
    // Initialize nodes
    $nodes = array_map(
        function ($id) { return new Node($id); },
        range(0, $num_vertices - 1)
    );

    // Get neighbors for this node based on weights
	foreach($weights as $r => $row)
    for ($row = 0; $row < $num_vertices; $row++)
    {
        for ($col = 0; $col < $num_vertices; $col++)
        {
            if ($weights[$row][$col] > 0)
            {
                $nodes[$row]->add_child($col, $weights[$row][$col]);
            }
        }
    }

    return $nodes;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

[$bindings, $edges] = count_bindings($data);

printf("Result 1: %d\n", $bindings);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

[$bindings, $edges] = count_bindings($data);

$num_vertices = sizeof($edges);
$num_weights = $num_vertices * $num_vertices;

// Create graph from weights
$graph = create_graph_e2026_q14($edges, $num_vertices);

// Get MST using Prim's algorithm
$mst = prim_mst($graph);

// Calculate total weight of MST and display
$total_weight = get_total_mst_weight($mst);

printf("Result 2: %d\n", $total_weight);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
