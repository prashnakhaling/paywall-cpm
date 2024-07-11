<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
    <input type="hidden" name="action" value="paywall_register">
    <?php wp_nonce_field('paywall_register', 'paywall_register_nonce'); ?>
    <p>
        <label for="username">Username</label>
        <input type="text" name="username" id="username">
    </p>
    <p>
        <label for="email">Email</label>
        <input type="email" name="email" id="email">
    </p>
    <p>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
    </p>
    <p>
        <input type="submit" value="Register">
    </p>
</form>