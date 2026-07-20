<?php
/**
 * Home: sticky-feel booking bar under the hero (vehicle search, pickup/return
 * dates, location).
 */
?>
<section class="booking-widget" data-reveal>
	<div class="container">
		<form class="booking-widget__form" action="<?php echo esc_url(home_url('/fleet')); ?>" method="get">
			<div class="field booking-widget__field-vehicle">
				<label class="field__label" for="vehicle-search"><?php esc_html_e('Vehicle', 'echelon'); ?></label>
				<div class="field__control-wrap">
					<svg class="field__search-icon" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.5"/><path d="M18 18L13.5 13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
					<input class="field__control" type="text" id="vehicle-search" name="s" placeholder="<?php esc_attr_e('Search for a car…', 'echelon'); ?>" autocomplete="off">
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
					<label class="field__label" for="pickup-location"><?php esc_html_e('Location', 'echelon'); ?></label>
					<div class="field__control-wrap">
						<select class="field__control" id="pickup-location" name="location">
							<option value="new-york"><?php esc_html_e('New York', 'echelon'); ?></option>
							<option value="new-jersey"><?php esc_html_e('New Jersey', 'echelon'); ?></option>
							<option value="connecticut"><?php esc_html_e('Connecticut', 'echelon'); ?></option>
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
