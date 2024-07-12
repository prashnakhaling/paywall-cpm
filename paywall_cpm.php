<?php
/*
Plugin Name: Paywall
Description: A plugin to manage content access with a credit system.
Version: 1.0
Author: Cpm
Text Domain: paywall
*/

// Ensure we don't directly access this file
if (!defined('ABSPATH')) {
    exit;
}
function enqueue_user_registration_css() {
    wp_enqueue_style('user-registration-css', plugin_dir_url(__FILE__) . 'public/css/user-registration.css');
    wp_enqueue_style('user-login-css', plugin_dir_url(__FILE__) . 'public/css/user-login.css');

}
add_action('wp_enqueue_scripts', 'enqueue_user_registration_css');

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'admin/paywall-cpm-admin.php';
require_once plugin_dir_path(__FILE__) . 'public/paywall-cpm-public.php';

// Initialize the plugin
class Paywall
{
    public function __construct()
    {
        // Initialize admin and public hooks
        $this->define_admin_hooks();
        $this->define_public_hooks();

        // Handle user registration and credits
        add_action('user_register', array($this, 'give_initial_credits'));
        add_action('init', array($this, 'handle_form_submission'));
        add_action('init', array($this, 'schedule_monthly_credit_renewal'));
        add_action('paywall_renew_monthly_credits', array($this, 'renew_monthly_credits'));
        add_filter('cron_schedules', array($this, 'add_monthly_schedule'));

        // Shortcodes for login, registration, and dashboard
        add_shortcode('paywall_login', array($this, 'login_form'));
        add_shortcode('paywall_register', array($this, 'register_form'));
        add_shortcode('paywall_dashboard', array($this, 'dashboard'));
    }

    // Initialize admin hooks
    private function define_admin_hooks()
    {
        $admin = new Paywall_Admin();
    }

    // Initialize public hooks
    private function define_public_hooks()
    {
        $public = new Paywall_Public();
    }

    // Give initial credits upon user registration
    public function give_initial_credits($user_id)
    {
        add_user_meta($user_id, 'paywall_credits', 5, true);
        add_user_meta($user_id, 'paywall_last_renewed', current_time('timestamp'), true);
    }

    public function handle_form_submission()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['paywall_register_nonce']) && wp_verify_nonce($_POST['paywall_register_nonce'], 'paywall_register')) {
                $this->handle_registration();
            } elseif (isset($_POST['paywall_login_nonce']) && wp_verify_nonce($_POST['paywall_login_nonce'], 'paywall_login')) {
                $this->handle_login();
            }
        }
    }

    private function handle_registration()
    {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];

        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            // Registration failed
            wp_redirect(home_url('/register?error=1'));
        } else {
            // Registration successful
            add_user_meta($user_id, 'paywall_credits', 5, true);
            add_user_meta($user_id, 'paywall_last_renewed', current_time('timestamp'), true);
            wp_redirect(home_url('/login?registered=1'));
        }
        exit;
    }

    private function handle_login()
    {
        $creds = array(
            'user_login'    => $_POST['username'],
            'user_password' => $_POST['password'],
            'remember'      => isset($_POST['remember'])
        );

        $user = wp_signon($creds, is_ssl());

        if (is_wp_error($user)) {
            // Login failed
            wp_redirect(home_url('/login?error=1'));
        } else {
            // Login successful
            wp_redirect(home_url('/dashboard'));
        }
        exit;
    }

    // Schedule monthly credit renewal
    public function schedule_monthly_credit_renewal()
    {
        if (!wp_next_scheduled('paywall_renew_monthly_credits')) {
            wp_schedule_event(time(), 'monthly', 'paywall_renew_monthly_credits');
        }
    }

    // Renew monthly credits for all users
    public function renew_monthly_credits()
    {
        $users = get_users();
        foreach ($users as $user) {
            update_user_meta($user->ID, 'paywall_credits', 5);
            update_user_meta($user->ID, 'paywall_last_renewed', current_time('timestamp'));
        }
    }

    // Add custom schedule interval for monthly renewal
    public function add_monthly_schedule($schedules)
    {
        $schedules['monthly'] = array(
            'interval' => 2592000, // 30 days in seconds
            'display' => __('Once Monthly')
        );
        return $schedules;
    }

    // Shortcode callbacks for login, registration, and dashboard
    public function login_form()
    {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/login-form.php';
        return ob_get_clean();
    }

    public function register_form()
    {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/register-form.php';
        return ob_get_clean();
    }

    public function dashboard()
    {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/dashboard.php';
        return ob_get_clean();
    }
}

new Paywall();
