<?php
/**
 * Box To Box Pages - Install Script
 *
 * Sets the page template meta for pages whose slugs match the plugin's page map.
 * Run once via: wp eval-file wp-content/plugins/boxtobox-wp/install.php
 * Or include/require from a one-off admin request.
 */

if ( ! defined( 'ABSPATH' ) ) {
    // Allow running via WP-CLI (wp eval-file).
    $wp_load = dirname( __FILE__ ) . '/../../../wp-load.php';
    if ( file_exists( $wp_load ) ) {
        require_once $wp_load;
    } else {
        die( "Cannot locate wp-load.php. Run this from inside the WordPress plugins directory.\n" );
    }
}

require_once plugin_dir_path( __FILE__ ) . 'boxtobox-pages.php';

$map     = boxtobox_get_page_map();
$updated = 0;

foreach ( array_keys( $map ) as $slug ) {
    $page = get_page_by_path( $slug );

    if ( ! $page ) {
        // Create the page if it does not exist.
        $page_id = wp_insert_post( array(
            'post_title'  => ucwords( str_replace( 'boxtobox-', 'Box To Box - ', str_replace( 'boxtobox', 'Box To Box', $slug ) ) ),
            'post_name'   => $slug,
            'post_status' => 'publish',
            'post_type'   => 'page',
        ) );

        if ( is_wp_error( $page_id ) ) {
            echo "ERROR creating page '{$slug}': " . $page_id->get_error_message() . "\n";
            continue;
        }
    } else {
        $page_id = $page->ID;
    }

    update_post_meta( $page_id, '_wp_page_template', BOXTOBOX_TEMPLATE_SLUG );
    echo "OK: '{$slug}' (ID {$page_id}) -> template set to " . BOXTOBOX_TEMPLATE_SLUG . "\n";
    $updated++;
}

echo "\nDone. {$updated} page(s) processed.\n";
