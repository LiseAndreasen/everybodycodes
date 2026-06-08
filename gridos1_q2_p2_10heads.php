<?php

///////////////////////////////////////////////////////////////////////////
// constants

///////////////////////////////////////////////////////////////////////////
// functions

function print_read($no) {
    for($k=0;$k<pow(2,$no);$k++) {
        $out1 = array_fill(0, 10, "_");
        $out2 = array_fill(0, 10, "*");
        $bin_no = decbin($k);
        $format = "%${no}d";
        $bin_pad = sprintf($format, $bin_no);
        $bin_arr = array_map('intval', str_split($bin_pad));
        for($i=0;$i<$no;$i+=2) {
            $a = chr($bin_arr[$i] + 65);
            $b = chr($bin_arr[$i+1] + 65);
            if($a==$b) {
                $ab = "@";
            } else {
                $ab = "_";
            }
            $out1[$i] = $a;
            $out1[$i+1] = $b;
            $out2[$i] = $ab;
        }
        printf("READ\t%s\tREAD\t%s\tRRRRRRRRRR\n", implode("", $out1), implode("", $out2));
    }
    print("\n");
}

///////////////////////////////////////////////////////////////////////////
// main program

print("HEADS	AAAAAAAAAA\n\n");

print("START	**********	START2	**********	SSDDDDDDDD\n");
print("START2	**********	START3	**********	SSSSDDDDDD\n");
print("START3	**********	START4	**********	SSSSSSDDDD\n");
print("START4	**********	START5	**********	SSSSSSSSDD\n");
print("START5	**********	READ	**********	SRSRSRSRSR\n\n");

print("READ	*_********	STOP	_*_*_*_*_*	SSSSSSSSSS\n\n");

print_read(2);
print_read(4);
print_read(6);
print_read(8);
print_read(10);
?>
