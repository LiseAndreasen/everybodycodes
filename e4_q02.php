<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "02";
$dirs = [[-1, 0], [0, -1], [0, 1], [1, 0]];
$illuminated = "X";
$firefly = "F";
$possible_moves = "ABC";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
		    $tmp_data1 = explode("=", $line);
		    if(strcmp($tmp_data1[0], "MOVES") == 0) {
		        $data["MOVES"] = str_split($tmp_data1[1]);
		    } else {
		        $name = $tmp_data1[0];
		        $tmp_data2 = preg_split("/[\[\],]+/", $tmp_data1[1]);
		        $data[$name] = [$tmp_data2[1], $tmp_data2[2]];
		    }
		}
	}
	return $data;
}

function illuminate($data, $part) {
    global $dirs, $illuminated, $firefly, $possible_moves;
    
    $visited = [];
    [$x, $y] = $data["START"];
    if($part <= 2) {
        // illumination
        $visited[$x][$y] = $illuminated;
        if(1 < $part) {
            foreach ($dirs as $d) {
                [$dx, $dy] = $d;
                // firefly
                $visited[$x+$dx][$y+$dy] = $firefly;
            }
        }
        foreach ($data["MOVES"] as $move) {
            [$bx, $by] = $data[$move];
            $x = floor(($x + $bx) / 2);
            $y = floor(($y + $by) / 2);
            // illumination
            $visited[$x][$y] = $illuminated;
            if(1 < $part) {
                foreach ($dirs as $d) {
                    [$dx, $dy] = $d;
                    if(!isset($visited[$x+$dx][$y+$dy])) {
                        // firefly
                        $visited[$x+$dx][$y+$dy] = $firefly;
                    }
                }
            }
        }
    } else {
        $p = str_split($possible_moves);
        $q = [[$x, $y]];
        while(0 < sizeof($q)) {
            $szq = sizeof($q);
            if($szq % 1000 == 0) {
                printf("Size of queue: %5d\n", $szq);
            }
            $pos = array_shift($q);
            [$x, $y] = $pos;
            if(!isset($visited[$x][$y])) {
                // illumination
                $visited[$x][$y] = $illuminated;
            } else {
                if($visited[$x][$y] == $illuminated) {
                    // we've been here before
                    continue;
                } else {
                    // illumination
                    $visited[$x][$y] = $illuminated;
                }
            }
            foreach ($dirs as $d) {
                [$dx, $dy] = $d;
                if(!isset($visited[$x+$dx][$y+$dy])) {
                    // firefly
                    $visited[$x+$dx][$y+$dy] = $firefly;
                }
            }
            // add all possible next moves to queue
            foreach($p as $move) {
                [$bx, $by] = $data[$move];
                $next_x = floor(($x + $bx) / 2);
                $next_y = floor(($y + $by) / 2);
                $q[] = [$next_x, $next_y];
            }
        }
    }
    
    $visited_flat = implode("", array_merge(...$visited));
    $counts = count_chars($visited_flat);
    if($part == 1) {
        $hash_value = ord($illuminated);
    } else {
        $hash_value = ord($firefly);
    }
    
    return $counts[$hash_value];
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e4_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$part = 1;

$hits = illuminate($data, $part);

printf("Result 1: %d\n", $hits);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e4_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$part = 2;

$hits = illuminate($data, $part);

printf("Result 2: %d\n", $hits);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e4_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);
$part = 3;

$hits = illuminate($data, $part);

printf("Result 3: %d\n", $hits);
?>
