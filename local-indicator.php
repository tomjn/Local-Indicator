<?php
/*
Plugin Name: Local Indicator
Plugin URI: http://tomjn.com
Description: Indicates the current server used via a colour coded IP in the top admin bar, useful for telling Live and Local dev environments apart
Author: Tom J Nowell, Interconnect/IT
Version: 1.1
Author URI: http://tomjn.com/
*/


// add links/menus to the admin bar
function tomjn_indicator_admin_bar_render() {
	if ( !is_super_admin() || !is_admin_bar_showing() )
        return;
	global $wp_admin_bar;
	$wp_admin_bar->add_menu( array(
		'parent' => false, // use 'false' for a root menu, or pass the ID of the parent menu
		'id' => 'server_ip', // link ID, defaults to a sanitized title value
		'title' => '<span style="font-weight:bolder; color:'.icit_label_to_chart_colour($_SERVER['SERVER_ADD']).';">'.$_SERVER['SERVER_ADDR'].'</span>', // link title
		'href' => '#', // name of file
		'meta' => FALSE //array('html' => '<span style="color:'.icit_label_to_chart_colour($_SERVER['SERVER_ADD']).';">'.$_SERVER['SERVER_ADDR'].'</span>')
		// array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
	));
}
add_action( 'wp_before_admin_bar_render', 'tomjn_indicator_admin_bar_render' );

if(!function_exists('HexToRGB')){
	function HexToRGB($hex) {
		$hex = ereg_replace("#", "", $hex);
		$color = array();

		if(strlen($hex) == 3) {
			$color['r'] = hexdec(substr($hex, 0, 1) . $r);
			$color['g'] = hexdec(substr($hex, 1, 1) . $g);
			$color['b'] = hexdec(substr($hex, 2, 1) . $b);
		}
		else if(strlen($hex) == 6) {
			$color['r'] = hexdec(substr($hex, 0, 2));
			$color['g'] = hexdec(substr($hex, 2, 2));
			$color['b'] = hexdec(substr($hex, 4, 2));
		}

		return $color;
	}
}

if(!function_exists('RGBToHex')){
	function RGBToHex($r, $g, $b) {
		//String padding bug found and the solution put forth by Pete Williams (http://snipplr.com/users/PeteW)
		$hex = "#";
		$hex.= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
		$hex.= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
		$hex.= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);

		return $hex;
	}
}

if(!function_exists('rgb2hsl')){
	function rgb2hsl($r, $g, $b) {
		$var_R = ($r / 255);
		$var_G = ($g / 255);
		$var_B = ($b / 255);

		$var_Min = min($var_R, $var_G, $var_B);
		$var_Max = max($var_R, $var_G, $var_B);
		$del_Max = $var_Max - $var_Min;

		$v = $var_Max;

		if ($del_Max == 0) {
			$h = 0;
			$s = 0;
		} else {
			$s = $del_Max / $var_Max;

			$del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
			$del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
			$del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

			if      ($var_R == $var_Max) $h = $del_B - $del_G;
			else if ($var_G == $var_Max) $h = ( 1 / 3 ) + $del_R - $del_B;
			else if ($var_B == $var_Max) $h = ( 2 / 3 ) + $del_G - $del_R;

			if ($h < 0) $h++;
			if ($h > 1) $h--;
		}

		return array($h, $s, $v);
	}
}

if(!function_exists('hsl2rgb')){
	function hsl2rgb($h, $s, $v) {
		if($s == 0) {
			$r = $g = $B = $v * 255;
		} else {
			$var_H = $h * 6;
			$var_i = floor( $var_H );
			$var_1 = $v * ( 1 - $s );
			$var_2 = $v * ( 1 - $s * ( $var_H - $var_i ) );
			$var_3 = $v * ( 1 - $s * (1 - ( $var_H - $var_i ) ) );

			if       ($var_i == 0) { $var_R = $v     ; $var_G = $var_3  ; $var_B = $var_1 ; }
			else if  ($var_i == 1) { $var_R = $var_2 ; $var_G = $v      ; $var_B = $var_1 ; }
			else if  ($var_i == 2) { $var_R = $var_1 ; $var_G = $v      ; $var_B = $var_3 ; }
			else if  ($var_i == 3) { $var_R = $var_1 ; $var_G = $var_2  ; $var_B = $v     ; }
			else if  ($var_i == 4) { $var_R = $var_3 ; $var_G = $var_1  ; $var_B = $v     ; }
			else                   { $var_R = $v     ; $var_G = $var_1  ; $var_B = $var_2 ; }

			$r = $var_R * 255;
			$g = $var_G * 255;
			$B = $var_B * 255;
		}
		return array($r, $g, $B);
	}
}

if(!function_exists('icit_label_to_chart_colour')){
	function icit_label_to_chart_colour($label){
		$colour = substr(sha1($label),0,6);
		$colour = icit_get_chart_colour($colour);
		return $colour;
	}
}

if(!function_exists('icit_get_chart_colour')){
		function icit_get_chart_colour($hex){
		$colour = HexToRGB($hex);
		$colour = rgb2hsl($colour['r'],$colour['g'],$colour['b']);
		$colour[1] = 0.35;
		$colour[2] = 0.87;
		$colour = hsl2rgb($colour[0],$colour[1],$colour[2]);
		$colour = RGBToHex($colour[0],$colour[1],$colour[2]);
		return $colour;
	}
}
