<?php
/*
Plugin Name: Paywall
Description: A plugin where use can log in and read article on the basis of the credits remaining in their account.
Version: 1.0
Author: CPM
License: GPL2
Text Domain: paywall-cpm

*/


// Define plugin directory paths
define('PAYWALL_CPM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PAYWALL_CPM_PLUGIN_URL', plugin_dir_url(__FILE__));
// define('PAYWALL_CPM_INCLUDES_DIR', PAYWALL_CPM_PLUGIN_DIR . 'includes/');


// Include the admin settings file
require_once PAYWALL_CPM_PLUGIN_DIR . 'admin/paywall-cpm-admin.php';
// require_once PAYWALL_CPM_PLUGIN_DIR . 'includes/single-paywall-cpm.php';
// require_once plugin_dir_path( __FILE__ ) . 'includes/single-paywall-cpm.php'; 


// Enqueue CSS and JS
function paywall_cpm_enqueue_scripts()
{
    wp_enqueue_style('paywall-cpm-style', PAYWALL_CPM_PLUGIN_URL . 'public/css/paywall-cpm-public-style.css');
    wp_enqueue_script('paywall-cpm-script', PAYWALL_CPM_PLUGIN_URL . 'public/js/paywall-cpm-public-script.js', ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', 'paywall_cpm_enqueue_scripts');

// Load the single.php template from the include folder
function use_custom_single_template($template) {
    if (is_single()) {
        $custom_template = plugin_dir_path(__FILE__) . 'includes/single-paywall-cpm.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('single_template', 'use_custom_single_template');
