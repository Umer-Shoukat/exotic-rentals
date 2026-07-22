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
?>
<div class="locations-page">
	<section class="locations-hero">
		<img class="locations-hero__image" src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/locations/hero.jpg')); ?>" alt="" aria-hidden="true">
		<div class="locations-hero__scrim"></div>
		<div class="container locations-hero__content">
			<p class="eyebrow"><?php esc_html_e('Service Areas', 'echelon'); ?></p>
			<h1><?php esc_html_e('Exotic Car Rentals Across', 'echelon'); ?><br><span><?php esc_html_e('New Jersey & Connecticut', 'echelon'); ?></span></h1>
			<p><?php esc_html_e('Concierge delivery across the tri-state — from Manhattan skylines to Montauk cliffs, vineyard roads to Hamptons estates. Wherever the moment is, the car arrives.', 'echelon'); ?></p>
			<div class="locations-hero__actions">
				<a class="btn btn--primary" href="<?php echo esc_url(home_url('/fleet/')); ?>"><?php esc_html_e('Book Your Vehicle', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a>
				<a class="btn btn--outline" href="<?php echo esc_url(home_url('/fleet/')); ?>"><?php esc_html_e('View Our Fleet', 'echelon'); ?></a>
			</div>
			<ul class="locations-hero__trust"><li><?php echelon_icon('star'); ?><?php esc_html_e('5-Star Rated', 'echelon'); ?></li><li><?php echelon_icon('shield-check'); ?><?php esc_html_e('Fully Insured', 'echelon'); ?></li><li><?php echelon_icon('headset'); ?><?php esc_html_e('24/7 Concierge', 'echelon'); ?></li></ul>
		</div>
	</section>

	<section class="location-proof" aria-label="<?php esc_attr_e('Delivery service benefits', 'echelon'); ?>">
		<div class="container location-proof__grid">
			<div><?php echelon_icon('pin'); ?><span><?php esc_html_e('NYC 5 Boroughs Covered', 'echelon'); ?></span></div>
			<div><?php echelon_icon('truck'); ?><span><?php esc_html_e('Long Island Delivery', 'echelon'); ?></span></div>
			<div><?php echelon_icon('star'); ?><span><?php esc_html_e('Airport & Event Delivery', 'echelon'); ?></span></div>
			<div><?php echelon_icon('headset'); ?><span><?php esc_html_e('Concierge Booking Support', 'echelon'); ?></span></div>
		</div>
	</section>

	<section class="section featured-locations" data-reveal>
		<div class="container">
			<header class="section-heading"><p class="eyebrow"><?php esc_html_e('Featured Locations', 'echelon'); ?></p><h2 class="section-heading__title"><?php esc_html_e('White-Glove Access', 'echelon'); ?><br><?php esc_html_e('Where', 'echelon'); ?> <span class="accent"><?php esc_html_e('Luxury Clients Move', 'echelon'); ?></span></h2></header>
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
							<div class="location-card__actions"><a class="btn btn--outline" href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View', 'echelon'); ?></a><a class="btn btn--primary" href="<?php echo esc_url(home_url('/reservation/?location=' . $location->post_name)); ?>"><?php esc_html_e('Book Now', 'echelon'); ?></a></div>
						</div>
					</article>
				<?php endforeach; else : foreach ($fallback_locations as $item) : ?>
					<article class="location-card"><div class="location-card__media"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/locations/' . $item['image'])); ?>" alt="<?php echo esc_attr($item['title']); ?>"></div><div class="location-card__body"><h3><?php echo esc_html($item['title']); ?></h3><p><?php echo esc_html($item['description']); ?></p><ul class="location-card__areas"><?php foreach ($item['areas'] as $area) : ?><li><?php echo esc_html($area); ?></li><?php endforeach; ?></ul><div class="location-card__actions"><a class="btn btn--outline" href="#locations-map"><?php esc_html_e('View', 'echelon'); ?></a><a class="btn btn--primary" href="<?php echo esc_url(home_url('/reservation/')); ?>"><?php esc_html_e('Book Now', 'echelon'); ?></a></div></div></article>
				<?php endforeach; endif; ?>
			</div>
		</div>
	</section>

	<div id="locations-map"><?php get_template_part('template-parts/home/serving-cities'); ?></div>

	<section class="section location-benefits" data-reveal><div class="container"><header class="section-heading"><p class="eyebrow"><?php esc_html_e('Why Rent With Us', 'echelon'); ?></p><h2 class="section-heading__title"><?php esc_html_e('Built For Clients Who', 'echelon'); ?><br><?php esc_html_e('Expect The', 'echelon'); ?> <span class="accent"><?php esc_html_e('Details Handled', 'echelon'); ?></span></h2></header><div class="location-benefits__grid">
		<?php foreach ([['truck','Curated Luxury Fleet','Every vehicle handpicked and mechanically vetted.'],['pin','White-Glove Service','Detailed, fueled, delivered — every single time.'],['calendar','Transparent Pricing','No hidden fees. What you see is what you pay.'],['clock','Flexible Rental Options','Hourly, daily, weekly, monthly — on your terms.'],['headset','24/7 Concierge','Real people, always on, from booking to return.'],['shield-check','Fully Insured','Comprehensive coverage included on every trip.']] as $benefit) : ?><article><?php echelon_icon($benefit[0]); ?><h3><?php echo esc_html($benefit[1]); ?></h3><p><?php echo esc_html($benefit[2]); ?></p></article><?php endforeach; ?>
	</div></div></section>

	<?php get_template_part('template-parts/home/more-than-rental'); ?>
	<?php get_template_part('template-parts/home/faq'); ?>
	<?php get_template_part('template-parts/home/cta'); ?>
</div>
<?php get_footer(); ?>
