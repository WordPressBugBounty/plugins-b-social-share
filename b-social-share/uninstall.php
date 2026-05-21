<?php
/**
 * Uninstall Cleanup
 *
 * @package BSocialShare
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete custom post type posts correctly using wp_delete_post loop to trigger hooks and cleanup relations.
$bssb_posts = get_posts( [
	'post_type'        => 'social_share_cpt',
	'post_status'      => 'any',
	'numberposts'      => -1,
	'fields'           => 'ids',
] );

if ( ! empty( $bssb_posts ) ) {
	foreach ( $bssb_posts as $bssb_post_id ) {
		wp_delete_post( $bssb_post_id, true );
	}
}

// Clean up plugin options
delete_option('fs_lite_accounts');
delete_option('fs_lite_unique_id');
delete_option('b-social-share-opt_in');
delete_option('b-social-share-marketing-allowed');
delete_option('b-social-share-redirect');
delete_option('b-social-share_opt_in');
delete_option('b-social-share_marketing-allowed');
delete_option('b-social-share_redirect');
