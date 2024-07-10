<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="paywall_cpm_register">
    <?php wp_nonce_field('paywall_cpm_register', 'paywall_cpm_register_nonce'); ?>

    <p>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </p>

    <p>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </p>

    <p>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </p>

    <p>
        <input type="submit" value="Register">
    </p>
</form>