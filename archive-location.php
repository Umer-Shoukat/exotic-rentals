<?php
/** Location archive: /locations/. */
get_header();

$locations = get_posts([
    'post_type'      => 'location',
    'posts_per_page' => -1,
    'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
    'order'          => 'ASC',
    'no_found_rows'  => true,
]);

$fallback_locations = [
    ['title' => 'New York', 'description' => 'Manhattan arrivals, penthouse deliveries, five-borough coverage.', 'areas' => ['Manhattan', 'Brooklyn', 'Bronx', 'Queens', 'Staten Island'], 'image' => 'new-york.jpg'],
    ['title' => 'New Jersey', 'description' => 'Hoboken skylines to Jersey Shore weekends — handled.', 'areas' => ['Manhattan', 'Brooklyn', 'Bronx', 'Queens', 'Staten Island'], 'image' => 'new-jersey.jpg'],
    ['title' => 'Connecticut', 'description' => 'Gold Coast estates, Greenwich drives, Yale Bowl events.', 'areas' => ['Manhattan', 'Brooklyn', 'Bronx', 'Queens', 'Staten Island'], 'image' => 'connecticut.jpg'],
    ['title' => 'Nassau County', 'description' => 'Manhasset, Great Neck, Old Westbury delivery.', 'areas' => ['Manhattan', 'Brooklyn', 'Bronx', 'Queens', 'Staten Island'], 'image' => 'nassau-county.jpg'],
];
$hero_title = echelon_field('locations_hero_title', 'option', __('Exotic Car Rentals Across New Jersey & Connecticut', 'echelon'));
$hero_accent = echelon_field('locations_hero_accent', 'option', __('New Jersey & Connecticut', 'echelon'));
$hero_image = echelon_field('locations_hero_image', 'option', null);
$primary_cta = echelon_field('locations_hero_primary_cta', 'option', ['title' => __('Book Your Vehicle', 'echelon'), 'url' => home_url('/fleet/'), 'target' => '']);
$secondary_cta = echelon_field('locations_hero_secondary_cta', 'option', ['title' => __('View Our Fleet', 'echelon'), 'url' => home_url('/fleet/'), 'target' => '']);
$trust_items = echelon_field('locations_hero_trust_items', 'option', [
	['icon' => 'star', 'label' => __('5-Star Rated', 'echelon')],
	['icon' => 'shield-check', 'label' => __('Fully Insured', 'echelon')],
	['icon' => 'headset', 'label' => __('24/7 Concierge', 'echelon')],
]);
$proof_items = echelon_field('locations_proof_items', 'option', [
	['icon' => 'pin', 'label' => __('NYC 5 Boroughs Covered', 'echelon')],
	['icon' => 'truck', 'label' => __('Long Island Delivery', 'echelon')],
	['icon' => 'star', 'label' => __('Airport & Event Delivery', 'echelon')],
	['icon' => 'headset', 'label' => __('Concierge Booking Support', 'echelon')],
]);
$list_heading = echelon_field('locations_list_heading', 'option', __('White-Glove Access Where Luxury Clients Move', 'echelon'));
$list_accent = echelon_field('locations_list_accent', 'option', __('Luxury Clients Move', 'echelon'));
$view_label = echelon_field('locations_view_label', 'option', __('View', 'echelon'));
$book_label = echelon_field('locations_book_label', 'option', __('Book Now', 'echelon'));
$benefits = echelon_field('locations_benefits', 'option', [
	['icon' => 'truck', 'title' => __('Curated Luxury Fleet', 'echelon'), 'description' => __('Every vehicle handpicked and mechanically vetted.', 'echelon')],
	['icon' => 'pin', 'title' => __('White-Glove Service', 'echelon'), 'description' => __('Detailed, fueled, delivered — every single time.', 'echelon')],
	['icon' => 'calendar', 'title' => __('Transparent Pricing', 'echelon'), 'description' => __('No hidden fees. What you see is what you pay.', 'echelon')],
	['icon' => 'clock', 'title' => __('Flexible Rental Options', 'echelon'), 'description' => __('Hourly, daily, weekly, monthly — on your terms.', 'echelon')],
	['icon' => 'headset', 'title' => __('24/7 Concierge', 'echelon'), 'description' => __('Real people, always on, from booking to return.', 'echelon')],
	['icon' => 'shield-check', 'title' => __('Fully Insured', 'echelon'), 'description' => __('Comprehensive coverage included on every trip.', 'echelon')],
]);
?>
<div class="locations-page">
	<section class="locations-hero">
		<?php if ($hero_image) : echelon_media($hero_image, 'full', ''); else : ?><img class="locations-hero__image" src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/locations/hero.jpg')); ?>" alt="" aria-hidden="true"><?php endif; ?>
		<div class="locations-hero__scrim"></div>
		<div class="container locations-hero__content">
			<p class="eyebrow"><?php echo esc_html(echelon_field('locations_hero_eyebrow', 'option', __('Service Areas', 'echelon'))); ?></p>
			<h1><?php echo wp_kses(echelon_accent_heading($hero_title, $hero_accent), ['span' => ['class' => true]]); ?></h1>
			<p><?php echo esc_html(echelon_field('locations_hero_description', 'option', __('Concierge delivery across the tri-state — from Manhattan skylines to Montauk cliffs, vineyard roads to Hamptons estates. Wherever the moment is, the car arrives.', 'echelon'))); ?></p>
			<div class="locations-hero__actions">
				<a class="btn btn--primary" href="<?php echo esc_url($primary_cta['url'] ?? home_url('/fleet/')); ?>"<?php echo !empty($primary_cta['target']) ? ' target="' . esc_attr($primary_cta['target']) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html($primary_cta['title'] ?? __('Book Your Vehicle', 'echelon')); ?><?php echelon_icon('arrow-right'); ?></a>
				<a class="btn btn--outline" href="<?php echo esc_url($secondary_cta['url'] ?? home_url('/fleet/')); ?>"<?php echo !empty($secondary_cta['target']) ? ' target="' . esc_attr($secondary_cta['target']) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html($secondary_cta['title'] ?? __('View Our Fleet', 'echelon')); ?></a>
			</div>
			<ul class="locations-hero__trust"><?php foreach ($trust_items as $item) : ?><li><?php echelon_icon($item['icon'] ?? 'check'); ?><?php echo esc_html($item['label'] ?? ''); ?></li><?php endforeach; ?></ul>
		</div>
	</section>

	<section class="location-proof" aria-label="<?php esc_attr_e('Delivery service benefits', 'echelon'); ?>">
		<div class="container location-proof__grid">
			<?php foreach ($proof_items as $item) : ?><div><?php echelon_icon($item['icon'] ?? 'check'); ?><span><?php echo esc_html($item['label'] ?? ''); ?></span></div><?php endforeach; ?>
		</div>
	</section>

	<section class="section featured-locations" data-reveal>
		<div class="container">
			<header class="section-heading"><p class="eyebrow"><?php echo esc_html(echelon_field('locations_list_eyebrow', 'option', __('Featured Locations', 'echelon'))); ?></p><h2 class="section-heading__title"><?php echo wp_kses(echelon_accent_heading($list_heading, $list_accent), ['span' => ['class' => true]]); ?></h2></header>
			<div class="location-card-grid">
				<?php if ($locations) : foreach ($locations as $location) :
					$description = echelon_field('description', $location->ID, get_the_excerpt($location));
					$areas = array_filter(array_map('trim', explode(',', (string) echelon_field('neighborhoods', $location->ID, ''))));
					$permalink = get_permalink($location);
					?>
					<article class="location-card">
						<div class="location-card__media"><?php if (has_post_thumbnail($location)) : echo get_the_post_thumbnail($location, 'large'); else : ?><img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/locations/new-york.jpg')); ?>" alt=""><?php endif; ?></div>
						<div class="location-card__body"><h3><?php echo esc_html(get_the_title($location)); ?></h3><p><?php echo esc_html($description); ?></p>
							<?php if ($areas) : ?><ul class="location-card__areas"><?php foreach ($areas as $area) : ?><li><?php echo esc_html($area); ?></li><?php endforeach; ?></ul><?php endif; ?>
							<div class="location-card__actions"><a class="btn btn--outline" href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($view_label); ?></a><a class="btn btn--primary" href="<?php echo esc_url(home_url('/reservation/?location=' . $location->post_name)); ?>"><?php echo esc_html($book_label); ?></a></div>
						</div>
					</article>
				<?php endforeach; else : foreach ($fallback_locations as $item) : ?>
					<article class="location-card"><div class="location-card__media"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/locations/' . $item['image'])); ?>" alt="<?php echo esc_attr($item['title']); ?>"></div><div class="location-card__body"><h3><?php echo esc_html($item['title']); ?></h3><p><?php echo esc_html($item['description']); ?></p><ul class="location-card__areas"><?php foreach ($item['areas'] as $area) : ?><li><?php echo esc_html($area); ?></li><?php endforeach; ?></ul><div class="location-card__actions"><a class="btn btn--outline" href="#locations-map"><?php echo esc_html($view_label); ?></a><a class="btn btn--primary" href="<?php echo esc_url(home_url('/reservation/')); ?>"><?php echo esc_html($book_label); ?></a></div></div></article>
				<?php endforeach; endif; ?>
			</div>
		</div>
	</section>

	<div id="locations-map"><?php get_template_part('template-parts/home/serving-cities'); ?></div>

	<section class="section location-benefits" data-reveal><div class="container"><header class="section-heading"><p class="eyebrow"><?php echo esc_html(echelon_field('locations_benefits_eyebrow', 'option', __('Why Rent With Us', 'echelon'))); ?></p><h2 class="section-heading__title"><?php echo wp_kses(echelon_accent_heading(echelon_field('locations_benefits_heading', 'option', __('Built For Clients Who Expect The Details Handled', 'echelon')), echelon_field('locations_benefits_accent', 'option', __('Details Handled', 'echelon'))), ['span' => ['class' => true]]); ?></h2></header><div class="location-benefits__grid">
		<?php foreach ($benefits as $benefit) : ?><article><?php echelon_icon($benefit['icon'] ?? 'check'); ?><h3><?php echo esc_html($benefit['title'] ?? ''); ?></h3><p><?php echo esc_html($benefit['description'] ?? ''); ?></p></article><?php endforeach; ?>
	</div></div></section>

	<?php get_template_part('template-parts/home/more-than-rental'); ?>
	<?php get_template_part('template-parts/home/faq'); ?>
	<?php get_template_part('template-parts/home/cta'); ?>
</div>
<?php get_footer(); ?>
