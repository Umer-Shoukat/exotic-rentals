<?php
/**
 * Mobile slide-in navigation panel.
 */
?>
<div id="mobile-nav" class="mobile-nav" data-mobile-nav>
	<?php
	if (has_nav_menu('primary')) {
		wp_nav_menu([
			'theme_location' => 'primary',
			'container'      => false,
			'items_wrap'     => '<ul class="mobile-nav__list">%3$s</ul>',
			'walker'         => new Echelon_Mobile_Nav_Walker(),
		]);
	} else {
		?>
		<ul class="mobile-nav__list">
			<li class="mobile-nav__item"><a class="mobile-nav__link" href="<?php echo esc_url(home_url('/fleet')); ?>"><span><?php esc_html_e('Fleet', 'echelon'); ?></span></a></li>
			<li class="mobile-nav__item"><a class="mobile-nav__link" href="<?php echo esc_url(home_url('/about')); ?>"><span><?php esc_html_e('About', 'echelon'); ?></span></a></li>
			<li class="mobile-nav__item"><a class="mobile-nav__link" href="<?php echo esc_url(home_url('/contact')); ?>"><span><?php esc_html_e('Contact', 'echelon'); ?></span></a></li>
		</ul>
		<?php
	}
	?>
	<a class="btn btn--primary btn--block mobile-nav__cta" href="<?php echo esc_url(home_url('/fleet')); ?>">
		<?php esc_html_e('Book Now', 'echelon'); ?>
	</a>
</div>
