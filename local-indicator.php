<?php
/*
Plugin Name: Local Indicator
Plugin URI: http://tomjn.com
Description: Indicates the current server used via a colour coded IP in the top admin bar, useful for telling live and Local dev environments apart
Author: Tom J Nowell, Interconnect/IT
Version: 1.6
Author URI: http://tomjn.com/
*/


class TomjnLocalIndicator {

	public static $instance = null;

	public static function get_instance() {
		if ( !isset( $instance ) ) {
			$instance = new self;
		}
		return $instance;
	}

	public function __construct() {
		add_action( 'wp_before_admin_bar_render', array( $this, 'render' ) );
	}
	/**
	 * add the link to the admin bar
	 */
	public function render() {
		if ( !is_admin_bar_showing() )
			return;

		if ( !defined( 'LOCALINDICATOR_ALWAYS_SHOWING' ) || ( defined( 'LOCALINDICATOR_ALWAYS_SHOWING' ) && !LOCALINDICATOR_ALWAYS_SHOWING ) ) {
			$capability = 'manage_options';

			if ( is_multisite() ) {
				$capability = 'manage_network_options';
			}
			$capability = apply_filters( 'tomjn_local_indicator_capability', $capability );
			if ( !current_user_can( $capability ) ) {
				return;
			}
		}

		global $wp_admin_bar;
		$colour = self::label_to_colour( $_SERVER['SERVER_ADDR'] );
		$colour = apply_filters( 'tomjn_indicator_colour', $colour );
		$indicator_text = '';
		if ( defined( 'LOCALINDICATOR_TEXT' ) ) {
			$indicator_text = LOCALINDICATOR_TEXT;
		} else {
			$indicator_text = php_uname( 'n' );
		}
		$indicator_text = apply_filters( 'tomjn_indicator_text', $indicator_text );
		$title = '<style>#wpadminbar #wp-admin-bar-server_name,#wpadminbar #wp-admin-bar-server_name:hover, #wpadminbar .local-indicator, #wpadminbar #wp-admin-bar-server_name:hover ,#wpadminbar #wp-admin-bar-server_name:hover, #wpadminbar #wp-admin-bar-server_name:hover .local-indicator, #wpadminbar #wp-admin-bar-server_name .ab-submenu, #wpadminbar #wp-admin-bar-server_name .ab-submenu a, #wpadminbar .ab-top-menu>#wp-admin-bar-server_name:hover>.ab-item,#wpadminbar .ab-top-menu>#wp-admin-bar-server_name>.ab-item { font-weight:bolder; color:#fff; text-shadow:none; background:'.$colour.';} @media only screen and (max-width : 640px) { #wpadminbar .ab-top-menu>#wp-admin-bar-server_name>.ab-item { margin-top:-2px; width:20px; } #wpadminbar .local-indicator{ background: '.$colour.'; display:none;} }</style>';
		$title .= '<span class="local-indicator">'.$indicator_text.'</span>';
		$wp_admin_bar->add_node(
			array(
				'parent' => false,
				'id' => 'server_name',
				'title' => $title, // link title
				'href' => '#',
				'meta' => FALSE,
				'menu_id' => 'local_indicator'
			)
		);
		$uname = php_uname( 'n' );
		$phpver = 'PHP v'.phpversion();
		$wp_admin_bar->add_node( array(
			'title' => $uname,
			'href' => '#',
			'parent' => 'server_name',
			'meta' => FALSE,
			'id' => 'server_uname'
		));
		$wp_admin_bar->add_node( array(
			'title' => $phpver,
			'href' => '#',
			'parent' => 'server_name',
			'meta' => FALSE,
			'id' => 'server_php'
		));
		$wp_admin_bar->add_node( array(
			'title' => $_SERVER['SERVER_ADDR'],
			'href' => '#',
			'parent' => 'server_name',
			'meta' => FALSE,
			'id' => 'server_addr'
		));
	}

	public static function label_to_colour( $label ) {
		$colour = substr( sha1( $label ), 0, 6 );
		$colour = self::get_colour( $colour );
		return $colour;
	}

