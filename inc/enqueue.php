<?php
/**
 * Front-end asset loading. Styles/scripts are pre-built by Vite into
 * assets/dist (run `npm run build` or `npm run dev` from the theme root).
 */

if (!defined('ABSPATH')) {
    exit;
}

function echelon_asset_version($relative_path) {
    $file = ECHELON_THEME_DIR . $relative_path;
    return file_exists($file) ? filemtime($file) : ECHELON_THEME_VERSION;
}

function echelon_enqueue_assets() {
    $css_path = '/assets/dist/main.css';
    $js_path  = '/assets/dist/main.js';

    if (file_exists(ECHELON_THEME_DIR . $css_path)) {
        wp_enqueue_style(
            'echelon-main',
            ECHELON_THEME_URI . $css_path,
            [],
            echelon_asset_version($css_path)
        );
    } else {
        // Build not run yet — fall back to the bare theme stylesheet so the
        // site isn't unstyled during initial setup.
        wp_enqueue_style('echelon-fallback', get_stylesheet_uri(), [], ECHELON_THEME_VERSION);
    }

    if (file_exists(ECHELON_THEME_DIR . $js_path)) {
        wp_enqueue_script(
            'echelon-main',
            ECHELON_THEME_URI . $js_path,
            [],
            echelon_asset_version($js_path),
            true
        );
    }

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'echelon_enqueue_assets');

/**
 * Provide the Echelon brand mark as a favicon until a Site Icon is selected
 * in WordPress. A dashboard-configured Site Icon always takes precedence.
 */
function echelon_favicon_fallback() {
    if (has_site_icon()) {
        return;
    }

    $favicon_path = '/assets/images/logo-icon.png';
    $favicon_url  = add_query_arg(
        'ver',
        echelon_asset_version($favicon_path),
        ECHELON_THEME_URI . $favicon_path
    );

    printf("<link rel=\"icon\" href=\"%s\" type=\"image/png\">\n", esc_url($favicon_url));
    printf("<link rel=\"apple-touch-icon\" href=\"%s\">\n", esc_url($favicon_url));
}
add_action('wp_head', 'echelon_favicon_fallback', 2);

/**
 * Mark <html> as JS-capable before paint so scroll-reveal CSS can safely
 * hide [data-reveal] elements only when JS is actually going to run them.
 */
function echelon_reveal_init_script() {
    echo "<script>document.documentElement.classList.add('reveal-init');</script>\n";
}
add_action('wp_head', 'echelon_reveal_init_script', 1);
