<?php

add_shortcode('contact', 'cfmsedkiewicz_show_contact_form');
add_action('rest_api_init', 'cfmsedkiewicz_create_rest_endpoint');
add_action('init', 'cfmsedkiewicz_create_submissions_page');
add_action('add_meta_boxes', 'cfmsedkiewicz_create_meta_box');

/* creating CPT for submissions */

function cfmsedkiewicz_create_submissions_page() {

    $args = [
        'public' => true,
        'has_archive' => true,
        'labels' => [
            'name' => 'Submissions',
            'singular_name' => 'Submission'
        ],
        'supports' => false
        // 'capabilities' => [
        //     'create_posts' => 'do_not_allow'
        // ]
    ];
    register_post_type('submission', $args);
}

/* create meta box for data display */
function cfmsedkiewicz_display_submission() {
    $postmetas = get_post_meta( get_the_ID() );

    echo '<ul>';

    foreach($postmetas as $key => $value) {
        echo '<li><strong>' . ucfirst($key) . '</strong>: ' . $value[0] . '</li>';
    }

    echo '</ul>';
}

function cfmsedkiewicz_create_meta_box() {
    add_meta_box('cfmsedkiewicz_custom_contact_form', 'Submission', 'cfmsedkiewicz_display_submission', 'submission');
}

/* display CF template on a front-end */

function cfmsedkiewicz_show_contact_form()
{
    include CFFORM_PATH . '/includes/templates/contact-form.php';
}

/* E-mail sending logic */

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

    //Send e-mail
    $headers = [];

    $admin_email = get_bloginfo('admin_email');
    $admin_name = get_bloginfo('name');

    $headers[] = "From: {$admin_name} < $admin_email >";
    $headers[] = "Reply-to: {$params['name']} <{$params['email']}>";
    $headers[] = "Content-Type: text/html";

    $subject = "New e-mail from {$params['name']}";

    $message = "";
    $message.= "<h1>Message has been sent from {$params['name']}</h1>";

    $postarr = [
        'post_title' => $params['name'],
        'post_type' => 'submission'
    ];

    $post_id = wp_insert_post($postarr);

    foreach($params as $label => $value) {
        $message .= '<strong>' . ucfirst($label) . '</strong>: ' . $value . '<br />';

        add_post_meta($post_id, $label, $value);
    }

    wp_mail($admin_email, $subject, $message, $headers);

    return new WP_Rest_Response('Message has been sent successfully.', 200);
}