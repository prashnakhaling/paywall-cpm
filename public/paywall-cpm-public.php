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
        if (is_singular()) {
            global $post;
            $options = get_option('paywall_settings');
            $post_types = $options['paywall_post_types'] ?? array();

            // Check if the current page is login, registration, or dashboard page
            $login_page = is_page('login');
            $register_page = is_page('register');
            $dashboard_page = is_page('dashboard');

            if (!$login_page && !$register_page && !$dashboard_page && in_array($post->post_type, $post_types)) {
                if (current_user_can('administrator')) {
                    return $content; // Admin can view full content without restriction
                } elseif (!is_user_logged_in()) {
                    return wp_trim_words($content, 100, '... <a href="' . wp_login_url() . '">Login to read more</a>');
                } else {
                    $user_id = get_current_user_id();
                    $credits = get_user_meta($user_id, 'paywall_credits', true) ?? 0;

                    if ($credits < 1) {
                        return wp_trim_words($content, 100, '... <a href="' . home_url('/dashboard') . '">Your credits for this month have expired.</a>');
                    } else {
                        // Check if the user has already read this post
                        $has_read = get_post_meta($post->ID, '_has_read_' . $user_id, true);

                        if (!$has_read) {
                            // Deduct one credit for reading and mark the post as read
                            update_user_meta($user_id, 'paywall_credits', $credits - 1);
                            update_post_meta($post->ID, '_has_read_' . $user_id, true);
                        }

                        return $content;
                    }
                }
            }
        }
        return $content;
    }

    public function enqueue_public_scripts()
    {
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
}
