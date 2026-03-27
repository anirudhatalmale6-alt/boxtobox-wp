<?php
/**
 * Plugin Name: Box To Box Pages
 * Description: Standalone HTML pages for the Box To Box boxing event site
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'BOXTOBOX_TEMPLATE_SLUG', 'boxtobox-standalone' );

/**
 * Slug-to-file mapping.
 */
function boxtobox_get_page_map() {
    return array(
        'boxtobox'          => 'pages/index.html',
        'boxtobox-tickets'  => 'pages/tickets.html',
        'boxtobox-live'     => 'pages/live.html',
        'boxtobox-sponsors' => 'pages/sponsors.html',
        'boxtobox-media'    => 'pages/media.html',
        'boxtobox-about'    => 'pages/about.html',
        'boxtobox-contact'  => 'pages/contact.html',
    );
}

/**
 * Register the custom page template so it appears in the Page Attributes dropdown.
 */
add_filter( 'theme_page_templates', function ( $templates ) {
    $templates[ BOXTOBOX_TEMPLATE_SLUG ] = 'Box To Box Standalone';
    return $templates;
} );

/**
 * Intercept page requests that use our template and serve raw HTML.
 */
add_action( 'template_redirect', function () {
    if ( ! is_page() ) {
        return;
    }

    $post = get_queried_object();
    if ( ! $post || get_page_template_slug( $post->ID ) !== BOXTOBOX_TEMPLATE_SLUG ) {
        return;
    }

    $map  = boxtobox_get_page_map();
    $slug = $post->post_name;

    if ( ! isset( $map[ $slug ] ) ) {
        return;
    }

    $file = plugin_dir_path( __FILE__ ) . $map[ $slug ];

    if ( ! file_exists( $file ) ) {
        wp_die(
            'Box To Box: HTML file not found (' . esc_html( $map[ $slug ] ) . ').',
            'File Not Found',
            array( 'response' => 404 )
        );
    }

    $html = file_get_contents( $file );

    // Rewrite relative image paths to the plugin URL.
    $img_url = plugin_dir_url( __FILE__ ) . 'pages/img/';
    $html    = str_replace( 'src="img/', 'src="' . esc_url( $img_url ), $html );
    $html    = str_replace( "src='img/", "src='" . esc_url( $img_url ), $html );
    $html    = str_replace( 'url(img/',  'url(' . esc_url( $img_url ),  $html );

    header( 'Content-Type: text/html; charset=UTF-8' );
    echo $html;
    die();
} );
