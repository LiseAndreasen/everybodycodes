<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "02";

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

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e4_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$visited = [];
[$x, $y] = $data["START"];
$visited[$x][$y] = true;
foreach ($data["MOVES"] as $move) {
    [$bx, $by] = $data[$move];
    $x = floor(($x + $bx) / 2);
    $y = floor(($y + $by) / 2);
    $visited[$x][$y] = true;
}

$visited_flat = array_merge(...$visited);

printf("Result 1: %d\n", sizeof($visited_flat));

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e4_q' . $quest . '_p2_ex1.txt';
//$input = file_get_contents($file2, true);
//$data = get_input($input);
//printf("Result 2: %d\n", $hits);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e4_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
