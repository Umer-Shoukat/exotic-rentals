<?php
/**
 * Custom post types + taxonomy for services, fleet, testimonials,
 * locations, and FAQs.
 */

if (!defined('ABSPATH')) {
    exit;
}

function echelon_register_post_types() {
    register_post_type('service', [
        'labels' => [
            'name'               => __('Services', 'echelon'),
            'singular_name'      => __('Service', 'echelon'),
            'add_new_item'       => __('Add New Service', 'echelon'),
            'edit_item'          => __('Edit Service', 'echelon'),
            'all_items'          => __('All Services', 'echelon'),
            'menu_name'          => __('Services', 'echelon'),
        ],
        'public'              => true,
        'hierarchical'        => true,
        'has_archive'         => 'services',
        'rewrite'             => ['slug' => 'services', 'with_front' => false, 'hierarchical' => true],
        'menu_icon'           => 'dashicons-star-filled',
        'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
        'show_in_rest'        => true,
        'publicly_queryable'  => true,
        'exclude_from_search' => false,
    ]);

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
        'supports'     => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
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

    register_post_type('rental_reservation', [
        'labels' => [
            'name'          => __('Reservations', 'echelon'),
            'singular_name' => __('Reservation', 'echelon'),
            'all_items'     => __('Reservations', 'echelon'),
            'edit_item'     => __('Review Reservation', 'echelon'),
            'menu_name'     => __('Reservations', 'echelon'),
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-calendar-alt',
        'supports'            => ['title'],
        'capability_type'     => 'post',
        'capabilities'        => ['create_posts' => 'do_not_allow'],
        'map_meta_cap'        => true,
        'exclude_from_search' => true,
        'show_in_rest'        => false,
    ]);

    register_post_type('instagram_item', [
        'labels' => [
            'name'          => __('Instagram Feed', 'echelon'),
            'singular_name' => __('Instagram Item', 'echelon'),
            'add_new_item'  => __('Add Instagram Item', 'echelon'),
            'edit_item'     => __('Edit Instagram Item', 'echelon'),
            'menu_name'     => __('Instagram Feed', 'echelon'),
        ],
        'public'              => false,
        'show_ui'             => true,
        'menu_icon'           => 'dashicons-instagram',
        'supports'            => ['title', 'thumbnail', 'excerpt', 'page-attributes'],
        'show_in_rest'        => true,
        'exclude_from_search' => true,
    ]);
}
add_action('init', 'echelon_register_post_types');

/** Flush routes once when the public content hierarchy changes. */
function echelon_maybe_flush_rewrite_rules() {
    $rewrite_version = 'services-v3';
    if (get_option('echelon_rewrite_version') === $rewrite_version) {
        return;
    }

    flush_rewrite_rules(false);
    update_option('echelon_rewrite_version', $rewrite_version, false);
}
add_action('init', 'echelon_maybe_flush_rewrite_rules', 99);

/**
 * Consolidate the retired Occasion content type into Services without
 * changing post IDs, media, authors, dates, or custom-field data.
 */
function echelon_migrate_occasions_to_services() {
    if (get_option('echelon_services_consolidated')) {
        return;
    }

    global $wpdb;
    $legacy_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s",
        'occasion'
    ));

    foreach (array_map('absint', $legacy_ids) as $post_id) {
        $post = get_post($post_id);
        if (!$post) {
            continue;
        }

        $legacy_description = (string) get_post_meta($post_id, 'description', true);
        if (!$post->post_excerpt && $legacy_description) {
            wp_update_post(['ID' => $post_id, 'post_excerpt' => $legacy_description]);
        }
        foreach (['service_kicker', 'service_menu_description', 'service_hero_description'] as $meta_key) {
            if (!metadata_exists('post', $post_id, $meta_key) && $legacy_description) {
                update_post_meta($post_id, $meta_key, $legacy_description);
            }
        }

        $wpdb->update($wpdb->posts, ['post_type' => 'service'], ['ID' => $post_id], ['%s'], ['%d']);
        clean_post_cache($post_id);
    }

    update_option('echelon_services_consolidated', gmdate('c'), false);
}
add_action('init', 'echelon_migrate_occasions_to_services', 20);

