<?php

///////////////////////////////////////////////////////////////////////////
// constants

///////////////////////////////////////////////////////////////////////////
// functions

function convert_to_binary($no, $k) {
    $bin_no = decbin($k);
    $format = "%${no}d";
    $bin_pad = sprintf($format, $bin_no);
    $bin_arr = array_map('intval', str_split($bin_pad));
    return $bin_arr;
}

// check vertical and move right
function print_read1($no) {
    for($k=0;$k<pow(2,$no);$k++) {
        $out1 = array_fill(0, 5, "_");      // input
        $out2 = array_fill(0, 5, "*");      // output
        $bin_arr = convert_to_binary($no, $k);
        for($i=0;$i<$no;$i++) {
            $a = chr($bin_arr[$i] + 65);
            $out1[$i] = $a;
            if($i==0) {
                $b = $a;
                $out2[$i] = "_";
                continue;
            }
            if($a==$b) {
                $out2[$i] = "@";
            } else {
                $out2[$i] = "_";
            }
            $b = $a;
        }
        printf("READ\t\t%s*****\tREAD_%s\t%s*****\tRRRRRSSSSS\n", implode("", $out1), implode("", $out1), implode("", $out2));
    }
    print("\n");
}

// check horizontal and vertical and move right
function print_read2($no) {
    for($k=0;$k<pow(2,$no);$k++) {
        $out1 = array_fill(0, 5, "_");      // input, previous
        $bin_arr1 = convert_to_binary($no, $k);
        for($l=0;$l<pow(2,$no);$l++) {
            $out2A = array_fill(0, 5, "*");      // output, down
            $out2B = array_fill(0, 5, "*");      // output, up
            $out3 = array_fill(0, 5, "S");      // movement
            $out4 = array_fill(0, 5, "_");      // input, now
            $bin_arr2 = convert_to_binary($no, $l);
            for($i=0;$i<$no;$i++) {
                $a = chr($bin_arr2[$i] + 65);       // here, now
                $out4[$i] = $a;
                
                $c = chr($bin_arr1[$i] + 65);       // previous
                $out1[$i] = $c;
                // horizontal check, output above, go right
                if($a == $c) {
                    $out2B[$i] = "@";
                    $out3[$i] = "R";
                }
                
                if($i==0) {
                    $b = $a;
                    $out2A[$i] = "_";
                    continue;
                }
                // vertical check, output here
                if($a==$b) {                        // here and above
                    $out2A[$i] = "@";
                } else {
                    $out2A[$i] = "_";
                }
                $b = $a;
            }
            printf("READ_%s\t%s*****\tREAD_%s\t%s%s\tRRRRR%s\n",
                implode("", $out1), implode("", $out4), implode("", $out4),
                implode("", $out2A), implode("", $out2B), implode("", $out3));
        }
        printf("READ_%s\t_*********\tSTOP\t\t**********\tSSSSSSSSSS\n",
            implode("", $out1));
    }
    print("\n");
}

///////////////////////////////////////////////////////////////////////////
// main program

print("HEADS\t\tAAAAAAAAAA\n\n");

print("START\t\t**********\tSTART2\t**********\tSSSSSUUUUU\n");
print("START2\t\t**********\tSTART3\t**********\tSDDDDUUUUS\n");
print("START3\t\t**********\tSTART4\t**********\tSSDDDUUUSS\n");
print("START4\t\t**********\tSTART5\t**********\tSSSDDUUSSS\n");
print("START5\t\t**********\tREAD\t**********\tSSSSDUSSSS\n\n");

print_read1(1);
print_read2(1);

print_read1(2);
print_read2(2);

print_read1(3);
print_read2(3);

print_read1(4);
print_read2(4);

print_read1(5);
print_read2(5);
?>
