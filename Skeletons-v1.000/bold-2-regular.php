#!/usr/bin/php -f
<?php

$mode = 0;
$pathes = 0;
$outfile = false;
$infile_name = "AnkaCoder-C75-SkelI";
$angle = 0.0;		# really it is a sin(angle)


function do_path( $lines )
{
	global	$pathes;
	global	$angle;

	$pathes++;
	
	# do not update bounding boxes (named curves)
	foreach ( $lines as $L ) {
		$tags = explode( ":", trim($L) );
		if ( "Named" == $tags[0] ) return $lines;
	}

	# do not update small curves
	$Xmin = $Ymin = 10000; $Xmax = $Ymax = -10000;
	foreach ( $lines as $i=>$L ) {
		if ( "" == $L && $i+1==count($lines) ) {
			$leading = " ";
			continue;
		}
		$items = explode( " ", trim($L) );

		# calculate count of numbers in string before character designator
		$n = 0;
		while ( isset( $items[$n] ) ) {
			if ( !is_numeric( $items[$n] ) ) break;
			$n++;
		}

		# each pair is an x-y coordiantes; last pair is a point coordinates, previouse pairs are control points
		if ( 1 == ( $n & 1 ) || 0 == $n ) {
			echo "Err2a!\n";
		} else {
			$ptX = $items[$n-2];	$ptY = $items[$n-1];
			if ( $Xmin > $ptX ) $Xmin = $ptX;
			if ( $Xmax < $ptX ) $Xmax = $ptX;
			if ( $Ymin > $ptY ) $Ymin = $ptY;
			if ( $Ymax < $ptY ) $Ymax = $ptY;
		}
	}
	if ( 512 > $Xmax-$Xmin && 512 > $Ymax-$Ymin ) return $lines;

	# analyse curve points
	$leading = "";
	$move = 0;
	foreach ( $lines as $i=>$L ) {
		if ( "" == $L && $i+1==count($lines) ) {
			$leading = " ";
			continue;
		}
		$items = explode( " ", trim($L) );

		# calculate count of numbers in string before character designator
		$n = 0;
		while ( isset( $items[$n] ) ) {
			if ( !is_numeric( $items[$n] ) ) break;
			$n++;
		}

		# each pair is an x-y coordiantes; last pair is a point coordinates, previouse pairs are control points
		if ( 1 == ( $n & 1 ) || 0 == $n ) {
			echo "Err2b!\n";
		} else {
			$changed = false;
			$ptX = $items[$n-2];	$ptY = $items[$n-1];
			if ( 0 != $move && 2 < $n ) {
				$x = $items[0];		$y = $items[1];
				
				$y += $move;
				$x += ceil( $angle*$move );
				
				$items[0] = $x;		$items[1] = $y;
				$move = 0;
				$changed = true;
			}
			if (
				( $ptY > 30 && $ptY < 300 ) ||
				( $ptY > 650 && $ptY < 970 ) ||
				( $ptY > 1100 && $ptY < 1360 )
			) {
				$move = $ptY < 512 ? -32 : 32;
				for ( $j = 2 < $n ? 2 : 0; $j<$n; $j+=2 ) {
					$x = $items[$j];	$y = $items[$j+1];
					
					$y += $move;
					$x += ceil( $angle*$move );
					
					$items[$j] = $x;	$items[$j+1] = $y;
					$changed = true;
				}
			}
			# rebuild string
			if ( true == $changed ) $lines[$i] = $leading . implode( " ", $items );
		}
		$leading = " ";
	}
	return $lines;
}


function out( $str )
{
	global	$outfile;

	fwrite( $outfile, $str );
}


function do_clause( $clause )
{
	global	$mode;
	global	$angle;

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
	if ( 0 == $mode ) {
		if ( $tags[0] == "StartChar" ) {
			$mode = 1;
		} else if ( $tags[0] == "ItalicAngle" ) {
			$angle = sin( deg2rad( -1 * $tags[1] ) );
			echo "SIN(ItalicAngle) = $angle\n";
		}
	} else if ( 1 == $mode ) {
		if ( $tags[0] == "SplineSet" ) {
			$mode = 2;
		} else if ( $tags[0] == "Encoding" ) {
			$codes = explode( " ", trim( $tags[1] ) );
			if ( ( $codes[0] >= 0x2C6 && $codes[0] <= 0x38F ) || ( $codes[0] >= 0x2100 && $codes[0] <= 0x26FF ) ) {
				$mode = 11;
			}
		}
	} else if ( 2 == $mode && $tags[0] == "EndSplineSet" ) {
		$mode = 1;
	} else if ( ( 1 == $mode || 11 == $mode ) && $tags[0] == "EndChar" ) {
		$mode = 0;
	} else if ( 2 == $mode ) {
		# path ok
		$clause = implode( "\n", do_path( $lines ) );
	} else {
		#some other...
	}
	out( $clause );
}


#
# process input file
#
$sfd = @fopen( $infile_name.".sfd", "r" );
#$sfd = @fopen( "AnkaCoder-C75-SkelI.sfd", "r" );
if ( false === $sfd ) die( "Can't open input file\n" );

$outfile = @fopen( $infile_name."-RESULT.sfd", "w" );
if ( false === $outfile ) die( "Can't open output file\n" );
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

fclose( $outfile );
fclose( $sfd );
?>
