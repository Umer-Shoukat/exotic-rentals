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
        $is_services = 0 === $depth && (false !== stripos($item->title, 'service') || untrailingslashit($item->url) === untrailingslashit(get_post_type_archive_link('service')));
        $is_locations = 0 === $depth && (false !== stripos($item->title, 'location') || untrailingslashit($item->url) === untrailingslashit(get_post_type_archive_link('location')));
        $is_mega = $is_services || $is_locations;
        $classes = [];
        if ($has_children) $classes[] = 'menu-item-has-children';
        if ($is_mega) $classes[] = 'primary-nav__item--mega';

        $output .= '<li' . ($classes ? ' class="' . esc_attr(implode(' ', $classes)) . '"' : '') . ($is_mega ? ' data-mega-menu-root' : '') . '>';
        $url = $is_services ? get_post_type_archive_link('service') : ($is_locations ? get_post_type_archive_link('location') : $item->url);
        $label = $is_services ? __('Services', 'echelon') : ($is_locations ? __('Locations', 'echelon') : $item->title);
        $output .= '<a class="primary-nav__link' . ($item->current ? ' is-active' : '') . '" href="' . esc_url($url) . '"' . ($is_mega ? ' aria-haspopup="true" aria-expanded="false" data-mega-menu-trigger' : '') . '>';
        $output .= esc_html($label);
        if ($has_children || $is_mega) {
            $output .= echelon_icon_string('chevron-down');
        }
        $output .= '</a>';
        if ($is_services) $output .= echelon_services_mega_menu();
        if ($is_locations) $output .= echelon_locations_mega_menu();
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}

function echelon_services_mega_menu() {
    $services = get_posts([
        'post_type' => 'service', 'post_status' => 'publish', 'posts_per_page' => 12,
        'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'], 'no_found_rows' => true,
    ]);
    ob_start(); ?>
    <div class="mega-menu mega-menu--services" data-mega-menu-panel>
        <div class="mega-menu__inner">
            <div class="mega-menu__heading"><div><p><?php esc_html_e('Premium Services', 'echelon'); ?></p><h2><?php esc_html_e('Luxury Support For Every Occasion.', 'echelon'); ?></h2></div><a href="<?php echo esc_url(get_post_type_archive_link('service')); ?>"><?php esc_html_e('View All Services', 'echelon'); ?></a></div>
            <?php if ($services) : ?><div class="mega-menu__service-grid">
                <?php foreach ($services as $service) :
                    $description = echelon_field('service_menu_description', $service->ID, echelon_field('service_kicker', $service->ID, get_the_excerpt($service)));
                    $icon = echelon_field('service_menu_icon', $service->ID, 'star'); ?>
                    <a class="mega-menu-card" href="<?php echo esc_url(get_permalink($service)); ?>"><span class="mega-menu-card__icon"><?php echelon_icon($icon); ?></span><span><strong><?php echo esc_html(get_the_title($service)); ?></strong><?php if ($description) : ?><small><?php echo esc_html($description); ?></small><?php endif; ?></span></a>
                <?php endforeach; ?>
            </div><?php else : ?><p class="mega-menu__empty"><?php esc_html_e('Publish Services to populate this menu automatically.', 'echelon'); ?></p><?php endif; ?>
        </div>
    </div><?php
    return ob_get_clean();
}

