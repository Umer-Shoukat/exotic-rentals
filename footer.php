</main><!-- #main -->

<footer class="site-footer">
	<div class="site-footer__glow" aria-hidden="true"></div>
	<div class="site-footer__grid">
		<?php get_template_part('template-parts/global/footer-columns'); ?>
	</div>
	<?php
	$footer_locations = get_posts([
		'post_type'      => 'location',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
		'no_found_rows'  => true,
	]);
	$footer_locations = array_values(array_filter($footer_locations, static function ($location) {
		return !preg_match('/\bdemo\b|\bsample\b/i', get_the_title($location));
	}));
	?>
	<?php if ($footer_locations) : ?>
		<nav class="site-footer__areas" aria-label="<?php esc_attr_e('Service areas', 'echelon'); ?>">
			<span class="site-footer__areas-label"><?php esc_html_e('Service Areas', 'echelon'); ?></span>
			<ul>
				<?php foreach ($footer_locations as $location) : ?>
					<li><a href="<?php echo esc_url(get_permalink($location)); ?>"><?php echo esc_html(get_the_title($location)); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>
	<div class="site-footer__bottom">
		<div class="site-footer__bottom-inner">
			<p><?php echo esc_html(echelon_setting('footer_copyright')); ?></p>
			<div class="legal-links">
				<a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php esc_html_e('Privacy Policy', 'echelon'); ?></a>
				<?php $terms_page = get_page_by_path('terms-and-conditions'); ?>
				<?php if ($terms_page) : ?>
					<a href="<?php echo esc_url(get_permalink($terms_page)); ?>"><?php esc_html_e('Terms & Conditions', 'echelon'); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
