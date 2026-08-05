<?php
/**
 * Small reusable view helpers.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Echo an inline SVG icon from assets/images/icons/{$name}.svg so it can be
 * styled with `currentColor` / CSS. Falls back to nothing if missing.
 */
function echelon_icon($name, $class = '') {
    static $cache = [];

    $name = sanitize_file_name($name);

    if (!isset($cache[$name])) {
        $path = ECHELON_THEME_DIR . '/assets/images/icons/' . $name . '.svg';
        $cache[$name] = file_exists($path) ? file_get_contents($path) : '';
    }

    if (empty($cache[$name])) {
        return;
    }

    $svg = $cache[$name];

    if ($class) {
        if (preg_match('/class="([^"]*)"/', $svg)) {
            $svg = preg_replace('/class="([^"]*)"/', 'class="$1 ' . esc_attr($class) . '"', $svg, 1);
        } else {
            $svg = preg_replace('/<svg /', '<svg class="' . esc_attr($class) . '" ', $svg, 1);
        }
    }

    echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput -- trusted local SVG partials only.
}

/**
 * Render a 5-star rating (filled/outline) for the given score.
 */
function echelon_stars($rating, $out_of = 5) {
    $rating = max(0, min($out_of, (float) $rating));
    echo '<span class="star-rating" aria-label="' . esc_attr(sprintf(__('Rated %s out of %s', 'echelon'), $rating, $out_of)) . '">';
    for ($i = 1; $i <= $out_of; $i++) {
        $filled = $i <= round($rating);
        echo '<span class="star-rating__star' . ($filled ? ' is-filled' : '') . '">';
        echelon_icon('star', 'star-rating__icon');
        echo '</span>';
    }
    echo '</span>';
}

/**
 * Format a price for display, e.g. 1200 -> "$1,200".
 */
function echelon_price($amount) {
    if ($amount === '' || $amount === null) {
        return '';
    }
    return '$' . number_format((float) $amount);
}

/**
 * ACF field getter that degrades gracefully when ACF isn't active.
 */
function echelon_field($selector, $post_id = false, $default = '') {
    if (!function_exists('get_field')) {
        return $default;
    }

    // Fixed ACF Free collection fields are the current editing interface.
    // Prefer them when populated so stale values from a former Pro repeater do
    // not mask changes made in the individual homepage fields.
    $fixed_value = echelon_free_home_collection($selector, $post_id);
    if ($fixed_value !== null && $fixed_value !== []) {
        return $fixed_value;
    }

    $value = get_field($selector, $post_id);
    return ($value === null || $value === false || $value === '') ? $default : $value;
}

/**
 * Rebuild collection arrays from ACF Free-compatible fixed fields.
 * Populated fixed fields are authoritative; legacy Pro repeater/gallery values
 * remain available as a fallback through echelon_field().
 */
function echelon_free_home_collection($selector, $post_id = false) {
    $collections = [
        'hero_badges' => ['prefix' => 'hero_badge', 'count' => 4, 'columns' => ['icon', 'label'], 'content' => ['label']],
        'stats' => ['prefix' => 'stat_card', 'count' => 4, 'columns' => ['icon', 'value', 'label'], 'content' => ['value', 'label']],
        'brands' => ['prefix' => 'brand_logo', 'count' => 8, 'columns' => ['logo', 'name'], 'content' => ['logo', 'name']],
        'concierge_checklist' => ['prefix' => 'concierge_check', 'count' => 4, 'columns' => ['icon', 'label'], 'content' => ['label']],
        'concierge_chat_messages' => ['prefix' => 'concierge_message', 'count' => 4, 'columns' => ['sender', 'message'], 'content' => ['message']],
        'terms' => ['prefix' => 'requirement_card', 'count' => 4, 'columns' => ['icon', 'value', 'label'], 'content' => ['value', 'label']],
        'features' => ['prefix' => 'driver_feature', 'count' => 4, 'columns' => ['icon', 'title', 'description'], 'content' => ['title', 'description']],
        'about_stats' => ['prefix' => 'about_stat', 'count' => 4, 'columns' => ['value', 'label'], 'content' => ['value', 'label']],
        'about_values' => ['prefix' => 'about_value', 'count' => 6, 'columns' => ['icon', 'title', 'description'], 'content' => ['title', 'description']],
        'about_journey_steps' => ['prefix' => 'about_journey_step', 'count' => 6, 'columns' => ['title', 'description'], 'content' => ['title', 'description']],
    ];
    if (!isset($collections[$selector]) || !function_exists('get_field')) {
        return null;
    }

    $config = $collections[$selector];
    $rows = [];
    for ($index = 1; $index <= $config['count']; $index++) {
        $row = [];
        foreach ($config['columns'] as $column) {
            $value = get_field($config['prefix'] . '_' . $index . '_' . $column, $post_id);
            $row[$column] = $value;
        }
        $has_value = array_filter($config['content'], static function ($column) use ($row) {
            return isset($row[$column]) && $row[$column] !== '' && $row[$column] !== false;
        });
        if ($has_value) {
            $rows[] = !empty($config['flatten']) ? $row[$config['columns'][0]] : $row;
        }
    }
    return $rows;
}

