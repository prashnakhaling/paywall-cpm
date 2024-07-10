<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
get_header();

?>
<!-- section for single to show the content if user is logged in or not logged in  -->
<section class="paywall-singlepage-section">
<div>
    <div>
       <h2>PayWall</h2>
    </div>

    <?php
          if (!is_user_logged_in()) {
         echo "this is a test";
    } 
    else
    echo "not registered";
    ?>
    </div>
</section>

<?php get_footer();