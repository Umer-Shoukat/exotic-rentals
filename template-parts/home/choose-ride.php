<?php
/**
 * Home: "Choose Your Ride" — category-tabbed vehicle slider.
 */

$categories = get_terms([
    'taxonomy'   => 'vehicle_category',
    'hide_empty' => true,
]);

if (is_wp_error($categories)) {
    $categories = [];
} else {
	// get_terms() may preserve non-zero array keys. Normalize them so the first
	// tab and its panel always receive the initial active state together.
	$categories = array_values($categories);
}
?>
<section class="section choose-ride" id="fleet" data-reveal>
	<div class="container">
		<header class="section-heading">
			<p class="eyebrow"><?php esc_html_e('Explore By Category', 'echelon'); ?></p>
			<h2 class="section-heading__title"><?php esc_html_e('Choose Your', 'echelon'); ?> <span class="accent"><?php esc_html_e('Vehicle', 'echelon'); ?></span></h2>
		</header>

		<?php if ($categories) : ?>
			<div class="choose-ride__tabs" role="tablist" data-tabs>
				<?php foreach ($categories as $index => $term) : ?>
					<button
						type="button"
						class="choose-ride__tab<?php echo 0 === $index ? ' is-active' : ''; ?>"
						role="tab"
						data-tab-target="cat-<?php echo esc_attr($term->slug); ?>"
						aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
					>
						<?php echo esc_html($term->name); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<?php foreach ($categories as $index => $term) :
				$vehicles = new WP_Query([
					'post_type'      => 'fleet_vehicle',
					'posts_per_page' => 9,
					'no_found_rows'  => true,
					'tax_query'      => [[
						'taxonomy' => 'vehicle_category',
						'field'    => 'slug',
						'terms'    => $term->slug,
					]],
				]);
				if (!$vehicles->have_posts()) {
					$vehicles = new WP_Query([
						'post_type'      => 'fleet_vehicle',
						'posts_per_page' => 9,
						'no_found_rows'  => true,
					]);
				}
				if (!$vehicles->have_posts()) {
					continue;
				}
				?>
				<div class="choose-ride__panel<?php echo 0 === $index ? ' is-active' : ''; ?>" id="cat-<?php echo esc_attr($term->slug); ?>" data-tab-panel role="tabpanel">
					<div class="choose-ride__category-intro">
						<h3 class="choose-ride__category-title"><?php echo esc_html($term->name); ?></h3>
						<?php if ($term->description) : ?>
							<p class="choose-ride__category-desc"><?php echo esc_html($term->description); ?></p>
						<?php endif; ?>
						<p class="choose-ride__category-count"><?php echo esc_html(sprintf(_n('%s vehicle available', '%s vehicles available', $term->count, 'echelon'), number_format_i18n($term->count))); ?></p>
					</div>
					<div class="choose-ride__slider" data-swiper>
						<div class="swiper-wrapper">
							<?php while ($vehicles->have_posts()) : $vehicles->the_post(); ?>
								<div class="swiper-slide">
									<?php get_template_part('template-parts/fleet/card'); ?>
								</div>
							<?php endwhile; ?>
						</div>
					</div>
					<div class="choose-ride__slider-footer">
						<a class="btn btn--ghost" href="<?php echo esc_url(add_query_arg('category', $term->slug, home_url('/fleet'))); ?>">
							<?php esc_html_e('View More', 'echelon'); ?>
							<?php echelon_icon('arrow-right'); ?>
						</a>
						<div class="slider-arrows">
							<button type="button" class="slider-arrow" data-swiper-prev aria-label="<?php esc_attr_e('Previous', 'echelon'); ?>"><?php echelon_icon('arrow-left'); ?></button>
							<button type="button" class="slider-arrow" data-swiper-next aria-label="<?php esc_attr_e('Next', 'echelon'); ?>"><?php echelon_icon('arrow-right'); ?></button>
						</div>
					</div>
				</div>
				<?php
				wp_reset_postdata();
			endforeach; ?>

		<?php else :
			$vehicles = new WP_Query(['post_type' => 'fleet_vehicle', 'posts_per_page' => 9, 'no_found_rows' => true]);
			if ($vehicles->have_posts()) :
				?>
				<div class="choose-ride__panel is-active" data-tab-panel role="tabpanel">
					<div class="choose-ride__slider" data-swiper>
						<div class="swiper-wrapper">
							<?php while ($vehicles->have_posts()) : $vehicles->the_post(); ?>
								<div class="swiper-slide">
									<?php get_template_part('template-parts/fleet/card'); ?>
								</div>
							<?php endwhile; ?>
						</div>
					</div>
				</div>
				<?php
				wp_reset_postdata();
			else :
				?>
				<p><?php esc_html_e('Vehicles will appear here once added to the Fleet.', 'echelon'); ?></p>
				<?php
			endif;
		endif;
		?>
	</div>
</section>
