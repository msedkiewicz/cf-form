<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('after_setup_theme', 'cfmsedkiewicz_load_carbon_fields');
add_action('carbon_fields_register_fields', 'cfmsedkiewicz_create_options_page');

function cfmsedkiewicz_load_carbon_fields() {
    \Carbon_Fields\Carbon_Fields::boot();
}

function cfmsedkiewicz_create_options_page() {
    Container::make( 'theme_options', 'CF Form Options' )
    ->set_icon('dashicons-email-alt')
    ->add_fields( array(
        Field::make( 'checkbox', 'cfform_plugin_active', 'Is Active' ),

        Field::make( 'text', 'cfform_plugin_recipients', __( 'Recipient Email') )
            ->set_attribute( 'placeholder', 'youremail@gmail.com' )
            ->set_help_text('The email that the form is submitted to'),

        Field::make( 'textarea', 'cfform_plugin_message', __( 'Confirmation Message' ) )
            ->set_attribute( 'placeholder', 'Enter confirmation message' )
            ->set_help_text('Type the message you want the submitter to receive'),
    ) );
}