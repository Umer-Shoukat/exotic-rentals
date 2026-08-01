<?php
/**
 * Private, revisionable storage for public archive-page content.
 *
 * ACF Options Pages require ACF Pro. These singleton post types provide the
 * same editorial workflow using standard post meta, while remaining hidden
 * from the public site and from top-level admin navigation.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Archive configuration shared by registration, admin links, and helpers.
 */
function echelon_archive_content_types() {
    return [
        'fleet' => [
            'post_type' => 'fleet_archive',
            'title'     => __('Fleet Archive', 'echelon'),
            'parent'    => 'edit.php?post_type=fleet_vehicle',
        ],
        'services' => [
            'post_type' => 'service_archive',
            'title'     => __('Services Archive', 'echelon'),
            'parent'    => 'edit.php?post_type=service',
        ],
        'locations' => [
            'post_type' => 'location_archive',
            'title'     => __('Locations Archive', 'echelon'),
            'parent'    => 'edit.php?post_type=location',
        ],
    ];
}

/** Register three deliberately separate types so ACF Free can target them. */
function echelon_register_archive_content_types() {
    foreach (echelon_archive_content_types() as $config) {
        register_post_type($config['post_type'], [
            'labels' => [
                'name'          => $config['title'],
                'singular_name' => $config['title'],
                'edit_item'     => sprintf(__('Edit %s', 'echelon'), $config['title']),
            ],
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_rest'        => false,
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'supports'            => ['revisions'],
            'capability_type'     => 'page',
            'map_meta_cap'        => true,
            'capabilities'        => [
                'create_posts' => 'do_not_allow',
            ],
        ]);
    }
}
add_action('init', 'echelon_register_archive_content_types', 8);

/**
 * Return the singleton ID, creating it only after its post type is registered.
 */
function echelon_archive_content_id($archive, $create = true) {
    $types = echelon_archive_content_types();
    if (!isset($types[$archive])) {
        return 0;
    }

    $post_type = $types[$archive]['post_type'];
    $option_key = 'echelon_' . $post_type . '_id';
    $stored_id = absint(get_option($option_key));
    $stored_status = $stored_id ? get_post_status($stored_id) : false;
    if ($stored_id && get_post_type($stored_id) === $post_type && $stored_status && 'trash' !== $stored_status) {
        return $stored_id;
    }

    $existing = get_posts([
        'post_type'              => $post_type,
        'post_status'            => ['private', 'draft', 'publish'],
        'posts_per_page'         => 1,
        'fields'                 => 'ids',
        'orderby'                => 'ID',
        'order'                  => 'ASC',
        'no_found_rows'          => true,
        'suppress_filters'       => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ]);
    if ($existing) {
        update_option($option_key, (int) $existing[0], false);
        return (int) $existing[0];
    }

    if (!$create || !post_type_exists($post_type)) {
        return 0;
    }

    $post_id = wp_insert_post([
        'post_type'   => $post_type,
        'post_status' => 'private',
        'post_title'  => $types[$archive]['title'],
        'post_name'   => sanitize_title($types[$archive]['title']),
    ], true);
    if (is_wp_error($post_id)) {
        return 0;
    }

    update_option($option_key, (int) $post_id, false);
    return (int) $post_id;
}

/** Ensure all singleton records exist before admin links are rendered. */
function echelon_ensure_archive_content_records() {
    foreach (array_keys(echelon_archive_content_types()) as $archive) {
        echelon_archive_content_id($archive, true);
    }
}
add_action('init', 'echelon_ensure_archive_content_records', 12);

/** Add a direct Archive Settings link beneath each matching content type. */
function echelon_add_archive_content_submenus() {
    foreach (echelon_archive_content_types() as $archive => $config) {
        $post_id = echelon_archive_content_id($archive, false);
        if (!$post_id) {
            continue;
        }
        add_submenu_page(
            $config['parent'],
            $config['title'],
            __('Archive Settings', 'echelon'),
            'edit_pages',
            'post.php?post=' . $post_id . '&action=edit'
        );
    }
}
add_action('admin_menu', 'echelon_add_archive_content_submenus', 30);

/** Remove creation affordances; each type owns exactly one managed record. */
function echelon_archive_content_admin_cleanup() {
    foreach (echelon_archive_content_types() as $config) {
        remove_submenu_page('edit.php?post_type=' . $config['post_type'], 'post-new.php?post_type=' . $config['post_type']);
    }
}
add_action('admin_menu', 'echelon_archive_content_admin_cleanup', 999);

/** Singleton archive records are infrastructure and cannot be deleted. */
function echelon_protect_archive_content_records($caps, $cap, $user_id, $args) {
    if ('delete_post' !== $cap || empty($args[0])) {
        return $caps;
    }

    $protected_types = wp_list_pluck(echelon_archive_content_types(), 'post_type');
    if (in_array(get_post_type((int) $args[0]), $protected_types, true)) {
        return ['do_not_allow'];
    }

    return $caps;
}
add_filter('map_meta_cap', 'echelon_protect_archive_content_records', 10, 4);

/**
 * Fetch an archive field from its singleton, with legacy Options Page and
 * code-default fallbacks during and after migration.
 */
function echelon_archive_field($archive, $selector, $default = '') {
    $post_id = echelon_archive_content_id($archive, false);
    if ($post_id && function_exists('get_field')) {
        $value = get_field($selector, $post_id);
        if ($value !== null && $value !== false && $value !== '') {
            return $value;
        }

        $legacy = get_field($selector, 'option');
        if ($legacy !== null && $legacy !== false && $legacy !== '') {
            return $legacy;
        }
    }
    return $default;
}

/** Copy real legacy Options Page values once; ACF field defaults are ignored. */
function echelon_migrate_archive_option_fields() {
    if (!function_exists('get_field') || !function_exists('update_field') || get_option('echelon_archive_content_migrated_v1')) {
        return;
    }

    $fields = [
        'services' => [
            'services_hero_eyebrow', 'services_hero_title', 'services_hero_description', 'services_hero_image',
            'services_hero_primary_cta', 'services_hero_secondary_cta', 'services_proof_items',
            'services_list_eyebrow', 'services_list_title', 'services_list_description',
            'services_steps_eyebrow', 'services_steps_heading', 'services_steps',
        ],
        'locations' => [
            'locations_hero_eyebrow', 'locations_hero_title', 'locations_hero_accent', 'locations_hero_description',
            'locations_hero_image', 'locations_hero_primary_cta', 'locations_hero_secondary_cta',
            'locations_hero_trust_items', 'locations_proof_items', 'locations_list_eyebrow',
            'locations_list_heading', 'locations_list_accent', 'locations_view_label', 'locations_book_label',
            'locations_benefits_eyebrow', 'locations_benefits_heading', 'locations_benefits_accent', 'locations_benefits',
        ],
    ];

    foreach ($fields as $archive => $selectors) {
        $post_id = echelon_archive_content_id($archive, false);
        if (!$post_id) {
            continue;
        }
        foreach ($selectors as $selector) {
            if (false === get_option('options_' . $selector, false)) {
                continue;
            }
            $value = get_field($selector, 'option');
            if ($value !== null && $value !== false && $value !== '') {
                update_field($selector, $value, $post_id);
            }
        }
    }

    update_option('echelon_archive_content_migrated_v1', gmdate('c'), false);
}
add_action('init', 'echelon_migrate_archive_option_fields', 25);
