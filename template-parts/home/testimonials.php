<?php
/**
 * Home: "Trusted, Quietly." — paginated testimonial grid (3 cols x 2 rows).
 */

$testimonials = new WP_Query([
    'post_type'      => 'testimonial',
    'posts_per_page' => 18,
    'no_found_rows'  => true,
]);

$fallback = [
    'quote'  => 'The 720S was delivered to my hotel in under an hour, spotless and fueled. This is how exotic rental should feel.',
    'name'   => 'Marcus D.',
    'title'  => 'Founder, Atlas Ventures',
    'rating' => 5,
];

$page_id = get_queried_object_id();
$eyebrow = echelon_field('testimonials_eyebrow', $page_id, 'Clients');
$heading = echelon_field('testimonials_heading', $page_id, 'Trusted, Quietly.');
$heading_parts = preg_split('/(?<=Trusted,)/i', $heading, 2);
$heading_accent = trim($heading_parts[0] ?? $heading);
$heading_primary = trim($heading_parts[1] ?? '');

function echelon_testimonial_card($quote, $name, $title, $rating, $photo) {
    ?>
    <div class="testimonial-card">
        <div class="testimonial-card__avatar">
            <?php if ($photo) : ?>
                <?php echelon_media($photo, 'avatar-sm', '', 'headset'); ?>
            <?php else : ?>
                <img src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/testimonial-avatar.png'); ?>" alt="" loading="lazy" decoding="async">
            <?php endif; ?>
        </div>
        <p class="testimonial-card__quote"><?php echo esc_html($quote); ?></p>
        <div class="testimonial-card__divider"></div>
        <?php echelon_stars($rating); ?>
        <span class="testimonial-card__name"><?php echo esc_html($name); ?></span>
        <span class="testimonial-card__title"><?php echo esc_html($title); ?></span>
    </div>
    <?php
}
?>
<section class="section testimonials" id="testimonials" data-reveal>
	<div class="container">
		<header class="section-heading testimonials__header">
			<p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
			<h2 class="section-heading__title"><span class="accent"><?php echo esc_html($heading_accent); ?></span><?php if ($heading_primary) : ?> <?php echo esc_html($heading_primary); ?><?php endif; ?></h2>
		</header>
	</div>

	<div class="container">
		<div class="testimonials__slider swiper" data-swiper>
			<div class="swiper-wrapper">
				<?php if ($testimonials->have_posts()) : ?>
					<?php while ($testimonials->have_posts()) : $testimonials->the_post();
						$tid = get_the_ID();
						?>
						<div class="swiper-slide">
							<?php
							echelon_testimonial_card(
								echelon_field('quote', $tid, get_the_title()),
								echelon_field('author_name', $tid, ''),
								echelon_field('author_title', $tid, ''),
								echelon_field('rating', $tid, 5),
								echelon_field('author_photo', $tid, null)
							);
							?>
						</div>
					<?php endwhile;
					wp_reset_postdata();
					?>
				<?php else : ?>
					<?php for ($i = 0; $i < 6; $i++) : ?>
						<div class="swiper-slide">
							<?php echelon_testimonial_card($fallback['quote'], $fallback['name'], $fallback['title'], $fallback['rating'], null); ?>
						</div>
					<?php endfor; ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="dots testimonials__dots" data-swiper-pagination></div>
	</div>
</section>
