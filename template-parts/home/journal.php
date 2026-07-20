<?php
/**
 * Home: "Latest From Exotic Rental" — journal/blog preview.
 */

$journal_args = [
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'no_found_rows'  => true,
];

// Prefer the "Journal" category if it exists so unrelated legacy posts on
// this install never crowd out the intended homepage feed.
if (get_category_by_slug('journal')) {
    $journal_args['category_name'] = 'journal';
}

$posts = new WP_Query($journal_args);
$page_id = get_queried_object_id();
$eyebrow = echelon_field('journal_eyebrow', $page_id, 'The Journal');
$heading = echelon_field('journal_heading', $page_id, 'Latest From Exotic Rental');
$description = echelon_field('journal_desc', $page_id, 'Fleet drops, driving guides, and the occasional look behind the garage door.');
$cta = echelon_field('journal_cta', $page_id, ['title' => 'View All Articles', 'url' => get_post_type_archive_link('post') ?: home_url('/blog')]);
$heading_parts = preg_split('/(?=Exotic Rental)/i', $heading, 2);
$heading_primary = trim($heading_parts[0] ?? $heading);
$heading_accent = trim($heading_parts[1] ?? '');
?>
<section class="section journal" id="journal" data-reveal>
	<div class="container">
		<header class="section-heading journal__header">
			<div class="journal__heading">
				<p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
				<h2 class="section-heading__title"><?php echo esc_html($heading_primary); ?><?php if ($heading_accent) : ?> <span class="accent"><?php echo esc_html($heading_accent); ?></span><?php endif; ?></h2>
			</div>
			<p class="journal__desc"><?php echo esc_html($description); ?></p>
		</header>

		<?php if ($posts->have_posts()) : ?>
			<div class="post-grid">
				<?php while ($posts->have_posts()) : $posts->the_post(); ?>
					<?php get_template_part('template-parts/content'); ?>
				<?php endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p><?php esc_html_e('New stories are on the way — check back soon.', 'echelon'); ?></p>
		<?php endif; ?>

		<?php if (!empty($cta['url'])) : ?>
		<div class="journal__footer">
			<a class="btn btn--outline" href="<?php echo esc_url($cta['url']); ?>"<?php echo !empty($cta['target']) ? ' target="' . esc_attr($cta['target']) . '" rel="noopener"' : ''; ?>>
				<?php echo esc_html($cta['title'] ?: 'View All Articles'); ?>
				<?php echelon_icon('arrow-right'); ?>
			</a>
		</div>
		<?php endif; ?>
	</div>
</section>
