<?php
/*
 * Plugin Name: Impress for WordPress
 * Version: 0.1
 * Plugin URI: http://premium.wpmudev.org
 * Description: Easily create presentations within your WordPress content using simple shortcodes [impresswp], relying on the powerful impress.js library.
 * Author: David (incsub)
 * Author URI: http://premium.wpmudev.org/
 * Requires at least: 3.9
 * Tested up to: 4.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ImpressWP' ) ) {

	class ImpressWP {
		static $counter=0;

		var $version = '0.1';
		var $name = 'Impress for WordPress';
		var $dir_name = 'impress-for-wordpress';
		var $location = '';
		var $plugin_dir = '';
		var $plugin_url = '';

		function __construct() {
			$this->counter = 0;
			//Register Globals
			$GLOBALS['plugin_dir'] = $this->plugin_dir;
			add_shortcode( 'impresswp', array( $this, 'impress_shortcode' ) );
			add_shortcode( 'imstep', array( $this, 'imstep_shortcode' ) );
		}

		public function enqueue_scripts() {
			wp_enqueue_script( 'impress-init', plugins_url( 'js/impress-init.js', __FILE__ ) );
		}

		static function impress_shortcode( $atts, $content = null ) {
			// get shortcode attributes
			$a = shortcode_atts( array(
			    'id' => 'impresswp',
				'width' => '600px',
				'height' => '400px',
				'data-min-scale' => '1',
				'data-max-scale' => '1'
			), $atts );
			return self::impress_html( $a, $content);
		}

		static function impress_html( $a, $content) {
			// enqueue init script
			self::enqueue_scripts();
            $result .= '<iframe id="impress-iframe" style="width:' . $a['width'] . ';height:' . $a['height'] . ';" seamless></iframe>';
            $result .= '<div id="impress-replace">';
            $result .= '<div id="impress" data-min-scale="' . $a['data-min-scale'] . '" data-max-scale="' . $a['data-max-scale'] . '">';
            $result .= apply_filters( 'the_content', $content );
            $result .= '</div></div>';
			return $result;
		}
		
		static function imstep_shortcode( $atts, $content = null ) {

			// get shortcode attributes
			$a = shortcode_atts( array(
			    'id' => 'not-provided',
				'data-x' => '0',
				'data-y' => '0',
				'data-z' => '0',
				'data-rotate' => '0',
				'data-scale' => '0'
			), $atts );

		    self::$counter += 1;
		    // render step div based on user provided args
		    $result = '<div class="step"';
		    // if user doesn't provide id, use counter
		    if ($a['id'] == 'not-provided')
		        $result .= ' id=step-"' . self::$counter . '"';
		    else
		        $result .= ' id="' . $a['id'] . '"';
		    $result .= ' data-x="' . $a['data-x'] . '"';
		    $result .= ' data-y="' . $a['data-y'] . '"';
		    $result .= ' data-z="' . $a['data-z'] . '"';
		    $result .= ' data-rotate="' . $a['data-rotate'] . '"';
		    $result .= ' data-scale="' . $a['data-scale'] . '"';
		    $result .= '>';
		    $result .= apply_filters( 'the_content', $content );
		    $result .= '</div>';
			return $result;
		}
	}
}

global $impresswp;
$impresswp = new ImpressWP();
?>