/**
 * True if the ACF plugin is active.
 */
function echelon_acf_active() {
    return function_exists('get_field');
}

/**
 * Escape a heading and wrap its final matching accent phrase in a span.
 */
function echelon_accent_heading($heading, $accent = '') {
    $heading = trim((string) $heading);
    $accent = trim((string) $accent);

    if ($heading === '' || $accent === '') {
        return esc_html($heading);
    }

    $position = strripos($heading, $accent);
    if ($position === false) {
        return esc_html($heading);
    }

    $before = substr($heading, 0, $position);
    $match = substr($heading, $position, strlen($accent));
    $after = substr($heading, $position + strlen($accent));

    return esc_html($before) . '<span class="accent">' . esc_html($match) . '</span>' . esc_html($after);
}

/**
 * Build the branded Google Static Maps URL used behind the interactive
 * location overlay. Returns an empty string until an API key is configured.
 */
function echelon_google_static_map_url($width = 640, $height = 480, $viewport = []) {
    $api_key = trim((string) echelon_setting('google_maps_api_key', ''));
    if ($api_key === '') {
        return '';
    }

    $center_lat = isset($viewport['latitude']) ? (float) $viewport['latitude'] : (float) echelon_setting('google_maps_center_lat', '40.730610');
    $center_lng = isset($viewport['longitude']) ? (float) $viewport['longitude'] : (float) echelon_setting('google_maps_center_lng', '-74.006000');
    $zoom = isset($viewport['zoom']) ? echelon_sanitize_map_zoom($viewport['zoom']) : echelon_sanitize_map_zoom(echelon_setting('google_maps_zoom', 8));
    $params = [
        'center' => $center_lat . ',' . $center_lng,
        'zoom'   => $zoom,
        'size'   => min(640, absint($width)) . 'x' . min(640, absint($height)),
        'scale'  => 2,
        'format' => 'png32',
        'maptype'=> 'roadmap',
        'key'    => $api_key,
        'style'  => [
            'feature:all|element:geometry|color:0x090909',
            'feature:all|element:labels.text.fill|color:0x707070',
            'feature:all|element:labels.text.stroke|color:0x090909',
            'feature:administrative|element:geometry.stroke|color:0x292929',
            'feature:landscape|element:geometry|color:0x090909',
            'feature:poi|visibility:off',
            'feature:road|element:geometry|color:0x202020',
            'feature:road|element:geometry.stroke|color:0x121212',
            'feature:road|element:labels|visibility:simplified',
            'feature:transit|visibility:off',
            'feature:water|element:geometry|color:0x050505',
        ],
    ];

    $query_parts = [];
    foreach ($params as $key => $value) {
        foreach ((array) $value as $item) {
            $query_parts[] = rawurlencode($key) . '=' . rawurlencode((string) $item);
        }
    }

    $path = '/maps/api/staticmap?' . implode('&', $query_parts);
    $secret = trim((string) echelon_setting('google_maps_signing_secret', ''));
    if ($secret !== '') {
        $decoded_secret = base64_decode(strtr($secret, '-_', '+/'), true);
        if ($decoded_secret !== false) {
            $signature = hash_hmac('sha1', $path, $decoded_secret, true);
            $path .= '&signature=' . rawurlencode(rtrim(strtr(base64_encode($signature), '+/', '-_'), '='));
        }
    }

    return 'https://maps.googleapis.com' . $path;
}

