<html>
<section class="paywall-login">
    <div class="paywall-login-maindiv">
        <div class="login-heading">
            <h1>PayWall</h1>
        </div>
        <div class="paywall-loginform">
            <h3 class="login-title">Log In</h3>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <input type="hidden" name="action" value="paywall_login">
                <?php wp_nonce_field('paywall_login', 'paywall_login_nonce'); ?>

                <!-- <label for="username">Username</label> -->
                <input type="text" name="username" id="username" class="form-detail" placeholder="Username">

                <!-- <label for="password">Password</label> -->
                <input type="password" name="password" id="password"class="form-detail" placeholder="Password">

                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember Me</label><br>

                <input type="submit" value="Login">
        </div>
    </div>

    </form>
</section>

</html>