</main><!-- #main -->

<footer class="site-footer">
	<div class="site-footer__glow" aria-hidden="true"></div>
	<div class="site-footer__grid">
		<?php get_template_part('template-parts/global/footer-columns'); ?>
	</div>
	<div class="site-footer__bottom">
		<div class="site-footer__bottom-inner">
			<p><?php echo esc_html(echelon_setting('footer_copyright')); ?></p>
			<div class="legal-links">
				<a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php esc_html_e('Privacy Policy', 'echelon'); ?></a>
				<a href="<?php echo esc_url(home_url('/terms-of-service')); ?>"><?php esc_html_e('Terms Of Services', 'echelon'); ?></a>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
