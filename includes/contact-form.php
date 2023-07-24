<?php

add_shortcode('contact', 'cfmsedkiewicz_show_contact_form');
add_action('rest_api_init', 'cfmsedkiewicz_create_rest_endpoint');

function cfmsedkiewicz_show_contact_form()
{
    include CFFORM_PATH . '/includes/templates/contact-form.php';
}

function cfmsedkiewicz_create_rest_endpoint()
{
    $post = 'POST';
    $callback = 'cfmsedkiewicz_handle_enquiry';

    register_rest_route('v1/contact-form', 'submit', array(
        'methods' -> $post,
        'callback' -> $callback,
    ));
}

function cfmsedkiewicz_handle_enquiry()
{
    echo 'hello';
}