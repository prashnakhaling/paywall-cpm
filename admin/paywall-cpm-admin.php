<?php

class Paywall_Admin

{

    public function __construct()

    {

        add_action('admin_menu', array($this, 'add_admin_menu'));

        add_action('admin_init', array($this, 'settings_init'));

        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }



    public function enqueue_admin_scripts()

    {

        wp_enqueue_style('paywall-admin-css', plugin_dir_url(__FILE__) . 'css/cpm-paywall-admin.css');

        wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');

        wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), null, true);

        wp_enqueue_script('paywall-admin-js', plugin_dir_url(__FILE__) . 'js/paywall-cpm-admin.js', array('jquery'), null, true);
    }



    public function add_admin_menu()

    {

        add_options_page('Paywall', 'Paywall', 'manage_options', 'paywall', array($this, 'options_page'));
    }



    public function settings_init()

    {

        register_setting('paywall', 'paywall_settings');



        add_settings_section(

            'paywall_section',

            __('Paywall Settings', 'paywall'),

            null,

            'paywall'

        );



        add_settings_field(

            'paywall_post_types',

            __('Post Types', 'paywall'),

            array($this, 'post_types_render'),

            'paywall',

            'paywall_section'

        );
    }



    public function post_types_render()

    {

        $options = get_option('paywall_settings');

        $post_types = get_post_types(array('public' => true), 'objects');

        $selected_post_types = isset($options['paywall_post_types']) ? (array) $options['paywall_post_types'] : array();

?>

<select id='paywall_cpm_post_types' name='paywall_settings[paywall_post_types][]' multiple='multiple'
    style='width: 50%;'>
    <?php foreach ($post_types as $post_type) : ?>
    <option value='<?php echo esc_attr($post_type->name); ?>'
        <?php if (in_array($post_type->name, $selected_post_types)) echo 'selected="selected"'; ?>>
        <?php echo esc_html($post_type->label); ?></option>
    <?php endforeach; ?>
</select>

<?php

    }



    public function options_page()

    {

    ?>

<form action='options.php' method='post'>

    <h2>Paywall</h2>

    <?php

            settings_fields('paywall');

            do_settings_sections('paywall');

            submit_button();

            ?>

</form>

<?php

    }
}

?>