	public static function get_colour( $hex ) {
		$colour = HexToRGB( $hex );
		$colour = rgb2hsl( $colour['r'], $colour['g'], $colour['b'] );
		$colour[1] = 1.00;
		$colour[2] = 0.67;
		$colour = hsl2rgb( $colour[0], $colour[1], $colour[2] );
		$colour = RGBToHex( $colour[0], $colour[1], $colour[2] );
		return $colour;
	}
}
add_action( 'init', array( 'TomjnLocalIndicator', 'get_instance' ) );

if ( !function_exists( 'HexToRGB' ) ) {
	function HexToRGB( $hex ) {
		$hex = str_replace( '#', '', $hex );
		$color = array();

		if ( strlen( $hex ) == 3 ) {
			$color['r'] = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$color['g'] = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$color['b'] = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		}
		else if ( strlen( $hex ) == 6 ) {
				$color['r'] = hexdec( substr( $hex, 0, 2 ) );
				$color['g'] = hexdec( substr( $hex, 2, 2 ) );
				$color['b'] = hexdec( substr( $hex, 4, 2 ) );
			}

		return $color;
	}
}

if ( !function_exists( 'RGBToHex' ) ) {
	function RGBToHex( $r, $g, $b ) {
		//String padding bug found and the solution put forth by Pete Williams (http://snipplr.com/users/PeteW)
		$hex = '#';
		$hex .= str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
		$hex .= str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
		$hex .= str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );

		return $hex;
	}
}

if ( !function_exists( 'rgb2hsl' ) ) {
	function rgb2hsl( $r, $g, $b ) {
		$var_R = ( $r / 255 );
		$var_G = ( $g / 255 );
		$var_B = ( $b / 255 );

		$var_Min = min( $var_R, $var_G, $var_B );
		$var_Max = max( $var_R, $var_G, $var_B );
		$del_Max = $var_Max - $var_Min;

		$v = $var_Max;

		if ( $del_Max == 0 ) {
			$h = 0;
			$s = 0;
		} else {
			$s = $del_Max / $var_Max;

			$del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
			$del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
			$del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

			if      ( $var_R == $var_Max ) $h = $del_B - $del_G;
			else if ( $var_G == $var_Max ) $h = ( 1 / 3 ) + $del_R - $del_B;
				else if ( $var_B == $var_Max ) $h = ( 2 / 3 ) + $del_G - $del_R;

					if ( $h < 0 ) $h++;
					if ( $h > 1 ) $h--;
		}

		return array( $h, $s, $v );
	}
}

if ( !function_exists( 'hsl2rgb' ) ) {
	function hsl2rgb( $h, $s, $v ) {
		if ( $s == 0 ) {
			$r = $g = $B = $v * 255;
		} else {
			$var_H = $h * 6;
			$var_i = floor( $var_H );
			$var_1 = $v * ( 1 - $s );
			$var_2 = $v * ( 1 - $s * ( $var_H - $var_i ) );
			$var_3 = $v * ( 1 - $s * ( 1 - ( $var_H - $var_i ) ) );

			if ( $var_i == 0 ) {
				$var_R = $v;
				$var_G = $var_3;
				$var_B = $var_1;
			} else if ( $var_i == 1 ) {
				$var_R = $var_2;
				$var_G = $v;
				$var_B = $var_1;
			} else if ( $var_i == 2 ) {
				$var_R = $var_1;
				$var_G = $v;
				$var_B = $var_3;
			} else if ( $var_i == 3 ) {
				$var_R = $var_1;
				$var_G = $var_2;
				$var_B = $v;
			} else if ( $var_i == 4 ) {
				$var_R = $var_3;
				$var_G = $var_1;
				$var_B = $v;
			} else {
				$var_R = $v;
				$var_G = $var_1;
				$var_B = $var_2;
			}

			$r = $var_R * 255;
			$g = $var_G * 255;
			$B = $var_B * 255;
		}
		return array( $r, $g, $B );
	}
}


