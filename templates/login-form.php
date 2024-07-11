<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
    <input type="hidden" name="action" value="paywall_login">
    <?php wp_nonce_field('paywall_login', 'paywall_login_nonce'); ?>
    <p>
        <label for="username">Username</label>
        <input type="text" name="username" id="username">
    </p>
    <p>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
    </p>
    <p>
        <input type="checkbox" name="remember" id="remember">
        <label for="remember">Remember Me</label>
    </p>
    <p>
        <input type="submit" value="Login">
    </p>
</form>