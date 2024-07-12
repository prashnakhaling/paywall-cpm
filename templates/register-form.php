<section class="paywall-registration">
    <div class="paywall-main-section">
        <div class="paywall-header-section">
            <h1>Paywall</h1>

        </div>
        <div class="paywall-form-section">
            <h3 class="paywall-heading">Registration Form </h3>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <input type="hidden" name="action" value="paywall_register">
                <?php wp_nonce_field('paywall_register', 'paywall_register_nonce'); ?>

                <!-- <label for="username">Username</label><br> -->
                <input type="text" name="username" id="form-detail" placeholder="Username" required><br>

                <!-- <label for="email">Email</label><br> -->
                <input type="email" name="email" id="form-detail" placeholder="Email" required><br>

                <!-- <label for="password">Password</label><br> -->
                <input type="password" name="password" id="form-detail" placeholder="Password" required><br>
                <div class="registration-btn">
                    <input type="submit" value="Register" id="paywall-registration-btn">

                </div>

            </form>
        </div>
    </div>

</section>