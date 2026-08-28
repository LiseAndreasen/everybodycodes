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
    
    return max($surrounded);
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

$largest_surrounded = count_surrounded2($height, $width, $up, $left);

printf("Result 2: %d\n", $largest_surrounded);

///////////////////////////////////////////////////////////////////////////
// main program, part 3

$file3 = './everybody_codes_e4_q' . $quest . '_p3_ex1.txt';
//$input = file_get_contents($file3, true);
//$data = get_input($input);
//printf("Result 3: %d\n", $hits);

?>
