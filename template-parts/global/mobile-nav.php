<?php
/**
 * Mobile slide-in navigation panel.
 */
?>
<nav id="mobile-nav" class="mobile-nav" data-mobile-nav aria-label="<?php esc_attr_e('Mobile navigation', 'echelon'); ?>" aria-hidden="true">
	<?php
	if (has_nav_menu('primary')) {
		wp_nav_menu([
			'theme_location' => 'primary',
			'container'      => false,
			'items_wrap'     => '<ul class="mobile-nav__list">%3$s</ul>',
			'walker'         => new Echelon_Mobile_Nav_Walker(),
		]);
	} else {
		$fallback_items = [
			['Fleet', '/fleet/', 'gauge'],
			['Services', '/services/', 'star'],
			['Locations', '/locations/', 'pin'],
			['About', '/about/', 'shield-check'],
			['Contact', '/contact/', 'mail'],
		];
		?>
		<ul class="mobile-nav__list">
			<?php foreach ($fallback_items as [$label, $path, $icon]) : ?>
				<li class="mobile-nav__item">
					<a class="mobile-nav__link" href="<?php echo esc_url(home_url($path)); ?>">
						<span class="mobile-nav__link-main"><span class="mobile-nav__link-icon"><?php echelon_icon($icon); ?></span><span><?php echo esc_html__($label, 'echelon'); ?></span></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
	?>
	<a class="btn btn--primary btn--block mobile-nav__cta" href="<?php echo esc_url(home_url('/fleet')); ?>">
		<?php esc_html_e('Book Now', 'echelon'); ?>
	</a>
</nav>
