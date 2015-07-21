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
			    'class' => 'myclass',
				'width' => '600px',
				'height' => '400px',
				'min_scale' => '1',
				'max_scale' => '1'
			), $atts );
			return self::impress_html( $a, $content);
		}

		static function impress_html( $a, $content) {
			// enqueue init script
			self::enqueue_scripts();
            $result .= '<iframe id="impress-iframe" style="width:' . $a['width'] . ';height:' . $a['height'] . ';" seamless></iframe>';
            $result .= '<div id="impress-replace">';
            // add all queued styles to iframe
            global $wp_styles;
			foreach( $wp_styles->queue as $handle ) {
			        $obj = $wp_styles->registered [$handle];
			        $filename = $obj->src;
			        $result .= '<link rel="stylesheet" property="stylesheet" href="' . $filename . '">';
         	}
            $result .= '<div id="impress" class="' . $a['class'] . '" data-min-scale="' . $a['min_scale'] . '" data-max-scale="' . $a['max_scale'] . '">';
            $result .= apply_filters( 'the_content', $content );
            $result .= '</div></div>';
			return $result;
		}
		
		static function imstep_shortcode( $atts, $content = null ) {

			// get shortcode attributes
			$a = shortcode_atts( array(
			    'id' => '',
			    'class' => 'step slide',
				'x' => '0',
				'y' => '0',
				'z' => '0',
				'rotate' => '0',
				'scale' => '1'
			), $atts );

		    self::$counter += 1;
		    // render step div based on user provided args
		    $result = '<div class="' . $a['class'] . '"';
		    // if user doesn't provide id, use counter
		    if ($a['id'] == '')
		        $result .= ' id="step-' . self::$counter . '"';
		    else
		        $result .= ' id="' . $a['id'] . '"';
		    $result .= ' data-x="' . $a['x'] . '"';
		    $result .= ' data-y="' . $a['y'] . '"';
		    $result .= ' data-z="' . $a['z'] . '"';
		    $result .= ' data-rotate="' . $a['rotate'] . '"';
		    $result .= ' data-scale="' . $a['scale'] . '"';
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