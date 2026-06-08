<?php

///////////////////////////////////////////////////////////////////////////
// constants

///////////////////////////////////////////////////////////////////////////
// functions

// looking for the 1st head
function print_rows1($no) {
    print("//$no line(s)\n");

    print("READ1\t\t");
    for($j=1;$j<=$no;$j++) {
        print("=");
    }
    for($j=$no+1;$j<=10;$j++) {
        print("_");
    }
    print("\tREAD1\t**********\tRRRRRRRRRR\n");

    for($i=0;$i<$no;$i++) {
        print("READ1\t\t");
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
        print("\tREAD2\t");
        for($j=1;$j<=$no;$j++) {
            print("|");
        }
        for($j=$no+1;$j<=10;$j++) {
            print("_");
        }
        print("\tRRRRRRRRRR\n");
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

print("HEADS\t\tAAAAAAAAAA\n\n");

print("//spread heads\n");
print("START\t\t**********\tSTART2\t**********\tSDDDDDDDDD\n");
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
print("START9\t\t**********\tREAD1\t**********\tSSSSSSSSSD\n\n");

print("READ1\t\t#********_\tREAD1\t**********\tRRRRRRRRRR\n\n");

for($i=1;$i<=9;$i++) {
    print_rows1($i);
}

print_rows2(9);

print("//end\n");
print("READ2\t\t__________\tSTOP\t**********\tSSSSSSSSSS\n\n");

print("//10+ lines\n");
print("READ1\t\t*********#\tREAD3D\t**********\tRRRRRRRRRR\n\n");

print("//going down, up and right\n");
print("READ3D\t\t==========\tREAD3D\t**********\tDDDDDDDDDD\n");
print("READ3D\t\t=========_\tREAD3LU\t**********\tRRRRRRRRRR\n");
print("READ3LU\t\t**********\tREAD3U\t**********\tUUUUUUUUUU\n");
print("READ3U\t\t==========\tREAD3U\t**********\tUUUUUUUUUU\n");
print("READ3U\t\t_=========\tREAD3LD\t**********\tRRRRRRRRRR\n");
print("READ3LD\t\t**********\tREAD3D\t**********\tDDDDDDDDDD\n\n");

print("//finding the first head\n");

for($i=0;$i<10;$i++) {
    $str1 = "";
    for($j=1;$j<=$i;$j++) {
        $str1 .= "=";
    }
    $str1 .= "|";
    for($j=$i+2;$j<=10;$j++) {
        $str1 .= "*";
    }
    printf("READ3D\t\t%s\tREAD4D\t**********\tDDDDDDDDDD\n", $str1);
    printf("READ3U\t\t%s\tREAD4U\t**********\tUUUUUUUUUU\n", $str1);
}

print("READ4D\t\t!!!!!!!!!_\tMARK7LU\t**********\tSSSSSSSSSR\n");
print("READ4D\t\t!!!!!!!!!!\tREAD4D\t**********\tDDDDDDDDDD\n");
print("READ4U\t\t_!!!!!!!!!\tMARK7LD\t**********\tRSSSSSSSSS\n");
print("READ4U\t\t!!!!!!!!!!\tREAD4U\t**********\tUUUUUUUUUU\n\n");

print("//mark top/bottom of next culumn, for deletion\n");
print("MARK7LU\t\t**********\tMARK8LU\t*********@\tSSSSSSSSSL\n");
print("MARK8LU\t\t**********\tHEAD5U\t**********\tUUUUUUUUUU\n\n");

print("MARK7LD\t\t**********\tMARK8LD\t@*********\tLSSSSSSSSS\n");
print("MARK8LD\t\t**********\tHEAD5D\t**********\tDDDDDDDDDD\n");

print("//hit the nails\n");
print("HEAD5D\t\t!!!!!!!!!!\tHEAD5D\t||||||||||\tDDDDDDDDDD\n");
print("HEAD5U\t\t!!!!!!!!!!\tHEAD5U\t||||||||||\tUUUUUUUUUU\n\n");

print("//mark top/bottom of next culumn, for deletion\n");
print("HEAD5D\t\t!!!!!!!!!_\tDEL6LU\t**********\tRRRRRRRRRR\n");
print("DEL6LU\t\t**********\tDEL7LU\t**********\tSSSSSSSSSR\n");
print("DEL7LU\t\t**********\tDEL8LU\t*********@\tSSSSSSSSSL\n");
print("DEL8LU\t\t**********\tDEL9UE\t**********\tUUUUUUUUUU\n\n");

print("HEAD5U\t\t_!!!!!!!!!\tDEL6LD\t**********\tRRRRRRRRRR\n");
print("DEL6LD\t\t**********\tDEL7LD\t**********\tRSSSSSSSSS\n");
print("DEL7LD\t\t**********\tDEL8LD\t@*********\tLSSSSSSSSS\n");
print("DEL8LD\t\t**********\tDEL9DE\t**********\tDDDDDDDDDD\n\n");

print("//delete the rest\n");

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
    print("$str@\tDEL6LU\t__________\tRRRRRRRRRR\n");
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
    print("$str1@$str2\tDEL6LD\t__________\tRRRRRRRRRR\n");
}

print("\n//cleanup\n");

print("DEL9DE\t\t__________\tDEL9DE\t**********\tDDDDDDDDDD\n");
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
print("DEL9DE\t\t*********=\tDEL9D\t__________\tDDDDDDDDDD\n");
print("DEL9DE\t\t*********|\tDEL9D\t__________\tDDDDDDDDDD\n");
print("DEL9DE\t\t*********@\tDEL0U\t*********_\tRRRRRRRRRR\n");
print("DEL0U\t\t__________\tDEL0U\t*********_\tUUUUUUUUUU\n");
print("DEL0U\t\t@_________\tSTOP\t_*********\tUUUUUUUUUU\n\n");

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
print("DEL9UE\t\t__________\tDEL9UE\t**********\tUUUUUUUUUU\n");

print("DEL9UE\t\t=*********\tDEL9U\t__________\tUUUUUUUUUU\n");
print("DEL9UE\t\t|*********\tDEL9U\t__________\tUUUUUUUUUU\n");
print("DEL9UE\t\t@*********\tDEL0D\t_*********\tRRRRRRRRRR\n");
print("DEL0D\t\t__________\tDEL0D\t*********_\tDDDDDDDDDD\n");
print("DEL0D\t\t_________@\tSTOP\t*********_\tDDDDDDDDDD\n");

?>
