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
   </form>
   </div>

  
<?php
}
add_shortcode('cpm_investor_form', 'cpm_paywall_registration_form');
