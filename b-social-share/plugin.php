<?php

/**
 * Plugin Name: B Social Share - Block
 * Description: Share your website/website-page link to social networks and mobile messengers
 * Version: 2.2.0
 * Author: bPlugins
 * Author URI: https://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: social-share
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'bss_fs' ) ) {
    bss_fs()->set_basename( false, __FILE__ );
} else {
    define( 'BSSB_PLUGIN_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && ('localhost' === $_SERVER['HTTP_HOST'] || 'counter-block.local' === $_SERVER['HTTP_HOST']) ? time() : '2.2.0' ) );
    define( 'BSSB_DIR_PATH', plugin_dir_path( __FILE__ ) );
    define( 'BSSB_DIR_URL', plugin_dir_url( __FILE__ ) );
    define( "BSSB_DIR", __DIR__ );
    define( 'BSSB_HAS_PRO', file_exists( BSSB_DIR_PATH . 'vendor/freemius/start.php' ) );
    if ( !function_exists( 'bss_fs' ) ) {
        function bss_fs() {
            global $bss_fs;
            if ( !isset( $bss_fs ) ) {
                $fsLitePath = BSSB_DIR_PATH . 'vendor/freemius-lite/start.php';
                $fsPath = BSSB_DIR_PATH . 'vendor/freemius/start.php';
                if ( BSSB_HAS_PRO && file_exists( $fsPath ) ) {
                    require_once $fsPath;
                } else {
                    require_once $fsLitePath;
                }
                $config = array(
                    'id'                  => '20173',
                    'slug'                => 'b-social-share',
                    'premium_slug'        => 'b-social-share-pro',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_d6bd48e230834275b20b6a3ee1a68',
                    'is_premium'          => false,
                    'premium_suffix'      => 'Pro',
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'menu'                => array(
                        'slug'       => 'edit.php?post_type=social_share_cpt',
                        'first-path' => 'edit.php?post_type=social_share_cpt&page=social_share_Dashboard',
                    ),
                );
                $bss_fs = ( BSSB_HAS_PRO && file_exists( $fsPath ) ? fs_dynamic_init( $config ) : fs_lite_dynamic_init( $config ) );
            }
            return $bss_fs;
        }

        bss_fs();
        do_action( 'bss_fs_loaded' );
    }
    require_once BSSB_DIR_PATH . 'inc/class_bssPlugin.php';
    new bssPlugin();
    if ( BSSB_HAS_PRO ) {
        require_once BSSB_DIR_PATH . 'inc/LicenseActivation.php';
    }
}