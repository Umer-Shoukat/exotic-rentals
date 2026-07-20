<?php
/**
 * Core theme setup: supports, menus, image sizes.
 */

if (!defined('ABSPATH')) {
    exit;
}

function echelon_setup() {
    load_theme_textdomain('echelon', ECHELON_THEME_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
        'navigation-widgets',
    ]);
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 60,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('responsive-embeds');
    add_theme_support('automatic-feed-links');

    register_nav_menus([
        'primary' => __('Primary Navigation', 'echelon'),
        'footer'  => __('Footer Quick Links', 'echelon'),
    ]);

    add_image_size('vehicle-card', 640, 480, true);
    add_image_size('vehicle-hero', 1600, 1000, true);
    add_image_size('content-card', 640, 480, true);
    add_image_size('avatar-sm', 96, 96, true);

    $GLOBALS['content_width'] = 1320;
}
add_action('after_setup_theme', 'echelon_setup');

/**
 * Register a default "Home" front page + primary menu on first activation
 * only if nothing is configured yet, so the site isn't blank after install.
 */
function echelon_after_switch_theme() {
    if ('page' !== get_option('show_on_front')) {
        $home = get_page_by_path('home');
        if (!$home) {
            $home_id = wp_insert_post([
                'post_title'   => 'Home',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'page_template' => 'front-page.php',
            ]);
        } else {
            $home_id = $home->ID;
        }
        if ($home_id) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $home_id);
        }
    }

    echelon_install_starter_content();
}
add_action('after_switch_theme', 'echelon_after_switch_theme');

/**
 * Create the functional reservation page and sample service zones once.
 * Sample locations are intentionally marked so they cannot be mistaken for
 * launch-ready business information.
 */
function echelon_install_starter_content() {
    $reservation_page = get_page_by_path('reservation');
    if (!$reservation_page) {
        $reservation_id = wp_insert_post([
            'post_title' => 'Reservation', 'post_name' => 'reservation',
            'post_status' => 'publish', 'post_type' => 'page',
            'page_template' => 'page-reservation.php',
        ]);
        if ($reservation_id && !is_wp_error($reservation_id)) {
            update_post_meta($reservation_id, '_wp_page_template', 'page-reservation.php');
        }
    } else {
        update_post_meta($reservation_page->ID, '_wp_page_template', 'page-reservation.php');
    }

    $existing_locations = get_posts(['post_type' => 'location', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids']);
    $samples = [
        ['Manhattan Demo Zone', 40.7580, -73.9855, 'Midtown concierge delivery'],
        ['Brooklyn Demo Zone', 40.6782, -73.9442, 'Brooklyn-wide concierge delivery'],
        ['Queens Demo Zone', 40.7282, -73.7949, 'Queens and airport delivery requests'],
        ['Bronx Demo Zone', 40.8448, -73.8648, 'Bronx concierge delivery'],
        ['Staten Island Demo Zone', 40.5795, -74.1502, 'Staten Island delivery requests'],
        ['Jersey City Demo Zone', 40.7178, -74.0431, 'Jersey City concierge delivery'],
        ['Newark Demo Zone', 40.7357, -74.1724, 'Newark and airport delivery requests'],
        ['Hoboken Demo Zone', 40.7430, -74.0324, 'Hoboken concierge delivery'],
        ['Stamford Demo Zone', 41.0534, -73.5387, 'Stamford concierge delivery'],
        ['Greenwich Demo Zone', 41.0262, -73.6282, 'Greenwich concierge delivery'],
    ];
    foreach ($samples as $order => [$title, $latitude, $longitude, $description]) {
        if (count($existing_locations) >= 10) {
            break;
        }
        if (get_page_by_title($title, OBJECT, 'location')) {
            continue;
        }
        $id = wp_insert_post([
            'post_title' => $title, 'post_status' => 'publish', 'post_type' => 'location',
            'menu_order' => $order,
        ]);
        if (!$id || is_wp_error($id)) {
            continue;
        }
        $existing_locations[] = $id;
        $fields = [
            'description' => $description . ' (sample data — replace before launch)',
            'address' => 'Sample service zone — replace with the operational address before launch',
            'phone' => '+1 (000) 000-0000', 'latitude' => $latitude, 'longitude' => $longitude,
            'is_active' => 1,
        ];
        foreach ($fields as $name => $value) {
            if (function_exists('update_field')) {
                update_field($name, $value, $id);
            } else {
                update_post_meta($id, $name, $value);
            }
        }
    }

    $coordinate_defaults = [
        'Manhattan' => [40.7580, -73.9855], 'Brooklyn' => [40.6782, -73.9442],
        'New Jersey' => [40.7178, -74.0431], 'Connecticut' => [41.0534, -73.5387],
        'Los Angeles' => [34.0522, -118.2437], 'Dallas' => [32.7767, -96.7970],
        'Manhattan Demo Zone' => [40.7580, -73.9855], 'Brooklyn Demo Zone' => [40.6782, -73.9442],
        'Queens Demo Zone' => [40.7282, -73.7949], 'Bronx Demo Zone' => [40.8448, -73.8648],
    ];
    foreach (get_posts(['post_type' => 'location', 'post_status' => 'any', 'posts_per_page' => -1]) as $location) {
        $title = get_the_title($location);
        if (!isset($coordinate_defaults[$title])) {
            continue;
        }
        [$latitude, $longitude] = $coordinate_defaults[$title];
        foreach (['latitude' => $latitude, 'longitude' => $longitude] as $name => $value) {
            if (echelon_field($name, $location->ID, '') !== '') {
                continue;
            }
            function_exists('update_field') ? update_field($name, $value, $location->ID) : update_post_meta($location->ID, $name, $value);
        }
    }
}

function echelon_maybe_install_starter_content() {
    if (get_option('echelon_starter_content_version') === '3') {
        return;
    }
    echelon_install_starter_content();
    update_option('echelon_starter_content_version', '3', false);
}
add_action('init', 'echelon_maybe_install_starter_content', 99);
