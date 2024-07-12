<?php
class Paywall_Public
{
    public function __construct()
    {
        add_action('the_content', array($this, 'restrict_content'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts'));
        add_action('wp_nav_menu_items', array($this, 'add_login_logout_link'), 10, 2);
        add_action('wp_logout', array($this, 'custom_logout_message'));
    }


    public function restrict_content($content)
    {
        global $post;
        // var_dump($content);
        // Get the ID of the home page
        // $home_page_id = get_option('page_on_front');
        // var_dump($home_page_id);

        // if (is_singular() && $post->ID === $home_page_id) {
        //     return $content;
        //     // var_dump($content);
        // }

        if (is_front_page()) {
            return $content;
        }

        if (!is_singular()) {
            return $content;
        }

        // Get paywall settings
        $options = get_option('paywall_settings', array());
        $post_types = isset($options['paywall_post_types']) ? (array) $options['paywall_post_types'] : array();
        // var_dump($post_types);

        // Check if the current page is login, registration, or dashboard page
        $login_page = is_page('login');
        $register_page = is_page('register');
        $dashboard_page = is_page('dashboard');
        if (!$login_page && !$register_page && !$dashboard_page && in_array($post->post_type, $post_types)) {
            // Check if the current page is a singular page of a selected post type
            // if (in_array($post->post_type, $post_types)) {
            if (current_user_can('administrator')) {
                return $content; // Admin can view full content without restriction
            } else if (!is_user_logged_in()) {
                // For non-logged-in users, restrict content to 100 words
                return wp_trim_words($content, 100, '... <a href="' . wp_login_url() . '">Login to read more</a>');
            } else {
                $user_id = get_current_user_id();
                $credits = (int) get_user_meta($user_id, 'paywall_credits', true);
                $viewed_posts = get_user_meta($user_id, 'viewed_posts', true) ?: array();

                // Admins can view full content without needing credits
                if (current_user_can('manage_options')) {
                    return $content;
                }

                // Check if the user has already viewed this post
                if (in_array($post->ID, $viewed_posts)) {
                    return $content; // Show full content if already viewed
                }

                // Check if credits are available
                if ($credits > 0) {
                    // Deduct one credit for reading
                    update_user_meta($user_id, 'paywall_credits', $credits - 1);
                    $viewed_posts[] = $post->ID;
                    update_user_meta($user_id, 'viewed_posts', $viewed_posts);

                    return $content; // Show full content after deducting credit
                } else {
                    // No credits left, show 100 words and credit expired message
                    return wp_trim_words($content, 100, '... <a href="' . home_url('/dashboard') . '">Your credits for this month have expired.</a>');
                }
            }
        }

        return $content; // Return original content if not restricted
    }

    public function enqueue_public_scripts()
    {
        // Enqueue necessary scripts and styles
        wp_enqueue_style('paywall-public-css', plugin_dir_url(__FILE__) . 'css/paywall-cpm-public-style.css');
        wp_enqueue_script('paywall-public-js', plugin_dir_url(__FILE__) . 'js/paywall-cpm-public-script.js', array('jquery'), null, true);
        wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
    }

    public function add_login_logout_link($items, $args)
    {
        if (is_user_logged_in()) {
            $items .= '<li class="menu-item"><a href="' . wp_logout_url(home_url()) . '">Logout</a></li>';
        } else {
            $items .= '<li class="menu-item"><a href="' . home_url('/login') . '">Login</a></li>';
        }
        return $items;
    }

    public function custom_logout_message()
    {
        add_action('wp_footer', function () {
            echo '<div class="logout-message">You have successfully logged out.</div>';
        });
    }
    // Shortcode callback for login form
    public function login_form_shortcode()
    {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/login-form.php';
        return ob_get_clean();
    }

    // Shortcode callback for registration form
    public function register_form_shortcode()
    {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/register-form.php';
        return ob_get_clean();
    }
}
