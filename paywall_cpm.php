<?php
/*
Plugin Name: Paywall
Description: A plugin where use can log in and read article on the basis of the credits remaining in their account.
Version: 1.0
Author: CPM
License: GPL2
Text Domain: paywall-cpm

*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
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
}
