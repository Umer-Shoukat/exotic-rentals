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
			<li class="primary-nav__item--mega" data-mega-menu-root><a class="primary-nav__link" href="<?php echo esc_url(get_post_type_archive_link('service')); ?>" aria-haspopup="true" aria-expanded="false" data-mega-menu-trigger><?php esc_html_e('Services', 'echelon'); ?><?php echelon_icon('chevron-down'); ?></a><?php echo echelon_services_mega_menu(); ?></li>
			<li class="primary-nav__item--mega" data-mega-menu-root><a class="primary-nav__link" href="<?php echo esc_url(get_post_type_archive_link('location')); ?>" aria-haspopup="true" aria-expanded="false" data-mega-menu-trigger><?php esc_html_e('Locations', 'echelon'); ?><?php echelon_icon('chevron-down'); ?></a><?php echo echelon_locations_mega_menu(); ?></li>
			<li><a class="primary-nav__link" href="<?php echo esc_url(home_url('/about')); ?>"><?php esc_html_e('About', 'echelon'); ?></a></li>
			<li><a class="primary-nav__link" href="<?php echo esc_url(home_url('/contact')); ?>"><?php esc_html_e('Contact', 'echelon'); ?></a></li>
		</ul>
		<?php
	}
	?>
</nav>
