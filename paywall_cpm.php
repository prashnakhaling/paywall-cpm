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

// Include the admin settings file
require_once PAYWALL_CPM_PLUGIN_DIR . 'admin/paywall-cpm-admin.php';

// Enqueue CSS and JS
function paywall_cpm_enqueue_scripts()
{
    wp_enqueue_style('paywall-cpm-style', PAYWALL_CPM_PLUGIN_URL . 'public/css/paywall-cpm-public-style.css');
    wp_enqueue_script('paywall-cpm-script', PAYWALL_CPM_PLUGIN_URL . 'public/js/paywall-cpm-public-script.js', ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', 'paywall_cpm_enqueue_scripts');

//load single page
function paywall_cpm_template($template)
{
    if (is_single() && !is_user_logged_in()) {
        $new_template = locate_template(array('single-paywall-cpm.php'));
        if ($new_template) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'paywall_cpm_template');
