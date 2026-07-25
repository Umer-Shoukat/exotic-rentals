<?php
/**
 * Template Name: Contact Page
 *
 * Contact page template. The page content is the integration point for a
 * form block or shortcode supplied by the site's form provider.
 */

get_header();

$address = echelon_setting('contact_address', '8500 Beverly Blvd, Los Angeles CA');
$email = echelon_setting('contact_email', 'concierge@exoticrental.com');
$phone = echelon_setting('contact_phone', '+1 (310) 555-0199');
$phone_href = preg_replace('/[^0-9+]/', '', $phone);

while (have_posts()) : the_post();
    $hero_title = echelon_field('contact_hero_title', get_the_ID(), __("Let's Plan Your Drive", 'echelon'));
    $hero_accent = echelon_field('contact_hero_accent', get_the_ID(), __('Drive', 'echelon'));
    $hero_description = echelon_field('contact_hero_description', get_the_ID(), get_the_excerpt() ?: __('Tell our concierge what you have in mind. We will help with the vehicle, timing, delivery, and every detail in between.', 'echelon'));
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
    $address_label = echelon_field('contact_address_label', get_the_ID(), __('Visit Us', 'echelon'));
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
			<div class="contact-page__form">
				<?php if (trim(get_the_content())) : ?>
					<div class="entry-content"><?php the_content(); ?></div>
				<?php else : ?>
					<div class="contact-form-placeholder">
						<p class="eyebrow"><?php echo esc_html(echelon_field('contact_form_eyebrow', get_the_ID(), __('Send An Inquiry', 'echelon'))); ?></p>
						<h2><?php echo esc_html(echelon_field('contact_form_heading', get_the_ID(), __('Contact Form', 'echelon'))); ?></h2>
						<p><?php echo esc_html(echelon_field('contact_form_description', get_the_ID(), __('Add your preferred form block or shortcode to this page in the WordPress editor.', 'echelon'))); ?></p>
						<a class="btn btn--primary" href="mailto:<?php echo esc_attr(antispambot($email)); ?>"><?php echo esc_html(echelon_field('contact_form_button_label', get_the_ID(), __('Email Concierge', 'echelon'))); ?><?php echelon_icon('arrow-right'); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endwhile; ?>

<?php get_footer(); ?>
