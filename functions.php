<?php
/**
 * Echelon Motions theme bootstrap.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ECHELON_THEME_DIR', get_template_directory());
define('ECHELON_THEME_URI', get_template_directory_uri());
define('ECHELON_THEME_VERSION', wp_get_theme()->get('Version'));

require ECHELON_THEME_DIR . '/inc/setup.php';
require ECHELON_THEME_DIR . '/inc/enqueue.php';
require ECHELON_THEME_DIR . '/inc/helpers.php';
require ECHELON_THEME_DIR . '/inc/nav-walker.php';
require ECHELON_THEME_DIR . '/inc/post-types.php';
require ECHELON_THEME_DIR . '/inc/archive-content.php';
require ECHELON_THEME_DIR . '/inc/acf-fields.php';
require ECHELON_THEME_DIR . '/inc/customizer.php';
require ECHELON_THEME_DIR . '/inc/reservations.php';
require ECHELON_THEME_DIR . '/inc/contact-inquiries.php';
require ECHELON_THEME_DIR . '/inc/vehicle-gallery.php';

/**
 * Preserve legacy navigation URLs until matching editorial pages are
 * published. This prevents stored menu items from sending visitors to a 404.
 */
function echelon_redirect_legacy_routes() {
    if (is_admin() || !is_404()) {
        return;
    }

    $path = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if ('chauffeur-services' !== $path) {
        return;
    }

    $destination = get_post_type_archive_link('service') ?: home_url('/services/');
    wp_safe_redirect($destination, 301, 'Echelon Motions');
    exit;
}
add_action('template_redirect', 'echelon_redirect_legacy_routes');
