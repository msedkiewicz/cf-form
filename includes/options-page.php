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
    ->add_fields( array(
        Field::make( 'text', 'crb_facebook_url') ,
        Field::make( 'textarea', 'crb_footer_text' )
    ) );
}