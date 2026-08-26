<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "01";
$no_crossing = 0;
$type1crossing = 1;     // lowest number between ends of arc
$type2crossing = 2;     // highest number

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$data[] = explode(",", $line);
		}
	}
	return $data;
}

// there's a crossing, when exactly 1 of x and y
// are between the endpoints of an arc
function arc_crossing($arcs, $x, $y) {
    global $no_crossing, $type1crossing, $type2crossing;
    
    $a = min($x, $y);
    $b = max($x, $y);
    foreach ($arcs as $arc) {
        $m = $arc[0];
        $n = $arc[1];
        if($m < $a && $a < $n && $n < $b) {
            return $type1crossing;
        }
        if($a < $m && $m < $b && $b < $n) {
            return $type2crossing;
        }
    }
    return $no_crossing;
}

function jump($data, $where_min, $part) {
    global $no_crossing, $type1crossing, $type2crossing;
    
    $final_sum = 0;
    
    foreach($data as $line) {
        $visited = [];
        $where = 0;
        $visited[$where] = $where;
        $arc_up = [];
        $arc_down = [];
        $i = 0;
        foreach($line as $no) {
            $new_where = $where - $no;
            $move_forward = false;
            if($new_where < $where_min) {
                $move_forward = true;
            }
            if(isset($visited[$new_where])) {
                $move_forward = true;
            }
            if($part == 3) {
                if($i % 2 == 0) {
                    $cross = arc_crossing($arc_up, $where, $new_where);
                } else {
                    $cross = arc_crossing($arc_down, $where, $new_where);
                }                
                if($cross != $no_crossing) {
                    $move_forward = true;
                }
            }
            if($move_forward) {
                $new_where = $where + $no;
                if(2 <= $part) {
                    $skip_forward = false;
                    if(isset($visited[$new_where])) {
                        $skip_forward = true;
                    }
                    if(!$skip_forward && $part == 3) {
                        if($i % 2 == 0) {
                            $cross = arc_crossing($arc_up, $where, $new_where);
                        } else {
                            $cross = arc_crossing($arc_down, $where, $new_where);
                        }
                        if($cross == $type1crossing) {
                            continue;
                        }
                        if($cross == $type2crossing) {
                            $skip_forward = true;
                        }
                    }
                    while($skip_forward) {
                        $new_where++;
                        $skip_forward = false;
                        if(isset($visited[$new_where])) {
                            $skip_forward = true;
                        }
                        if(!$skip_forward && $part == 3) {
                            if($i % 2 == 0) {
                                $cross = arc_crossing($arc_up, $where, $new_where);
                            } else {
                                $cross = arc_crossing($arc_down, $where, $new_where);
                            }
                            if($cross == $type1crossing) {
                                continue 2;
                            }
                            if($cross == $type2crossing) {
                                $skip_forward = true;
                            }
                        }
                    }
                }
            }
            if($i % 2 == 0) {
                $arc_up[] = [min($where, $new_where), max($where, $new_where)];
            } else {
                $arc_down[] = [min($where, $new_where), max($where, $new_where)];
            }
            $where = $new_where;
            $visited[$where] = $where;
            $i++;
        }
        $final_sum += $where;
    }
    return $final_sum;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e4_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$part = 1;

$where_min = 0;
$final_sum = jump($data, $where_min, $part); 

printf("Result 1: %d\n", $final_sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e4_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$part = 2;

$where_min = 0;
$final_sum = jump($data, $where_min, $part);

printf("Result 2: %d\n", $final_sum);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e4_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);
$part = 3;

$where_min = 0;
$final_sum = jump($data, $where_min, $part);

// p1_ex2: 27, p3_ex1: 35

printf("Result 3: %d\n", $final_sum);

?>
