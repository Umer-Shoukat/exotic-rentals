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

$fleet_archive_url = get_post_type_archive_link('fleet_vehicle') ?: home_url('/fleet');
$vehicle_count     = (int) wp_count_posts('fleet_vehicle')->publish;
?>
<section class="section choose-ride" id="fleet" data-reveal>
	<div class="container">
		<header class="section-heading">
			<p class="eyebrow"><?php esc_html_e('Explore By Category', 'echelon'); ?></p>
			<h2 class="section-heading__title"><?php esc_html_e('Choose Your', 'echelon'); ?> <span class="accent"><?php esc_html_e('Vehicle', 'echelon'); ?></span></h2>
		</header>

		<?php if ($vehicle_count) : ?>
			<div class="choose-ride__tabs" role="tablist" data-tabs>
				<button
					type="button"
					class="choose-ride__tab is-active"
					role="tab"
					data-tab-target="cat-all"
					aria-selected="true"
				>
					<?php esc_html_e('All', 'echelon'); ?>
				</button>
				<?php foreach ($categories as $term) : ?>
					<button
						type="button"
						class="choose-ride__tab"
						role="tab"
						data-tab-target="cat-<?php echo esc_attr($term->slug); ?>"
						aria-selected="false"
					>
						<?php echo esc_html($term->name); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<?php
			$all_vehicles = new WP_Query([
				'post_type'      => 'fleet_vehicle',
				'post_status'    => 'publish',
				'posts_per_page' => 10,
				'no_found_rows'  => true,
			]);
			?>
			<?php if ($all_vehicles->have_posts()) : ?>
				<div class="choose-ride__panel is-active" id="cat-all" data-tab-panel role="tabpanel">
					<div class="choose-ride__category-intro">
						<h3 class="choose-ride__category-title"><?php esc_html_e('All', 'echelon'); ?></h3>
						<p class="choose-ride__category-count"><?php echo esc_html(sprintf(_n('%s vehicle available', '%s vehicles available', $vehicle_count, 'echelon'), number_format_i18n($vehicle_count))); ?></p>
					</div>
					<div class="choose-ride__slider" data-swiper>
						<div class="swiper-wrapper">
							<?php while ($all_vehicles->have_posts()) : $all_vehicles->the_post(); ?>
								<div class="swiper-slide">
									<?php get_template_part('template-parts/fleet/card'); ?>
								</div>
							<?php endwhile; ?>
						</div>
					</div>
					<div class="choose-ride__slider-footer">
						<a class="btn btn--ghost" href="<?php echo esc_url($fleet_archive_url); ?>">
							<?php esc_html_e('View All', 'echelon'); ?>
							<?php echelon_icon('arrow-right'); ?>
						</a>
						<div class="slider-arrows">
							<button type="button" class="slider-arrow" data-swiper-prev aria-label="<?php esc_attr_e('Previous', 'echelon'); ?>"><?php echelon_icon('arrow-left'); ?></button>
							<button type="button" class="slider-arrow" data-swiper-next aria-label="<?php esc_attr_e('Next', 'echelon'); ?>"><?php echelon_icon('arrow-right'); ?></button>
						</div>
					</div>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>

			<?php foreach ($categories as $term) :
				$vehicles = new WP_Query([
					'post_type'      => 'fleet_vehicle',
					'post_status'    => 'publish',
					'posts_per_page' => 10,
					'no_found_rows'  => true,
					'tax_query'      => [[
						'taxonomy' => 'vehicle_category',
						'field'    => 'slug',
						'terms'    => $term->slug,
					]],
				]);
				if (!$vehicles->have_posts()) {
					continue;
				}
				?>
				<div class="choose-ride__panel" id="cat-<?php echo esc_attr($term->slug); ?>" data-tab-panel role="tabpanel">
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
						<a class="btn btn--ghost" href="<?php echo esc_url($fleet_archive_url); ?>">
							<?php esc_html_e('View All', 'echelon'); ?>
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
			?>
			<p><?php esc_html_e('Vehicles will appear here once added to the Fleet.', 'echelon'); ?></p>
			<?php
		endif;
		?>
	</div>
</section>
