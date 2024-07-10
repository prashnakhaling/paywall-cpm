<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
get_header();

?>
<?php
function registration_form_shortcode()
{
    ob_start();
?>
    <form id="registration-form" action="" method="post">
        <!-- <input type="hidden" name="action" value="process_registration"> -->

        <label for="user_name">Name</label>
        <input type="text" name="user_name" id="user_name" required>

        <label for="user_email">Email Address</label>
        <input type="email" name="user_email" id="user_email" required>


        <label for="user_password">Password</label>
        <input type="password" name="user_password" id="user_password" required>


        <input type="submit" value="Register">

    </form>
<?php
    return ob_get_clean();
}
add_shortcode('registration_form', 'registration_form_shortcode');
