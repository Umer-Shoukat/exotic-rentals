<?php
/**
 * Global site settings (contact info, socials, footer) via the WP
 * Customizer — used instead of an ACF Options Page since Options Pages
 * require ACF PRO and this theme targets the free plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

function echelon_customize_register(WP_Customize_Manager $wp_customize) {
    $wp_customize->add_panel('echelon_theme_settings', [
        'title'    => __('Theme Settings', 'echelon'),
        'priority' => 30,
    ]);

    // Contact
    $wp_customize->add_section('echelon_contact', [
        'title' => __('Contact Info', 'echelon'),
        'panel' => 'echelon_theme_settings',
    ]);

    $contact_fields = [
        'contact_address' => ['label' => __('Address', 'echelon'), 'default' => '8500 Beverly Blvd, Los Angeles CA'],
        'contact_email'   => ['label' => __('Email', 'echelon'), 'default' => 'concierge@exoticrental.com'],
        'contact_phone'   => ['label' => __('Phone', 'echelon'), 'default' => '+1 (310) 555-0199'],
    ];
    foreach ($contact_fields as $id => $field) {
        $wp_customize->add_setting($id, [
            'default'           => $field['default'],
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control($id, [
            'label'   => $field['label'],
            'section' => 'echelon_contact',
            'type'    => 'text',
        ]);
    }

    // Social
    $wp_customize->add_section('echelon_social', [
        'title' => __('Social Links', 'echelon'),
        'panel' => 'echelon_theme_settings',
    ]);

    foreach (['instagram', 'facebook', 'x'] as $network) {
        $id = 'social_' . $network;
        $wp_customize->add_setting($id, [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control($id, [
            'label'   => ucfirst($network) . ' ' . __('URL', 'echelon'),
            'section' => 'echelon_social',
            'type'    => 'url',
        ]);
    }

    // Google Static Maps
    $wp_customize->add_section('echelon_maps', [
        'title'       => __('Google Maps', 'echelon'),
        'description' => __('Optional Static Maps configuration for the Serving Cities section. The uploaded map image remains the fallback.', 'echelon'),
        'panel'       => 'echelon_theme_settings',
    ]);

    $map_fields = [
        'google_maps_api_key' => [
            'label'       => __('Static Maps API Key', 'echelon'),
            'description' => __('Restrict this key to the Maps Static API and your website referrers in Google Cloud.', 'echelon'),
            'default'     => '',
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
        ],
        'google_maps_signing_secret' => [
            'label'       => __('URL Signing Secret (optional)', 'echelon'),
            'description' => __('Recommended for production. This value is used server-side and is never printed directly.', 'echelon'),
            'default'     => '',
            'type'        => 'password',
            'sanitize'    => 'sanitize_text_field',
        ],
        'google_maps_center_lat' => [
            'label'    => __('Map Center Latitude', 'echelon'),
            'default'  => '40.730610',
            'type'     => 'number',
            'sanitize' => 'echelon_sanitize_latitude',
        ],
        'google_maps_center_lng' => [
            'label'    => __('Map Center Longitude', 'echelon'),
            'default'  => '-74.006000',
            'type'     => 'number',
            'sanitize' => 'echelon_sanitize_longitude',
        ],
        'google_maps_zoom' => [
            'label'    => __('Map Zoom', 'echelon'),
            'default'  => 8,
            'type'     => 'number',
            'sanitize' => 'echelon_sanitize_map_zoom',
        ],
    ];

    foreach ($map_fields as $id => $field) {
        $wp_customize->add_setting($id, [
            'default'           => $field['default'],
            'sanitize_callback' => $field['sanitize'],
        ]);
        $wp_customize->add_control($id, [
            'label'       => $field['label'],
            'description' => $field['description'] ?? '',
            'section'     => 'echelon_maps',
            'type'        => $field['type'],
            'input_attrs' => $id === 'google_maps_zoom' ? ['min' => 1, 'max' => 20, 'step' => 1] : ['step' => '0.000001'],
        ]);
    }

    // Footer
    $wp_customize->add_section('echelon_footer', [
        'title' => __('Footer', 'echelon'),
        'panel' => 'echelon_theme_settings',
    ]);

    $wp_customize->add_setting('footer_tagline', [
        'default'           => 'Handpicked exotic and luxury cars. Delivered, insured, and ready when you are.',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('footer_tagline', [
        'label'   => __('Tagline', 'echelon'),
        'section' => 'echelon_footer',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('footer_copyright', [
        'default'           => sprintf(__('© %s Exotic Rental. All rights reserved.', 'echelon'), gmdate('Y')),
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('footer_copyright', [
        'label'   => __('Copyright Text', 'echelon'),
        'section' => 'echelon_footer',
        'type'    => 'text',
    ]);
}
add_action('customize_register', 'echelon_customize_register');

function echelon_sanitize_latitude($value) {
    return (string) max(-85, min(85, (float) $value));
}

function echelon_sanitize_longitude($value) {
    return (string) max(-180, min(180, (float) $value));
}

function echelon_sanitize_map_zoom($value) {
    return max(1, min(20, absint($value)));
}

/**
 * theme_mod getter with a plain-value fallback.
 */
function echelon_setting($key, $default = '') {
    return get_theme_mod($key, $default);
}
