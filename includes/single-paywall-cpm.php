<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
get_header();

?>
<section class="paywall-cpm-maindiv">
    <div class="paywall-">
        <div>
            <h2 class="cpm-paywall-header">PayWall</h2>
        </div>
        <!-- main content for single page  -->
        <div class= "paywall-article">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post();
            ?>    
           
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>
                        <div class="entry-content">
                            <?php
                            
                            if (!is_user_logged_in()) {
                                // Display around 100 words of the post content
                                echo wp_trim_words(get_the_content(), 100, '...');
                            ?>
                                <button onclick="location.href='<?php echo wp_login_url(get_permalink()); ?>';">Log in to read more</button>
                            <?php

                            } else {
                                // Display full content for logged-in users
                                the_content();
                            }
                            ?>
                        </div>


                   
            <?php
                endwhile;
            endif;


            ?>
        </div>
    </div>
</section>
<?php
get_footer();
