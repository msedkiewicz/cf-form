<?php

/*

Plugin Name: Simple Contact Form
Plugin URI: https://lenasedkiewicz.com/
Description: Simple contact form
Version: 1.0
Requires at least: 6.2
Requires PHP: 8.0
Author: Lena Sędkiewicz
Author URI: https://msedkiewicz.pl/
License: GPLv2 or later
Text Domain: cfform
Domain Path:  /languages

*/

if( !defined('ABSPATH') ){
    die('Go and watch LOTR!');
};

if( !class_exists('CFFormMsedkiewicz')) {
    class CFFormMsedkiewicz {

        public function __construct()
        {
            require_once( plugin_dir_path( __FILE__ ) . '/vendor/autoload.php');
        }
    }
    new CFFormMsedkiewicz;
}