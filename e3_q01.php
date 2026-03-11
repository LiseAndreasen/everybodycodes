<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "01";
$RED = 0;
$GREEN = 1;
$BLUE = 2;
$SHINE = 3;
$SHINE_MATTE = 30; // this or lower
$SHINE_SHINY = 33; // this or higher
$color_name[$RED] = "red";
$color_name[$GREEN] = "green";
$color_name[$BLUE] = "blue";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$data1 = explode(":", $line);
			$data2 = explode(" ", $data1[1]);
			$data[] = [$data1[0], $data2];
		}
	}
	return $data;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

$file1 = './everybody_codes_e3_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);

$green_is_dominant = 0;
foreach ($data as $scale) {
    [$scale_id, $colors] = $scale;
    $letters = ["r", "g", "b", "R", "G", "B"];
    $letter_values = [0, 0, 0, 1, 1, 1];
    foreach ($colors as $i => $color) {
        $color_no[$i] = bindec(str_replace($letters, $letter_values, $color));
    }
    if($color_no[$RED] < $color_no[$GREEN] && $color_no[$BLUE] < $color_no[$GREEN]) {
        $green_is_dominant += $scale_id;
    }
}

printf("Result 1: %d\n", $green_is_dominant);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e3_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);

$max_shine = 0;
foreach ($data as $scale) {
    [$scale_id, $colors] = $scale;
    $letters = ["r", "g", "b", "s", "R", "G", "B", "S"];
    $letter_values = [0, 0, 0, 0, 1, 1, 1, 1];
    foreach ($colors as $i => $color) {
        $color_no[$i] = bindec(str_replace($letters, $letter_values, $color));
    }
    if($max_shine < $color_no[$SHINE]) {
        $max_shine = $color_no[$SHINE];
        $min_darkness = 100000; // big number
    }
    if($color_no[$SHINE] == $max_shine) {
        $darkness = array_sum(array_slice($color_no, 0, -1));
        if($darkness < $min_darkness) {
            $min_darkness = $darkness;
            $max_id = $scale_id;
        }
    }
}

printf("Result 2: %d\n", $max_id);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e3_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);

foreach ($data as $scale) {
    [$scale_id, $colors] = $scale;
    $letters = ["r", "g", "b", "s", "R", "G", "B", "S"];
    $letter_values = [0, 0, 0, 0, 1, 1, 1, 1];
    foreach ($colors as $i => $color) {
        $color_no[$i] = bindec(str_replace($letters, $letter_values, $color));
    }
    
    $shine = "";
    if($color_no[$SHINE] <= $SHINE_MATTE) {
        $shine = "matte";
    }
    if($SHINE_SHINY <= $color_no[$SHINE]) {
        $shine = "shiny";
    }
    if($shine == "") {
        // ambigious shine
        continue;
    }
    
    unset($color_no[$SHINE]);
    $color_no_cp = $color_no;
    rsort($color_no_cp);
    if($color_no_cp[0] == $color_no_cp[1]) {
        // no dominant color
        continue;
    }
    $dominant_color = $color_name[array_search($color_no_cp[0], $color_no)];
    
    $group[$dominant_color][$shine][$scale_id] = $scale_id;
}

$max_group_size = 0;
foreach ($group as $group_color) {
    foreach ($group_color as $group_shine) {
        $group_size = sizeof($group_shine);
        if($max_group_size < $group_size) {
            $max_group_size = $group_size;
            $max_group_sum = array_sum($group_shine);
        }
    }
}

printf("Result 3: %d\n", $max_group_sum);

?>