/** Seed a small, editable starter set without duplicating existing services. */
function echelon_seed_sample_services() {
    if (get_option('echelon_sample_services_seeded')) {
        return;
    }

    $samples = [
        'wedding-car-rental' => ['Wedding Transportation', 'A composed, on-time arrival for your ceremony and reception.', 'star', 'wedding'],
        'prom-car-rental' => ['Prom Transportation', 'A safe, chauffeured ride your group will remember.', 'star', 'prom'],
        'corporate-car-rental' => ['Corporate Car Rental', 'Professional executive transportation for meetings, events, and client arrivals.', 'id-card', 'corporate'],
        'photoshoot-car-rental' => ['Photoshoot Car Rental', 'Distinctive vehicles prepared for editorial, commercial, and creator productions.', 'star', 'photoshoot'],
    ];

    $existing_services = get_posts(['post_type' => 'service', 'post_status' => 'any', 'posts_per_page' => -1]);
    foreach ($samples as $slug => [$title, $description, $icon, $concept]) {
        $concept_exists = (bool) array_filter($existing_services, static function ($service) use ($concept) {
            return false !== stripos($service->post_title, $concept);
        });
        if ($concept_exists || get_page_by_path($slug, OBJECT, 'service')) {
            continue;
        }
        $post_id = wp_insert_post([
            'post_type' => 'service', 'post_status' => 'publish', 'post_title' => $title,
            'post_name' => $slug, 'post_excerpt' => $description,
            'post_content' => '<p>' . esc_html($description) . '</p>',
        ], true);
        if (is_wp_error($post_id)) {
            continue;
        }
        update_post_meta($post_id, 'service_kicker', $description);
        update_post_meta($post_id, 'service_menu_description', $description);
        update_post_meta($post_id, 'service_hero_description', $description);
        update_post_meta($post_id, 'service_menu_icon', $icon);
        update_post_meta($post_id, '_echelon_sample_content', 1);
    }

    update_option('echelon_sample_services_seeded', gmdate('c'), false);
}
add_action('init', 'echelon_seed_sample_services', 30);

/** Remove only semantic duplicates created by the starter-data seeder. */
function echelon_dedupe_seeded_services() {
    if (get_option('echelon_seeded_services_deduped')) {
        return;
    }
    $services = get_posts(['post_type' => 'service', 'post_status' => 'any', 'posts_per_page' => -1]);
    foreach (['wedding', 'prom', 'corporate', 'photoshoot'] as $concept) {
        $matches = array_values(array_filter($services, static function ($service) use ($concept) {
            return false !== stripos($service->post_title, $concept);
        }));
        if (count($matches) < 2) {
            continue;
        }
        foreach ($matches as $service) {
            if (get_post_meta($service->ID, '_echelon_sample_content', true)) {
                wp_delete_post($service->ID, true);
            }
        }
    }
    update_option('echelon_seeded_services_deduped', gmdate('c'), false);
}
add_action('init', 'echelon_dedupe_seeded_services', 35);

/** Normalize migrated starter URLs to the public Services SEO hierarchy. */
function echelon_normalize_service_slugs() {
    if (get_option('echelon_service_slugs_normalized')) {
        return;
    }
    $canonical_slugs = [
        'wedding' => 'wedding-car-rental',
        'prom' => 'prom-car-rental',
        'corporate' => 'corporate-car-rental',
        'photoshoot' => 'photoshoot-car-rental',
    ];
    $services = get_posts(['post_type' => 'service', 'post_status' => 'any', 'posts_per_page' => -1]);
    foreach ($canonical_slugs as $concept => $slug) {
        foreach ($services as $service) {
            if (false === stripos($service->post_title, $concept)) {
                continue;
            }
            $owner = get_page_by_path($slug, OBJECT, 'service');
            if (!$owner || (int) $owner->ID === (int) $service->ID) {
                wp_update_post(['ID' => $service->ID, 'post_name' => $slug]);
            }
            break;
        }
    }
    update_option('echelon_service_slugs_normalized', gmdate('c'), false);
}
add_action('init', 'echelon_normalize_service_slugs', 40);

/** Preserve old inbound links while enforcing the canonical /services/ URL. */
function echelon_redirect_legacy_occasion_urls() {
    $path = trim((string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH), '/');
    if (!preg_match('#^(?:occasions|services)/([^/]+)$#', $path, $matches)) {
        return;
    }
    $requested_slug = sanitize_title($matches[1]);
    $legacy_aliases = [
        'prom-night-rental' => 'prom-car-rental',
        'corporate-executive-2' => 'corporate-car-rental',
        'photoshoot-content-2' => 'photoshoot-car-rental',
    ];
    $target_slug = $legacy_aliases[$requested_slug] ?? $requested_slug;
    $service = get_page_by_path($target_slug, OBJECT, 'service');
    if (!$service) {
        $legacy_match = get_posts([
            'post_type' => 'service', 'post_status' => 'publish', 'posts_per_page' => 1,
            'meta_key' => '_wp_old_slug', 'meta_value' => $requested_slug,
        ]);
        $service = $legacy_match[0] ?? null;
    }
    $is_legacy_path = 0 === strpos($path, 'occasions/') || $target_slug !== $requested_slug;
    if ($service && $is_legacy_path) {
        wp_safe_redirect(get_permalink($service), 301);
        exit;
    }
}
add_action('template_redirect', 'echelon_redirect_legacy_occasion_urls', 1);

