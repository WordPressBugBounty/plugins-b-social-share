<?php
/**
 * Plugin Name: Social Share Block – Social Sharing Buttons for Posts and Pages
 * Plugin URI: https://bplugins.com/products/b-social-share
 * Description: Share your website/website-page link to social networks and mobile messengers
 * Version: 2.2.3
 * Author: bPlugins
 * Author URI: https://bplugins.com
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: b-social-share
 * Domain Path: /languages
 * Requires at least: 6.5
 * Tested up to: 7.0
 * Requires PHP: 7.4
 */ 
 
if (!defined('ABSPATH')) {
    exit;
}

define('BSSB_PLUGIN_VERSION', '2.2.2');
define('BSSB_DIR_PATH', plugin_dir_path(__FILE__));
define('BSSB_DIR_URL', plugin_dir_url(__FILE__)); 

if (function_exists('bssb_fs')) {
    bssb_fs()->set_basename(true, __FILE__);
} else {
    function bssb_fs()
    {
        global $bssb_fs;

        if (!isset($bssb_fs)) {
            require_once BSSB_DIR_PATH . 'vendor/freemius-lite/start.php';

            $config = array(
                'id' => '20173',
                'slug' => 'b-social-share',
                'premium_slug' => 'b-social-share-pro',
                'type' => 'plugin',
                'public_key' => 'pk_d6bd48e230834275b20b6a3ee1a68',
                'is_premium' => false,
                'menu' => array(
                    'slug' => 'edit.php?post_type=social_share_cpt',
                    'first-path' => 'edit.php?post_type=social_share_cpt&page=social_share_Dashboard',
                    'contact' => false,
                    'support' => false,
                    'affiliation' => false
                ),
            );

            $bssb_fs = fs_lite_dynamic_init($config);
        }
        return $bssb_fs;
    }

    bssb_fs();
    do_action('bssb_fs_loaded');
}

require_once BSSB_DIR_PATH . 'inc/class_bssPlugin.php';

register_activation_hook(__FILE__, 'bssb_activate');
register_deactivation_hook(__FILE__, 'bssb_deactivate');

function bssb_activate() {
    BSSB_Plugin::bssb_social_share_block_post_type();
    flush_rewrite_rules();
}

function bssb_deactivate() {
    flush_rewrite_rules();
}

new BSSB_Plugin();