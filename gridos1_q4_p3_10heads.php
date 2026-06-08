<?php

///////////////////////////////////////////////////////////////////////////
// constants

///////////////////////////////////////////////////////////////////////////
// functions

// number k, no digits in binary
function convert_to_binary($no, $k) {
    $bin_no = decbin($k);
    $format = "%${no}d";
    $bin_pad = sprintf($format, $bin_no);
    $bin_arr = array_map('intval', str_split($bin_pad));
    return $bin_arr;
}

// looking for the 0th head
function print_rows0($no) {
    print("//$no line(s)\n");

    print("READ0\t\t");
    for($j=1;$j<=$no;$j++) {
        print("=");
    }
    for($j=$no+1;$j<=10;$j++) {
        print("_");
    }
    print("\tREAD0\t**********\tRRRRRRRRRR\n");

    for($i=0;$i<$no;$i++) {
        print("READ0\t\t");
        for($j=1;$j<=$i;$j++) {
            print("=");
        }
        print("|");
        for($j=$i+2;$j<=$no;$j++) {
            print("!");
        }
        for($j=$no+1;$j<=10;$j++) {
            print("_");
        }
        print("\tREAD1\t**********\tRRRRRRRRRR\n");
    }
    
    print("\n");
}

// looking for the 1st head
function print_rows1($no) {
    print("//$no line(s)\n");
    
    // 1 blank is acceptable
    
    for($k=1;$k<=$no;$k++) {
        print("READ1\t\t");
        for($j=1;$j<$k;$j++) {
            print("=");
        }
        print("_");
        for($j=$k+1;$j<=$no;$j++) {
            print("=");
        }
        for($j=$no+1;$j<=10;$j++) {
            print("_");
        }
        print("\tREAD1\t**********\tRRRRRRRRRR\n");
    }

    // 1 blank + 1 head is not
    
    // l < k
    for($l=1;$l<=$no;$l++) {
        for($k=$l+1;$k<=$no;$k++) {
            print("READ1\t\t");
            for($j=1;$j<$l;$j++) {
                print("=");
            }
            print("|");
            for($j=$l+1;$j<$k;$j++) {
                print("!");
            }
            print("_");
            for($j=$k+1;$j<=$no;$j++) {
                print("!");
            }
            for($j=$no+1;$j<=10;$j++) {
                print("_");
            }
            print("\tREAD2\t");
            for($j=1;$j<$k;$j++) {
                print("|");
            }
            print("*");
            for($j=$k+1;$j<=$no;$j++) {
                print("|");
            }
            for($j=$no+1;$j<=10;$j++) {
                print("*");
            }
            print("\tRRRRRRRRRR\n");
        }
    }

    // k < l
    for($k=1;$k<=$no;$k++) {
        for($l=$k+1;$l<=$no;$l++) {
            print("READ1\t\t");
            for($j=1;$j<$k;$j++) {
                print("=");
            }
            print("_");
            for($j=$k+1;$j<$l;$j++) {
                print("=");
            }
            print("|");
            for($j=$l+1;$j<=$no;$j++) {
                print("!");
            }
            for($j=$no+1;$j<=10;$j++) {
                print("_");
            }
            print("\tREAD2\t");
            for($j=1;$j<$k;$j++) {
                print("|");
            }
            print("*");
            for($j=$k+1;$j<=$no;$j++) {
                print("|");
            }
            for($j=$no+1;$j<=10;$j++) {
                print("*");
            }
            print("\tRRRRRRRRRR\n");
        }
    }

    print("\n");
}

// cleaning up after the head
function print_rows2($no) {
    print("//post nails\n");
    
    for($i=0;$i<$no;$i++) {
        print("READ2\t\t");
        for($j=1;$j<=$i;$j++) {
            print("_");
        }
        print("!");
        for($j=$i+2;$j<=$no;$j++) {
            print("*");
        }
        for($j=$no+1;$j<=10;$j++) {
            print("_");
        }
        print("\tREAD2\t__________\tRRRRRRRRRR\n");
    }
        
    print("\n");
}

///////////////////////////////////////////////////////////////////////////
// main program

