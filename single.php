<?php
/**
 * Single post (journal entry).
 */

get_header();

while (have_posts()) : the_post();
	$categories = get_the_category();
	$posts_page_id = (int) get_option('page_for_posts');
	$blog_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/blog/');
	get_template_part('template-parts/global/inner-hero', null, [
		'eyebrow'     => $categories ? $categories[0]->name : __('The Journal', 'echelon'),
		'title'       => esc_html(get_the_title()),
		'description' => sprintf(
			/* translators: 1: publication date, 2: author name. */
			esc_html__('%1$s · By %2$s', 'echelon'),
			get_the_date(),
			get_the_author()
		),
		'modifier' => 'inner-hero--article',
	]);
	?>

	<article class="section single-article">
		<div class="container container--narrow">
			<?php if (has_post_thumbnail()) : ?>
				<div class="single-thumbnail">
					<?php the_post_thumbnail('vehicle-hero'); ?>
				</div>
			<?php endif; ?>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
			<footer class="single-article__footer">
				<?php the_tags('<div class="tag-list"><span>' . esc_html__('Topics:', 'echelon') . '</span> ', ' ', '</div>'); ?>
				<a class="btn btn--outline" href="<?php echo esc_url($blog_url); ?>"><?php esc_html_e('Back To Journal', 'echelon'); ?></a>
			</footer>
		</div>
	</article>
	<?php
endwhile;

get_footer();
