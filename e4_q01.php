<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "01";

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

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e4_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$where_min = 0;
$final_sum = 0;

foreach($data as $line) {
    $visited = [];
    $where = 0;
    $visited[$where] = $where;
    foreach($line as $no) {
        $new_where = $where - $no;
        if($new_where < $where_min || isset($visited[$new_where])) {
            $new_where = $where + $no;
        }
        $where = $new_where;
        $visited[$where] = $where;
    }
    $final_sum += $where;
}

printf("Result 1: %d\n", $final_sum);

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
