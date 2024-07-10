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
function use_custom_single_template($template)
{
    if (is_single()) {
        $custom_template = plugin_dir_path(__FILE__) . 'includes/single-paywall-cpm.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('single_template', 'use_custom_single_template');

// function use_custom_single_templates($templates) {
//     if (is_single()) {
//         $custom_templates = plugin_dir_path(__FILE__) . 'templates/single-paywall-cpm.php';
//         if (file_exists($custom_templates)) {
//             return $custom_templates;
//         }
//     }
//     return $templates;
// }
// add_filter('single_template', 'use_custom_single_templates');

function cpm_paywall_registration_form()
{
    ob_start();

    // if (isset($_GET['submission']) && $_GET['submission'] == 'success') {
    //     echo '<div class="cpm-form-success">' . __('Your form submission was successful!') . '</div>';
    // }

?>
    <form id="registration-form" action="" method="post">

        <label for="name">Username</label>
        <input type="text" name="name" id="name" required>

        <label for="user_email">Email Address</label>
        <input type="email" name="email" id="email" required>


        <label for="user_password">Password</label>
        <input type="password" name="password" id="password" required>
        <input type="submit" name="submit">
    </form>
<?php
    return ob_get_clean();
}
add_shortcode('registration_form', 'cpm_paywall_registration_form');

//form submission handling
function cpm_process_registration_form()
{
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
        // Sanitize and validate inputs
        $user_name = sanitize_text_field($_POST['name']);
        $user_email = sanitize_email($_POST['email']);
        $user_password = sanitize_text_field($_POST['password']);

        // Check required fields
        if (empty($user_name) || empty($user_email) || empty($user_password)) {
            wp_die('All fields are required.');
        }

        // Check if user exists
        if (email_exists($user_email)) {
            wp_die('User with this email already exists.');
        }

        // Create user
        $post_id = wp_insert_post([
            'post_title'   => $user_name,
            'post_content' => $user_email,
            'post_status'  => 'publish',
        ]);
        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, 'username', $user_name);
            update_post_meta($post_id, 'email', $user_email);
            update_post_meta($post_id, 'content', $user_password);

            //success message
            // echo 'Message delivery successfully';
            $login_url = wp_login_url(get_permalink()); // Replace with your plugin's login page URL function
            wp_redirect($login_url); // Redirect to the login page

            exit;
        }
    }
}
add_action('init', 'cpm_process_registration_form');
