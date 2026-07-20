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
require ECHELON_THEME_DIR . '/inc/acf-fields.php';
require ECHELON_THEME_DIR . '/inc/customizer.php';
require ECHELON_THEME_DIR . '/inc/reservations.php';
