<?php
/**
 * Blog posts index. WordPress uses this template for the configured Posts page.
 */

get_header();

$posts_page_id = (int) get_option('page_for_posts');
$page_title = $posts_page_id ? get_the_title($posts_page_id) : __('The Journal', 'echelon');
$page_intro = $posts_page_id ? get_post_field('post_excerpt', $posts_page_id) : '';

get_template_part('template-parts/global/inner-hero', null, [
    'eyebrow'     => __('Insights & Inspiration', 'echelon'),
    'title'       => esc_html($page_title),
    'description' => $page_intro ?: __('Driving guides, fleet stories, and inspiration for your next unforgettable journey.', 'echelon'),
]);
?>

<section class="section editorial-listing">
	<div class="container">
		<?php if (have_posts()) : ?>
			<div class="post-grid">
				<?php while (have_posts()) : the_post(); ?>
					<?php get_template_part('template-parts/content'); ?>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination([
				'prev_text' => __('Previous', 'echelon'),
				'next_text' => __('Next', 'echelon'),
			]); ?>
		<?php else : ?>
			<div class="empty-state">
				<h2><?php esc_html_e('Fresh stories are on the way.', 'echelon'); ?></h2>
				<p><?php esc_html_e('Check back soon for driving guides and the latest from our fleet.', 'echelon'); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
