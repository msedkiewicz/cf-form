<?php

if( !defined('ABSPATH') ){
    die('Go and watch LOTR!');
};

add_shortcode('contact', 'cfmsedkiewicz_show_contact_form');

add_action('rest_api_init', 'cfmsedkiewicz_create_rest_endpoint');
add_action('init', 'cfmsedkiewicz_create_submissions_page');
add_action('add_meta_boxes', 'cfmsedkiewicz_create_meta_box');

add_filter('manage_submission_posts_columns', 'cfmsedkiewicz_custom_submission_columns');

add_action('manage_submission_posts_custom_column', 'cfmsedkiewicz_fill_submission_columns', 10, 2);

add_action('admin_init', 'cfmsedkiewicz_setup_search');

add_action('wp_enqueue_scripts', 'cfmsedkiewicz_enqueue_custom_scripts');

/* enqueue custom stylesheet for form */

function cfmsedkiewicz_enqueue_custom_scripts() {
    wp_enqueue_style('cfmsedkiewicz-cfform', CFFORM_URL . '/assets/css/cfmsedkiewicz.css' );
}

/* creating CPT for submissions */

function cfmsedkiewicz_create_submissions_page() {

    $args = [
        'public' => true,
        'has_archive' => true,
        'labels' => [
            'name' => 'Submissions',
            'singular_name' => 'Submission'
        ],
        'supports' => false,
        'capability_type' => 'post',
        'capabilities' => array(
            'create_posts' => 'false'
        ),
        'map_meta_cap' => true, // with true users are allowed to edit /delete post
    ];
    register_post_type('submission', $args);
}

/* create meta box for data display */
function cfmsedkiewicz_display_submission() {
    $postmetas = get_post_meta( get_the_ID() );

    // unset($postmetas['_edit_lock']);

    // echo '<ul>';

    // foreach($postmetas as $key => $value) {
    //     echo '<li><strong>' . ucfirst($key) . '</strong>:<br />' . $value[0] . '</li>';
    // }

    // echo '</ul>';

    /* hardcoding option

    echo 'Name: ' . get_post_meta( get_the_ID(), 'name', true ); */

    echo '<ul>';
    echo '<li><strong>Name:</strong><br />' . esc_html(get_post_meta( get_the_ID(), 'name', true )) . '</li>';
    echo '<li><strong>Email:</strong><br />' . esc_html(get_post_meta( get_the_ID(), 'email', true )) . '</li>';
    echo '<li><strong>Phone:</strong><br />' . esc_html(get_post_meta( get_the_ID(), 'phone', true )) . '</li>';
    echo '<li><strong>message:</strong><br />' . esc_html(get_post_meta( get_the_ID(), 'message', true )) . '</li>';
    echo '</ul>';
}

function cfmsedkiewicz_create_meta_box() {
    add_meta_box('cfmsedkiewicz_custom_contact_form', 'Submission', 'cfmsedkiewicz_display_submission', 'submission');
}
/* Creating custom columns to display data */

function cfmsedkiewicz_custom_submission_columns($columns) {
    $columns = array(
        'cb' => $columns['cb'],
        'name' => __('Name', 'cfform'),
        'email' => __('Email', 'cfform'),
        'phone' => __('Phone', 'cfform'),
        'message' => __('Message', 'cfform'),
    );

    return $columns;
}

function cfmsedkiewicz_fill_submission_columns($column, $post_id) {
    switch($column) {
        case 'name':
            echo esc_html(get_post_meta($post_id, 'name', true));
        break;
        case 'email':
            echo esc_html(get_post_meta($post_id, 'email', true));
        break;
        case 'phone':
            echo esc_html(get_post_meta($post_id, 'phone', true));
        break;
        case 'message':
            echo esc_html(get_post_meta($post_id, 'message', true));
        break;
    }
}
/* Add broader search option on backend*/
function cfmsedkiewicz_setup_search() {
    global $typenow;

    if($typenow == 'submission') {
        add_filter('posts_search', 'cfmsedkiewicz_submission_search_override', 10, 2);
    }
}

function cfmsedkiewicz_submission_search_override($search, $query) {
    // Override the submissions page search to include custom meta data

    global $wpdb;

    if ($query->is_main_query() && !empty($query->query['s'])) {
          $sql    = "
            or exists (
                select * from {$wpdb->postmeta} where post_id={$wpdb->posts}.ID
                and meta_key in ('name','email','phone')
                and meta_value like %s
            )
        ";
          $like   = '%' . $wpdb->esc_like($query->query['s']) . '%';
          $search = preg_replace(
                "#\({$wpdb->posts}.post_title LIKE [^)]+\)\K#",
                $wpdb->prepare($sql, $like),
                $search
          );
    }

    return $search;
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
        'post_type' => 'submission',
        'post_status' => 'publish'
    ];

    $post_id = wp_insert_post($postarr);

    foreach($params as $label => $value) {
        $message .= '<strong>' . ucfirst($label) . '</strong>: ' . $value . '<br />';

        add_post_meta($post_id, $label, sanitize_text_field($value) );
    }

    wp_mail($admin_email, $subject, $message, $headers);

    return new WP_Rest_Response('Message has been sent successfully.', 200);
}