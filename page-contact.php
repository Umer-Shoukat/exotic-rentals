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
    get_template_part('template-parts/global/inner-hero', null, [
        'eyebrow'     => __('Contact Us', 'echelon'),
        'title'       => __('Let\'s Plan Your <span class="accent">Drive</span>', 'echelon'),
        'description' => get_the_excerpt() ?: __('Tell our concierge what you have in mind. We will help with the vehicle, timing, delivery, and every detail in between.', 'echelon'),
    ]);
    ?>
	<section class="section contact-page">
		<div class="container contact-page__grid">
			<div class="contact-page__details">
				<p class="eyebrow"><?php esc_html_e('Our Concierge', 'echelon'); ?></p>
				<h2><?php esc_html_e('Start The Conversation', 'echelon'); ?></h2>
				<p><?php esc_html_e('Reach us directly or send an inquiry using the form. Our team will respond as soon as possible.', 'echelon'); ?></p>
				<div class="contact-methods">
					<a class="contact-method" href="tel:<?php echo esc_attr($phone_href); ?>">
						<span class="contact-method__icon"><?php echelon_icon('phone'); ?></span>
						<span><small><?php esc_html_e('Call Us', 'echelon'); ?></small><strong><?php echo esc_html($phone); ?></strong></span>
					</a>
					<a class="contact-method" href="mailto:<?php echo esc_attr(antispambot($email)); ?>">
						<span class="contact-method__icon"><?php echelon_icon('mail'); ?></span>
						<span><small><?php esc_html_e('Email Us', 'echelon'); ?></small><strong><?php echo esc_html(antispambot($email)); ?></strong></span>
					</a>
					<div class="contact-method">
						<span class="contact-method__icon"><?php echelon_icon('pin'); ?></span>
						<span><small><?php esc_html_e('Visit Us', 'echelon'); ?></small><strong><?php echo esc_html($address); ?></strong></span>
					</div>
				</div>
			</div>
			<div class="contact-page__form">
				<?php if (trim(get_the_content())) : ?>
					<div class="entry-content"><?php the_content(); ?></div>
				<?php else : ?>
					<div class="contact-form-placeholder">
						<p class="eyebrow"><?php esc_html_e('Send An Inquiry', 'echelon'); ?></p>
						<h2><?php esc_html_e('Contact Form', 'echelon'); ?></h2>
						<p><?php esc_html_e('Add your preferred form block or shortcode to this page in the WordPress editor.', 'echelon'); ?></p>
						<a class="btn btn--primary" href="mailto:<?php echo esc_attr(antispambot($email)); ?>"><?php esc_html_e('Email Concierge', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endwhile; ?>

<?php get_footer(); ?>
