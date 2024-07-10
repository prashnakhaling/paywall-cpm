<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
get_header();
//registration form
function cpm_paywall_registration_form()
{
    ob_start();
?>
    <form id="registration-form" action="<?php echo esc_url(admin_url('paywall-cpm-admin.php')); ?>" method="post">
        <!-- <input type="hidden" name="action" value="process_registration"> -->

        <label for="name">Username</label>
        <input type="text" name="name" id="name" required>

        <label for="user_email">Email Address</label>
        <input type="email" name="email" id="email" required>


        <label for="user_password">Password</label>
        <input type="password" name="password" id="password" required>
        <input type="submit">
    </form>
<?php
    return ob_get_clean();
}
add_shortcode('registration_form', 'cpm_paywall_registration_form');

//form submission
if (isset($_GET['submission']) && $_GET['submission'] == 'success') {
    echo '<div class="cpm-form-success">' . __('Your form submission was successful!') . '</div>';
}
function handle_form_submission()
{
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $password = sanitize_textarea_field($_POST['password']);

        // Create a new user
        $user_id = wp_create_user($name, $password, $email);

        if (!is_wp_error($user_id)) {
            // Update user meta if needed
            // update_user_meta($user_id, 'meta_key', 'meta_value');

            // Redirect after successful registration
            wp_redirect(add_query_arg('submission', 'success', wp_get_referer()));
            exit;
        } else {
            // Handle error
            wp_die('User registration failed: ' . $user_id->get_error_message());
        }
    }
}
add_action('admin_post_nopriv_handle_form_submission', 'handle_form_submission');
add_action('admin_post_handle_form_submission', 'handle_form_submission');

if (isset($_GET['submission']) && $_GET['submission'] == 'success') {
    echo '<div class="cpm-form-success">' . __('Your form submission was successful!') . '</div>';
}
function cpm_check_shortcode_loading()
{
    echo '<div style="display: none;">Shortcode function loaded.</div>';
}
