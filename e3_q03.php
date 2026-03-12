<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "03";
$ID = 0;
$PLUG = 1;
$LEFT = 2;
$LEFT_TREE = 3;
$RIGHT = 4;
$RIGHT_TREE = 5;
$DATASTR = 6;

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
		    // id=1, plug=BLUE HEXAGON, leftSocket=GREEN CIRCLE, rightSocket=BLUE PENTAGON, data=?
		    preg_match('#id=(.*), plug=(.*), leftSocket=(.*), rightSocket=(.*), data=(.*)#', $line, $m);
		    [$all, $id, $plug, $left, $right, $datastr] = $m;
		    $data[] = [$id, $plug, $left, $right, $datastr];
		}
	}
	return $data;
}

// call first time with level 0
function print_tree($tree, $level) {
    global $ID, $PLUG, $LEFT, $LEFT_TREE, $RIGHT, $RIGHT_TREE, $DATASTR;
    
    for($i=0;$i<=$level;$i++) {
        print("    ");
    }
    if(sizeof($tree) == 0) {
        print("-\n");
        return;
    } else {
        printf("%d\n", $tree[$ID]);
    }
    print_tree($tree[$LEFT_TREE], $level + 1);
    print_tree($tree[$RIGHT_TREE], $level + 1);
    if($level == 0) {
        print("============================\n");
    }
}

function read_tree($tree) {
    global $ID, $PLUG, $LEFT, $LEFT_TREE, $RIGHT, $RIGHT_TREE, $DATASTR;
    
    if(sizeof($tree) == 0) {
        return [];
    }
    
    $tree_read = [];
    if(0 < sizeof($tree[$LEFT_TREE])) {
        $tree_read = array_merge($tree_read, read_tree($tree[$LEFT_TREE]));
    }
    $tree_read[] = $tree[$ID];
    if(0 < sizeof($tree[$RIGHT_TREE])) {
        $tree_read = array_merge($tree_read, read_tree($tree[$RIGHT_TREE]));
    }
    return $tree_read;
}

// depth first search construction
function dfs($parent_plug, &$tree) {
    global $ID, $PLUG, $LEFT, $LEFT_TREE, $RIGHT, $RIGHT_TREE, $DATASTR;
    global $part;
    global $node, $node_placed;
    
    // 1st attempt: right here
    if(sizeof($tree) == 0) {
        // figure out whether the node would fit here
        // if it wouldn't, give up
        $strong_bond = false;
        $weak_bond = false;
        if($parent_plug == "") {
            $strong_bond = true;
        } else {
            $node_plug_exp = explode(" ", $node[$PLUG]);
            $parent_plug_exp = explode(" ", $parent_plug);
            $bond0 = (strcmp($node_plug_exp[0],
                $parent_plug_exp[0]) == 0);
            $bond1 = (strcmp($node_plug_exp[1],
                $parent_plug_exp[1]) == 0);
            if($bond0 && $bond1) {
                $strong_bond = true;
            }
            if($bond0 || $bond1) {
                $weak_bond = true;
            }
        }
        
        if($part == 1) {
            if($strong_bond) {
                // place the node here
                $tree = $node;
                $node_placed = true;
                return true;
            } else {
                return false;
            }
        }
        
        if($part == 2 || $part == 3) {
            if($strong_bond || $weak_bond) {
                // place the node here
                $tree = $node;
                $node_placed = true;
                return true;
            } else {
                return false;
            }
        }
    } else {
        if($part == 3) {
            // the node might fit better than what's already here
            $old_strong_bond = false;
            $old_weak_bond = false;
            $new_strong_bond = false;
            $new_weak_bond = false;
            
            if($parent_plug == "") {
                $old_strong_bond = true;
                $old_weak_bond = true;
                $new_strong_bond = true;
                $new_weak_bond = true;
            } else {
                $old_node_plug_exp = explode(" ", $tree[$PLUG]);
                $new_node_plug_exp = explode(" ", $node[$PLUG]);
                $parent_plug_exp = explode(" ", $parent_plug);
            
                $old_bond0 = (strcmp($old_node_plug_exp[0],
                    $parent_plug_exp[0]) == 0);
                $old_bond1 = (strcmp($old_node_plug_exp[1],
                    $parent_plug_exp[1]) == 0);
                $new_bond0 = (strcmp($new_node_plug_exp[0],
                    $parent_plug_exp[0]) == 0);
                $new_bond1 = (strcmp($new_node_plug_exp[1],
                    $parent_plug_exp[1]) == 0);
                
                if($old_bond0 && $old_bond1) {
                    $old_strong_bond = true;
                }
                if($old_bond0 || $old_bond1) {
                    $old_weak_bond = true;
                }
                if($new_bond0 && $new_bond1) {
                    $new_strong_bond = true;
                }
                if($new_bond0 || $new_bond1) {
                    $new_weak_bond = true;
                }
            }
            
            if($old_weak_bond && !$old_strong_bond && $new_strong_bond) {
                // swap!
                $swap = $tree;
                $tree = $node;
                $node = $swap;
            
                // but otherwise, let the algorithm continue
                return false;
            }
            
        }
    }
    
    // 2nd attempt: go left
    if(dfs($tree[$LEFT], $tree[$LEFT_TREE])) {
        return true;
    }
    // 3rd attempt: go right
    if(dfs($tree[$RIGHT], $tree[$RIGHT_TREE])) {
        return true;
    }
    
    // if nothing worked
    return false;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e3_q' . $quest . '_p1.txt';
$part = 1;
$input = file_get_contents($file1, true);
$data = get_input($input);

$tree = [];
foreach ($data as $node_flat) {
    // node is global
    [$id, $plug, $left, $right, $datastr] = $node_flat;
    $node = [$id, $plug, $left, [], $right, [], $datastr];
    dfs("", $tree);
}

$ids = read_tree($tree);

$checksum = 0;
foreach ($ids as $i => $id) {
    $checksum += ($i + 1) * $id;
}

printf("Result 1: %d\n", $checksum);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e3_q' . $quest . '_p2.txt';
$part = 2;
$input = file_get_contents($file2, true);
$data = get_input($input);

$tree = [];
foreach ($data as $node_flat) {
    [$id, $plug, $left, $right, $datastr] = $node_flat;
    $node = [$id, $plug, $left, [], $right, [], $datastr];
    dfs("", $tree);
}

$ids = read_tree($tree);

$checksum = 0;
foreach ($ids as $i => $id) {
    $checksum += ($i + 1) * $id;
}

printf("Result 2: %d\n", $checksum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e3_q' . $quest . '_p3.txt';
$part = 3;
$input = file_get_contents($file3, true);
$data = get_input($input);

$tree = [];
foreach ($data as $node_flat) {
    $node_placed = false;
    [$id, $plug, $left, $right, $datastr] = $node_flat;
    $node = [$id, $plug, $left, [], $right, [], $datastr];
    while(!$node_placed) {
        dfs("", $tree);
    }
}

$ids = read_tree($tree);

$checksum = 0;
foreach ($ids as $i => $id) {
    $checksum += ($i + 1) * $id;
}

printf("Result 3: %d\n", $checksum);

?>
