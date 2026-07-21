<?php
/**
 * 404 template.
 */

get_header();
?>

<section class="error-404">
	<div class="container container--narrow">
		<p class="error-404__code" aria-hidden="true">404</p>
		<p class="eyebrow eyebrow--center"><?php esc_html_e('Page Not Found', 'echelon'); ?></p>
		<h1 class="section-heading__title"><?php esc_html_e('Wrong Turn.', 'echelon'); ?></h1>
		<p><?php esc_html_e("The page you're looking for has taken a detour. Let's get you back on the road.", 'echelon'); ?></p>
		<div class="error-404__actions">
			<a class="btn btn--primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back to Home', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a>
			<a class="btn btn--outline" href="<?php echo esc_url(home_url('/fleet/')); ?>"><?php esc_html_e('Explore The Fleet', 'echelon'); ?></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
