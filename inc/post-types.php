<?php
/**
 * Custom post types + taxonomy for the fleet, testimonials, occasions,
 * locations, and FAQs.
 */

if (!defined('ABSPATH')) {
    exit;
}

function echelon_register_post_types() {
    register_post_type('fleet_vehicle', [
        'labels' => [
            'name'               => __('Fleet Vehicles', 'echelon'),
            'singular_name'      => __('Vehicle', 'echelon'),
            'add_new_item'       => __('Add New Vehicle', 'echelon'),
            'edit_item'          => __('Edit Vehicle', 'echelon'),
            'all_items'          => __('Fleet', 'echelon'),
            'menu_name'          => __('Fleet', 'echelon'),
        ],
        'public'       => true,
        'has_archive'  => 'fleet',
        'rewrite'      => ['slug' => 'fleet'],
        'menu_icon'    => 'dashicons-car',
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
    ]);

    register_taxonomy('vehicle_category', 'fleet_vehicle', [
        'labels' => [
            'name'          => __('Vehicle Categories', 'echelon'),
            'singular_name' => __('Category', 'echelon'),
        ],
        'public'            => true,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'fleet-category'],
        'show_in_rest'      => true,
    ]);

    register_post_type('testimonial', [
        'labels' => [
            'name'          => __('Testimonials', 'echelon'),
            'singular_name' => __('Testimonial', 'echelon'),
            'add_new_item'  => __('Add New Testimonial', 'echelon'),
            'edit_item'     => __('Edit Testimonial', 'echelon'),
        ],
        'public'       => false,
        'show_ui'      => true,
        'menu_icon'    => 'dashicons-format-quote',
        'supports'     => ['title'],
        'show_in_rest' => true,
    ]);

    register_post_type('occasion', [
        'labels' => [
            'name'          => __('Occasions', 'echelon'),
            'singular_name' => __('Occasion', 'echelon'),
            'add_new_item'  => __('Add New Occasion', 'echelon'),
            'edit_item'     => __('Edit Occasion', 'echelon'),
        ],
        'public'       => true,
        'has_archive'  => false,
        'rewrite'      => ['slug' => 'occasions'],
        'menu_icon'    => 'dashicons-star-filled',
        'supports'     => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
    ]);

    register_post_type('location', [
        'labels' => [
            'name'          => __('Locations', 'echelon'),
            'singular_name' => __('Location', 'echelon'),
            'add_new_item'  => __('Add New Location', 'echelon'),
            'edit_item'     => __('Edit Location', 'echelon'),
        ],
        'public'       => true,
        'has_archive'  => 'locations',
        'rewrite'      => ['slug' => 'locations'],
        'menu_icon'    => 'dashicons-location',
        'supports'     => ['title', 'editor'],
        'show_in_rest' => true,
    ]);

    register_post_type('faq', [
        'labels' => [
            'name'          => __('FAQs', 'echelon'),
            'singular_name' => __('FAQ', 'echelon'),
            'add_new_item'  => __('Add New FAQ', 'echelon'),
            'edit_item'     => __('Edit FAQ', 'echelon'),
        ],
        'public'       => false,
        'show_ui'      => true,
        'menu_icon'    => 'dashicons-editor-help',
        'supports'     => ['title', 'editor', 'page-attributes'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'echelon_register_post_types');
