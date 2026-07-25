<?php
/**
 * Home: sticky-feel booking bar under the hero (vehicle search, pickup/return
 * dates, location).
 */
$booking_vehicles = get_posts([
	'post_type' => 'fleet_vehicle', 'post_status' => 'publish', 'posts_per_page' => -1,
	'orderby' => ['menu_order' => 'ASC', 'date' => 'DESC'],
]);
$booking_locations = get_posts([
	'post_type' => 'location', 'post_status' => 'publish', 'posts_per_page' => -1,
	'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
]);
?>
<section class="booking-widget" data-reveal>
	<div class="container">
		<form class="booking-widget__form" action="<?php echo esc_url(home_url('/reservation/')); ?>" method="get">
			<div class="field booking-widget__field-vehicle" data-vehicle-combobox>
				<label class="field__label" for="vehicle-search"><?php esc_html_e('Vehicle', 'echelon'); ?></label>
				<div class="field__control-wrap">
					<svg class="field__search-icon" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.5"/><path d="M18 18L13.5 13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
					<input class="field__control" type="text" id="vehicle-search" placeholder="<?php esc_attr_e('Search for a car…', 'echelon'); ?>" autocomplete="off" role="combobox" aria-autocomplete="list" aria-controls="vehicle-search-results" aria-expanded="false" data-vehicle-search>
					<input type="hidden" name="vehicle" value="" data-vehicle-value>
				</div>
				<div class="vehicle-search-results" id="vehicle-search-results" role="listbox" aria-label="<?php esc_attr_e('Available vehicles', 'echelon'); ?>" data-vehicle-results hidden>
					<div class="vehicle-search-results__items">
						<?php foreach ($booking_vehicles as $index => $vehicle) :
							$vehicle_id = $vehicle->ID;
							$brand = echelon_field('brand', $vehicle_id, '');
							$gallery = echelon_field('gallery', $vehicle_id, []);
							$cover = $gallery[0] ?? get_post_thumbnail_id($vehicle_id);
							$search_text = trim($brand . ' ' . get_the_title($vehicle_id) . ' ' . echelon_field('tagline', $vehicle_id, ''));
							$daily_rate = echelon_field('daily_rental_price', $vehicle_id, '');
							?>
							<button type="button" class="vehicle-search-option" id="vehicle-option-<?php echo esc_attr($vehicle_id); ?>" role="option" aria-selected="false" data-vehicle-option data-vehicle-id="<?php echo esc_attr($vehicle_id); ?>" data-vehicle-label="<?php echo esc_attr(get_the_title($vehicle_id)); ?>" data-vehicle-search-text="<?php echo esc_attr(strtolower($search_text)); ?>"<?php echo $index >= 4 ? ' hidden' : ''; ?>>
								<span class="vehicle-search-option__image"><?php echelon_media($cover, 'thumbnail', '', 'bolt'); ?></span>
								<span class="vehicle-search-option__copy"><strong><?php echo esc_html($brand ?: get_the_title($vehicle_id)); ?></strong><?php if ($brand) : ?><small><?php echo esc_html(get_the_title($vehicle_id)); ?></small><?php endif; ?></span>
								<?php $hourly_rate = echelon_field('price_per_hour', $vehicle_id, ''); ?>
								<?php if ($hourly_rate !== '' || $daily_rate !== '') : ?><span class="vehicle-search-option__price"><?php if ($hourly_rate !== '') : ?><?php echo esc_html(echelon_price($hourly_rate)); ?>/<?php esc_html_e('hour', 'echelon'); ?><?php endif; ?><?php if ($hourly_rate !== '' && $daily_rate !== '') : ?><br><?php endif; ?><?php if ($daily_rate !== '') : ?><?php echo esc_html(echelon_price($daily_rate)); ?>/<?php esc_html_e('day', 'echelon'); ?><?php endif; ?></span><?php endif; ?>
								<span class="vehicle-search-option__arrow" aria-hidden="true">→</span>
							</button>
						<?php endforeach; ?>
						<p class="vehicle-search-results__empty" data-vehicle-empty hidden><?php esc_html_e('No vehicles match your search.', 'echelon'); ?></p>
					</div>
					<a class="vehicle-search-results__all" href="<?php echo esc_url(home_url('/fleet')); ?>"><?php esc_html_e('Browse Full Fleet', 'echelon'); ?><span aria-hidden="true">→</span></a>
				</div>
			</div>

			<div class="booking-widget__row">
				<div class="field booking-widget__field">
					<label class="field__label" for="pickup-date"><?php esc_html_e('Pick - Up Date', 'echelon'); ?></label>
					<div class="field__control-wrap">
						<input class="field__control" type="text" id="pickup-date" name="pickup_date" placeholder="dd/mm/yyyy" data-datepicker autocomplete="off">
						<?php echelon_icon('calendar'); ?>
					</div>
				</div>
				<div class="field booking-widget__field">
					<label class="field__label" for="return-date"><?php esc_html_e('Return Date', 'echelon'); ?></label>
					<div class="field__control-wrap">
						<input class="field__control" type="text" id="return-date" name="return_date" placeholder="dd/mm/yyyy" data-datepicker autocomplete="off">
						<?php echelon_icon('calendar'); ?>
					</div>
				</div>
				<div class="field booking-widget__field">
					<label class="field__label" for="pickup-time"><?php esc_html_e('Pick-up Time', 'echelon'); ?></label>
					<div class="field__control-wrap"><input class="field__control" type="time" id="pickup-time" name="pickup_time"></div>
				</div>
				<div class="field booking-widget__field">
					<label class="field__label" for="return-time"><?php esc_html_e('Return Time', 'echelon'); ?></label>
					<div class="field__control-wrap"><input class="field__control" type="time" id="return-time" name="return_time"></div>
				</div>
				<div class="field booking-widget__field">
					<label class="field__label" for="pickup-location"><?php esc_html_e('Location', 'echelon'); ?></label>
					<div class="field__control-wrap">
						<select class="field__control" id="pickup-location" name="pickup_location">
							<?php foreach ($booking_locations as $location) : ?><option value="<?php echo esc_attr($location->post_name); ?>"><?php echo esc_html(get_the_title($location)); ?></option><?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>

			<div class="booking-widget__actions">
				<a class="btn btn--outline" href="<?php echo esc_url(home_url('/fleet')); ?>">
					<?php esc_html_e('View Our Fleet', 'echelon'); ?>
				</a>
				<button type="submit" class="btn btn--primary">
					<?php esc_html_e('Book Your Ride', 'echelon'); ?>
					<?php echelon_icon('arrow-right'); ?>
				</button>
			</div>
		</form>
	</div>
</section>
