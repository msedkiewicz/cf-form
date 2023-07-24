<?php

add_shortcode('contact', 'cfmsedkiewicz_show_contact_form');
add_action('rest_api_init', 'cfmsedkiewicz_create_rest_endpoint');

function cfmsedkiewicz_show_contact_form()
{
    include CFFORM_PATH . '/includes/templates/contact-form.php';
}

function cfmsedkiewicz_create_rest_endpoint()
{
    register_rest_route('v1/contact-form', 'submit', array(
        'methods' => 'POST',
        'callback' => 'cfmsedkiewicz_handle_enquiry',
    ));
}

function cfmsedkiewicz_handle_enquiry($data)
{
    $params = $data->get_params();
    if( !wp_verify_nonce($params['_wpnonce'], 'wp_rest') )
    {
        return new WP_Rest_Response('Message not sent', 422);
    }

    unset($params['_wpnonce']);
    unset($params['_wp_http_referer']);
}