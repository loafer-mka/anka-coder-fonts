#!/usr/bin/php -f
<?php

$mode = 0;
$pathes = 0;
$outfile = false;
$angle = 0.0;		# really it is a sin(angle)
$bold = false;
$old_width = 922.0;
$new_width = 1075.0;
$char_height = 2048.0;


function prop_widen_path( $lines )
{
	global	$pathes, $angle, $bold, $new_width, $old_width, $char_height;

	$pathes++;
	
	# analyse curve points
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
			echo "Err2h!\n";
		} else {
			for ( $j = 0; $j<$n; $j+=2 ) {
				$x = $items[$j];	$y = $items[$j+1];

				$x = ceil( $x * $new_width / $old_width );
				
				$items[$j] = $x;	$items[$j+1] = $y;
				$changed = true;
			}
			$lines[$i] = $leading . implode( " ", $items );
		}
		$leading = " ";
	}
	return $lines;
}


function widen_path( $lines )
{
	global	$pathes, $angle, $bold, $new_width, $old_width, $char_height;

	$pathes++;
	
	# analyse curve points
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
			for ( $j = 0; $j<$n; $j+=2 ) {
				$x = $items[$j];	$y = $items[$j+1];
				
				# move to new position (shift to half distance)
				if ( $x > 780 ) {
					$x += ceil( $new_width - $old_width );
				} else if ( $x > 300 ) {
					$x += ceil( ( $new_width - $old_width ) / 2 );
				}
				
				$items[$j] = $x;	$items[$j+1] = $y;
				$changed = true;
			}
			$lines[$i] = $leading . implode( " ", $items );
		}
		$leading = " ";
	}
	return $lines;
}


function center_path( $lines )
{
	global	$pathes, $angle, $bold, $new_width, $old_width, $char_height;

	$pathes++;
	
	# analyse curve points
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
			echo "Err2b!\n";
		} else {
			for ( $j = 0; $j<$n; $j+=2 ) {
				$x = $items[$j];	$y = $items[$j+1];
				
				# move to new position (shift to half distance)
				$x += ceil( ( $new_width - $old_width ) / 2 );
				
				$items[$j] = $x;	$items[$j+1] = $y;
				$changed = true;
			}
			$lines[$i] = $leading . implode( " ", $items );
		}
		$leading = " ";
	}
	return $lines;
}


function xy_scale_path( $lines )
{
	global	$pathes, $angle, $bold, $new_width, $old_width, $char_height;

	$pathes++;
	
	# analyse curve points
	$leading = "";
	$scale = $new_width / $old_width;
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
			echo "Err2b!\n";
		} else {
			for ( $j = 0; $j<$n; $j+=2 ) {
				$x = $items[$j];	$y = $items[$j+1];
				
				# move to new position (shift to half distance)
				$x = ceil( $x * $scale );
				$y = ceil( $y * $scale );
				
				$items[$j] = $x;	$items[$j+1] = $y;
				$changed = true;
			}
			$lines[$i] = $leading . implode( " ", $items );
		}
		$leading = " ";
	}
	return $lines;
}


function get_scaled_x( $x, $y )
{
	global	$pathes, $angle, $bold, $new_width, $old_width, $char_height;

	if ( $angle > 0.01 || $angle < -0.01 ) {
		$y_off = $y * $angle - 112.0;
		return 215.0 + $y_off + ($x - $y_off - 182.0) * (860.0-215.0) / (740.0-182.0);
	} else {
#		return 205.0 + ($x - 182.0) * (870.0-205.0) / (740.0-182.0);
		return 215.0 + ($x - 182.0) * (860.0-215.0) / (740.0-182.0);
	}
}