print("HEADS\t\tAAAAAAAAAA

//spread heads
START\t\t**********\tSTART2\t**********\tSDDDDDDDDD\n");
for($i=2;$i<=8;$i++) {
    printf("START%d\t\t**********\tSTART%d\t**********\tS", $i, $i+1);
    for($j=1;$j<$i;$j++) {
        print("S");
    }
    for($j=$i;$j<=9;$j++) {
        print("D");
    }
    print("\n");
}
print("START9\t\t**********\tREAD0\t**********\tSSSSSSSSSD

READ0\t\t#********_\tREAD0\t**********\tRRRRRRRRRR

//looking for head 1\n");
for($i=2;$i<=9;$i++) {
    print_rows0($i);
}
print("//looking for head 2\n");
for($i=2;$i<=9;$i++) {
    print_rows1($i);
}

print_rows2(9);

print("//end
READ2\t\t__________\tSTOP\t**********\tSSSSSSSSSS

//10+ lines
READ0\t\t*********#\tREAD3D\t**********\tRRRRRRRRRR

//going down, up and right
READ3D\t\t==========\tREAD3D\t**********\tDDDDDDDDDD
READ3D\t\t=========_\tREAD3LU\t**********\tRRRRRRRRRR
READ3LU\t\t**********\tREAD3U\t**********\tUUUUUUUUUU
READ3U\t\t==========\tREAD3U\t**********\tUUUUUUUUUU
READ3U\t\t_=========\tREAD3LD\t**********\tRRRRRRRRRR
READ3LD\t\t**********\tREAD3D\t**********\tDDDDDDDDDD

//finding the 1st head\n");

for($i=0;$i<10;$i++) {
    $str1 = "";
    for($j=1;$j<=$i;$j++) {
        $str1 .= "=";
    }
    $str1 .= "|";
    for($j=$i+2;$j<=10;$j++) {
        $str1 .= "*";
    }
    printf("READ3D\t\t%s\tREAD3DX\t**********\tDDDDDDDDDD\n", $str1);
    printf("READ3U\t\t%s\tREAD3UX\t**********\tUUUUUUUUUU\n", $str1);
}

print("
READ3DX\t\t!!!!!!!!!_\tMARK7LUX\t*********@\tSSSSSSSSSR
READ3DX\t\t!!!!!!!!!!\tREAD3DX\t\t**********\tDDDDDDDDDD
READ3UX\t\t_!!!!!!!!!\tMARK7LDX\t@*********\tRSSSSSSSSS
READ3UX\t\t!!!!!!!!!!\tREAD3UX\t\t**********\tUUUUUUUUUU

//mark top/bottom of next column
MARK7LUX\t\t**********\tMARK8LUX\t*********@\tSSSSSSSSSL
MARK8LUX\t\t**********\tREAD5UX\t\t**********\tUUUUUUUUUU

MARK7LDX\t\t**********\tMARK8LDX\t@*********\tLSSSSSSSSS
MARK8LDX\t\t**********\tREAD5DX\t\t**********\tDDDDDDDDDD

// leave this 1st column alone
READ5DX\t\t!!!!!!!!!!\tREAD5DX\t**********\tDDDDDDDDDD
READ5UX\t\t!!!!!!!!!!\tREAD5UX\t**********\tUUUUUUUUUU

//mark top/bottom of next column
READ5DX\t\t\t!!!!!!!!!_\tREAD6LUX\t**********\tRRRRRRRRRR
READ6LUX\t\t**********\tREAD7LUX\t**********\tSSSSSSSSSR
READ7LUX\t\t**********\tREAD8LUX\t*********@\tSSSSSSSSSL
READ8LUX\t\t**********\tREAD3UY\t*********@\tUUUUUUUUUU

READ5UX\t\t\t_!!!!!!!!!\tREAD6LDX\t**********\tRRRRRRRRRR
READ6LDX\t\t**********\tREAD7LDX\t**********\tRSSSSSSSSS
READ7LDX\t\t**********\tREAD8LDX\t@*********\tLSSSSSSSSS
READ8LDX\t\t**********\tREAD3DY\t@*********\tDDDDDDDDDD

//finding the 2nd head\n");

for($i=0;$i<10;$i++) {
    $str1 = "";
    for($j=1;$j<=$i;$j++) {
        $str1 .= "=";
    }
    if($i<9) {
        $str1 .= "_";
    }
    for($j=$i+2;$j<=9;$j++) {
        $str1 .= "=";
    }
    printf("READ3DY\t\t%s=\tREAD3DY\t\t**********\tDDDDDDDDDD\n", $str1);
    printf("READ3DY\t\t%s_\tREAD3DY\t\t**********\tDDDDDDDDDD\n", $str1);
    printf("READ3DY\t\t%s@\tREAD6LUX\t*********_\tRRRRRRRRRR\n", $str1);
    printf("READ3UY\t\t=%s\tREAD3UY\t\t**********\tUUUUUUUUUU\n", $str1);
    printf("READ3UY\t\t_%s\tREAD3UY\t\t**********\tUUUUUUUUUU\n", $str1);
    printf("READ3UY\t\t@%s\tREAD6LDX\t_*********\tRRRRRRRRRR\n", $str1);
}

print("\n");

// l < k
for($l=1;$l<=10;$l++) {
    for($k=$l+1;$k<=10;$k++) {
        print("READ3DY\t\t");
        for($j=1;$j<$l;$j++) { print("="); }
        print("|");
        for($j=$l+1;$j<$k;$j++) { print("!"); }
        print("_");
        for($j=$k+1;$j<=10;$j++) { print("!"); }
        print("\tREAD4D\t**********\tDDDDDDDDDD\n");
    }
}
// hack
printf("READ3DY\t\t=========|\tREAD4D\t**********\tDDDDDDDDDD\n", $str1);
print("\n");

for($l=1;$l<=10;$l++) {
    for($k=$l+1;$k<=10;$k++) {
        print("READ3UY\t\t");
        for($j=1;$j<$l;$j++) { print("="); }
        print("|");
        for($j=$l+1;$j<$k;$j++) { print("!"); }
        print("_");
        for($j=$k+1;$j<=10;$j++) { print("!"); }
        print("\tREAD4U\t**********\tUUUUUUUUUU\n");
    }
}
// hack
printf("READ3UY\t\t====|=====\tREAD4U\t**********\tDDDDDDDDDD\n", $str1);
print("\n");

// k < l
for($k=1;$k<=10;$k++) {
    for($l=$k+1;$l<=10;$l++) {
        print("READ3DY\t\t");
        for($j=1;$j<$k;$j++) { print("="); }
        print("_");
        for($j=$k+1;$j<$l;$j++) { print("="); }
        print("|");
        for($j=$l+1;$j<=10;$j++) { print("!"); }
        print("\tREAD4D\t**********\tDDDDDDDDDD\n");
    }
}
print("\n");

for($k=1;$k<=10;$k++) {
    for($l=$k+1;$l<=10;$l++) {
        print("READ3UY\t\t");
        for($j=1;$j<$k;$j++) { print("="); }
        print("_");
        for($j=$k+1;$j<$l;$j++) { print("="); }
        print("|");
        for($j=$l+1;$j<=10;$j++) { print("!"); }
        print("\tREAD4U\t**********\tUUUUUUUUUU\n");
    }
}

print("
READ4D\t\t*********@\tMARK7LU\t**********\tSSSSSSSSSR
READ4D\t\t*********=\tREAD4D\t**********\tDDDDDDDDDD
READ4D\t\t*********_\tREAD4D\t**********\tDDDDDDDDDD
READ4U\t\t@*********\tMARK7LD\t**********\tRSSSSSSSSS
READ4U\t\t=*********\tREAD4U\t**********\tUUUUUUUUUU
READ4U\t\t_*********\tREAD4U\t**********\tUUUUUUUUUU

//mark top/bottom of next column, for deletion
MARK7LU\t\t**********\tMARK8LU\t*********@\tSSSSSSSSSL
MARK8LU\t\t**********\tHEAD5U\t**********\tUUUUUUUUUU

MARK7LD\t\t**********\tMARK8LD\t@*********\tLSSSSSSSSS
MARK8LD\t\t**********\tHEAD5D\t**********\tDDDDDDDDDD

//hit the nails\n");

for($i=0;$i<=9;$i++) {
    $str1 = "";
    $str2 = "";
    for($j=1;$j<=$i;$j++) {
        $str1 .= "!";
        $str2 .= "|";
    }
    if($i < 9) {
        $str1 .= "_";
        $str2 .= "_";
    }
    for($j=$i+2;$j<10;$j++) {
        $str1 .= "!";
        $str2 .= "|";
    }
    if($i<9) {
        print("HEAD5D\t\t$str1=\tHEAD5D\t$str2|\tDDDDDDDDDD\n");
        print("HEAD5D\t\t$str1|\tHEAD5D\t$str2|\tDDDDDDDDDD\n");
        print("HEAD5D\t\t$str1@\tDEL6LU\t${str2}*\tRRRRRRRRRR\n");
        print("HEAD5U\t\t=$str1\tHEAD5U\t|$str2\tUUUUUUUUUU\n");
        print("HEAD5U\t\t|$str1\tHEAD5U\t|$str2\tUUUUUUUUUU\n");
        print("HEAD5U\t\t@$str1\tDEL6LD\t*$str2\tRRRRRRRRRR\n");
    } else {
        print("HEAD5D\t\t${str1}_\tHEAD5D\t{$str2}_\tDDDDDDDDDD\n");
        print("HEAD5D\t\t$str1=\tHEAD5D\t$str2|\tDDDDDDDDDD\n");
        print("HEAD5D\t\t$str1|\tHEAD5D\t$str2|\tDDDDDDDDDD\n");
        print("HEAD5D\t\t$str1@\tDEL6LU\t${str2}*\tRRRRRRRRRR\n");
        print("HEAD5U\t\t_$str1\tHEAD5U\t_$str2\tUUUUUUUUUU\n");
        print("HEAD5U\t\t=$str1\tHEAD5U\t|$str2\tUUUUUUUUUU\n");
        print("HEAD5U\t\t|$str1\tHEAD5U\t|$str2\tUUUUUUUUUU\n");
        print("HEAD5U\t\t@$str1\tDEL6LD\t*$str2\tRRRRRRRRRR\n");
    }
}

print("
//mark top/bottom of next column, for deletion
DEL6LU\t\t**********\tDEL7LU\t**********\tSSSSSSSSSR
DEL7LU\t\t**********\tDEL8LU\t*********@\tSSSSSSSSSL
DEL8LU\t\t**********\tDEL9UE\t**********\tUUUUUUUUUU

DEL6LD\t\t**********\tDEL7LD\t**********\tRSSSSSSSSS
DEL7LD\t\t**********\tDEL8LD\t@*********\tLSSSSSSSSS
DEL8LD\t\t**********\tDEL9DE\t**********\tDDDDDDDDDD

//delete the rest\n");

for($i=0;$i<=9;$i++) {
    $str = "DEL9D\t\t";
    for($j=1;$j<=$i;$j++) {
        $str .= "_";
    }
    if($i < 9) {
        $str .= "!";
    }
    for($j=$i+2;$j<=9;$j++) {
        $str .= "*";
    }
    if($i < 9) {
        print("${str}_\tDEL9D\t__________\tDDDDDDDDDD\n");
    } else {
        print("${str}_\tDEL9D\t__________\tDDDDDDDDDD\n");
    }
    print("$str=\tDEL9D\t__________\tDDDDDDDDDD\n");
    print("$str|\tDEL9D\t__________\tDDDDDDDDDD\n");
    print("$str@\tDEL6LU\t_________*\tRRRRRRRRRR\n");
}

for($i=0;$i<=9;$i++) {
    $str1 = "DEL9U\t\t";
    $str2 = "";
    for($j=1;$j<=$i;$j++) {
        $str2 .= "_";
    }
    if($i < 9) {
        $str2 .= "!";
    }
    for($j=$i+2;$j<=9;$j++) {
        $str2 .= "*";
    }
    if($i < 9) {
        print("${str1}_$str2\tDEL9U\t__________\tUUUUUUUUUU\n");
    } else {
        print("${str1}_$str2\tDEL9U\t__________\tUUUUUUUUUU\n");
    }
    print("$str1=$str2\tDEL9U\t__________\tUUUUUUUUUU\n");
    print("$str1|$str2\tDEL9U\t__________\tUUUUUUUUUU\n");
    print("$str1@$str2\tDEL6LD\t*_________\tRRRRRRRRRR\n");
}

print("\n//cleanup\n
DEL9DE\t\t__________\tDEL9DE\t**********\tDDDDDDDDDD\n");
for($i=0;$i<9;$i++) {
    $str="DEL9DE\t\t";
    for($j=1;$j<=$i;$j++) {
        $str .= "_";
    }
    $str .= "!";
    for($j=$i+2;$j<10;$j++) {
        $str .= "*";
    }
    print("${str}_\tDEL9D\t__________\tDDDDDDDDDD\n");
}
print("
DEL9DE\t\t*********=\tDEL9D\t__________\tDDDDDDDDDD
DEL9DE\t\t*********|\tDEL9D\t__________\tDDDDDDDDDD
DEL9DE\t\t*********@\tDEL0U\t**********\tRRRRRRRRRR
DEL0U\t\t__________\tDEL0U\t*********_\tUUUUUUUUUU
DEL0U\t\t@_________\tDELX1U\t**********\tSSSSSSSSSS\n\n");

for($i=0;$i<9;$i++) {
    $str="DEL9UE\t\t_";
    for($j=1;$j<=$i;$j++) {
        $str .= "_";
    }
    $str .= "!";
    for($j=$i+2;$j<10;$j++) {
        $str .= "*";
    }
    print("${str}\tDEL9U\t__________\tDDDDDDDDDD\n");
}
print("DEL9UE\t\t__________\tDEL9UE\t**********\tUUUUUUUUUU

DEL9UE\t\t=*********\tDEL9U\t__________\tUUUUUUUUUU
DEL9UE\t\t|*********\tDEL9U\t__________\tUUUUUUUUUU
DEL9UE\t\t@*********\tDEL0D\t**********\tRRRRRRRRRR
DEL0D\t\t__________\tDEL0D\t*********_\tDDDDDDDDDD
DEL0D\t\t_________@\tDELX1D\t**********\tSSSSSSSSSS

DELX1D\t\t*********@\tDELX2D\t*********_\tLLLLLLLLLL
DELX2D\t\t**********\tDELX3D\t**********\tUSSSSSSSSS
DELX3D\t\t_*********\tDELX3D\t**********\tUSSSSSSSSS
DELX3D\t\t@*********\tDELX4\t_*********\tLLLLLLLLLL

DELX1U\t\t@*********\tDELX2U\t_*********\tLLLLLLLLLL
DELX2U\t\t**********\tDELX3U\t**********\tSSSSSSSSSD
DELX3U\t\t*********_\tDELX3U\t**********\tSSSSSSSSSD
DELX3U\t\t*********@\tDELX4\t*********_\tLLLLLLLLLL

DELX4\t\t*__*******\tDELX4\t_********_\tLLLLLLLLLL
DELX4\t\t*_|*******\tDELX4\t_********_\tLLLLLLLLLL
DELX4\t\t*_=*******\tDELX4\t_********_\tLLLLLLLLLL
DELX4\t\t*|_*******\tDELX4\t_********_\tLLLLLLLLLL
DELX4\t\t*||*******\tDELX4\t_********_\tLLLLLLLLLL
DELX4\t\t*=_*******\tDELX4\t_********_\tLLLLLLLLLL
DELX4\t\t*==*******\tDELX5\t_********_\tLLLLLLLLLL
DELX5\t\t*=********\tDELX5\t_********_\tLLLLLLLLLL

DELX4\t\t*|=*******\tSTOP\t_********_\tLLLLLLLLLL
DELX4\t\t*=|*******\tSTOP\t_********_\tSSSSSSSSSS
DELX5\t\t*#********\tSTOP\t**********\tSSSSSSSSSS

");

?>
