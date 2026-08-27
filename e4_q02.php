<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "02";
$dirs = [[-1, 0], [0, -1], [0, 1], [1, 0]];

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
    global $dirs;
    
    $visited = [];
    [$x, $y] = $data["START"];
    $visited[$x][$y] = "X";
    if(1 < $part) {
        foreach ($dirs as $d) {
            [$dx, $dy] = $d;
            $visited[$x+$dx][$y+$dy] = "F";
        }
    }
    foreach ($data["MOVES"] as $move) {
        [$bx, $by] = $data[$move];
        $x = floor(($x + $bx) / 2);
        $y = floor(($y + $by) / 2);
        $visited[$x][$y] = "X";
        if(1 < $part) {
            foreach ($dirs as $d) {
                [$dx, $dy] = $d;
                if(!isset($visited[$x+$dx][$y+$dy])) {
                    $visited[$x+$dx][$y+$dy] = "F";
                }
            }
        }
    }
    
    $visited_flat = implode("", array_merge(...$visited));
    $counts = count_chars($visited_flat);
    if($part == 1) {
        $hash_value = ord("X");
    } else {
        $hash_value = ord("F");
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

$file3 = './everybody_codes_e4_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
