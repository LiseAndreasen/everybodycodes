<?php

///////////////////////////////////////////////////////////////////////////
// constants

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			$data[] = explode(":", $line);
		}
	}
	return $data;
}

function find_child($person1, $person2, $person3) {
	global $data;

	// find child
	// possible child positions: 1, 2 or 3
	$poss1 = 1;
	$poss2 = 1;
	$poss3 = 1;
	$dna_lgt = strlen($data[0][1]);
	for($i=0;$i<$dna_lgt;$i++) {
		if($poss1 + $poss2 + $poss3 == 0) {
			return -1;
		}
		$p1letter = $data[$person1][1][$i];
		$p2letter = $data[$person2][1][$i];
		$p3letter = $data[$person3][1][$i];
		if($poss1 == 1) {
			// compare next letter
			if($p1letter != $p2letter && $p1letter != $p3letter) {
				// could not be this combo
				$poss1 = 0;
			}
		}
		if($poss2 == 1) {
			// compare next letter
			if($p2letter != $p1letter && $p2letter != $p3letter) {
				// could not be this combo
				$poss2 = 0;
			}
		}
		if($poss3 == 1) {
			// compare next letter
			if($p3letter != $p1letter && $p3letter != $p2letter) {
				// could not be this combo
				$poss3 = 0;
			}
		}
	}
	
	if($poss1 == 1) {
		$child = $person1;
	}
	if($poss2 == 1) {
		$child = $person2;
	}
	if($poss3 == 1) {
		$child = $person3;
	}
	return $child;
}

function find_sim($child, $par1, $par2) {
	global $data;
	$dna_lgt = strlen($data[0][1]);

	// find the similarities to each parent
	$sim_par1 = 0;
	$sim_par2 = 0;
	for($i=0;$i<$dna_lgt;$i++) {
		if($data[$child][1][$i] == $data[$par1][1][$i]) {
			$sim_par1++;
		}
		if($data[$child][1][$i] == $data[$par2][1][$i]) {
			$sim_par2++;
		}
	}
	return array($sim_par1, $sim_par2);
}

function find_families() {
	global $data;
	
	$progress = 0;
	$children = array();
	// find all combos of children and parents
	$persons = sizeof($data);
	// all 3 persons have to be different
	for($i=0;$i<$persons-2;$i++) {
		for($j=$i+1;$j<$persons-1;$j++) {
			for($k=$j+1;$k<$persons;$k++) {
				$progress++;
				if($progress % 1000000 == 0) {
					print(".");
				}
					$child = find_child($i, $j, $k);
					if($child == $i) {
						$children[$j][$k][$i] = 1;
					}
					if($child == $j) {
						$children[$i][$k][$j] = 1;
					}
					if($child == $k) {
						$children[$i][$j][$k] = 1;
					}
			}
		}
	}
	return $children;
}

///////////////////////////////////////////////////////////////////////////
// main program

$input1 = file_get_contents('./everybody_codes_e2025_q09_p1.txt', true);
$data = get_input($input1);
$dna_lgt = strlen($data[0][1]);

$child = find_child(0, 1, 2);
if($child == 0) {
	$par1 = 1;
	$par2 = 2;
}
if($child == 1) {
	$par1 = 0;
	$par2 = 2;
}
if($child == 2) {
	$par1 = 0;
	$par2 = 1;
}
$sim_par = find_sim($child, $par1, $par2);

printf("Result 1: %d\n", $sim_par[0] * $sim_par[1]);

$input2 = file_get_contents('./everybody_codes_e2025_q09_p2.txt', true);
$data = get_input($input2);

$children = find_families();

$sim_sum = 0;
foreach($children as $key1 => $parent1) {
	foreach($parent1 as $key2 => $parent2) {
		foreach($parent2 as $key3 => $child) {
			$sim_par = find_sim($key3, $key1, $key2);
			$sim_sum += $sim_par[0] * $sim_par[1];	
		}
	}	
}

printf("Result 2: %d\n", $sim_sum);

$input3 = file_get_contents('./everybody_codes_e2025_q09_p3.txt', true);
$data = get_input($input3);

print("Find families\n");
$children = find_families();

// construct the basic families
foreach($children as $key1 => $parent1) {
	foreach($parent1 as $key2 => $parent2) {
		foreach($parent2 as $key3 => $child) {
			$families[] = array($key1, $key2, $key3);
		}
	}
}
print("\nCombine families\n");
// compare all pairs of families X and Y, can they be combined?
$hit_possible = 1;
while($hit_possible == 1) {
	$hit_possible = 0;
	foreach($families as $keyx => $familyx) {
		foreach($families as $keyy => $familyy) {
			// only compare different families
			if($keyx == $keyy) {
				continue;
			}
			foreach($familyx as $person1) {
				// if this person from family X is also in family Y
				// these 2 families can be combined
				$hit = array_search($person1, $familyy);
				if($hit !== false) {
					$families[$keyx] = array_unique(array_merge($familyx, $familyy));
					unset($families[$keyy]);
					// move to the next family Y
					$hit_possible = 1;
					break 3;
				}
			}
		}
	}
}

// find largest family
$family_sz = 0;
foreach($families as $key => $family) {
	$this_family_sz = sizeof($family);
	if($family_sz < $this_family_sz) {
		$family_sz = $this_family_sz;
		$family_big = $key;
	}
}

// calculate sum of scale ids
$scale_sum = 0;
foreach($families[$family_big] as $person) {
	$scale_sum += $data[$person][0];
}

printf("Result 3: %d\n", $scale_sum);

?>