/**
 * Calculate a Web Mercator viewport that contains every supplied coordinate.
 * The returned values are shared by the Static Maps image and HTML pin overlay.
 */
function echelon_map_viewport_for_locations($coordinates, $width = 640, $height = 480, $padding = 56) {
    $points = [];
    foreach ((array) $coordinates as $coordinate) {
        $latitude = isset($coordinate['latitude']) ? (float) $coordinate['latitude'] : null;
        $longitude = isset($coordinate['longitude']) ? (float) $coordinate['longitude'] : null;
        if ($latitude === null || $longitude === null || $latitude < -85 || $latitude > 85 || $longitude < -180 || $longitude > 180) {
            continue;
        }
        $sin = sin(deg2rad($latitude));
        $points[] = [
            ($longitude + 180) / 360,
            0.5 - log((1 + $sin) / (1 - $sin)) / (4 * M_PI),
        ];
    }
    if (!$points) {
        return [];
    }

    $xs = array_column($points, 0);
    $ys = array_column($points, 1);
    $min_x = min($xs);
    $max_x = max($xs);
    $min_y = min($ys);
    $max_y = max($ys);
    $available_width = max(1, (int) $width - (2 * (int) $padding));
    $available_height = max(1, (int) $height - (2 * (int) $padding));
    $longitude_zoom = $max_x > $min_x ? log($available_width / (256 * ($max_x - $min_x)), 2) : 20;
    $latitude_zoom = $max_y > $min_y ? log($available_height / (256 * ($max_y - $min_y)), 2) : 20;
    $zoom = max(1, min(20, (int) floor(min($longitude_zoom, $latitude_zoom))));
    $center_x = ($min_x + $max_x) / 2;
    $center_y = ($min_y + $max_y) / 2;
    $center_lng = ($center_x * 360) - 180;
    $center_lat = rad2deg(atan(sinh(M_PI * (1 - (2 * $center_y)))));

    return ['latitude' => $center_lat, 'longitude' => $center_lng, 'zoom' => $zoom];
}

/**
 * Convert coordinates to percentages in the fixed Static Maps viewport.
 * This uses the same Web Mercator projection as the roadmap tiles.
 */
function echelon_map_coordinate_percent($latitude, $longitude, $center_lat, $center_lng, $zoom, $width = 640, $height = 480) {
    $project = static function ($lat, $lng, $world_size) {
        $lat = max(-85.05112878, min(85.05112878, (float) $lat));
        $sin = sin(deg2rad($lat));
        return [
            (($lng + 180) / 360) * $world_size,
            (0.5 - log((1 + $sin) / (1 - $sin)) / (4 * M_PI)) * $world_size,
        ];
    };

    $world_size = 256 * pow(2, (int) $zoom);
    [$point_x, $point_y] = $project($latitude, $longitude, $world_size);
    [$center_x, $center_y] = $project($center_lat, $center_lng, $world_size);
    $pixel_x = ($width / 2) + ($point_x - $center_x);
    $pixel_y = ($height / 2) + ($point_y - $center_y);

    return [($pixel_x / $width) * 100, ($pixel_y / $height) * 100];
}

/**
 * Render an <img> from an ACF image array/ID or a WP attachment ID, or a
 * neutral placeholder block (with an icon) when no image is set yet — used
 * throughout so the site never shows a broken-image icon before real
 * photography is uploaded.
 */
function echelon_media($image, $size = 'large', $class = '', $icon = 'bolt') {
    $url = '';
    $alt = '';

    if (is_array($image) && !empty($image['ID'])) {
        $url = wp_get_attachment_image_url($image['ID'], $size);
        $alt = $image['alt'] ?? '';
    } elseif (is_numeric($image)) {
        $url = wp_get_attachment_image_url((int) $image, $size);
        $alt = get_post_meta((int) $image, '_wp_attachment_image_alt', true);
    }

    if ($url) {
        printf(
            '<img src="%s" alt="%s" class="%s" loading="lazy" decoding="async">',
            esc_url($url),
            esc_attr($alt),
            esc_attr($class)
        );
        return;
    }

    printf('<div class="media-placeholder %s" aria-hidden="true">', esc_attr($class));
    echelon_icon($icon, 'media-placeholder__icon');
    echo '</div>';
}