function echelon_locations_mega_menu() {
    $locations = get_posts([
        'post_type' => 'location', 'post_status' => 'publish', 'posts_per_page' => -1,
        'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'], 'no_found_rows' => true,
    ]);
    $groups = ['New York' => [], 'New Jersey' => [], 'Connecticut' => [], 'Long Island' => []];
    foreach ($locations as $location) {
        if (preg_match('/\bdemo\b|\bsample\b/i', get_the_title($location))) {
            continue;
        }
        $region = echelon_field('menu_region', $location->ID, '');
        if (!$region || !isset($groups[$region])) {
            $name = strtolower(get_the_title($location));
            if (preg_match('/jersey|newark|hoboken|elizabeth/', $name)) $region = 'New Jersey';
            elseif (preg_match('/stamford|greenwich|norwalk|connecticut/', $name)) $region = 'Connecticut';
            elseif (preg_match('/nassau|suffolk|hampton|montauk|north fork|long island/', $name)) $region = 'Long Island';
            else $region = 'New York';
        }
        $groups[$region][] = $location;
    }
    ob_start(); ?>
    <div class="mega-menu mega-menu--locations" data-mega-menu-panel>
        <div class="mega-menu__inner mega-menu__location-grid">
            <?php foreach ($groups as $region => $items) : if (!$items) continue; $items = array_slice($items, 0, 5); ?>
                <section class="mega-menu__location-group"><h2><?php echo esc_html($region); ?></h2>
                    <div><?php foreach ($items as $location) :
                        $description = echelon_field('menu_description', $location->ID, echelon_field('description', $location->ID, get_the_excerpt($location))); ?>
                        <a class="mega-menu-card" href="<?php echo esc_url(get_permalink($location)); ?>"><span class="mega-menu-card__icon"><?php echelon_icon('pin'); ?></span><span><strong><?php echo esc_html(get_the_title($location)); ?></strong><?php if ($description) : ?><small><?php echo esc_html($description); ?></small><?php endif; ?></span></a>
                    <?php endforeach; ?></div>
                </section>
            <?php endforeach; ?>
        </div>
        <a class="mega-menu__all-locations" href="<?php echo esc_url(get_post_type_archive_link('location')); ?>"><?php esc_html_e('View All Locations', 'echelon'); ?></a>
    </div><?php
    return ob_get_clean();
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
            $icon = echelon_mobile_nav_icon($item->title, $item->url);
            $label = '<span class="mobile-nav__link-main"><span class="mobile-nav__link-icon">' . echelon_icon_string($icon) . '</span><span>' . esc_html($item->title) . '</span></span>';
            $active_class = $item->current || in_array('current-menu-ancestor', $item->classes, true) ? ' is-active' : '';

            if ($has_children) {
                $output .= '<div class="mobile-nav__row">';
                $output .= '<a class="mobile-nav__link' . $active_class . '" href="' . esc_url($item->url) . '">' . $label . '</a>';
                $output .= '<button type="button" class="mobile-nav__submenu-toggle" data-mobile-submenu-toggle aria-expanded="false" aria-label="' . esc_attr(sprintf(__('Toggle %s submenu', 'echelon'), $item->title)) . '">';
                $output .= echelon_icon_string('chevron-down');
                $output .= '</button></div>';
            } else {
                $output .= '<a class="mobile-nav__link' . $active_class . '" href="' . esc_url($item->url) . '">' . $label . '</a>';
            }
        } else {
            $output .= '<li><a href="' . esc_url($item->url) . '"><span class="mobile-nav__submenu-icon">' . echelon_icon_string('arrow-right') . '</span>' . esc_html($item->title) . '</a>';
        }
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}

/**
 * Choose a useful icon for a WordPress-managed top-level mobile menu item.
 * URL matching keeps the mapping stable when editors rename a menu label.
 */
function echelon_mobile_nav_icon($title, $url = '') {
    $path = strtolower(trim((string) wp_parse_url($url, PHP_URL_PATH), '/'));
    $searchable = strtolower(wp_strip_all_tags($title) . ' ' . str_replace(['-', '_'], ' ', $path));
    $icons = [
        'service'     => 'star',
        'location'    => 'pin',
        'fleet'       => 'gauge',
        'vehicle'     => 'gauge',
        'reservation' => 'calendar',
        'book'        => 'calendar',
        'contact'     => 'mail',
        'concierge'   => 'headset',
        'about'       => 'shield-check',
        'home'        => 'logo-mark',
    ];

    foreach ($icons as $keyword => $icon) {
        if (false !== strpos($searchable, $keyword)) {
            return $icon;
        }
    }

    return 'arrow-right';
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
