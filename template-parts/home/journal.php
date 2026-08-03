<?php
/**
 * Home: journal/blog preview.
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
$heading = echelon_field('journal_heading', $page_id, 'Latest From Echelon Motions');
$description = echelon_field('journal_desc', $page_id, 'Fleet drops, driving guides, and the occasional look behind the garage door.');
$cta = echelon_field('journal_cta', $page_id, ['title' => 'View All Articles', 'url' => get_post_type_archive_link('post') ?: home_url('/blog')]);
$heading_parts = preg_split('/(?=Echelon Motions)/i', $heading, 2);
$heading_primary = trim($heading_parts[0] ?? $heading);
$heading_accent = trim($heading_parts[1] ?? '');
$fallback_posts = [
	[
		'title' => 'The New Corvette Joins The Collection',
		'excerpt' => 'The Corvette C8 joins our fleet as a sharper option for clients who want a chauffeured sports-car arrival without sacrificing comfort.',
	],
	[
		'title' => 'Introducing The 2026 Maybach GLS 600 To Our Fleet',
		'excerpt' => 'The Maybach GLS 600 brings executive-level comfort to our SUV lineup, built for airport transfers and multi-stop corporate days.',
	],
	[
		'title' => 'Behind The Wheel: Our Matte Black Huracan',
		'excerpt' => 'A closer look at our matte black Huracan, one of the most requested vehicles for evening arrivals and photo productions.',
	],
];
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
			<div class="post-grid">
				<?php foreach ($fallback_posts as $item) : ?>
					<article class="content-card">
						<div class="content-card__body">
							<div class="content-card__meta">
								<span><?php esc_html_e('Journal', 'echelon'); ?></span>
							</div>
							<h2 class="content-card__title"><?php echo esc_html($item['title']); ?></h2>
							<div class="content-card__excerpt"><p><?php echo esc_html($item['excerpt']); ?></p></div>
							<a class="btn btn--ghost" href="<?php echo esc_url($cta['url'] ?: home_url('/blog')); ?>">
								<?php esc_html_e('Read More', 'echelon'); ?>
								<?php echelon_icon('arrow-right'); ?>
							</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
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