/**
 * Apply the public fleet archive filters without creating a second query.
 */
function echelon_filter_fleet_archive($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('fleet_vehicle')) {
        return;
    }

    $query->set('posts_per_page', 9);
    $search = sanitize_text_field(wp_unslash($_GET['fleet_search'] ?? ''));
    if ($search !== '') {
        $title_matches = get_posts([
            'post_type' => 'fleet_vehicle', 'post_status' => 'publish',
            'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true,
            's' => $search,
        ]);
        $meta_matches = get_posts([
            'post_type' => 'fleet_vehicle', 'post_status' => 'publish',
            'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true,
            'meta_query' => [
                'relation' => 'OR',
                ['key' => 'brand', 'value' => $search, 'compare' => 'LIKE'],
                ['key' => 'tagline', 'value' => $search, 'compare' => 'LIKE'],
            ],
        ]);
        $query->set('post__in', array_values(array_unique(array_merge($title_matches, $meta_matches))) ?: [0]);
    }

    $tax_query = [];
    $categories = array_filter(array_map('sanitize_title', (array) ($_GET['body_type'] ?? [])));
    if ($categories) {
        $tax_query[] = ['taxonomy' => 'vehicle_category', 'field' => 'slug', 'terms' => $categories];
    }
    if ($tax_query) {
        $query->set('tax_query', $tax_query);
    }

    $meta_query = [];
    $brands = array_filter(array_map('sanitize_text_field', (array) ($_GET['make'] ?? [])));
    if ($brands) {
        $meta_query[] = ['key' => 'brand', 'value' => $brands, 'compare' => 'IN'];
    }
    foreach (['min_price' => ['price_per_hour', '>='], 'max_price' => ['price_per_hour', '<='], 'min_hp' => ['horsepower', '>=']] as $parameter => [$key, $compare]) {
        if (isset($_GET[$parameter]) && $_GET[$parameter] !== '') {
            $meta_query[] = ['key' => $key, 'value' => (float) $_GET[$parameter], 'compare' => $compare, 'type' => 'NUMERIC'];
        }
    }
    $seats = absint($_GET['seats'] ?? 0);
    if ($seats) {
        $meta_query[] = ['key' => 'seats', 'value' => $seats, 'compare' => $seats >= 5 ? '>=' : '=', 'type' => 'NUMERIC'];
    }
    if ($meta_query) {
        $query->set('meta_query', $meta_query);
    }

    $pickup = echelon_parse_reservation_datetime($_GET['pickup_date'] ?? '', $_GET['pickup_time'] ?? '');
    $return = echelon_parse_reservation_datetime($_GET['return_date'] ?? '', $_GET['return_time'] ?? '');
    if ($pickup && $return && $return > $pickup) {
        $conflicts = get_posts([
            'post_type' => 'rental_reservation',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
            'meta_query' => [
                'relation' => 'AND',
                ['key' => '_echelon_status', 'value' => 'confirmed'],
                ['key' => '_echelon_pickup_at', 'value' => $return->format('Y-m-d H:i:s'), 'compare' => '<', 'type' => 'DATETIME'],
                ['key' => '_echelon_return_at', 'value' => $pickup->format('Y-m-d H:i:s'), 'compare' => '>', 'type' => 'DATETIME'],
            ],
        ]);
        $unavailable_vehicle_ids = array_values(array_unique(array_filter(array_map(
            static fn($reservation_id) => absint(get_post_meta($reservation_id, '_echelon_vehicle_id', true)),
            $conflicts
        ))));
        if ($unavailable_vehicle_ids) {
            $query->set('post__not_in', $unavailable_vehicle_ids);
        }
    }

    $sort = sanitize_key($_GET['fleet_sort'] ?? 'recommended');
    if (in_array($sort, ['price_asc', 'price_desc', 'horsepower'], true)) {
        $query->set('meta_key', $sort === 'horsepower' ? 'horsepower' : 'price_per_hour');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', $sort === 'price_desc' ? 'DESC' : ($sort === 'horsepower' ? 'DESC' : 'ASC'));
    }
}
add_action('pre_get_posts', 'echelon_filter_fleet_archive');
