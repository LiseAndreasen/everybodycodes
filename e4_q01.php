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

function jump($data, $where_min, $part) {
    $final_sum = 0;
    
    foreach($data as $line) {
        $visited = [];
        $where = 0;
        $visited[$where] = $where;
        foreach($line as $no) {
            $new_where = $where - $no;
            if($new_where < $where_min || isset($visited[$new_where])) {
                $new_where = $where + $no;
                if(2 == $part) {
                    while(isset($visited[$new_where])) {
                        $new_where++;
                    }
                }
            }
            $where = $new_where;
            $visited[$where] = $where;
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

$file3 = './everybody_codes_e4_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
