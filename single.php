<?php
/**
 * Single post (journal entry).
 */

get_header();
?>

<article class="section">
	<div class="container container--narrow">
		<?php
		while (have_posts()) :
			the_post();
			?>
			<header class="section-heading">
				<p class="eyebrow"><?php echo esc_html(get_the_date()); ?></p>
				<h1 class="section-heading__title"><?php the_title(); ?></h1>
			</header>
			<?php if (has_post_thumbnail()) : ?>
				<div class="single-thumbnail">
					<?php the_post_thumbnail('vehicle-hero'); ?>
				</div>
			<?php endif; ?>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
			<?php
		endwhile;
		?>
	</div>
</article>

<?php get_footer(); ?>
