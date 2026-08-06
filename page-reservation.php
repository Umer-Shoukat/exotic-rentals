<?php
/**
 * Template Name: Reservation
 * Four-step reservation request flow based on the approved Figma frames.
 */

$vehicles = get_posts([
    'post_type' => 'fleet_vehicle', 'post_status' => 'publish', 'posts_per_page' => -1,
    'orderby' => ['menu_order' => 'ASC', 'date' => 'DESC'],
]);
$vehicles = array_values(array_filter($vehicles, static function ($vehicle) {
    return echelon_vehicle_supports_service($vehicle->ID, 'chauffeur') || echelon_vehicle_supports_service($vehicle->ID, 'self_drive');
}));
$locations = get_posts([
    'post_type' => 'location', 'post_status' => 'publish', 'posts_per_page' => -1,
    'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
]);
$preselected_vehicle = isset($_GET['vehicle']) ? absint($_GET['vehicle']) : 0;
$vehicle_ids = array_map(static fn($vehicle) => (int) $vehicle->ID, $vehicles);
$has_preselected_vehicle = $preselected_vehicle > 0 && in_array($preselected_vehicle, $vehicle_ids, true);
$initial_pickup = isset($_GET['pickup_date']) ? sanitize_text_field(wp_unslash($_GET['pickup_date'])) : '';
$initial_return = isset($_GET['return_date']) ? sanitize_text_field(wp_unslash($_GET['return_date'])) : '';
$initial_pickup_time = isset($_GET['pickup_time']) ? sanitize_text_field(wp_unslash($_GET['pickup_time'])) : '';
$initial_return_time = isset($_GET['return_time']) ? sanitize_text_field(wp_unslash($_GET['return_time'])) : '';
$time_options = [];
for ($minutes = 0; $minutes < 24 * 60; $minutes += 30) {
    $value = sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    $time_options[$value] = wp_date('g:i A', strtotime($value));
}
$initial_location = isset($_GET['pickup_location']) ? sanitize_title(wp_unslash($_GET['pickup_location'])) : '';
$received = isset($_GET['reservation']) && $_GET['reservation'] === 'received';
$reference = isset($_GET['reference']) ? sanitize_text_field(wp_unslash($_GET['reference'])) : '';
$error_code = isset($_GET['reservation_error']) ? sanitize_key($_GET['reservation_error']) : '';
$reservation_settings = echelon_reservation_settings();
$minimum_chauffeur_hours = (int) $reservation_settings['minimum_chauffeur_hours'];
$minimum_self_drive_hours = (int) $reservation_settings['minimum_self_drive_hours'];
$minimum_driver_age = (int) $reservation_settings['minimum_driver_age'];
$maximum_upload_mb = (int) $reservation_settings['maximum_upload_mb'];
$errors = [
    'session' => __('Your session expired. Please submit the form again.', 'echelon'),
    'invalid' => __('We could not validate this request.', 'echelon'),
    'vehicle' => __('Please select an available vehicle.', 'echelon'),
    'location' => __('Please select valid pick-up and return locations.', 'echelon'),
    'dates' => __('Return must be after pick-up and must meet the minimum duration for the selected service.', 'echelon'),
    'details' => __('Please complete all required details and accept the rental terms.', 'echelon'),
    'name' => __('Please enter your full name.', 'echelon'),
    'email' => __('Please enter a valid email address.', 'echelon'),
    'phone' => __('Please enter a valid phone number.', 'echelon'),
    'licence' => __('Please enter a valid driving licence number.', 'echelon'),
    'service' => __('Please select a valid rental service for this vehicle.', 'echelon'),
    'trip' => __('Please select a valid trip type.', 'echelon'),
    'documents' => sprintf(__('Please upload the required verification documents using a supported file type (maximum %d MB each).', 'echelon'), $maximum_upload_mb),
    'age' => sprintf(__('Drivers must be at least %d years old.', 'echelon'), $minimum_driver_age),
    'terms' => __('You must accept the rental terms and insurance policy.', 'echelon'),
    'duplicate' => __('This reservation was already submitted. Please check your email.', 'echelon'),
    'save' => __('We could not save your reservation. Please try again or call the concierge.', 'echelon'),
];

