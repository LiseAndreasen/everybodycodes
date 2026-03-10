<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "02";
$dir = [[0, -1], [1, 0], [0, 1], [-1, 0]];

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
    $extra_space = 20;
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		if(strlen($line)>2) {
			// convert string to array
			// add extra space before and after - hack
			$extra_line = array_fill(0, $extra_space, ".");
			$line = $line;
			$data[] = array_merge($extra_line, str_split($line), $extra_line);
		}
	}
	// add extra space before and after - hack
	for($i=0;$i<$extra_space;$i++) {
    	$data[] = array_fill(0, sizeof($data[0]), ".");
    	$data[-$i] = array_fill(0, sizeof($data[0]), ".");
	}
	ksort($data);
	$data = array_values($data);
	return pivot($data);
}

// change (y, x) map to (x, y) map
function pivot($map) {
    foreach($map[0] as $j => $cell) {
        foreach($map as $i => $col) {
            $map2[$j][$i] = $map[$i][$j];
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

// 3x3 square
function detect_surrounded($square) {
    // main idea: if something is surrounded,
    // it will be on the edge of this little square
    // turn the little square into something linear
    // walking around the middle of the square
    // count how many times we change between empty and not
    $linear_top = $square[0];
    $linear_right = $square[1][2];
    $linear_bottom = array_reverse($square[2]);
    $linear_left = $square[1][0];
    $linear = array_merge($linear_top, [$linear_right],
        $linear_bottom, [$linear_left]);
    
    // important: was the bone here?
    $linear_flat = array_merge(...$square);
    $bone_found = array_search("#", $linear_flat);
    $bone_detected = is_int($bone_found);
    
    $changes = 0;
    foreach ($linear as $i => $cell) {
        if(0 < $i) {
            if($cell != $prev) {
                $changes++;
            }
        }
        $prev = $cell;
    }
    $surrounded = (3 <= $changes);
    
    return [$surrounded, $bone_detected];
}

function find_empty_bits($map, $here) {
    [$x, $y] = $here;
    
    $empty_right = true;
    $i = $x + 1;
    while (isset($map[$i][$y])) {
        if($map[$i][$y] != ".") {
            $empty_right = false;
            break;
        }
        $i++;
    }
    
    $empty_left = true;
    $i = $x - 1;
    while (isset($map[$i][$y])) {
        if($map[$i][$y] != ".") {
            $empty_left = false;
            break;
        }
        $i--;
    }
    
    $empty_down = true;
    $j = $y + 1;
    while(isset($map[$x][$j])) {
        if($map[$x][$j] != ".") {
            $empty_up = false;
            break;
        }
        $j++;
    }
    
    $empty_up = true;
    $j = $y - 1;
    while(isset($map[$x][$j])) {
        if($map[$x][$j] != ".") {
            $empty_up = false;
            break;
        }
        $j--;
    }
    
    return [$empty_right, $empty_left, $empty_down, $empty_up];
}

function walk_map($data, $part) {
    global $dir;

    // find beginning and bone
    for($i=0;$i<sizeof($data);$i++) {
        $j = array_search("@", $data[$i]);
        if($j !== false) {
            $at = [$i, $j];
            break;
        }
    }
    for($i=0;$i<sizeof($data);$i++) {
        $j = array_search("#", $data[$i]);
        if($j !== false) {
            $bone = [$i, $j];
            break;
        }
    }
    
    // up, right, down, left
    $i_am_here = $at;
    $dir_no = 0;
    $steps = 0;
    $bone_detected = false; // important for surround purposes
    while(true) {
        $this_dir = $dir[$dir_no];
        $new_i_am_here = [$i_am_here[0] + $this_dir[0], $i_am_here[1] + $this_dir[1]];
        
        if($data[$new_i_am_here[0]][$new_i_am_here[1]] != "+") {
            if($part == 2 && $data[$new_i_am_here[0]][$new_i_am_here[1]] == "#") {
                // don't walk over bones anymore
                $dir_no = ($dir_no + 1) % 4;
                continue;
            }
            
            // update my path
            $data[$i_am_here[0]][$i_am_here[1]] = "+";
            
            // update rest of me
            $i_am_here = $new_i_am_here;
            $steps++;
            
            // stopping condition
            if($part == 1) {
                if($data[$i_am_here[0]][$i_am_here[1]] == "#") {
                    break;
                }
            }
            
            // update me
            $data[$i_am_here[0]][$i_am_here[1]] = "@";

            // update next to path
            if($part == 2) {
                // check whether area is surrounded
                // this happens when in my little 3x3 area
                // with new me in the middle
                // there are now 2 empty parts with no connection
                // assumption: we are away from the edge of the map
                $my_little_square = array_slice($data, $i_am_here[0] - 1, 3);
                foreach ($my_little_square as $i => $col) {
                    $my_little_square[$i] = array_slice($col, $i_am_here[1] - 1, 3);
                }
                [$surrounded, $bone_detected_here] = detect_surrounded($my_little_square);
                // we are surrounded!
                // but remember to check that this isn't the first encounter with the bone
                if($surrounded && !($bone_detected_here && !$bone_detected)) {
                    // to one side there will be completely empty, to the other not
                    // the surrounded bit will not be diagonal to the middle
                    $empty_or_not = find_empty_bits($data, $i_am_here);
                    [$empty_right, $empty_left, $empty_down, $empty_up] = $empty_or_not;
                    if($data[$i_am_here[0]+1][$i_am_here[1]] == "." && $empty_left && !$empty_right) {
                        // fill up to the right
                        $data[$i_am_here[0]+1][$i_am_here[1]] = "+";
                    }
                    if($data[$i_am_here[0]-1][$i_am_here[1]] == "." && $empty_right && !$empty_left) {
                        // fill up to the left
                        $data[$i_am_here[0]-1][$i_am_here[1]] = "+";
                    }
                    if($data[$i_am_here[0]][$i_am_here[1]+1] == "." && $empty_up && !$empty_down) {
                        // fill up downwards
                        $data[$i_am_here[0]][$i_am_here[1]+1] = "+";
                    }
                    if($data[$i_am_here[0]][$i_am_here[1]-1] == "." && $empty_down && !$empty_up) {
                        // fill up downwards
                        $data[$i_am_here[0]][$i_am_here[1]-1] = "+";
                    }
                    if($bone_detected_here) {
                        $bone_detected = true;
                    }
                }
            } // update next to path
            
            // stopping condition
            if($part == 2) {
                if($data[$bone[0]-1][$bone[1]] == "+"
                && $data[$bone[0]+1][$bone[1]] == "+"
                && $data[$bone[0]][$bone[1]-1] == "+"
                && $data[$bone[0]][$bone[1]+1] == "+") {
                    return $steps - 1; // ahem
                }
            }
            
// for debugging
if($part == 2 && 3410 < $steps) {
    print_map($data);
    usleep(500000);
}
        }
        $dir_no = ($dir_no + 1) % 4;
    }
    
    return $steps;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e3_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$steps = walk_map($data, 1);

printf("Result 1: %d\n", $steps);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e3_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$steps = walk_map($data, 2);

printf("Result 2: %d\n", $steps);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e3_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