function x_scale_path( $lines )
{
	global	$pathes, $angle, $bold, $new_width, $old_width, $char_height;

	$pathes++;
	
	# move small curves to right for (1075-922)/2 pixels
	$Xmin = $Ymin = 10000; $Xmax = $Ymax = -10000;
	$small_curve = false;
	foreach ( $lines as $i=>$L ) {
		if ( "" == $L && $i+1==count($lines) ) continue;

		$items = explode( " ", trim($L) );
		if ( "Named:" == trim($items[0]) ) continue;

		# calculate count of numbers in string before character designator
		$n = 0;
		while ( isset( $items[$n] ) ) {
			if ( !is_numeric( $items[$n] ) ) break;
			$n++;
		}

		# each pair is an x-y coordiantes; last pair is a point coordinates, previouse pairs are control points
		if ( 1 == ( $n & 1 ) || 0 == $n ) {
			echo "Err2f!\n";
		} else {
			$ptX = $items[$n-2];	$ptY = $items[$n-1];
			if ( $Xmin > $ptX ) $Xmin = $ptX;
			if ( $Xmax < $ptX ) $Xmax = $ptX;
			if ( $Ymin > $ptY ) $Ymin = $ptY;
			if ( $Ymax < $ptY ) $Ymax = $ptY;
		}
	}
	if ( 384 > $Xmax-$Xmin && 384 > $Ymax-$Ymin ) $small_curve = true;

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
			echo "Err2g!\n";
		} else {
			# vertical adjustment for extra stroke width
			if ( false == $small_curve ) {
				$ptX = $items[$n-2];	$ptY = $items[$n-1];
				if ( 0 != $move && 2 < $n ) {
					$x = $items[0];		$y = $items[1];
					
					$y += $move;
					$x += $angle*$move;
					
					$items[0] = $x;		$items[1] = $y;
					$move = 0;
				}
				if (
					( $ptY > 20 && $ptY < 110 ) ||
					( $ptY > 910 && $ptY < 1000 ) ||
					( $ptY > 1300 && $ptY < 1380 )
				) {
					$move = true == $bold ? ( $ptY < 512 ? +13 : -13 ) : ( $ptY < 512 ? +15 : -15 );
					for ( $j = 2 < $n ? 2 : 0; $j<$n; $j+=2 ) {
						$x = $items[$j];	$y = $items[$j+1];
						
						$y += $move;
						$x += $angle*$move;
						
						$items[$j] = $x;	$items[$j+1] = $y;
					}
				}
			}

			# move and widen character
			for ( $j = 0; $j<$n; $j+=2 ) {
				$x = $items[$j];	$y = $items[$j+1];
				
				if ( true == $small_curve ) {
					$x += get_scaled_x( ($Xmax + $Xmin)/2.0, 0.0 ) - ($Xmax + $Xmin)/2.0;
				} else {
					$x = get_scaled_x( $x, $y );
				}
				
				$items[$j] = ceil($x);	$items[$j+1] = ceil($y);
			}

			$lines[$i] = $leading . implode( " ", $items );
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
			$angle = round( 1.15 * sin( deg2rad( -1 * $tags[1] ) ), 3 );	# 1.15 - is an experimental correction!!!
			echo "SIN(ItalicAngle) = $angle\n";
		}
	} else if ( 1 == $mode || 11 == $mode || 21 == $mode || 31 == $mode || 41 == $mode ) {
		if ( $tags[0] == "SplineSet" ) {
			$mode++;
		} else if ( $tags[0] == "Encoding" ) {
			$codes = explode( " ", trim( $tags[1] ) );
			if (
				( $codes[0] >= 0x2A && $codes[0] <= 0x2E ) ||
				( $codes[0] >= 0x3C && $codes[0] <= 0x3E ) ||
				( $codes[0] >= 0x5E && $codes[0] <= 0x60 ) ||
				( $codes[0] >= 0x7B && $codes[0] <= 0x7E ) ||
				( $codes[0] == 0xA6 ) ||
				( $codes[0] >= 0xAB && $codes[0] <= 0xAD ) ||
				( $codes[0] >= 0xAF && $codes[0] <= 0xB4 ) ||
				( $codes[0] >= 0xB7 && $codes[0] <= 0xB9 ) ||
				( $codes[0] == 0xBB ) ||
				( $codes[0] == 0xD7 ) ||
				( $codes[0] == 0xF7 ) ||
				( $codes[0] >= 0x2C6 && $codes[0] <= 0x38F ) ||
				( $codes[0] >= 0x2100 && $codes[0] <= 0x21FF )
			) {
				$mode = 11;	# center
			} else if ( $codes[0] >= 0x2591 && $codes[0] <= 0x259F ) {
				$mode = 41;	# prop-widen
			} else if ( $codes[0] >= 0x2200 && $codes[0] <= 0x2590 ) {
				$mode = 21;	# widen
			} else if ( $codes[0] >= 0x25A0 && $codes[0] <= 0x26FF ) {
				$mode = 31;	# x-y-scale
			}
		}
	} else if ( ( 2 == $mode || 12 == $mode || 22 == $mode || 32 == $mode || 42 == $mode ) && $tags[0] == "EndSplineSet" ) {
		$mode = 1;
	} else if ( 1 == $mode && $tags[0] == "EndChar" ) {
		$mode = 0;
	} else if ( 2 == $mode ) {
		# normal character, x-scale it
		$clause = implode( "\n", x_scale_path( $lines ) );
	} else if ( 12 == $mode ) {
		# center them only
		$clause = implode( "\n", center_path( $lines ) );
	} else if ( 22 == $mode ) {
		# graphic line character; widen it only 
		$clause = implode( "\n", widen_path( $lines ) );
	} else if ( 32 == $mode ) {
		# other graphic character; proportinally scale it
		$clause = implode( "\n", xy_scale_path( $lines ) );
	} else if ( 42 == $mode ) {
		# graphic line character; proportinally widen it only 
		$clause = implode( "\n", prop_widen_path( $lines ) );
	} else {
		#some other...
	}
	out( $clause );
}


#
# process input file
#
$infile_name_width = "C75";
$outfile_name_width = "C87";
$suffixes = array( "", "I", "B", "BI" );
foreach ( $suffixes as $filename_suffix ) {
	$infile_name = "AnkaCoder-".$infile_name_width."-Skel".$filename_suffix.".sfd";
	$sfd = @fopen( $infile_name, "r" );
	if ( false === $sfd ) die( "Can't open input file\n" );

	$outfile_name = "AnkaCoder-".$outfile_name_width."-Skel".$filename_suffix.".sfd";
	$outfile = @fopen( $outfile_name, "w" );
	if ( false === $outfile ) die( "Can't open output file\n" );

	echo "Converting $infile_name to $outfile_name\n";
	$angle = 0.0;
	$mode = 0;
	$bold = "B" == $filename_suffix || "BI" == $filename_suffix ? true : false;
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

	fclose( $outfile );
	fclose( $sfd );
}
?>
