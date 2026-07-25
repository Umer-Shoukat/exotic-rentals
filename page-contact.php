<?php
/**
 * Template Name: Contact Page
 *
 * Contact details and chauffeur inquiry form.
 */

get_header();

$address = echelon_setting('contact_address', 'New York City & Long Island — By Appointment');
$email = echelon_setting('contact_email', 'concierge@example.com');
$phone = echelon_setting('contact_phone', '+1 (212) 555-0100');
$phone_href = preg_replace('/[^0-9+]/', '', $phone);
$vehicles = get_posts([
    'post_type'      => 'fleet_vehicle',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
]);

while (have_posts()) : the_post();
    $hero_title = echelon_field('contact_hero_title', get_the_ID(), __("Let's Plan Your Drive", 'echelon'));
    $hero_accent = echelon_field('contact_hero_accent', get_the_ID(), __('Drive', 'echelon'));
    $hero_description = echelon_field('contact_hero_description', get_the_ID(), __("Tell our concierge what you have in mind. We'll help with the vehicle, chauffeur, timing, and every detail in between.", 'echelon'));
    if (stripos($hero_description, 'vehicle, timing, delivery') !== false) {
        $hero_description = __("Tell our concierge what you have in mind. We'll help with the vehicle, chauffeur, timing, and every detail in between.", 'echelon');
    }
    get_template_part('template-parts/global/inner-hero', null, [
        'eyebrow'     => echelon_field('contact_hero_eyebrow', get_the_ID(), __('Contact Us', 'echelon')),
        'title'       => echelon_accent_heading($hero_title, $hero_accent),
        'description' => $hero_description,
        'image'       => echelon_field('contact_hero_image', get_the_ID(), null),
    ]);
    $intro_eyebrow = echelon_field('contact_intro_eyebrow', get_the_ID(), __('Our Concierge', 'echelon'));
    $intro_heading = echelon_field('contact_intro_heading', get_the_ID(), __('Start The Conversation', 'echelon'));
    $intro_description = echelon_field('contact_intro_description', get_the_ID(), __('Reach us directly or send an inquiry using the form. Our team will respond as soon as possible.', 'echelon'));
    $phone_label = echelon_field('contact_phone_label', get_the_ID(), __('Call Us', 'echelon'));
    $email_label = echelon_field('contact_email_label', get_the_ID(), __('Email Us', 'echelon'));
    $address_label = echelon_field('contact_address_label', get_the_ID(), __('Where We Serve', 'echelon'));
    if ('Visit Us' === $address_label) {
        $address_label = __('Where We Serve', 'echelon');
    }
    $form_description = echelon_field('contact_form_description', get_the_ID(), '');
    if (!$form_description || stripos($form_description, 'preferred form block or shortcode') !== false) {
        $form_description = __('Submit your trip details and our concierge team will confirm vehicle availability, chauffeur scheduling, and pricing.', 'echelon');
    }
    ?>
	<section class="section contact-page">
		<div class="container contact-page__grid">
			<div class="contact-page__details">
				<p class="eyebrow"><?php echo esc_html($intro_eyebrow); ?></p>
				<h2><?php echo esc_html($intro_heading); ?></h2>
				<p><?php echo esc_html($intro_description); ?></p>
				<div class="contact-methods">
					<a class="contact-method" href="tel:<?php echo esc_attr($phone_href); ?>">
						<span class="contact-method__icon"><?php echelon_icon('phone'); ?></span>
						<span><small><?php echo esc_html($phone_label); ?></small><strong><?php echo esc_html($phone); ?></strong></span>
					</a>
					<a class="contact-method" href="mailto:<?php echo esc_attr(antispambot($email)); ?>">
						<span class="contact-method__icon"><?php echelon_icon('mail'); ?></span>
						<span><small><?php echo esc_html($email_label); ?></small><strong><?php echo esc_html(antispambot($email)); ?></strong></span>
					</a>
					<div class="contact-method">
						<span class="contact-method__icon"><?php echelon_icon('pin'); ?></span>
						<span><small><?php echo esc_html($address_label); ?></small><strong><?php echo esc_html($address); ?></strong></span>
					</div>
				</div>
			</div>
			<div class="contact-page__form" id="contact-form">
				<?php if (trim(get_the_content())) : ?>
					<div class="entry-content"><?php the_content(); ?></div>
				<?php else : ?>
					<div class="contact-form">
						<p class="eyebrow"><?php echo esc_html(echelon_field('contact_form_eyebrow', get_the_ID(), __('Send An Inquiry', 'echelon'))); ?></p>
						<h2><?php echo esc_html(echelon_field('contact_form_heading', get_the_ID(), __('Contact Form', 'echelon'))); ?></h2>
						<p class="contact-form__intro"><?php echo esc_html($form_description); ?></p>

						<?php if (isset($_GET['contact']) && 'received' === sanitize_key(wp_unslash($_GET['contact']))) : ?>
							<div class="contact-form__notice contact-form__notice--success" role="status"><?php esc_html_e('Thank you. Your inquiry has been sent and our concierge will follow up shortly.', 'echelon'); ?></div>
						<?php elseif (!empty($_GET['contact_error'])) : ?>
							<div class="contact-form__notice contact-form__notice--error" role="alert"><?php esc_html_e('We could not send your inquiry. Please review the required fields and try again, or contact our concierge directly.', 'echelon'); ?></div>
						<?php endif; ?>

						<form class="contact-inquiry-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
							<input type="hidden" name="action" value="echelon_submit_contact">
							<input type="hidden" name="submission_token" value="<?php echo esc_attr(wp_generate_uuid4()); ?>">
							<?php wp_nonce_field('echelon_submit_contact', 'echelon_contact_nonce'); ?>
							<div class="contact-inquiry-form__honeypot" aria-hidden="true">
								<label for="contact-website"><?php esc_html_e('Website', 'echelon'); ?></label>
								<input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off">
							</div>
							<div class="contact-inquiry-form__grid">
								<label class="field">
									<span class="field__label"><?php esc_html_e('Name', 'echelon'); ?> *</span>
									<input class="field__control" name="contact_name" type="text" maxlength="100" autocomplete="name" required>
								</label>
								<label class="field">
									<span class="field__label"><?php esc_html_e('Phone', 'echelon'); ?> *</span>
									<input class="field__control" name="contact_phone" type="tel" maxlength="30" autocomplete="tel" required>
								</label>
								<label class="field">
									<span class="field__label"><?php esc_html_e('Email', 'echelon'); ?> *</span>
									<input class="field__control" name="contact_email" type="email" maxlength="254" autocomplete="email" required>
								</label>
								<label class="field">
									<span class="field__label"><?php esc_html_e('Requested Date', 'echelon'); ?> *</span>
									<input class="field__control" name="requested_date" type="date" min="<?php echo esc_attr(wp_date('Y-m-d')); ?>" required>
								</label>
								<label class="field">
									<span class="field__label"><?php esc_html_e('Pickup Location', 'echelon'); ?> *</span>
									<input class="field__control" name="pickup_location" type="text" maxlength="180" autocomplete="street-address" required>
								</label>
								<label class="field">
									<span class="field__label"><?php esc_html_e('Destination', 'echelon'); ?> *</span>
									<input class="field__control" name="destination" type="text" maxlength="180" required>
								</label>
								<label class="field">
									<span class="field__label"><?php esc_html_e('Requested Vehicle', 'echelon'); ?></span>
									<select class="field__control" name="requested_vehicle">
										<option value=""><?php esc_html_e('No preference', 'echelon'); ?></option>
										<?php foreach ($vehicles as $vehicle) : ?>
											<option value="<?php echo esc_attr($vehicle->ID); ?>"><?php echo esc_html(get_the_title($vehicle)); ?></option>
										<?php endforeach; ?>
									</select>
								</label>
								<label class="field">
									<span class="field__label"><?php esc_html_e('Service Type', 'echelon'); ?> *</span>
									<select class="field__control" name="service_type" required>
										<option value=""><?php esc_html_e('Select a service', 'echelon'); ?></option>
										<option value="wedding"><?php esc_html_e('Wedding', 'echelon'); ?></option>
										<option value="corporate"><?php esc_html_e('Corporate', 'echelon'); ?></option>
										<option value="airport"><?php esc_html_e('Airport', 'echelon'); ?></option>
										<option value="prom"><?php esc_html_e('Prom', 'echelon'); ?></option>
										<option value="photoshoot"><?php esc_html_e('Photoshoot', 'echelon'); ?></option>
										<option value="other"><?php esc_html_e('Other', 'echelon'); ?></option>
									</select>
								</label>
								<label class="field">
									<span class="field__label"><?php esc_html_e('Number of Passengers', 'echelon'); ?> *</span>
									<input class="field__control" name="passengers" type="number" min="1" max="99" required>
								</label>
								<label class="field contact-inquiry-form__details">
									<span class="field__label"><?php esc_html_e('Additional Details', 'echelon'); ?></span>
									<textarea class="field__control" name="additional_details" rows="5" maxlength="3000"></textarea>
								</label>
							</div>
							<p class="contact-inquiry-form__disclaimer"><?php esc_html_e('Submitting this form sends a request only. Your booking is not confirmed until our concierge verifies availability, scheduling, and pricing.', 'echelon'); ?></p>
							<button class="btn btn--primary" type="submit"><?php echo esc_html(echelon_field('contact_form_button_label', get_the_ID(), __('Email Concierge', 'echelon'))); ?><?php echelon_icon('arrow-right'); ?></button>
						</form>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endwhile; ?>

<?php get_footer(); ?>
