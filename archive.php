<?php
/**
 * Generic archive template (fleet, locations, etc. — refined further in
 * Phase B once dedicated archive designs exist).
 */

get_header();

get_template_part('template-parts/global/inner-hero', null, [
	'eyebrow'     => __('Explore', 'echelon'),
	'title'       => wp_strip_all_tags(get_the_archive_title()),
	'description' => wp_strip_all_tags(get_the_archive_description()),
]);
?>

<section class="section editorial-listing">
	<div class="container">
		<?php if (have_posts()) : ?>
			<div class="post-grid">
				<?php
				while (have_posts()) :
					the_post();
					get_template_part('template-parts/content', get_post_type());
				endwhile;
				?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e('Nothing found.', 'echelon'); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
