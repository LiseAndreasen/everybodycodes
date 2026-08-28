<?php

///////////////////////////////////////////////////////////////////////////
// constants

$quest = "03";

///////////////////////////////////////////////////////////////////////////
// functions

function get_input($input) {
	// absorb input file, line by line
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $input) as $line) {
		//print("$line\n");
		if(strlen($line)>2) {
			// convert csv to array
			$tmpdata = explode("=", $line);
			$name = $tmpdata[0];
			if(strcmp($name, "width") == 0 || strcmp($name, "height") == 0) {
			    $data[$name] = $tmpdata[1];
			} else {
			    $data[$name] = str_split($tmpdata[1]);
			}
		}
	}
	return $data;
}

// change (y, x) map to (x, y) map
function pivot($map) {
    $map_height = sizeof($map);
    $map_width = sizeof($map[0]);
    for($j=0;$j<$map_height;$j++) {
        for($i=0;$i<$map_width;$i++) {
            $map2[$i][$j] = $map[$j][$i];
        }
    }
    return $map2;
}

function print_map($map) {
    $map = pivot($map);
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

// https://stackoverflow.com/questions/34034730/how-to-enable-color-for-php-cli

// example
// output red text
// php -r 'echo "\033[31m some colored text \033[0m some white text \n";'
//Examples:
//formatPrint(['blue', 'bold', 'italic','strikethrough'], "Wohoo");

function formatPrint(array $format=[],string $text = '') {
    $codes=[
        'bold'=>1,
        'italic'=>3, 'underline'=>4, 'strikethrough'=>9,
        'black'=>30,   'red'=>31,   'green'=>32,   'yellow'=>33,
        'blue'=>34,   'magenta'=>35,   'cyan'=>36,   'white'=>37,
        'blackbg'=>40, 'redbg'=>41, 'greenbg'=>42, 'yellowbg'=>43,
        'bluebg'=>44, 'magentabg'=>45, 'cyanbg'=>46, 'lightgreybg'=>47
    ];
    $formatMap = array_map(function ($v) use ($codes) {
        return $codes[$v];
    }, $format);
        return "\e[".implode(';',$formatMap).'m'.$text."\e[0m";
}

function construct_up_and_left($width, $height, $horizontal_offsets,
$horizontal_offsets_size, $vertical_offsets, $vertical_offsets_size) {
    $up = [];
    $left= [];
    
    // do the up array
    for($row=0;$row<=$height;$row++) {
        // find the correct hor. offset
        $offset_no = $row % $horizontal_offsets_size;
        $offset = $horizontal_offsets[$offset_no];
        for($column=0;$column<$width;$column+=2) {
            if($offset == 0) {
                $up[$row][$column] = true;
                $up[$row][$column+1] = false;
            } else {
                $up[$row][$column] = false;
                $up[$row][$column+1] = true;
            }
        }
    }
    
    // do the left array
    for($column=0;$column<=$width;$column++) {
        // find the correct ver. offset
        $offset_no = $column % $vertical_offsets_size;
        $offset = $vertical_offsets[$offset_no];
        for($row=0;$row<$height;$row+=2) {
            if($offset == 0) {
                $left[$row][$column] = true;
                $left[$row+1][$column] = false;
            } else {
                $left[$row][$column] = false;
                $left[$row+1][$column] = true;
            }
        }
    }
    
    return [$up, $left];
}

function count_surrounded1($height, $width, $up, $left) {
    $surrounded = 0;
    for($row=0;$row<$height;$row++) {
        for($column=0;$column<$width;$column++) {
            if($up[$row][$column] && $up[$row+1][$column]
                && $left[$row][$column] && $left[$row][$column+1]) {
                    $surrounded++;
                }
        }
    }
    
    return $surrounded;
}

function count_surrounded2($height, $width, $up, $left) {
    $grid = [];
    $grid_color = 0;            // flips between 0 and 1
    $grid[0][0] = $grid_color;
    
    // color the 1st row
    $row = 0;
    for($column=1;$column<$width;$column++) {
        if($left[$row][$column]) {
            $grid_color = 1 - $grid_color;
        }
        $grid[$row][$column] = $grid_color;
    }
    
    // color the rest of each column
    for($column=0;$column<$width;$column++) {
        $grid_color = $grid[0][$column];
        for($row=1;$row<$height;$row++) {
            if($up[$row][$column]) {
                $grid_color = 1 - $grid_color;
            }
            $grid[$row][$column] = $grid_color;
        }
    }
    
    $surrounded[0] = 0;
    $surrounded[1] = 0;
    for($row=0;$row<$height;$row++) {
        for($column=0;$column<$width;$column++) {
            if($up[$row][$column] && $up[$row+1][$column]
                && $left[$row][$column] && $left[$row][$column+1]) {
                    $grid_color = $grid[$row][$column];
                    $surrounded[$grid_color]++;
                }
        }
    }
    
    return $surrounded;
}

///////////////////////////////////////////////////////////////////////////
// main program, part 1

// data model:
// 2 2d arrays
// up notes whether a stich is placed above a certain tile
// left notes left
// a tile is surrounded if:
// - this tile and the tile below both have up
// - this tile and the tile on the right both have left

$file1 = './everybody_codes_e4_q' . $quest . '_p1.txt';
$input = file_get_contents($file1, true);
$data = get_input($input);
$width = $data["width"];
$height = $data["height"];
$horizontal_offsets = $data["horizontal-offsets"];
$horizontal_offsets_size = sizeof($horizontal_offsets);
$vertical_offsets = $data["vertical-offsets"];
$vertical_offsets_size = sizeof($vertical_offsets);

[$up, $left] = construct_up_and_left($width, $height, $horizontal_offsets,
    $horizontal_offsets_size, $vertical_offsets, $vertical_offsets_size);

$surrounded = count_surrounded1($height, $width, $up, $left);

printf("Result 1: %d\n", $surrounded);

///////////////////////////////////////////////////////////////////////////
// main program, part 2

$file2 = './everybody_codes_e4_q' . $quest . '_p2.txt';
$input = file_get_contents($file2, true);
$data = get_input($input);
$width = $data["width"];
$height = $data["height"];
$horizontal_offsets = $data["horizontal-offsets"];
$horizontal_offsets_size = sizeof($horizontal_offsets);
$vertical_offsets = $data["vertical-offsets"];
$vertical_offsets_size = sizeof($vertical_offsets);

[$up, $left] = construct_up_and_left($width, $height, $horizontal_offsets,
    $horizontal_offsets_size, $vertical_offsets, $vertical_offsets_size);

$largest_surrounded = max(count_surrounded2($height, $width, $up, $left));

printf("Result 2: %d\n", $largest_surrounded);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e4_q' . $quest . '_p3.txt';
$input = file_get_contents($file3, true);
$data = get_input($input);
$width = $data["width"];
$height = $data["height"];
$horizontal_offsets = $data["horizontal-offsets"];
$horizontal_offsets_size = sizeof($horizontal_offsets);
$vertical_offsets = $data["vertical-offsets"];
$vertical_offsets_size = sizeof($vertical_offsets);

// construct up and left for a slice of the floor
// with width and height double that of the repeating offset sizes
// this slice will repeat

$slice_width = $vertical_offsets_size * 2;
$slice_height = $horizontal_offsets_size * 2;

[$slice_up, $slice_left] = construct_up_and_left($slice_width,
    $slice_height, $horizontal_offsets, $horizontal_offsets_size,
    $vertical_offsets, $vertical_offsets_size);

$slice_surrounded = count_surrounded2($slice_height, $slice_width,
    $slice_up, $slice_left);

///////////////////////////////////////////////////////////////////////////

$slice_width_string_8 = formatPrint(['redbg'],
    sprintf("%8s", $slice_width));
$slice_width_string_16 = formatPrint(['redbg'],
    sprintf("%16s", $slice_width));

$slice_height_string_8 = formatPrint(['greenbg'],
    sprintf("%8s", $slice_height));
$slice_height_string_16 = formatPrint(['greenbg'],
    sprintf("%16s", $slice_height));

printf("\n%s %36s is width of slice\n", $slice_width_string_16, "");
printf("%s %36s is height of slice\n", $slice_height_string_16, "");

$slice_width_no_string_12 = formatPrint(['bluebg'],
    sprintf("%12s", floor($width / $slice_width)));

$slice_height_no_string_8 = formatPrint(['yellowbg'],
    sprintf("%8s", floor($height / $slice_height)));
$slice_height_no_string_12 = formatPrint(['yellowbg'],
    sprintf("%12s", floor($height / $slice_height)));

$right_edge_width = $width % $slice_width;
$down_edge_height = $height % $slice_height;

$right_edge_width_string_8 = formatPrint(['magentabg'],
    sprintf("%8s", $right_edge_width));
$right_edge_width_string_16 = formatPrint(['magentabg'],
    sprintf("%16s", $right_edge_width));

$down_edge_height_string_8 = formatPrint(['cyanbg'],
    sprintf("%8s", $down_edge_height));
$down_edge_height_string_16 = formatPrint(['cyanbg'],
    sprintf("%16s", $down_edge_height));

printf("%16d = %s * %s + %s is width of floor\n", $width,
    $slice_width_no_string_12, $slice_width_string_8,
    $right_edge_width_string_8);
printf("%16d = %s * %s + %s is height of floor\n", $height,
    $slice_height_no_string_12, $slice_height_string_8,
    $down_edge_height_string_8);

$repeating_slices = floor($width / $slice_width)
    * floor($height / $slice_height);

$repeating_slices_string_12 = formatPrint(['lightgreybg', 'black'],
    sprintf("%12s", $repeating_slices));
$repeating_slices_string_16 = formatPrint(['lightgreybg', 'black'],
    sprintf("%16s", $repeating_slices));
    
printf("%s = %s * %s %10s repeating slices covers most of the floor\n",
    $repeating_slices_string_16, $slice_width_no_string_12,
    $slice_height_no_string_8, "");

$repeating_slices_id = 1;

$surrounded = [];

$surrounded[0][$repeating_slices_id] = $repeating_slices
    * $slice_surrounded[0];
$surrounded[1][$repeating_slices_id] = $repeating_slices
    * $slice_surrounded[1];

printf("%16d = %s * %8d %10s surrounded tiles in the 0 group\n",
    $surrounded[0][$repeating_slices_id], $repeating_slices_string_12,
    $slice_surrounded[0], "");
printf("%16d = %s * %8d %10s surrounded tiles in the 1 group\n",
    $surrounded[1][$repeating_slices_id], $repeating_slices_string_12,
    $slice_surrounded[1], "");

///////////////////////////////////////////////////////////////////////////

printf("\n%s %36s is width of remaining edge on the right\n",
    $right_edge_width_string_16, "");

[$slice_right_up, $slice_right_left] = construct_up_and_left($right_edge_width,
    $slice_height, $horizontal_offsets, $horizontal_offsets_size,
    $vertical_offsets, $vertical_offsets_size);

$slice_right_surrounded = count_surrounded2($slice_height, $right_edge_width,
    $slice_right_up, $slice_right_left,
    $horizontal_offsets_size, $vertical_offsets_size);

$right_edge_id = 2;

$surrounded[0][$right_edge_id] = floor($height / $slice_height)
    * $slice_right_surrounded[0];
$surrounded[1][$right_edge_id] = floor($height / $slice_height)
    * $slice_right_surrounded[1];

printf("%16d = %s * %8d %10s surrounded tiles in the 0 group\n",
    $surrounded[0][$right_edge_id], $slice_height_no_string_12,
    $slice_right_surrounded[0], "");
printf("%16d = %s * %8d %10s surrounded tiles in the 1 group\n",
    $surrounded[1][$right_edge_id], $slice_height_no_string_12,
    $slice_right_surrounded[1], "");

///////////////////////////////////////////////////////////////////////////

printf("\n%s %36s is height of remaining edge below\n",
    $down_edge_height_string_16, "");

[$slice_down_up, $slice_down_left] = construct_up_and_left($slice_width,
    $down_edge_height, $horizontal_offsets, $horizontal_offsets_size,
    $vertical_offsets, $vertical_offsets_size);

$slice_down_surrounded = count_surrounded2($down_edge_height, $slice_width,
    $slice_down_up, $slice_down_left,
    $horizontal_offsets_size, $vertical_offsets_size);

$down_edge_id = 3;

$surrounded[0][$down_edge_id] = floor($width / $slice_width)
    * $slice_down_surrounded[0];
$surrounded[1][$down_edge_id] = floor($width / $slice_width)
    * $slice_down_surrounded[1];

printf("%16d = %s * %8d %10s surrounded tiles in the 0 group\n",
    $surrounded[0][$down_edge_id], $slice_width_no_string_12,
    $slice_down_surrounded[0], "");
printf("%16d = %s * %8d %10s surrounded tiles in the 1 group\n",
    $surrounded[1][$down_edge_id], $slice_width_no_string_12,
    $slice_down_surrounded[1], "");

///////////////////////////////////////////////////////////////////////////

[$slice_corner_up, $slice_corner_left] = construct_up_and_left($right_edge_width,
    $down_edge_height, $horizontal_offsets, $horizontal_offsets_size,
    $vertical_offsets, $vertical_offsets_size);

$slice_corner_surrounded = count_surrounded2($down_edge_height,
    $right_edge_width, $slice_corner_up, $slice_corner_left,
    $horizontal_offsets_size, $vertical_offsets_size);

printf("\n%s %36s is width of remaining corner\n",
    $right_edge_width_string_16, "");
printf("%s %36s is height of remaining corner\n",
    $down_edge_height_string_16, "");

$corner_id = 4;

$surrounded[0][$corner_id] = $slice_corner_surrounded[0];
$surrounded[1][$corner_id] = $slice_corner_surrounded[1];

printf("%16d = %12d * %8d %10s surrounded tiles in the 0 group\n",
    $surrounded[0][$corner_id], 1, $slice_corner_surrounded[0], "");
printf("%16d = %12d * %8d %10s surrounded tiles in the 1 group\n",
    $surrounded[1][$corner_id], 1, $slice_corner_surrounded[1], "");


///////////////////////////////////////////////////////////////////////////

printf("\n%16d %36s surrounded tiles in the 0 group\n",
    array_sum($surrounded[0]), "");
printf("%16d %36s surrounded tiles in the 1 group\n",
    array_sum($surrounded[1]), "");

$result = max(array_sum($surrounded[0]), array_sum($surrounded[1]));

printf("\nResult 3: %d\n", $result);

?>
