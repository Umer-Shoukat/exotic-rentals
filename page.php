<?php
/**
 * Default page template.
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
				<h1 class="section-heading__title"><?php the_title(); ?></h1>
			</header>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
			<?php
		endwhile;
		?>
	</div>
</article>

<?php get_footer(); ?>
