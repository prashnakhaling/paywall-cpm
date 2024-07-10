<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function cpm_paywall_registration_form()
{
    ob_start();
?>
   <div class="paywall_registration">

   <form action="">
    <label for="username">Username:</label>
    <input type="text" name="username" >
   </form>
   </div>

  
<?php

    return ob_get_clean();
}
add_shortcode('paywall_form', 'cpm_paywall_registration_form');