get_header();
?>
<div id="reservation-main" class="reservation" data-reservation-flow data-initial-step="1" data-minimum-chauffeur-hours="<?php echo esc_attr($minimum_chauffeur_hours); ?>" data-minimum-self-drive-hours="<?php echo esc_attr($minimum_self_drive_hours); ?>" data-minimum-driver-age="<?php echo esc_attr($minimum_driver_age); ?>" data-maximum-upload-mb="<?php echo esc_attr($maximum_upload_mb); ?>">
    <div class="reservation__shell">
        <?php if ($received) : ?>
            <section class="reservation-success" aria-labelledby="reservation-success-title">
                <p class="reservation__eyebrow"><?php esc_html_e('Request Received', 'echelon'); ?></p>
                <h1 id="reservation-success-title"><?php esc_html_e('Your Drive Is In Motion.', 'echelon'); ?></h1>
                <p><?php esc_html_e('A concierge will review availability and contact you within one hour. We also sent a copy of your request by email.', 'echelon'); ?></p>
                <?php if ($reference) : ?><p class="reservation-success__reference"><?php esc_html_e('Reference', 'echelon'); ?> <strong><?php echo esc_html($reference); ?></strong></p><?php endif; ?>
                <a class="btn btn--primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Return Home', 'echelon'); ?></a>
            </section>
        <?php elseif (!$vehicles || !$locations) : ?>
            <section class="reservation-success"><h1><?php esc_html_e('Reservations Are Being Prepared.', 'echelon'); ?></h1><p><?php esc_html_e('Please add at least one fleet vehicle and one active location before accepting reservations.', 'echelon'); ?></p></section>
        <?php else : ?>
            <header class="reservation__intro">
                <p class="reservation__eyebrow"><?php esc_html_e('The Collection', 'echelon'); ?></p>
                <h1><?php esc_html_e('Reserve Your', 'echelon'); ?> <span><?php esc_html_e('Drive.', 'echelon'); ?></span></h1>
                <p><?php esc_html_e('Four quiet steps. A concierge will personally confirm every detail within the hour.', 'echelon'); ?></p>
            </header>

            <?php if ($error_code && isset($errors[$error_code])) : ?><div class="reservation__alert" role="alert"><?php echo esc_html($errors[$error_code]); ?></div><?php endif; ?>

            <form class="reservation__form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="action" value="echelon_submit_reservation">
                <input type="hidden" name="billing_type" value="">
                <input type="hidden" name="hours_required" value="">
                <input type="hidden" name="submission_token" value="<?php echo esc_attr(wp_generate_uuid4()); ?>">
                <input type="text" class="reservation__honeypot" name="company" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
                <?php wp_nonce_field('echelon_submit_reservation', 'echelon_reservation_nonce'); ?>

                <ol class="reservation-progress" aria-label="<?php esc_attr_e('Reservation progress', 'echelon'); ?>">
                    <?php foreach ([__('Select Vehicle', 'echelon'), __('Trip Details', 'echelon'), __('Your Details', 'echelon'), __('Review', 'echelon')] as $index => $label) : ?>
                        <li class="<?php echo $index === 0 ? 'is-active' : ''; ?>" data-progress-step="<?php echo esc_attr($index + 1); ?>"><span>0<?php echo esc_html($index + 1); ?></span><strong><?php echo esc_html($label); ?></strong></li>
                    <?php endforeach; ?>
                </ol>

                <div class="reservation__layout">
                    <div class="reservation__content">
                        <section class="reservation-step is-active" data-step="1" aria-labelledby="step-1-title">
                            <div class="reservation-step__heading"><p><?php esc_html_e('Step 01 / Select Vehicle', 'echelon'); ?></p><h2 id="step-1-title"><?php esc_html_e('Choose Your Car', 'echelon'); ?></h2></div>
                            <div class="reservation-step__vehicle-action">
                                <button class="btn btn--primary" type="button" data-reservation-next><?php esc_html_e('Continue With Selected Car', 'echelon'); ?> <?php echelon_icon('arrow-right'); ?></button>
                            </div>
                            <div class="reservation-fleet">
                                <?php foreach ($vehicles as $index => $vehicle) :
                                    $id = $vehicle->ID;
                                    $gallery = echelon_vehicle_gallery($id);
                                    $cover = $gallery[0] ?? get_post_thumbnail_id($id);
                                    $price = (float) echelon_field('price_per_hour', $id, 0);
                                    $daily_price = (float) echelon_field('daily_rental_price', $id, 0);
                                    $minimum_hours = max(3, (int) echelon_field('minimum_booking_hours', $id, 3));
                                    $selected = $has_preselected_vehicle ? $id === $preselected_vehicle : $index === 0;
                                    ?>
                                    <label class="reservation-vehicle<?php echo $selected ? ' is-selected' : ''; ?>" data-vehicle-card data-chauffeur-available="<?php echo echelon_vehicle_supports_service($id, 'chauffeur') ? '1' : '0'; ?>" data-self-drive-available="<?php echo echelon_vehicle_supports_service($id, 'self_drive') ? '1' : '0'; ?>">
                                        <input type="radio" name="vehicle_id" value="<?php echo esc_attr($id); ?>" <?php checked($selected); ?> required
                                            data-vehicle-name="<?php echo esc_attr(get_the_title($id)); ?>" data-vehicle-price="<?php echo esc_attr($price); ?>" data-vehicle-daily-price="<?php echo esc_attr($daily_price); ?>" data-vehicle-minimum-hours="<?php echo esc_attr($minimum_hours); ?>" data-vehicle-image="<?php echo esc_url(wp_get_attachment_image_url(is_array($cover) ? ($cover['ID'] ?? 0) : $cover, 'vehicle-card') ?: ''); ?>">
                                        <div class="reservation-vehicle__media"><?php echelon_media($cover, 'vehicle-card'); ?><span class="reservation-vehicle__check">✓</span></div>
                                        <div class="reservation-vehicle__body">
                                            <div class="reservation-vehicle__top"><h3><?php echo esc_html(get_the_title($id)); ?></h3><?php if ($price > 0 || $daily_price > 0) : ?><span><?php if ($price > 0) : ?><span data-service-price="chauffeur"><?php echo esc_html(echelon_price($price)); ?><em>/<?php esc_html_e('hour', 'echelon'); ?></em></span><?php endif; ?><?php if ($daily_price > 0) : ?><small data-service-price="self_drive"><?php echo esc_html(echelon_price($daily_price)); ?>/<?php esc_html_e('day', 'echelon'); ?></small><?php endif; ?></span><?php endif; ?></div>
                                            <div class="reservation-vehicle__specs"><span><?php echelon_icon('bolt'); ?><?php echo esc_html(echelon_field('horsepower', $id, '—')); ?> HP</span><span><?php echelon_icon('gauge'); ?>0–60 <?php echo esc_html(echelon_field('zero_to_sixty', $id, '—')); ?></span><span><?php echelon_icon('seat'); ?><?php echo esc_html(echelon_field('seats', $id, '—')); ?> <?php esc_html_e('Seats', 'echelon'); ?></span></div>
                                            <span class="reservation-vehicle__select"><?php esc_html_e('Reserve', 'echelon'); ?> <b>→</b></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </section>

                        <section class="reservation-step" data-step="2" aria-labelledby="step-2-title" hidden>
                            <button class="reservation-step__back" type="button" data-reservation-back><?php echo esc_html__('← Back', 'echelon'); ?></button>
                            <div class="reservation-step__heading"><p><?php esc_html_e('Step 02 / Trip Details', 'echelon'); ?></p><h2 id="step-2-title"><?php esc_html_e('When & Where', 'echelon'); ?></h2></div>
                            <fieldset class="reservation-service-options">
                                <legend><?php esc_html_e('How would you like to drive?', 'echelon'); ?> *</legend>
                                <?php foreach (echelon_reservation_service_types() as $value => $label) : ?>
                                    <label><input type="radio" name="rental_service" value="<?php echo esc_attr($value); ?>" required><span><i aria-hidden="true"><?php echelon_icon($value === 'chauffeur' ? 'headset' : 'id-card'); ?></i><b><strong><?php echo esc_html($label); ?></strong><small><?php echo $value === 'chauffeur' ? esc_html(sprintf(__('Hourly service · minimum %d hours', 'echelon'), $minimum_chauffeur_hours)) : esc_html(sprintf(__('Daily service · minimum %d hours', 'echelon'), $minimum_self_drive_hours)); ?></small></b></span></label>
                                <?php endforeach; ?>
                            </fieldset>
                            <div class="reservation-fields">
                                <label data-service-field="chauffeur"><span><?php esc_html_e('Trip Type', 'echelon'); ?><?php echo echelon_reservation_field_required('trip_type') ? ' *' : ''; ?></span><select name="trip_type" data-config-required="<?php echo echelon_reservation_field_required('trip_type') ? '1' : '0'; ?>"><option value=""><?php esc_html_e('Select trip type', 'echelon'); ?></option><?php foreach (echelon_reservation_trip_types() as $value => $label) : ?><option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label>
                                <label><span><?php esc_html_e('Pick-up Date', 'echelon'); ?> *</span><input type="text" name="pickup_date" value="<?php echo esc_attr($initial_pickup); ?>" placeholder="dd/mm/yyyy" data-reservation-date="pickup" required autocomplete="off"></label>
                                <label><span><?php esc_html_e('Return Date', 'echelon'); ?> *</span><input type="text" name="return_date" value="<?php echo esc_attr($initial_return); ?>" placeholder="dd/mm/yyyy" data-reservation-date="return" required autocomplete="off"></label>
                                <label><span><?php esc_html_e('Pick-up Time', 'echelon'); ?> *</span><input type="text" name="pickup_time" value="<?php echo esc_attr($initial_pickup_time); ?>" data-time-picker required autocomplete="off"></label>
                                <label><span><?php esc_html_e('Return Time', 'echelon'); ?> *</span><input type="text" name="return_time" value="<?php echo esc_attr($initial_return_time); ?>" data-time-picker required autocomplete="off"></label>
                                <label><span><?php esc_html_e('Pick-up Location', 'echelon'); ?> *</span><select name="pickup_location_id" required><?php foreach ($locations as $location) : ?><option value="<?php echo esc_attr($location->ID); ?>" <?php selected($initial_location, $location->post_name); ?>><?php echo esc_html(get_the_title($location)); ?></option><?php endforeach; ?></select></label>
                                <label><span><?php esc_html_e('Return Location', 'echelon'); ?> *</span><select name="return_location_id" required><?php foreach ($locations as $location) : ?><option value="<?php echo esc_attr($location->ID); ?>" <?php selected($initial_location, $location->post_name); ?>><?php echo esc_html(get_the_title($location)); ?></option><?php endforeach; ?></select></label>
                                <label data-service-field="self_drive"><span><?php esc_html_e('Estimated Mileage', 'echelon'); ?><?php echo echelon_reservation_field_required('estimated_mileage') ? ' *' : ''; ?></span><select name="estimated_mileage" data-config-required="<?php echo echelon_reservation_field_required('estimated_mileage') ? '1' : '0'; ?>"><option value=""><?php esc_html_e('Select mileage', 'echelon'); ?></option><option value="150">150 mi / day</option><option value="250">250 mi / day</option><option value="unlimited"><?php esc_html_e('Request unlimited', 'echelon'); ?></option></select></label>
                            </div>
                        </section>

                        <section class="reservation-step" data-step="3" aria-labelledby="step-3-title" hidden>
                            <button class="reservation-step__back" type="button" data-reservation-back><?php echo esc_html__('← Back', 'echelon'); ?></button>
                            <div class="reservation-step__heading"><p><?php esc_html_e('Step 03 / Your Details', 'echelon'); ?></p><h2 id="step-3-title"><?php esc_html_e('Your Information', 'echelon'); ?></h2></div>
                            <div class="reservation-fields">
                                <label><span><?php esc_html_e('Full Name', 'echelon'); ?><?php echo echelon_reservation_field_required('customer_name') ? ' *' : ''; ?></span><input type="text" name="customer_name" autocomplete="name" minlength="2" maxlength="100" <?php echo echelon_reservation_field_required('customer_name') ? 'required' : ''; ?>></label>
                                <label><span><?php esc_html_e('Email', 'echelon'); ?><?php echo echelon_reservation_field_required('customer_email') ? ' *' : ''; ?></span><input type="email" name="customer_email" autocomplete="email" maxlength="254" <?php echo echelon_reservation_field_required('customer_email') ? 'required' : ''; ?>></label>
                                <label><span><?php esc_html_e('Phone', 'echelon'); ?><?php echo echelon_reservation_field_required('customer_phone') ? ' *' : ''; ?></span><input type="tel" name="customer_phone" autocomplete="tel" inputmode="tel" minlength="7" maxlength="25" <?php echo echelon_reservation_field_required('customer_phone') ? 'required' : ''; ?>></label>
                                <label data-service-field="self_drive"><span><?php esc_html_e('Driving Licence Number', 'echelon'); ?><?php echo echelon_reservation_field_required('licence_number') ? ' *' : ''; ?></span><input type="text" name="licence_number" minlength="4" maxlength="50" data-config-required="<?php echo echelon_reservation_field_required('licence_number') ? '1' : '0'; ?>"><small><?php esc_html_e('Your information is encrypted in transit and used only for verification.', 'echelon'); ?></small></label>
                                <label data-service-field="self_drive"><span><?php esc_html_e('Driver’s License — Front', 'echelon'); ?><?php echo echelon_reservation_field_required('licence_front') ? ' *' : ''; ?></span><input type="file" name="licence_front" accept=".jpg,.jpeg,.png,image/jpeg,image/png" data-config-required="<?php echo echelon_reservation_field_required('licence_front') ? '1' : '0'; ?>"><small><?php echo esc_html(sprintf(__('JPG or PNG, maximum %d MB.', 'echelon'), $maximum_upload_mb)); ?></small></label>
                                <label data-service-field="self_drive"><span><?php esc_html_e('Driver’s License — Back', 'echelon'); ?><?php echo echelon_reservation_field_required('licence_back') ? ' *' : ''; ?></span><input type="file" name="licence_back" accept=".jpg,.jpeg,.png,image/jpeg,image/png" data-config-required="<?php echo echelon_reservation_field_required('licence_back') ? '1' : '0'; ?>"><small><?php echo esc_html(sprintf(__('JPG or PNG, maximum %d MB.', 'echelon'), $maximum_upload_mb)); ?></small></label>
                                <label data-service-field="self_drive"><span><?php esc_html_e('Insurance Document', 'echelon'); ?><?php echo echelon_reservation_field_required('insurance_document') ? ' *' : ''; ?></span><input type="file" name="insurance_document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png" data-config-required="<?php echo echelon_reservation_field_required('insurance_document') ? '1' : '0'; ?>"><small><?php echo esc_html(sprintf(__('PDF, DOC, DOCX, JPG or PNG, maximum %d MB.', 'echelon'), $maximum_upload_mb)); ?></small></label>
                                <label data-service-field="self_drive"><span><?php esc_html_e('Date of Birth', 'echelon'); ?><?php echo echelon_reservation_field_required('date_of_birth') ? ' *' : ''; ?></span><input type="text" name="date_of_birth" placeholder="dd/mm/yyyy" data-reservation-date="birth" data-config-required="<?php echo echelon_reservation_field_required('date_of_birth') ? '1' : '0'; ?>" autocomplete="bday"></label>
                                <label><span><?php esc_html_e('Occasion', 'echelon'); ?><?php echo echelon_reservation_field_required('occasion') ? ' *' : ''; ?></span><select name="occasion" <?php echo echelon_reservation_field_required('occasion') ? 'required' : ''; ?>><option value=""><?php esc_html_e('Select an occasion', 'echelon'); ?></option><option>Wedding</option><option>Corporate</option><option>Prom</option><option>Photoshoot</option><option>Other</option></select></label>
                            </div>
                        </section>

                        <section class="reservation-step" data-step="4" aria-labelledby="step-4-title" hidden>
                            <button class="reservation-step__back" type="button" data-reservation-back><?php echo esc_html__('← Back', 'echelon'); ?></button>
                            <div class="reservation-step__heading"><p><?php esc_html_e('Step 04 / Review', 'echelon'); ?></p><h2 id="step-4-title"><?php esc_html_e('Review & Confirm', 'echelon'); ?></h2></div>
                            <div class="reservation-review" data-reservation-review></div>
                            <label class="reservation-terms"><input type="checkbox" name="terms_accepted" value="1" required><span data-reservation-terms><?php esc_html_e('I agree to the applicable rental terms and policies.', 'echelon'); ?></span></label>
                            <button class="btn btn--primary btn--block" type="submit"><?php esc_html_e('Confirm Reservation', 'echelon'); ?> <?php echelon_icon('arrow-right'); ?></button>
                            <p class="reservation-review__note"><?php esc_html_e('A concierge will contact you within 1 hour to finalize details.', 'echelon'); ?></p>
                        </section>

                        <div class="reservation-actions">
                            <button class="btn btn--outline" type="button" data-reservation-back><?php esc_html_e('Back', 'echelon'); ?></button>
                            <button class="btn btn--primary" type="button" data-reservation-next><?php esc_html_e('Continue', 'echelon'); ?> <?php echelon_icon('arrow-right'); ?></button>
                        </div>
                    </div>

                    <aside class="reservation-summary" aria-live="polite">
                        <div class="reservation-summary__media"><img src="" alt="" data-summary-image></div>
                        <h2 data-summary-vehicle></h2><p class="reservation-summary__accent"><?php esc_html_e('Selected Vehicle', 'echelon'); ?></p>
                        <dl><div><dt><?php esc_html_e('Service', 'echelon'); ?></dt><dd data-summary-service>—</dd></div><div><dt><?php esc_html_e('Schedule', 'echelon'); ?></dt><dd data-summary-dates>—</dd></div><div><dt data-summary-duration-label><?php esc_html_e('Duration', 'echelon'); ?></dt><dd data-summary-hours>—</dd></div><div><dt><?php esc_html_e('Pick-up', 'echelon'); ?></dt><dd data-summary-pickup>—</dd></div><div><dt><?php esc_html_e('Return', 'echelon'); ?></dt><dd data-summary-return>—</dd></div></dl>
                        <div class="reservation-summary__total"><span><?php esc_html_e('Estimated Total', 'echelon'); ?></span><strong data-summary-total>—</strong></div>
                        <ul><li><?php echelon_icon('phone'); ?><?php esc_html_e('24/7 Concierge Support', 'echelon'); ?></li><li><?php echelon_icon('shield-check'); ?><?php esc_html_e('Fully Insured Fleet', 'echelon'); ?></li><li><?php echelon_icon('truck'); ?><?php esc_html_e('White-Glove Delivery', 'echelon'); ?></li></ul>
                        <div class="reservation-summary__faq"><p><?php esc_html_e('Common Questions', 'echelon'); ?></p><details open><summary><?php esc_html_e("What's included?", 'echelon'); ?></summary><span><?php esc_html_e('Every reservation includes comprehensive insurance, roadside assistance, 24/7 concierge support, and white-glove delivery within 60 miles of our garage.', 'echelon'); ?></span></details><details><summary><?php esc_html_e('Can I cancel?', 'echelon'); ?></summary><span><?php esc_html_e('Your concierge will explain the cancellation terms before confirmation.', 'echelon'); ?></span></details><details><summary><?php esc_html_e('Do you deliver to airports?', 'echelon'); ?></summary><span><?php esc_html_e('Airport delivery is available in supported service zones.', 'echelon'); ?></span></details></div>
                    </aside>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php get_footer(); ?>
