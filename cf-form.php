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
            define('CFFORM_PATH', plugin_dir_path( __FILE__ ));
            require_once( CFFORM_PATH . '/vendor/autoload.php');
        }

        public function initialize(){
            include_once CFFORM_PATH . 'includes/utilities.php';

            include_once CFFORM_PATH . 'includes/options-page.php';

            include_once CFFORM_PATH . 'includes/contact-form.php';
        }
    }
$cfForm = new CFFormMsedkiewicz;
$cfForm->initialize();
}