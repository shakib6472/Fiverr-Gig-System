<?php
	/*
	* Plugin Name: Fiverr Market
	* Plugin URI: https://github.com/shakib6472/
	* Description: This plugin is to create a Fiverr marketplace type of gig creation. Each teacher will be a gig and student can book His schedule. Teacher will be paid Hourly.
	* Version: 1.1.1
	* Requires at least: 5.2
	* Requires PHP: 7.2
	* Author: Shakib Shown
	* Author URI: https://github.com/shakib6472/
	* License: GPL v2 or later
	* License URI: https://www.gnu.org/licenses/gpl-2.0.html
	* Text Domain: fiverr-market
	* Domain Path: /languages
	*/

	if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
	}
	require_once 'vendor/autoload.php';
	require_once(__DIR__.'/d-tag.php');
	require_once(__DIR__.'/ajax.php');
	require_once(__DIR__.'/functions.php');
	function fiverr_market_enque_scripts()
	{
	//css
	wp_enqueue_style('fiverr-market-style', plugin_dir_url(__FILE__) . '/style.css' );
	wp_enqueue_style('fiverr-market-bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' );
	wp_enqueue_style('fiverr-market-bootstrap-datepicker-style', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css' );

	//js
	wp_enqueue_script('fiverr-market-jquery-script',  'https://code.jquery.com/jquery-3.7.1.min.js' , array(), '1.0.0', true);
	wp_enqueue_script('fiverr-market-script', plugin_dir_url(__FILE__) . 'scripts.js' , array('fiverr-market-jquery-script'), '1.0.0', true);
	wp_enqueue_script('fiverr-market-font-owesomw-script', 'https://kit.fontawesome.com/46882cce5e.js' , array(), null, true);
	wp_enqueue_script('fiverr-market-date-picker-script', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js' , array(), null, true);
	wp_enqueue_script('fiverr-market-toast-script',plugin_dir_url(__FILE__) . 'jquery.toast.js', array('jquery'), null, true);

	// Localize the script with new data
	wp_localize_script('fiverr-market-script', 'ajax_object' , array( 'ajax_url'=> admin_url('admin-ajax.php'),
	// Add other variables you want to pass to your script here
	));
	}
	add_action('wp_enqueue_scripts', 'fiverr_market_enque_scripts');

	// Elementor Widget Setip
	function fiverr_market_elemetore_widgets($widgets_manager)
{

	require_once(__DIR__ . '/elementor/regi.php');
	require_once(__DIR__ . '/elementor/s-regi.php');
	require_once(__DIR__ . '/elementor/teachers.php');

	$widgets_manager->register(new \Elementor_fiverr_market_teacher_loop());
	$widgets_manager->register(new \Elementor_fiverr_market_teacher_registration_form());
	$widgets_manager->register(new \Elementor_fiverr_market_student_registration_form());
}
add_action('elementor/widgets/register', 'fiverr_market_elemetore_widgets');




// Activation Hook
register_activation_hook(__FILE__, 'chat_by_shakib_activation_function');

// Deactivation Hook
register_deactivation_hook(__FILE__, 'chat_by_shakib_deactivation_function');

// Activation function
function chat_by_shakib_activation_function() {
    // Your activation code here
    // For example, create database tables or set default options
	create_teacher_student_message_cpt();
}

// Deactivation function
function chat_by_shakib_deactivation_function() {
    // Your deactivation code here
    // For example, delete database tables or clean up options
}


