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
    $value = get_field($selector, $post_id);
    return ($value === null || $value === false || $value === '') ? $default : $value;
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
function echelon_google_static_map_url($width = 640, $height = 480) {
    $api_key = trim((string) echelon_setting('google_maps_api_key', ''));
    if ($api_key === '') {
        return '';
    }

    $center_lat = (float) echelon_setting('google_maps_center_lat', '40.730610');
    $center_lng = (float) echelon_setting('google_maps_center_lng', '-74.006000');
    $zoom = echelon_sanitize_map_zoom(echelon_setting('google_maps_zoom', 8));
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
