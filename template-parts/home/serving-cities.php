<?php
/**
 * Home: service area map and location list.
 */

$locations = get_posts([
    'post_type'      => 'location',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'no_found_rows'  => true,
    'meta_query'     => [
        'relation' => 'OR',
        ['key' => 'is_active', 'compare' => 'NOT EXISTS'],
        ['key' => 'is_active', 'value' => '1', 'compare' => '='],
    ],
]);
$locations = array_values(array_filter($locations, static function ($location) {
    return !preg_match('/\bdemo\b|\bsample\b/i', get_the_title($location));
}));

if (!$locations) {
    $locations = [
        (object) ['ID' => 0, 'title' => 'Manhattan', 'desc' => 'Midtown and downtown chauffeur service.', 'address' => '', 'phone' => '', 'pin_x' => 58, 'pin_y' => 42, 'active' => true],
        (object) ['ID' => 1, 'title' => 'Brooklyn', 'desc' => 'Borough-wide chauffeur coverage.', 'address' => '', 'phone' => '', 'pin_x' => 60, 'pin_y' => 48, 'active' => true],
        (object) ['ID' => 2, 'title' => 'Queens', 'desc' => 'Airport transfers and event transportation.', 'address' => '', 'phone' => '', 'pin_x' => 63, 'pin_y' => 45, 'active' => true],
        (object) ['ID' => 3, 'title' => 'The Bronx', 'desc' => 'Chauffeur service by request.', 'address' => '', 'phone' => '', 'pin_x' => 58, 'pin_y' => 38, 'active' => true],
        (object) ['ID' => 4, 'title' => 'Staten Island', 'desc' => 'Chauffeur service by request.', 'address' => '', 'phone' => '', 'pin_x' => 55, 'pin_y' => 54, 'active' => true],
        (object) ['ID' => 5, 'title' => 'Long Island (Nassau & Suffolk)', 'desc' => 'Wedding, event, and airport transportation throughout Nassau and Suffolk County.', 'address' => '', 'phone' => '', 'pin_x' => 70, 'pin_y' => 50, 'active' => true],
        (object) ['ID' => 6, 'title' => 'New Jersey / Connecticut / Pennsylvania', 'desc' => 'Extended service available on request.', 'address' => '', 'phone' => '', 'pin_x' => 50, 'pin_y' => 48, 'active' => true],
    ];
    $is_fallback = true;
} else {
    $is_fallback = false;
}

$page_id = get_queried_object_id();
$eyebrow = echelon_field('cities_eyebrow', $page_id, 'Where We Serve');
$heading = echelon_field('cities_heading', $page_id, 'Serving New York City and Long Island');
$intro = echelon_field('cities_intro', $page_id, 'Across Manhattan, Brooklyn, Queens, the Bronx, and Long Island, our concierge team coordinates the closest available vehicle and chauffeur. Extended service to New Jersey, Connecticut, and Pennsylvania is available by request.');
$map_image = echelon_field('cities_map', $page_id, null);
$map_link = echelon_field('cities_cta', $page_id, ['title' => 'View All Locations', 'url' => home_url('/locations')]);
if ($eyebrow === 'Where We Deliver') {
    $eyebrow = 'Where We Serve';
}
if ($heading === 'Serving The Cities That Demand More') {
    $heading = 'Serving New York City and Long Island';
}
if (stripos((string) $intro, 'From coast to coast') !== false) {
    $intro = 'Across Manhattan, Brooklyn, Queens, the Bronx, and Long Island, our concierge team coordinates the closest available vehicle and chauffeur. Extended service to New Jersey, Connecticut, and Pennsylvania is available by request.';
}
if (is_array($map_link) && ($map_link['title'] ?? '') === 'Active Service Zones') {
    $map_link['title'] = 'View All Locations';
}
$map_url = '';

if (is_array($map_image) && !empty($map_image['ID'])) {
    $map_url = wp_get_attachment_image_url($map_image['ID'], 'full');
} elseif (is_numeric($map_image)) {
    $map_url = wp_get_attachment_image_url((int) $map_image, 'full');
}

