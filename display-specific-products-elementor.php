<?php
/**
 * Plugin Name: Display Specific Products for Elementor
 * Description: Adds a widget to the Elementor Page Builder plugin, which displays a selected WooCommerce product's featured image and title.
 * Author: Udi Dollberg
 * Version: 1.0.2
 *
 * Text Domain: udi_dsc
 *
 * The Display Specific Products for Elementor plugin is free software: 
 * you can redistribute it and/or modifyit under the terms of the GNU 
 * General Public License as published bythe Free Software Foundation, 
 * either version 3 of the License, or any later version.
 *
 * The Display Specific Products for Elementor plugin is distributed in 
 * the hope that it will be useful,but WITHOUT ANY WARRANTY; without even 
 * the implied warranty ofMERCHANTABILITY or FITNESS FOR A PARTICULAR 
 * PURPOSE. See the GNU General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Create constants for file path references
define( 'UDI_DSPE__FILE__', __FILE__ );
define( 'UDI_DSPE_PLUGIN_BASE', plugin_basename( UDI_DSPE__FILE__ ) );
define( 'UDI_DSPE_PATH', plugin_dir_path( UDI_DSPE__FILE__ ) );
define( 'UDI_DSPE_URL', plugins_url( '/', UDI_DSPE__FILE__ ) );

$dspeProductsArray = array();

// Create an array of products with IDs as Keys and Titles as Values, then sort it alphabetically
function cspa($dspeProductsArray) {

	global $dspeProductsArray;

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1
        );
    $loop = new WP_Query( $args );
    if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) : $loop->the_post();
			$dspePostID = (string) get_the_ID();
			$dspePostTitle = get_the_title();
			$dspeProductsArray[$dspePostID] = $dspePostTitle;
        endwhile;
    } else {
        echo __( 'No products found' );
    }

	ksort($dspeProductsArray);

    wp_reset_postdata();
}

function udi_dspe_load_plugin() {
	// Check if Elementor is loaded before loading the plugin. If it isn't loaded, display admin notice.
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'udi_dspe_fail_load' );
		return;
	}
	// Check if the installed version of Elementor matches the minimum required version for the widget to work
	$elementor_version_required = '1.8.0';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'udi_dspe_fail_load_out_of_date' );
		return;
	}
	// Call the file with the actual widget
	DisplaySpecificProductsElementor::get_instance()->init();
	// Load Plugin CSS to display in Elementor
	add_action('wp_head', 'add_dspe_css');
}
add_action( 'plugins_loaded', 'udi_dspe_load_plugin' );

// This should run if Elementor fails to load when we try to load the plugin
function udi_dspe_fail_load() {
	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}
	$plugin = 'elementor/elementor.php';
	if ( _is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
		$message = '<p>' . __( 'Display Specific Products for Elementor is not working because you need to activate the Elementor plugin.', 'udi-dspe' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, __( 'Activate Elementor Now', 'udi-dspe' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
		$message = '<p>' . __( 'Display Specific Products for Elementor is not working because you need to install the Elementor plugin', 'udi-dspe' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Elementor Now', 'udi-dspe' ) ) . '</p>';
	}
	echo '<div class="error"><p>' . $message . '</p></div>';
}

// This should run if Elementor's version is too old for the plugin to run
function elementor_pro_fail_load_out_of_date() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}
	$file_path = 'elementor/elementor.php';
	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
	$message = '<p>' . __( 'Display Specific Products for Elementor is not working because you are using an old version of Elementor.', 'udi-dspe' ) . '</p>';
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, __( 'Update Elementor Now', 'udi-dspe' ) ) . '</p>';
	echo '<div class="error">' . $message . '</div>';
}

if ( ! function_exists( '_is_elementor_installed' ) ) {
	function _is_elementor_installed() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();
		return isset( $installed_plugins[ $file_path ] );
	}
}

class DisplaySpecificProductsElementor {

	private static $instance = null;
 
	public static function get_instance() {
	   if ( ! self::$instance )
		  self::$instance = new self;
	   return self::$instance;
	}
 
	public function init(){
	   add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ) );
	   add_action( 'elementor/widgets/widgets_registered', 'cspa' );
	}
 
	public function widgets_registered() {
 
	   // We check if the Elementor plugin has been installed / activated.
	   if(defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')){
 
		  // We look for any theme overrides for this custom Elementor element.
		  // If no theme overrides are found we use the default one in this plugin.
 
		  $widget_file = 'plugins/elementor/dspe.php';
		  $template_file = locate_template($widget_file);
		  if ( !$template_file || !is_readable( $template_file ) ) {
			 $template_file = plugin_dir_path(__FILE__).'dspe.php';
		  }
		  if ( $template_file && is_readable( $template_file ) ) {
			 require_once $template_file;
		  }
	   }
	}
 }

  // When invoked, adds plugin CSS to head
  function add_dspe_css() {

	?>

	<style>
		.elementor-widget-udi_dsc .elementor-widget-container {
			display: flex;
			flex-flow: row wrap;
		}
		.udi-dsc-container {
			display: inline-block;
			/* flex: 1; */
			vertical-align: top;
		}
		.udi-dsc-product-image {
			text-align: center;
		}
		.udi-dsc-product-title {
			text-align: center;
		}

		.udi-dsc-product-title a {
			font-weight: bold;
		}
	</style>

	 <?php
}