#!/usr/bin/php -f
<?php

$out_bolditalic = false;

$mode = 0;
$pathes = 0;
$spline = false;

function make_bold( $lines )
{
	global	$pathes, $mode, $spline;

	$pathes++;
	
	# analyse curve points
	$move = 0;
	$leading = "";
	foreach ( $lines as $i=>$L ) {
		if ( "" == $L && $i+1==count($lines) ) {  $leading = " ";  continue;  }

		$items = explode( " ", trim($L) );
		if ( "Named:" == trim($items[0]) ) {  $leading = " ";  continue;  }

		# calculate count of numbers in string before character's designator
		$n = 0;
		while ( isset( $items[$n] ) ) {
			if ( !is_numeric( $items[$n] ) ) break;
			$n++;
		}

		# each pair is an x-y coordiantes; last pair is a point coordinates, previouse pairs are control points
		if ( 1 == ( $n & 1 ) || 0 == $n ) {
			echo "Err2a!\n";
		} else {
			# move and widen character
			for ( $j = 0; $j<$n; $j+=2 ) {
				$x = $items[$j];	$y = $items[$j+1];

				$x = 112.0 + $x - $y * sin(deg2rad(12.0));

				if ( $y > 1395 ) {		# above 1395; ((1400:=caps top))
					# above caps: nothing
				} else if ( $y > 1355 ) {	# between 1355 and 1395
					$y = ($y - 1355.0)*((1395.0+30.0-1355.0)/(1395.0-1355.0)) + 1325.0;
				} else if ( $y > 1320 ) {	# between 1320 and 1355 ((1325:=caps-x; 1350:=caps-x-round))
					$y -= 30;	# caps horizontal line between 1029 and 1320 
				} else if ( $y > 1029 ) {	# between 1029 and 1320
					# between caps and small
					$y = ( $y - 1029 ) * ((1320.0-30.0-1029.0)/(1320.0-1029.0)) + 1029.0;
				} else if ( $y > 1019 ) {	# between 1019 and 1029 ((1024:=small top))
					# small top: nothing
				} else if ( $y > 979 ) {	# between 979 and 1029
					# some below small top
					$y = ($y - 979)*((1029.0-(979.0-30.))/(1029.0-979.0)) + (979.0-30.0);
				} else if ( $y > 944 ) {	# between 944 and 979
					$y -= 30;	# small horizontal line between 944 and 979
				} else if ( $y > 80 ) {		# beteen 80 and 944
					# small letter
					$y = ($y - 80) * ((944.0-30.0-(80.0+30.0))/(944.0-80.0)) + (80+30);
				} else if ( $y > 45 ) {		# between 45 and 80
					$y += 30;
				} else if ( $y > 5 ) {	# between 5 an 45
					$y = ($y - 5)*((45.0+30.0-5.0)/(45.0-5.0)) + 5.0;
				} else {			# below -10
					# around and below baseline: nothing
				}

				if ( $x < 140 ) {		# ... 140
					# too left: nothing
				} else if ( $x < 195 ) {	# 140..195
					$x = ($x - 140) * ((195.0+30.0-140.0)/(195.0-140.0)) + 140;
				} else if ( $x < 215 ) {	# 195..215
					$x += 30;	# left vertical line
				} else if ( $x < 1014 ) {	# 215..1014
					$x = ($x - 215)*((1014.0-30.0-(215.0+30.0))/(1014.0-215.0)) + (215.0+30.0);
				} else if ( $x < 1034 ) {	# 1014..1034
					$x -= 30;	# right vertical line
				} else if ( $x < 1089 ) {	# 1034..1089
					$x = ($x - 1034) * ((1089.0+30.0-1034.0)/(1089.0-1034.0)) + (1034.0-30.0);
				} else {			# 1089 ...
					# too right: nothing
				}
					
				$x = -112.0 + $x + $y * sin(deg2rad(12.0));

				$items[$j] = ceil($x);	$items[$j+1] = ceil($y);
			}

			$lines[$i] = $leading . implode( " ", $items );
		}
		$leading = " ";
	}
	return $lines;
}



function out( $str, $write_italic=true, $write_bold=true, $write_bolditalic=true )
{
	global	$out_italic, $out_bold, $out_bolditalic;

#	if ( $write_italic ) fwrite( $out_italic, $str );
#	if ( $write_bold ) fwrite( $out_bold, $str );
	if ( $write_bolditalic ) fwrite( $out_bolditalic, $str );
}


function do_clause( $clause )
{
	global	$pathes, $mode, $spline;

	$lines = explode( "\n", $clause );
	if ( count($lines) <= 0 ) {
		echo "Err 0!\n";
		return;
	}
	$tags = explode( ":", $lines[0] );
	if ( count($tags) <= 0 ) {
		echo "Err 1!\n";
		return;
	}

	if ( $tags[0] == "StartChar" ) {
		$mode = 1;
	} else if ( $tags[0] == "EndChar" ) {
		$mode = 0;
	} else if ( $tags[0] == "EndSplineSet" ) {
		$spline = false;
	}

	if ( 1 == $mode ) {
		if ( $tags[0] == "Encoding" ) {
			$codes = explode( " ", trim( $tags[1] ) );
			if ( $codes[0] >= 0x2100 && $codes[0] <= 0x21FF ) {	# graphics...
				$mode = 101;	# nothing
			} else if ( $codes[0] == 0x0023 ) {
				$mode = 101;	# nothing
			} else {
				$mode = 11;	# do it...
			}
		}
		out( $clause, true, true, true );
	} else if ( 11 == $mode ) {
		# center them only
		if ( true == $spline ) {
#			out( implode( "\n", make_italic( $lines ) ), true, false, false );
#			out( implode( "\n", make_bold( $lines ) ), false, true, false );
#			out( implode( "\n", make_italic( make_bold( $lines ) ) ), false, false, true );
			out( implode( "\n", make_bold( $lines ) ), false, false, true );
		} else {
			out( $clause, true, true, true );
		}
	} else if ( 101 == $mode ) {
		# graphic line character; nothing to do
		out( $clause, true, true, true );
	} else {
		#some other...
		out( $clause, true, true, true );
	}

	if ( $tags[0] == "SplineSet" ) {
		$spline = true;
	}
}


#
# process input file
#
$sfd = @fopen( "AnkaCoder-Norm-SkelI.sfd", "r" );
if ( false === $sfd ) die( "Can't open input file\n" );

$out_bolditalic = @fopen( "AnkaCoder-Norm-xSkelBI.sfd", "w" );
if ( false === $out_bolditalic ) die( "Can't open output file\n" );

echo "Go...\n";

#
$lines = 0;
$sentences = 0;
$clause = "";
while ( false !== ($str = fgets( $sfd )) ) {
	if ( "" == $str ) out( $str );
	$lines++;
	if ( $str{0} == " " ) {
		$clause .= $str;
	} else {
		$sentences++;
		if ( "" != $clause ) do_clause( $clause );
		$clause = $str;
	}
}
if ( "" != $clause ) do_clause( $clause );
echo "Total $lines lines and $sentences sentences with $pathes pathes\n";

fclose( $out_bolditalic );
fclose( $sfd );
?>