$google_map_url = echelon_google_static_map_url(640, 481);
$map_url = $google_map_url ?: ($map_url ?: ECHELON_THEME_URI . '/assets/images/figma/serving-cities-map.png');
$using_google_map = $google_map_url !== '';
$map_center_lat = (float) echelon_setting('google_maps_center_lat', '40.730610');
$map_center_lng = (float) echelon_setting('google_maps_center_lng', '-74.006000');
$map_zoom = echelon_sanitize_map_zoom(echelon_setting('google_maps_zoom', 8));
$heading_parts = preg_split('/(?=and Long Island)/i', $heading, 2);
$heading_primary = trim($heading_parts[0] ?? $heading);
$heading_accent = trim($heading_parts[1] ?? '');
?>
<section class="section section--alt serving-cities" id="locations" data-reveal data-city-map>
	<div class="container">
		<header class="section-heading serving-cities__heading">
			<p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
			<h2 class="section-heading__title"><?php echo esc_html($heading_primary); ?><?php if ($heading_accent) : ?><br><span class="accent"><?php echo esc_html($heading_accent); ?></span><?php endif; ?></h2>
		</header>

		<div class="serving-cities__grid">
			<div class="us-map<?php echo $using_google_map ? ' has-google-map' : ($map_image ? ' has-custom-map' : ' has-figma-map'); ?>" aria-label="<?php esc_attr_e('Map of active service cities', 'echelon'); ?>">
				<img class="us-map__image" src="<?php echo esc_url($map_url); ?>" alt="" loading="lazy" decoding="async">
				<?php foreach ($locations as $location) :
					if ($is_fallback) {
						$x = $location->pin_x;
						$y = $location->pin_y;
						$name = $location->title;
						$address = $location->address;
						$phone = $location->phone;
					} else {
						$x = (float) echelon_field('pin_x', $location->ID, 50);
						$y = (float) echelon_field('pin_y', $location->ID, 50);
						$name = get_the_title($location);
						$address = echelon_field('address', $location->ID, '');
						$phone = echelon_field('phone', $location->ID, '');
						$latitude = echelon_field('latitude', $location->ID, '');
						$longitude = echelon_field('longitude', $location->ID, '');
						if ($using_google_map && $latitude !== '' && $longitude !== '') {
							[$x, $y] = echelon_map_coordinate_percent($latitude, $longitude, $map_center_lat, $map_center_lng, $map_zoom, 640, 481);
						}
					}
					$location_key = 'location-' . (int) $location->ID;
					?>
					<button type="button" class="us-map__pin" style="left:<?php echo esc_attr($x); ?>%; top:<?php echo esc_attr($y); ?>%;" data-city-pin data-location-id="<?php echo esc_attr($location_key); ?>" aria-label="<?php echo esc_attr(sprintf(__('View details for %s', 'echelon'), $name)); ?>" aria-expanded="false">
						<span class="us-map__pin-dot"></span>
						<span class="us-map__pin-label" role="tooltip">
							<strong><?php echo esc_html($name); ?></strong>
							<?php if ($address) : ?><span><?php echo wp_kses_post(nl2br(esc_html($address))); ?></span><?php endif; ?>
							<?php if ($phone) : ?><span><?php echo esc_html($phone); ?></span><?php endif; ?>
						</span>
					</button>
				<?php endforeach; ?>
				<?php if (!empty($map_link['url'])) : ?>
					<a class="us-map__link" href="<?php echo esc_url($map_link['url']); ?>"<?php echo !empty($map_link['target']) ? ' target="' . esc_attr($map_link['target']) . '" rel="noopener"' : ''; ?>>
						<span aria-hidden="true"></span><?php echo esc_html($map_link['title'] ?: 'View All Locations'); ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="serving-cities__list">
				<p class="serving-cities__intro"><?php echo esc_html($intro); ?></p>
				<ul class="city-list">
					<?php foreach ($locations as $location) :
						if ($is_fallback) {
							$name = $location->title;
							$desc = $location->desc;
						} else {
							$name = get_the_title($location);
							$desc = echelon_field('description', $location->ID, '');
						}
						$location_key = 'location-' . (int) $location->ID;
						?>
						<li class="city-list__item">
							<button type="button" class="city-list__button" data-city-card data-location-id="<?php echo esc_attr($location_key); ?>" aria-pressed="false">
							<span class="city-list__icon"><?php echelon_icon('pin'); ?></span>
							<div>
								<span class="city-list__name"><?php echo esc_html($name); ?></span>
								<span class="city-list__desc"><?php echo esc_html($desc); ?></span>
							</div>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</section>
