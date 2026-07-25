<?php
/**
 * Fleet archive.
 */

get_header();

$vehicle_count = (int) wp_count_posts('fleet_vehicle')->publish;
$categories    = get_terms(['taxonomy' => 'vehicle_category', 'hide_empty' => true]);
$brands        = get_posts([
	'post_type'      => 'fleet_vehicle',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'fields'         => 'ids',
]);
$brand_counts = [];
foreach ($brands as $vehicle_id) {
	$brand = trim((string) echelon_field('brand', $vehicle_id, ''));
	if ($brand !== '') {
		$brand_counts[$brand] = ($brand_counts[$brand] ?? 0) + 1;
	}
}
ksort($brand_counts, SORT_NATURAL | SORT_FLAG_CASE);

$selected_categories = array_map('sanitize_title', (array) ($_GET['body_type'] ?? []));
$selected_brands     = array_map('sanitize_text_field', (array) ($_GET['make'] ?? []));
$selected_seats      = sanitize_text_field(wp_unslash($_GET['seats'] ?? ''));
?>

<section class="fleet-page">
	<div class="container">
		<nav class="fleet-breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'echelon'); ?>">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'echelon'); ?></a>
			<span aria-hidden="true">›</span>
			<span><?php esc_html_e('Fleet', 'echelon'); ?></span>
		</nav>

		<header class="fleet-hero">
			<p class="fleet-eyebrow"><?php esc_html_e('Our Fleet', 'echelon'); ?></p>
			<h1><?php esc_html_e('Exotic Car Rental', 'echelon'); ?> <em><?php esc_html_e('Fleet', 'echelon'); ?></em></h1>
			<p><?php esc_html_e('Browse our live collection, filter by body style, make, or rate, then reserve your exact dates directly from each vehicle page.', 'echelon'); ?></p>
		</header>

		<div class="fleet-stats" aria-label="<?php esc_attr_e('Fleet summary', 'echelon'); ?>">
			<div><strong><?php echo esc_html($vehicle_count); ?></strong><span><?php esc_html_e('Vehicles', 'echelon'); ?></span></div>
			<div><strong><?php echo esc_html(count($brand_counts)); ?></strong><span><?php esc_html_e('Makes', 'echelon'); ?></span></div>
			<div><strong><?php esc_html_e('Live', 'echelon'); ?></strong><span><?php esc_html_e('Synced Availability', 'echelon'); ?></span></div>
		</div>

		<form class="fleet-toolbar" method="get" action="<?php echo esc_url(get_post_type_archive_link('fleet_vehicle')); ?>">
			<button class="fleet-filter-toggle" type="button" aria-expanded="false" aria-controls="fleet-filters" data-fleet-filter-toggle>
				<span><small><?php esc_html_e('Refine', 'echelon'); ?></small><?php esc_html_e('Filters', 'echelon'); ?></span>
				<span aria-hidden="true">＋</span>
			</button>
			<label class="fleet-sort">
				<span class="screen-reader-text"><?php esc_html_e('Sort vehicles', 'echelon'); ?></span>
				<select name="fleet_sort" onchange="this.form.submit()">
					<option value="recommended"><?php esc_html_e('Sort: Recommended', 'echelon'); ?></option>
					<option value="price_asc" <?php selected($_GET['fleet_sort'] ?? '', 'price_asc'); ?>><?php esc_html_e('Price: Low to High', 'echelon'); ?></option>
					<option value="price_desc" <?php selected($_GET['fleet_sort'] ?? '', 'price_desc'); ?>><?php esc_html_e('Price: High to Low', 'echelon'); ?></option>
					<option value="horsepower" <?php selected($_GET['fleet_sort'] ?? '', 'horsepower'); ?>><?php esc_html_e('Horsepower', 'echelon'); ?></option>
				</select>
			</label>
		</form>

		<div class="fleet-layout">
			<form id="fleet-filters" class="fleet-filters" method="get" action="<?php echo esc_url(get_post_type_archive_link('fleet_vehicle')); ?>" data-fleet-filters>
				<input type="hidden" name="fleet_sort" value="<?php echo esc_attr($_GET['fleet_sort'] ?? 'recommended'); ?>">
				<div class="fleet-filter-dates">
					<label><?php esc_html_e('Pick-up date', 'echelon'); ?> <b>*</b><input type="date" name="pickup_date" value="<?php echo esc_attr($_GET['pickup_date'] ?? ''); ?>"></label>
					<label><?php esc_html_e('Return date', 'echelon'); ?> <b>*</b><input type="date" name="return_date" value="<?php echo esc_attr($_GET['return_date'] ?? ''); ?>"></label>
					<label><?php esc_html_e('Pick-up time', 'echelon'); ?> <b>*</b><input type="time" name="pickup_time" value="<?php echo esc_attr($_GET['pickup_time'] ?? ''); ?>"></label>
					<label><?php esc_html_e('Return time', 'echelon'); ?> <b>*</b><input type="time" name="return_time" value="<?php echo esc_attr($_GET['return_time'] ?? ''); ?>"></label>
				</div>

				<fieldset><legend><?php esc_html_e('Search', 'echelon'); ?></legend><input type="search" name="fleet_search" placeholder="<?php esc_attr_e('Search for a car…', 'echelon'); ?>" value="<?php echo esc_attr($_GET['fleet_search'] ?? ''); ?>"></fieldset>
				<fieldset><legend><?php esc_html_e('Hourly rental', 'echelon'); ?></legend><div class="fleet-filter-pair"><input type="number" min="0" name="min_price" placeholder="<?php esc_attr_e('Min $', 'echelon'); ?>" value="<?php echo esc_attr($_GET['min_price'] ?? ''); ?>"><input type="number" min="0" name="max_price" placeholder="<?php esc_attr_e('Max $', 'echelon'); ?>" value="<?php echo esc_attr($_GET['max_price'] ?? ''); ?>"></div></fieldset>

				<?php if (!is_wp_error($categories) && $categories) : ?>
					<fieldset><legend><?php esc_html_e('Body type', 'echelon'); ?></legend>
						<?php foreach ($categories as $category) : ?><label class="fleet-check"><input type="checkbox" name="body_type[]" value="<?php echo esc_attr($category->slug); ?>" <?php checked(in_array($category->slug, $selected_categories, true)); ?>><span><?php echo esc_html($category->name); ?></span><small><?php echo esc_html($category->count); ?></small></label><?php endforeach; ?>
					</fieldset>
				<?php endif; ?>

				<?php if ($brand_counts) : ?>
					<fieldset><legend><?php esc_html_e('Make', 'echelon'); ?></legend>
						<div class="fleet-filter-options<?php echo count($brand_counts) > 5 ? ' fleet-filter-options--scroll' : ''; ?>">
							<?php foreach ($brand_counts as $brand => $count) : ?><label class="fleet-check"><input type="checkbox" name="make[]" value="<?php echo esc_attr($brand); ?>" <?php checked(in_array($brand, $selected_brands, true)); ?>><span><?php echo esc_html($brand); ?></span><small><?php echo esc_html($count); ?></small></label><?php endforeach; ?>
						</div>
					</fieldset>
				<?php endif; ?>

				<fieldset><legend><?php esc_html_e('Minimum horsepower', 'echelon'); ?></legend><input type="number" min="0" step="50" name="min_hp" placeholder="<?php esc_attr_e('Any horsepower', 'echelon'); ?>" value="<?php echo esc_attr($_GET['min_hp'] ?? ''); ?>"></fieldset>
				<fieldset><legend><?php esc_html_e('Seats', 'echelon'); ?></legend><div class="fleet-seat-options"><?php foreach (['' => __('Any', 'echelon'), '2' => '2', '4' => '4', '5' => '5+'] as $value => $label) : ?><label><input type="radio" name="seats" value="<?php echo esc_attr($value); ?>" <?php checked($selected_seats, $value); ?>><span><?php echo esc_html($label); ?></span></label><?php endforeach; ?></div></fieldset>

				<div class="fleet-filter-actions"><a class="btn btn--outline" href="<?php echo esc_url(get_post_type_archive_link('fleet_vehicle')); ?>"><?php esc_html_e('Clear all', 'echelon'); ?></a><button class="btn btn--primary" type="submit"><?php esc_html_e('Apply filters', 'echelon'); ?></button></div>
			</form>

			<div class="fleet-results">
				<?php if (have_posts()) : ?>
					<div class="fleet-grid"><?php while (have_posts()) : the_post(); get_template_part('template-parts/fleet/card'); endwhile; ?></div>
					<?php the_posts_pagination(['prev_text' => __('Previous', 'echelon'), 'next_text' => __('Next', 'echelon')]); ?>
				<?php else : ?>
					<div class="fleet-empty"><h2><?php esc_html_e('No vehicles match those filters.', 'echelon'); ?></h2><p><?php esc_html_e('Clear a filter or try a broader search.', 'echelon'); ?></p></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
