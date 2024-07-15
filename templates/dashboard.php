<?php
$current_user = wp_get_current_user();
$credits = get_user_meta($current_user->ID, 'paywall_credits', true) ?? 0;
$last_renewed = get_user_meta($current_user->ID, 'paywall_last_renewed', true);
$renewal_date = date('F j, Y', strtotime('+1 month', $last_renewed)); // strototime ==This function adds one month to the timestamp stored in $last_renewed.
?>
<h2>Welcome, <?php echo esc_html($current_user->display_name); ?></h2><br>
<p>You have <?php echo esc_html($credits); ?> credits remaining for this month.</p><br>
<p>Your credits will renew on <?php echo esc_html($renewal_date); ?>.</p><br>
<p><a href="<?php echo wp_logout_url(home_url()); ?>">Logout</a></p>