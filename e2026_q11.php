<?php

include "dijkstra.php";

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "11";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			$line2 = explode(" ", $line);
			if(strcmp($line2[0], "portals:") == 0) {
				$data["portals"] = $line2[1];
			} else {
				$data["shortcuts"][] = $line2;
			}
		}
	}
	return $data;
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

function create_graph($portals, $neighbor_portals) {
	$graph_sub = array_fill(0, $portals, 0);
	$graph = array_fill(0, $portals, $graph_sub);
	for($i=0;$i<$portals;$i++) {
		for($j=1;$j<=$neighbor_portals;$j++) {
			$a = ($i + $j) % $portals;
			$graph[$i][$a] = 1;
			$graph[$a][$i] = 1;
		}
	}

	return $graph;
}

function add_shortcuts($graph, $shortcuts) {
	foreach($shortcuts as $shortcut) {
		[$a, $b] = $shortcut;
		$graph[$a-1][$b-1] = 1;
		$graph[$b-1][$a-1] = 1;
	}

	return $graph;
}

function portal_strain($graph) {
	$portals = sizeof($graph);
	$sum = 0;
	for($i=0;$i<$portals;$i++) {
		$distance = Dijkstra($graph, $i, $portals);
		$sum += array_sum($distance);
	}

	return $sum;
}

// https://stackoverflow.com/questions/5506888/permutations-all-possible-sets-of-numbers
// create permutations
function pc_permute($items, $perms = array( )) {
	global $perms1;
    if (empty($items)) { 
    	$perms1[] = $perms;
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

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e2026_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$neighbor_portals = 1;
$portals = $data["portals"];
$graph = create_graph($portals, $neighbor_portals);
$strain1 = portal_strain($graph);
$graph = add_shortcuts($graph, $data["shortcuts"]);
$strain2 = portal_strain($graph);

printf("Result 1: %d\n", $strain1 - $strain2);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e2026_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$neighbor_portals = 3;
$portals = $data["portals"];
$graph_basic = create_graph($portals, $neighbor_portals);
$strain1 = portal_strain($graph_basic);
$strain2_min = $strain1;
for($i=1;$i<=$portals;$i++) {
	print("=");			// progress
}
print("\n");
for($i=1;$i<=$portals;$i++) {
	print(".");			// progress
	for($j=1;$j<=$portals;$j++) {
		if($i <= $j) {
			continue;
		}
		$shortcuts = $data["shortcuts"];
		$shortcuts[] = [$i, $j];
		$graph_shortcut = add_shortcuts($graph_basic, $shortcuts);
		$strain2 = portal_strain($graph_shortcut);
		if($strain2 < $strain2_min) {
			$strain2_min = $strain2;
		}
	}
}
printf("\nResult 2: %d\n", $strain1 - $strain2_min);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e2026_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

// create all permutations of the 2x4 postions
$letters2 = str_split("01234567");
// result of permute ends up in perms1
$perms1 = array();
pc_permute($letters2);
// then sort them to get all the unique permutations
$perms2 = [];
foreach($perms1 as $perm) {
	$perm_split = [];
	for($i=0;$i<8;$i+=2) {
		$perm_copy = $perm;
		$perm_copy = array_slice($perm_copy, $i, 2);
		sort($perm_copy);
		$perm_split[] = implode("", $perm_copy);
	}
	sort($perm_split);
	$perm_imploded = implode("", $perm_split);
	$perms2[$perm_imploded] = $perm_imploded;
}

$neighbor_portals = 3;
$portals = $data["portals"];
$graph_basic = create_graph($portals, $neighbor_portals);
$strain1 = portal_strain($graph_basic);
$strain2_min = $strain1;
for($i=1;$i<=sizeof($perms2);$i++) {
	print("=");			// progress
}
print("\n");
// permutation
foreach($perms2 as $perm) {
	print(".");			// progress
	$perm_split = str_split($perm);
	// offset
	for($i=1;$i<=$portals/8;$i++) {
		$shortcuts = $data["shortcuts"];
		// combine permutation and offset
		for($j=0;$j<8;$j+=2) {
			$a = $perm_split[$j] * $portals / 8 + $i;
			$b = $perm_split[$j+1] * $portals / 8 + $i;
			$shortcuts[] = [$a, $b];
		}
		$graph_shortcut = add_shortcuts($graph_basic, $shortcuts);
		$strain2 = portal_strain($graph_shortcut);
		if($strain2 < $strain2_min) {
			$strain2_min = $strain2;
		}
	}
}

printf("\nResult 3: %d\n", $strain1 - $strain2_min);

?>
