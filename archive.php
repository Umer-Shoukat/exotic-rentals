<?php
/**
 * Generic archive template (fleet, locations, etc. — refined further in
 * Phase B once dedicated archive designs exist).
 */

get_header();
?>

<section class="section">
	<div class="container">
		<header class="section-heading">
			<h1 class="section-heading__title"><?php the_archive_title(); ?></h1>
			<?php the_archive_description('<div class="section-heading__desc">', '</div>'); ?>
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
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
