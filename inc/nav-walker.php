<?php
/**
 * Custom nav walkers producing markup that matches the theme's SCSS
 * (no framework classes), with accessible dropdown/submenu support.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Echelon_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $output .= '<ul class="sub-menu">';
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= '</ul>';
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $has_children = in_array('menu-item-has-children', $item->classes, true);
        $classes      = $has_children ? ' class="menu-item-has-children"' : '';

        $output .= '<li' . $classes . '>';
        $output .= '<a class="primary-nav__link' . ($item->current ? ' is-active' : '') . '" href="' . esc_url($item->url) . '">';
        $output .= esc_html($item->title);
        if ($has_children) {
            $output .= echelon_icon_string('chevron-down');
        }
        $output .= '</a>';
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}

class Echelon_Mobile_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $output .= '<ul class="mobile-nav__submenu">';
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= '</ul>';
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $has_children = in_array('menu-item-has-children', $item->classes, true);

        if (0 === $depth) {
            $output .= '<li class="mobile-nav__item"' . ($has_children ? ' data-mobile-nav-item' : '') . '>';
            if ($has_children) {
                $output .= '<button type="button" class="mobile-nav__link" data-mobile-submenu-toggle aria-expanded="false">';
                $output .= '<span>' . esc_html($item->title) . '</span>';
                $output .= echelon_icon_string('chevron-down');
                $output .= '</button>';
            } else {
                $output .= '<a class="mobile-nav__link" href="' . esc_url($item->url) . '"><span>' . esc_html($item->title) . '</span></a>';
            }
        } else {
            $output .= '<li><a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
        }
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}

/**
 * Return an icon as a string (used inside walkers, where we build up an
 * output buffer rather than echoing directly).
 */
function echelon_icon_string($name, $class = '') {
    ob_start();
    echelon_icon($name, $class);
    return ob_get_clean();
}
