<?php
/**
 * 404 template.
 */

get_header();
?>

<section class="section error-404">
	<div class="container container--narrow">
		<p class="eyebrow"><?php esc_html_e('404', 'echelon'); ?></p>
		<h1 class="section-heading__title"><?php esc_html_e('Wrong Turn.', 'echelon'); ?></h1>
		<p><?php esc_html_e("The page you're looking for has taken a detour. Let's get you back on the road.", 'echelon'); ?></p>
		<a class="btn btn--primary" href="<?php echo esc_url(home_url('/')); ?>">
			<?php esc_html_e('Back to Home', 'echelon'); ?>
			<?php echelon_icon('arrow-right'); ?>
		</a>
	</div>
</section>

<?php get_footer(); ?>
