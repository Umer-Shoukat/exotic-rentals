<?php
/**
 * Footer column grid: brand, quick links, collection, contact.
 */

$collection = get_posts([
    'post_type'      => 'fleet_vehicle',
    'posts_per_page' => 4,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
]);
?>
<div class="footer-col footer-col--brand">
	<a class="footer-logo" href="<?php echo esc_url(home_url('/')); ?>">
		<img src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/footer-logo.png'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" width="207" height="47" loading="lazy" decoding="async">
	</a>
	<p><?php echo esc_html(echelon_setting('footer_tagline')); ?></p>
	<div class="social-links">
		<?php foreach (['instagram', 'x', 'facebook'] as $network) :
			$url = echelon_setting('social_' . $network);
			if (!$url) continue;
			?>
			<a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(ucfirst($network)); ?>">
				<img src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/footer-' . $network . '.svg'); ?>" alt="" width="20" height="20">
			</a>
		<?php endforeach; ?>
	</div>
</div>

<div class="footer-col">
	<h3 class="footer-col__title"><?php esc_html_e('Quick Links', 'echelon'); ?></h3>
	<?php
	if (has_nav_menu('footer')) {
		wp_nav_menu([
			'theme_location' => 'footer',
			'container'      => false,
			'items_wrap'     => '<ul class="footer-col__list">%3$s</ul>',
			'depth'          => 1,
		]);
	} else {
		?>
		<ul class="footer-col__list">
			<li><a href="<?php echo esc_url(home_url('/fleet')); ?>"><?php esc_html_e('Fleet', 'echelon'); ?></a></li>
			<li><a href="<?php echo esc_url(home_url('/how-it-works')); ?>"><?php esc_html_e('How It Works', 'echelon'); ?></a></li>
			<li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php esc_html_e('About', 'echelon'); ?></a></li>
			<li><a href="<?php echo esc_url(home_url('/locations')); ?>"><?php esc_html_e('Location', 'echelon'); ?></a></li>
			<li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php esc_html_e('Contact', 'echelon'); ?></a></li>
		</ul>
		<?php
	}
	?>
</div>

<div class="footer-col">
	<h3 class="footer-col__title"><?php esc_html_e('The Collection', 'echelon'); ?></h3>
	<ul class="footer-col__list">
		<?php if ($collection) : ?>
			<?php foreach ($collection as $vehicle) : ?>
				<li><a href="<?php echo esc_url(get_permalink($vehicle)); ?>"><?php echo esc_html(get_the_title($vehicle)); ?></a></li>
			<?php endforeach; ?>
		<?php else : ?>
			<li><a href="<?php echo esc_url(home_url('/fleet')); ?>"><?php esc_html_e('Mercedes-Maybach S-Class', 'echelon'); ?></a></li>
			<li><a href="<?php echo esc_url(home_url('/fleet')); ?>"><?php esc_html_e('Mercedes-Maybach GLS 600', 'echelon'); ?></a></li>
			<li><a href="<?php echo esc_url(home_url('/fleet')); ?>"><?php esc_html_e('Mercedes-Maybach GLS', 'echelon'); ?></a></li>
			<li><a href="<?php echo esc_url(home_url('/fleet')); ?>"><?php esc_html_e('Rolls-Royce Cullinan', 'echelon'); ?></a></li>
		<?php endif; ?>
	</ul>
</div>

<div class="footer-col">
	<h3 class="footer-col__title"><?php esc_html_e('Contact', 'echelon'); ?></h3>
	<ul class="footer-col__list footer-col__contact">
		<li><img class="footer-contact-icon footer-contact-icon--pin" src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/footer-pin.svg'); ?>" alt=""><span><?php echo esc_html(echelon_setting('contact_address')); ?></span></li>
		<li><img class="footer-contact-icon footer-contact-icon--mail" src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/footer-mail.svg'); ?>" alt=""><a href="mailto:<?php echo esc_attr(echelon_setting('contact_email')); ?>"><?php echo esc_html(echelon_setting('contact_email')); ?></a></li>
		<li><img class="footer-contact-icon footer-contact-icon--phone" src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/footer-phone.svg'); ?>" alt=""><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', echelon_setting('contact_phone'))); ?>"><?php echo esc_html(echelon_setting('contact_phone')); ?></a></li>
	</ul>
</div>
