<?php
// Handle registration form submission
function paywall_cpm_handle_registration()
{
    if (isset($_POST['paywall_cpm_register_nonce']) && wp_verify_nonce($_POST['paywall_cpm_register_nonce'], 'paywall_cpm_register')) {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);

        // Validate username and email
        if (!username_exists($username) && !email_exists($email)) {
            $user_id = wp_create_user($username, $password, $email);

            if (!is_wp_error($user_id)) {
                // Optionally login the user after registration
                $user = get_user_by('id', $user_id);
                wp_set_current_user($user_id, $user->user_login);
                wp_set_auth_cookie($user_id);
                do_action('wp_login', $user->user_login);

                // Give user initial credits
                update_user_meta($user_id, 'credits', 5);

                wp_redirect(home_url('/login?registered=1'));
                exit;
            } else {
                wp_redirect(home_url('/register?error=registration_failed'));
                exit;
            }
        } else {
            wp_redirect(home_url('/register?error=username_email_exists'));
            exit;
        }
    }
}
add_action('admin_post_nopriv_paywall_cpm_register', 'paywall_cpm_handle_registration');
add_action('admin_post_paywall_cpm_register', 'paywall_cpm_handle_registration');