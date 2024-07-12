<section class="paywal-registration">
    <div class="paywall-main-section">
        <div class="paywall-header-section">
            <h1>Paywall</h1>
            <h3>Registaration Form </h3>
        </div>
        <div class="paywall-form-section">
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <input type="hidden" name="action" value="paywall_register">
                <?php wp_nonce_field('paywall_register', 'paywall_register_nonce'); ?>

                <label for="username">Username</label>
                <input type="text" name="username" id="username">

                <label for="email">Email</label>
                <input type="email" name="email" id="email">

                <label for="password">Password</label>
                <input type="password" name="password" id="password">

                <input type="submit" value="Register">

            </form>
        </div>
    </div>

</section>