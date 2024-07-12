<?php
/*
Plugin Name: Paywall
Description: A plugin to add a paywall for selected post types.
Version: 1.0
Author:Cpm

Text Domain: paywall
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
function enqueue_user_registration_css() {
    wp_enqueue_style('user-registration-css', plugin_dir_url(__FILE__) . 'public/css/user-registration.css');
}
add_action('wp_enqueue_scripts', 'enqueue_user_registration_css');

class Paywall
{
    public function __construct()
    {
        $this->define_admin_hooks();
        $this->define_public_hooks();

        // Handle user registration
        add_action('user_register', array($this, 'give_initial_credits'));
        // Handle form submissions
        add_action('init', array($this, 'handle_form_submission'));

        // Shortcodes for login and registration
        add_shortcode('paywall_login', array($this, 'login_form'));
        add_shortcode('paywall_register', array($this, 'register_form'));
        add_shortcode('paywall_dashboard', array($this, 'dashboard'));

        // Renew credits monthly
        add_action('init', array($this, 'schedule_monthly_credit_renewal'));
        add_action('paywall_renew_monthly_credits', array($this, 'renew_monthly_credits'));

        // Add custom schedule interval
        add_filter('cron_schedules', array($this, 'add_monthly_schedule'));
    }

    private function define_admin_hooks()
    {
        require_once plugin_dir_path(__FILE__) . 'admin/paywall-cpm-admin.php';
        $admin = new Paywall_Admin();
    }

    private function define_public_hooks()
    {
        require_once plugin_dir_path(__FILE__) . 'public/paywall-cpm-public.php';
        $public = new Paywall_Public();
    }

    public function give_initial_credits($user_id)
    {
        // Give 5 initial credits to new users upon registration
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

    public function schedule_monthly_credit_renewal()
    {
        if (!wp_next_scheduled('paywall_renew_monthly_credits')) {
            wp_schedule_event(time(), 'monthly', 'paywall_renew_monthly_credits');
        }
    }

    public function renew_monthly_credits()
    {
        $users = get_users();
        foreach ($users as $user) {
            update_user_meta($user->ID, 'paywall_credits', 5);
            update_user_meta($user->ID, 'paywall_last_renewed', current_time('timestamp'));
        }
    }

    public function add_monthly_schedule($schedules)
    {
        $schedules['monthly'] = array(
            'interval' => 2592000, // 30 days in seconds
            'display'  => __('Once Monthly')
        );
        return $schedules;
    }
}

new Paywall();
