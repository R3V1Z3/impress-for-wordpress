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
	    
        // keep track of how many times shortcode functions are called
        // used for determining if main div is rendered and also for incremented id numbering
        protected $_exec_counter = 0;

		var $version = '0.1';
		var $name = 'Impress for WordPress';
		var $dir_name = 'impress-for-wordpress';
		var $location = '';
		var $plugin_dir = '';
		var $plugin_url = '';

		function __construct() {
			//Register Globals
			$GLOBALS['plugin_dir'] = $this->plugin_dir;	
			add_shortcode( 'impresswp', array( __CLASS__, 'impress_shortcode' ) );
			add_filter( 'body_class', 'impresswp_body_class' );
		}
		
        function impresswp_body_class( $classes ) {
        	$classes[] = 'impress-not-supported';
        	return $classes;
        }

		public function enqueue_scripts(){
			wp_enqueue_script( 'impressjs', '//netdna.impressjscdn.com/impressjs/0.5.3/js/impress.js' );
			wp_enqueue_script( 'impress-init', plugins_url( 'js/impress-init.js', __FILE__ ) );
		}

		static function impress_shortcode( $atts, $content = null ) {
		    
			// get shortcode attributes
			$a = shortcode_atts( array(
			    'id' => 'not-provided',
				'data-x' => '0',
				'data-y' => '0',
				'data-z' => '0',
				'data-rotate' => '0',
				'data-scale' => '0'
			), $atts );
			
			// render the <div id="impress"> div if not already rendered
            if($this->_exec_counter === 0) {
                return render_impress_div( $content );
            }

            // return <div id=$counter class="step" data-x=$data-x ... >
			return $this->impress_step_html( $a, $content );
		}

		public function render_impress_div( $content ) {
            $this->enqueue_scripts();
			return $this->impress_div_html( $content );
		}
		
        public function impress_div_html( $content )
        {
            $this->_exec_counter += 1;
    	    $result = '<div class="fallback-message"><p>Your browser <b>doesnt support the features required</b> by impress.js, so you are presented with a simplified version of this presentation.</p><p>For the best experience please use the latest <b>Chrome</b>, <b>Safari</b> or <b>Firefox</b> browser.</p></div>';
    	    $result .= '<div id="impress">';
    	    $result .= apply_filters( 'the_content', $content );
    	    //$result .= do_shortcode( $content );
    	    $result .= '</div>';
            return $result;
        }

		public function impress_step_html( $a, $content ) {
		    // increment step counter
		    $this->_exec_counter += 1;
		    // render step div based on user provided args
		    $result = '<div class="step"';
		    // if user doesn't provide id, use counter
		    if ($a['id'] == 'not-provided')
		        $result .= ' id=step-"' . $this->_exec_counter . '"';
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