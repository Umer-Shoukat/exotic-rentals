<?php
/**
 * Fallback template: post loop (journal archive / search results).
 */

get_header();
?>

<section class="section">
	<div class="container">
		<header class="section-heading">
			<h1 class="section-heading__title">
				<?php
				if (is_search()) {
					printf(esc_html__('Search Results for: %s', 'echelon'), '<span class="accent">' . esc_html(get_search_query()) . '</span>');
				} else {
					esc_html_e('The Journal', 'echelon');
				}
				?>
			</h1>
		</header>

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
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
