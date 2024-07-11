<?php
function paywall_handle_registration()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['paywall_register_nonce']) && wp_verify_nonce($_POST['paywall_register_nonce'], 'paywall_register')) {
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
                wp_redirect(home_url('/login?registered=1'));
            }
            exit;
        }
    }
}
add_action('init', 'paywall_handle_registration');
