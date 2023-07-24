<?php

add_shortcode('contact', 'cfmsedkiewicz_show_contact_form');

function cfmsedkiewicz_show_contact_form()
{
    include CFFORM_PATH . '/includes/templates/contact-form.php';
}