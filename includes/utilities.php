<?php

if( !defined('ABSPATH') ){
    die('Go and watch LOTR!');
};

function cfmsedkiewicz_get_plugin_options($name) {
    return carbon_get_theme_option( $name );
}