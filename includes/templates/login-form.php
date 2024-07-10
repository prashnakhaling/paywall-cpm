<form method="post" action="<?php echo esc_url(wp_login_url()); ?>">
    <p>
        <label for="username">Username:</label>
        <input type="text" id="username" name="log" required>
    </p>

    <p>
        <label for="password">Password:</label>
        <input type="password" id="password" name="pwd" required>
    </p>

    <p>
        <input type="submit" value="Login">
    </p>
</form>