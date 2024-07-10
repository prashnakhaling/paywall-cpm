<?php
//function to enqueue the registration form 
function enqueue_registration_form() {
    include_once plugin_dir_url(__FILE__) . 'templates\registration-form.php';
}
add_action('init', 'enqueue_registration_form');