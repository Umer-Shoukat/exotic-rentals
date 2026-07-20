<?php
/**
 * Desktop primary navigation.
 */
?>
<nav class="primary-nav" aria-label="<?php esc_attr_e('Primary', 'echelon'); ?>">
	<?php
	if (has_nav_menu('primary')) {
		wp_nav_menu([
			'theme_location' => 'primary',
			'container'      => false,
			'items_wrap'     => '<ul class="primary-nav__list">%3$s</ul>',
			'walker'         => new Echelon_Nav_Walker(),
		]);
	} else {
		?>
		<ul class="primary-nav__list">
			<li><a class="primary-nav__link" href="<?php echo esc_url(home_url('/fleet')); ?>"><?php esc_html_e('Fleet', 'echelon'); ?></a></li>
			<li><a class="primary-nav__link" href="<?php echo esc_url(home_url('/chauffeur-services')); ?>"><?php esc_html_e('Chauffeur Services', 'echelon'); ?></a></li>
			<li><a class="primary-nav__link" href="<?php echo esc_url(home_url('/locations')); ?>"><?php esc_html_e('Locations', 'echelon'); ?></a></li>
			<li><a class="primary-nav__link" href="<?php echo esc_url(home_url('/about')); ?>"><?php esc_html_e('About', 'echelon'); ?></a></li>
			<li><a class="primary-nav__link" href="<?php echo esc_url(home_url('/contact')); ?>"><?php esc_html_e('Contact', 'echelon'); ?></a></li>
		</ul>
		<?php
	}
	?>
</nav>
