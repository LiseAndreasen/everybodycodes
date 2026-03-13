<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "02";
$dir_small = [[0, -1], [1, 0], [0, 1], [-1, 0]];
$dir_large = [[0, -1], [0, -1], [0, -1], [1, 0], [1, 0], [1, 0],
    [0, 1], [0, 1], [0, 1], [-1, 0], [-1, 0], [-1, 0]];

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
			$data[] = array_merge($extra_line, str_split($line),
			    $extra_line);
		}
	}
	// add extra space before and after - hack
	for($i=1;$i<=$extra_space;$i++) {
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

// check adjacent cells (no diagonals) to maybe fill
function fill_closeby(&$map, $cell, &$left) {
    [$x, $y] = $cell;
    $map_local = $map;
    
    // assume the area was in fact infinite, abort
    if($left == 0) {
        return false;
    }
    
    // going left
    if(isset($map_local[$x-1][$y]) && $map_local[$x-1][$y] == ".") {
        $map_local[$x-1][$y] = "+";
        $left--;
        fill_closeby($map_local, [$x-1, $y], $left);
    }
    if($left == 0) {
        return false;
    }

    // going right
    if(isset($map_local[$x+1][$y]) && $map_local[$x+1][$y] == ".") {
        $map_local[$x+1][$y] = "+";
        $left--;
        fill_closeby($map_local, [$x+1, $y], $left);
    }
    if($left == 0) {
        return false;
    }
    
    // going up
    if(isset($map_local[$x][$y-1]) && $map_local[$x][$y-1] == ".") {
        $map_local[$x][$y-1] = "+";
        $left--;
        fill_closeby($map_local, [$x, $y-1], $left);
    }
    if($left == 0) {
        return false;
    }

    // going down
    if(isset($map_local[$x][$y+1]) && $map_local[$x][$y+1] == ".") {
        $map_local[$x][$y+1] = "+";
        $left--;
        fill_closeby($map_local, [$x, $y+1], $left);
    }
    if($left == 0) {
        return false;
    }
    
    // a small area might have been filled in
    $map = $map_local;
    return true;
}

function fill_surrounded(&$map, $cell) {
    [$x, $y] = $cell;
    $map_local = $map;
    
    // if something has just now become surrounded
    // 1: there will be separate unfilled areas
    // 2: at least 1 area can be easily filled
    
    // main idea: if something is surrounded,
    // it will be on the edge of this little square
    // turn the little square into something linear
    // walking around the middle of the square
    // count how many times we change between empty and not
    $linear = [
        $map[$x-1][$y-1],
        $map[$x  ][$y-1],
        $map[$x+1][$y-1],
        $map[$x+1][$y  ],
        $map[$x+1][$y+1],
        $map[$x  ][$y+1],
        $map[$x-1][$y+1],
        $map[$x-1][$y  ]
    ];
    
    $changes = 0;
    foreach ($linear as $i => $cell_val) {
        if(0 < $i) {
            if($cell_val != $prev) {
                $changes++;
            }
        }
        $prev = $cell_val;
    }
    $surrounded = (3 <= $changes);
    
    if(!$surrounded) {
        return false;
    }
    
    // assume any surrounded areas can be filled in at most 100 steps
    $at_most_steps = 100;
    $something_was_filled = false;
    for($i=$x-1;$i<=$x+1;$i++) {
        for($j=$y-1;$j<=$y+1;$j++) {
            if($map[$i][$j] == ".") {
                $map[$i][$j] = "+";
                $left = $at_most_steps - 1;
                fill_closeby($map, [$i, $j], $left);
                if($left != 0 && $left < $at_most_steps) {
                    $something_was_filled = true;
                } else {
                    // infinite fill, abort
                    $map[$i][$j] = ".";
                }
            }
        }
    }
    
    return $something_was_filled;
}

function bones_surrounded($map) {
    global $all_the_bones;
    if(sizeof($all_the_bones) == 0) {
        return true;
    }
    
    foreach ($all_the_bones as $i => $cell) {
        if($map[$cell[0]-1][$cell[1]] == "."
        || $map[$cell[0]+1][$cell[1]] == "."
        || $map[$cell[0]][$cell[1]-1] == "."
        || $map[$cell[0]][$cell[1]+1] == ".") {
            return false;
        } else {
            // delete the bone the 1st time it is surrounded
            unset($all_the_bones[$i]);
        }
    }
    
    return true;
}

function walk_map($data, $part) {
    global $dir_small, $dir_large;
    
    if($part == 3) {
        $dir = $dir_large;
    } else {
        $dir = $dir_small;
    }
    $dir_size = sizeof($dir);

    // find beginning and bone
    for($i=0;$i<sizeof($data);$i++) {
        $j = array_search("@", $data[$i]);
        if($j !== false) {
            $at = [$i, $j];
            break;
        }
    }
    
    // up, right, down, left
    $i_am_here = $at;
    $dir_no = 0;
    $steps = 0;
    while(true) {
        $this_dir = $dir[$dir_no];
        $new_i_am_here = [$i_am_here[0] + $this_dir[0],
            $i_am_here[1] + $this_dir[1]];
        
        if($data[$new_i_am_here[0]][$new_i_am_here[1]] != "+") {
            if($part != 1
            && $data[$new_i_am_here[0]][$new_i_am_here[1]] == "#") {
                // don't walk over bones anymore
                $dir_no = ($dir_no + 1) % $dir_size;
                continue;
            }
            
            // update my path, where i was
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
            
            // stopping condition
            if($part != 1) {
                if(bones_surrounded($data)) {
                    return $steps - 1; // ahem
                }
            }
            
            // update me
            $data[$i_am_here[0]][$i_am_here[1]] = "@";

            // update next to path
            if($part != 1) {
                fill_surrounded($data, $i_am_here);
            }
            
            // stopping condition
            if($part != 1) {
                if(bones_surrounded($data)) {
                    return $steps;
                }
            }
        }
        $dir_no = ($dir_no + 1) % $dir_size;
    }
    
    return $steps;
}

function find_all_bones($map) {
    $bones = [];
    
    foreach ($map as $i => $col) {
        foreach ($col as $j => $cell) {
            if($map[$i][$j] == "#") {
                $bones[] = [$i, $j];
            }
        }
    }
    
    return $bones;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$steps = 0;

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

$all_the_bones = find_all_bones($data);

$steps = walk_map($data, 2);

printf("Result 2: %d\n", $steps);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e3_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

$all_the_bones = find_all_bones($data);

// find any area, that begin as surrounded
// assume any surrounded areas can be filled in at most 100 steps
$at_most_steps = 100;
for($i=min(array_keys($data));$i<=max(array_keys($data));$i++) {
    for($j=min(array_keys($data[0]));$j<=max(array_keys($data[0]));$j++) {
        if($data[$i][$j] == ".") {
            $data[$i][$j] = "+";
            $left = $at_most_steps - 1;
            fill_closeby($data, [$i, $j], $left);
            if($left != 0 && $left < $at_most_steps) {
                // do nothing
            } else {
                // infinite fill, abort
                $data[$i][$j] = ".";
            }
        }
    }
}

$steps = walk_map($data, 3);

printf("Result 3: %d\n", $steps);

?>
