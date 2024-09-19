<?php

/**
 * Plugin Name:       Gravity Forms Maxpay Add-On
 * Plugin URI:        https://mizanexpert.com/
 * Description:       Integrates Gravity Forms with Maxpay Payment Gateway.
 * Version:           1.0.0
 * Author:            MizanExpert
 * Author URI:        https://mizanexpert.com/
 * Text Domain:       gravityformsmaxpay
 * Domain Path:       /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define Constants.
define('GF_MAXPAY_VERSION', '1.0.0');
define('GF_MAXPAY_DIR', plugin_dir_path(__FILE__));

require GF_MAXPAY_DIR . '/vendor/autoload.php';

add_action('gform_loaded', array('GF_Maxpay_Bootstrap', 'load'), 5);

class GF_Maxpay_Bootstrap
{

    public static function load()
    {

        if (!method_exists('GFForms', 'include_payment_addon_framework')) {
            return;
        }

        require_once('class-gf-maxpay.php');

        GFAddOn::register('GFMaxpay');
    }
}

function gf_maxpay()
{
    return GFMaxpay::get_instance();
}
