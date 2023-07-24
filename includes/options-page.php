<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;


add_action('after_setup_theme', 'load_carbon_fields');

function load_carbon_fields() {
    \Carbon_Fields\Carbon_Fields::boot();
}