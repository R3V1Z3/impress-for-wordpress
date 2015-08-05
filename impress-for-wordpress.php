<?php
/*
 * Plugin Name: Impress for WordPress
 * Version: 0.2
 * Plugin URI: http://premium.wpmudev.org
 * Description: Easily create amazing presentations within your WordPress content using simple [slide] shortcodes that rely on the powerful impress.js library.
 * Author: David (incsub)
 * Author URI: http://premium.wpmudev.org/
 * Requires at least: 3.9
 * Tested up to: 4.0
 *
 */

// TODO: Create custom post type for slide creation that provides shortcode for that specific presentation
// These presentations will use different id system than run-time generated ones
// runtime ex: impresswp-iframe-1, impresswp-iframe-2
// This ex: impresswp-iframe-s1, impresswp-iframe-s425
// impresswp-iframe-s + post id

// TODO: Add option for transition duration per slide

/* TODO:

	+ Add CSS: needed for styling of options below iframe.

	Admin menu page:
	+ Add Custom CSS box
	+ Add settings to change default options

	+ Add feature to import regular impress.js presentations (simple converter from html divs to shortcodes)

	USE WP CODE PRETTIFY for options!
	It has all the options we'll need.

	Visual editor:
	+ Add icon and options to visual editor

*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ImpressWP' ) ) {

	class ImpressWP {
		private $details; // for tracking info about current presentation and slides
		
		var $version = '0.2';
		var $name = 'Impress for WordPress';
		var $dir_name = 'impress-for-wordpress';
		var $plugin_dir = '';
		var $plugin_url = '';
		
		public function __construct() {

			//Register Globals
			$GLOBALS['plugin_dir'] = $this->plugin_dir;
			add_shortcode( 'impresswp', array( $this, 'impress_shortcode' ) );
			add_shortcode( 'slide', array( $this, 'slide_shortcode' ) );

			// Keep count of impresswp occurrences to allow multiple divs on page
			$this->details['impresswp_counter'] = 0;

			// Defaults for new impress divs
			$this->details['slide_counter'] = 0;
			$this->details['class'] = 'impresswp';
			$this->details['width'] = '600px';
			$this->details['height'] = '400px';
			$this->details['min_scale'] = 0.5;
			$this->details['max_scale'] = 1.5;
			$this->details['options'] = 'fullscreen printable';

			// Default details for first slide
			$this->details['x'] = 0;
			$this->details['y'] = 0;
			$this->details['z'] = 0;
			$this->details['rotate'] = 0;
			$this->details['rotate_x'] = 0;
			$this->details['rotate_y'] = 0;
		}

		/* Getters */
		public function get_details( $key ) {
			return $this->details[ $key ];
		}

		/* Setters */
		public function set_details( $key, $value ) {
			$this->details[ $key ] = $value;
		}
		
		public function enqueue_scripts() {
			wp_enqueue_script( 'impress-init', plugins_url( 'js/impress-init.js', __FILE__ ) );
		}
		
		public function impress_shortcode( $atts, $content = null ) {

			// Get shortcode attributes
			$a = shortcode_atts( array(
			    'class' => $this->details['class'],
				'width' => $this->details['width'],
				'height' => $this->details['height'],
				'min_scale' => $this->details['min_scale'],
				'max_scale' => $this->details['max_scale'],
				'options' => $this->details['options'],
			), $atts );

			// Increment impresswp_counter
			$impresswp_counter = $this->get_details( 'impresswp_counter' );
			$impresswp_counter += 1;
			$this->set_details( 'impresswp_counter', $impresswp_counter);

			// Increment slide_counter to indicate impresswp div is rendered
			$this->set_details( 'slide_counter', 1);

			// Set other attributes
			$this->set_details( 'class', esc_attr( $a['class'] ) );
            $this->set_details( 'width', esc_attr( $a['width'] ) );
            $this->set_details( 'height', esc_attr( $a['height'] ) );
            $this->set_details( 'min_scale', esc_attr( $a['min_scale'] ) );
            $this->set_details( 'max_scale', esc_attr( $a['max_scale'] ) );
            $this->set_details( 'options', esc_attr( $a['options'] ) );
			return $this->impress_html( $this->details, $content );

		} // impress_shortcode()
		
		public function impress_html( $a, $content ) {
				
			// Enqueue init script
			$this->enqueue_scripts();

			// Give iframe id based on counter
            $result = '<iframe id="impresswp-iframe-' . $a['impresswp_counter'] . '"';

            // Give the iframe a class too
            $result .= ' class="impresswp-iframe"';

            // TODO: issue - how to create the div without requiring inline css for width and height
            // just add as dynamic css alongside css style option
            $result .= ' style="width:' . $a['width'];
            $result .= ';height:' . $a['height'] . ';"';

            // Add fullscreen flag to iframe
            $options = strtolower ( $this->get_details('options') );
            if ( false !== strpos( $options, 'fullscreen' ) ) {
	            $result .= ' allowfullscreen="true"';
        	}
            $result .= '></iframe>';
            
            // Render impresswp div class based on counter
            $result .= '<div class="impresswp-replace"';
            $result .= ' id="impresswp-replace-' . $a['impresswp_counter'] . '"';
            $result .= '>';

            // Add all queued styles and scripts within iframe content
            global $wp_styles, $wp_scripts;
			foreach( $wp_styles->queue as $handle ) {
			        $obj = $wp_styles->registered[ $handle ];
			        $filename = $obj->src;
			        $result .= '<link rel="stylesheet" property="stylesheet" href="' . $filename . '" />';
         	}
			foreach( $wp_scripts->queue as $handle ) {
			        $obj = $wp_scripts->registered[ $handle ];
			        $filename = $obj->src;
			        $result .= '<script src="' . $filename . '"></script>';
         	}

         	// Impress div
            $result .= '<div id="impress" class="' . $a['class'];
            $result .= '" data-min-scale="' . $a['min_scale'] . '" data-max-scale="' . $a['max_scale'] . '"';
            $result .= ' style="width:' . $a['width'];
            $result .= ';height:' . $a['height'] . ';" seamless';
            $result .= '>';

            // Trim content so leading/trailing newlines are removed
            $result .= trim( apply_filters( 'the_content', $content ) );
            $result .= '</div>'; // impresswp-replace
            $result .= '</div>'; // impress

            // Options section
            $result .= '<div id="impresswp-options">';

            // Printable option
            if ( false !== strpos( $options, 'printable' ) ) {
            	$quote = "'";
            	$result .= '<a title="printable" onclick="impress_printable(' . $quote;
            	$result .= 'impresswp-replace-' . $a['impresswp_counter'] . $quote. ');">';
            	$result .= 'Printable</a>';
            }

            // Full-screen option
            if ( false !== strpos( $options, 'fullscreen' ) ) {
            	$quote = "'";
            	$result .= '<a title="fullscreen" onclick="impress_fullscreen(' . $quote;
            	$result .= 'impresswp-iframe-' . $a['impresswp_counter'] . $quote. ');">';
            	$result .= 'Fullscreen</a>';
            }

            $result .= '</div>'; // impresswp-options

			return $result;
		} // impress_html()
		
		public function slide_shortcode( $atts, $content = null ) {
			
			// Get shortcode attributes
			$a = shortcode_atts( array(
			    'id' => 'nil',
			    'class' => 'step',
				'x' => 'nil',
				'y' => 'nil',
				'z' => 'nil',
				'rotate' => 'nil',
				'rotate_x' => 'nil',
				'rotate_y' => 'nil',
				'scale' => 'nil',
				'effect' => 'nil', // ex: slide-up rotate_45 zoom-in-1000
			), $atts );
			return $this->slide_html( $a, $content );
		} // slide_shortcode()

		public function slide_html( $a, $content ) {
			
			$id = esc_attr( $a['id'] );
			$class = esc_attr( $a['class'] );
			$x = esc_attr( $a['x'] );
			$y = esc_attr( $a['y'] );
			$z = esc_attr( $a['z'] );
			$rotate = esc_attr( $a['rotate'] );
			$rotate_x = esc_attr( $a['rotate_x'] );
			$rotate_y = esc_attr( $a['rotate_y'] );
			$scale = esc_attr( $a['scale'] );
			$effect = esc_attr( $a['effect'] );

			// Default to 'slide-right' effect when no params provided
			if ($x === 'nil' && $y === 'nil' && $z === 'nil' && $effect === 'nil') {
				$effect = 'slide-right';
			}

			// Get previous slide's values
			$prev_x = $this->get_details('x');
			$prev_y = $this->get_details('y');
			$prev_z = $this->get_details('z');
			$prev_rotate = $this->get_details('rotate');
			$prev_rotate_x = $this->get_details('rotate_x');
			$prev_rotate_y = $this->get_details('rotate_y');
			
			$result = '';
			$slide_counter = $this->get_details( 'slide_counter' );

			// Render impress div if not already done
			if ( 0 === $slide_counter ) {
				// TODO must somehow remove the existing $content since we've gotten them with get_the_content()
				$result = $this->impress_shortcode( $this->details, get_the_content() );
				$slide_counter = 1;
			}

			// Begin slide div
			$result .= '<div class="' . $class . '"';

			$width = $this->get_details('width');
			$height = $this->get_details('height');

			// Use slide_counter if user doesn't specify id
			if ( $a['id'] === 'nil' )
			    $result .= ' id="slide-' . $slide_counter . '"';
			else
			    $result .= ' id="' . $id . '"';

			// Setup defaults
			if ( 'nil' === $x ) $x = $prev_x;
			if ( 'nil' === $y ) $y = $prev_y;
			if ( 'nil' === $z ) $z = $prev_z;
			if ( 'nil' === $scale ) $scale = 1;
			if ( 'nil' === $rotate ) $rotate = 0;
			if ( 'nil' === $rotate_x ) {
				$rotate_x = 0;
			}
			if ( 'nil' === $rotate_y ) {
				$rotate_y = 0;
			}

			// Handle effects
			if ( 'nil' !== $effect ) {

				// Use defaults on first slide
				if ( 1 === $slide_counter ) {
					$x = 0;
					$y = 0;
					$z = 0;
					$rotate = 0;
					$scale = 1;
					$effect = '';					
				} else {

					// Defaults for use with effects
					$x = $prev_x;
					$y = $prev_y;
					$z = $prev_z;
					$rotate = $prev_rotate;
					$rotate_x = $prev_rotate_x;
					$rotate_y = $prev_rotate_y;

					// Slide effect
					if ( false !== strpos( $effect, 'slide' ) ) {
						if ( false !== strpos( $effect, 'slide-up' ) ) {
							$y = $height * 2 - $prev_y;
						} else if ( false !== strpos( $effect, 'slide-right' ) ) {
							$x = $width * 2 + $prev_x;
						} else if ( false !== strpos( $effect, 'slide-down' ) ) {
							$y = $height * 2 + $prev_y;
						} else if ( false !== strpos( $effect, 'slide-left' ) ) {
							$x = $width * 2 - $prev_x;
						}
					}

					// Zoom effect
					if ( false !== strpos( $effect, 'zoom' ) ) {

						if ( false !== strpos( $effect, 'zoom-in' ) ) {
							$z = $prev_z - 2000;
						} else if ( false !== strpos( $effect, 'zoom-out' ) ) {
							$z = $prev_z + 2000;
						}
					}

					// Rotate effect
					if ( false !== strpos( $effect, 'rotate' ) ) {

						// Add or subtract from previous rotation
						$rotate = self::get_numeric_value( $effect, 'rotate' );
						$rotate = $prev_rotate + $rotate;
					}
				}
			}

			// Render results
			$result .= ' data-x="' . $x . '"';
			$result .= ' data-y="' . $y . '"';
			$result .= ' data-z="' . $z . '"';
			if ( 'nil' !== $rotate ) {
				$result .= ' data-rotate="' . $rotate . '"';
			}
			$result .= ' data-rotate-x="' . $rotate_x . '"';
			$result .= ' data-rotate-y="' . $rotate_y . '"';
			$result .= ' data-scale="' . $scale . '"';

			$result .= '>';

			// Trim contents so leading/trailing newlines are removed
			$result .= trim( apply_filters( 'the_content', $content ) );
			
			// End slide div
			$result .= '</div>';

			// Store attributes in array for potential use in next slide
			$this->set_details( 'x', $x );
			$this->set_details( 'y', $y );
			$this->set_details( 'z', $z );
			$this->set_details( 'rotate', $rotate );
			$this->set_details( 'rotate_x', $rotate_x );
			$this->set_details( 'rotate_y', $rotate_y );

			// Increment slide_counter
			$slide_counter += 1;
			$this->set_details( 'slide_counter', $slide_counter );

			return $result;
		} // slide_html()

		// Return value of number after text string, if any exists
		public static function get_numeric_value( $search_str, $search_item ) {
			$plus = strpos( $search_str, $search_item . '+' );
			$minus = strpos( $search_str, $search_item . '-' );
			if ( false !== $plus || false !== $minus ) {
				$result = '';
				// Get index where number begins
				$pos = ( false !== $plus ) ? $plus : $minus;
				$pos += strlen( $search_item ) + 1;
				//wp_die($search_str . intval($pos));
				for ( $i = $pos; $i < strlen( $search_str ); $i++ ) {
					if ( is_numeric( $search_str[ $i ] ) ) {
						$result .= $search_str[ $i ];
					} else {
						break;
					}
				}
				return ( false !== $plus ) ? intval( $result ) : -intval( $result );
			} else {
				// No numeric value specified so return 0
				return 0;
			}
		} // get_numeric_value()

	} // ImpressWP()
}

global $impresswp;
$impresswp = new ImpressWP();
?>