<form action="<?php echo esc_url(wp_login_url()); ?>" method="POST">
    <label for="log">Username</label>
    <input type="text" name="log" id="log" required>
    <label for="pwd">Password</label>
    <input type="password" name="pwd" id="pwd" required>
    <input type="submit" value="Login">
</form>