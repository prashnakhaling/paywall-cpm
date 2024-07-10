<?php
// Register the settings page
function paywall_cpm_register_settings_page()
{
    add_options_page(
        __('Paywall CPM Settings', 'paywall-cpm'),
        __('Paywall CPM', 'paywall-cpm'),
        'manage_options',
        'paywall-cpm',
        'paywall_cpm_settings_page_callback'
    );
}
add_action('admin_menu', 'paywall_cpm_register_settings_page');

// Settings page callback
function paywall_cpm_settings_page_callback()
{
?>
    <div class="wrap">
        <h1><?php _e('Paywall CPM Settings', 'paywall-cpm'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('paywall_cpm_settings_group');
            do_settings_sections('paywall-cpm');
            submit_button();
            ?>
        </form>
    </div>
<?php
}

// Register settings and fields
function paywall_cpm_register_settings()
{
    register_setting('paywall_cpm_settings_group', 'paywall_cpm_post_types');

    add_settings_section(
        'paywall_cpm_main_section',
        __('Main Settings', 'paywall-cpm'),
        null,
        'paywall-cpm'
    );

    add_settings_field(
        'paywall_cpm_post_types',
        __('Select Post Types', 'paywall-cpm'),
        'paywall_cpm_post_types_callback',
        'paywall-cpm',
        'paywall_cpm_main_section'
    );
}
add_action('admin_init', 'paywall_cpm_register_settings');

// Post types callback
function paywall_cpm_post_types_callback()
{
    $selected_post_types = get_option('paywall_cpm_post_types', []);
    $post_types = get_post_types(['public' => true], 'objects');

    echo '<select id="paywall_cpm_post_types" name="paywall_cpm_post_types[]" multiple="multiple" style="width: 100%;">';
    foreach ($post_types as $post_type) {
        $selected = in_array($post_type->name, $selected_post_types) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($post_type->name) . '" ' . $selected . '>' . esc_html($post_type->label) . '</option>';
    }
    echo '</select>';
}

// Enqueue Select2 scripts and styles
function paywall_cpm_enqueue_admin_scripts($hook)
{
    if ($hook != 'settings_page_paywall-cpm') {
        return;
    }

    // Enqueue Select2 CSS and JS
    wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
    wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], null, true);

    // Enqueue custom script to initialize Select2
    wp_enqueue_script('paywall-cpm-admin-js', PAYWALL_CPM_PLUGIN_URL . 'admin/js/paywall-cpm-admin.js', ['jquery', 'select2-js'], null, true);
}
add_action('admin_enqueue_scripts', 'paywall_cpm_enqueue_admin_scripts');
