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
}
add_action('after_switch_theme', 'echelon_after_switch_theme');
