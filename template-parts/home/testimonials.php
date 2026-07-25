<?php
/**
 * Home: "Trusted, Quietly." — three continuously moving testimonial columns.
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

$testimonial_items = [];
if ($testimonials->have_posts()) {
    while ($testimonials->have_posts()) {
        $testimonials->the_post();
        $tid = get_the_ID();
        $testimonial_items[] = [
            'quote'  => echelon_field('quote', $tid, get_the_title()),
            'name'   => echelon_field('author_name', $tid, ''),
            'title'  => echelon_field('author_title', $tid, ''),
            'rating' => echelon_field('rating', $tid, 5),
            'photo'  => echelon_field('author_photo', $tid, null),
        ];
    }
    wp_reset_postdata();
} else {
    for ($i = 0; $i < 9; $i++) {
        $testimonial_items[] = $fallback + ['photo' => null];
    }
}

$testimonial_columns = [[], [], []];
foreach ($testimonial_items as $index => $item) {
    $testimonial_columns[$index % 3][] = $item;
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
		<div class="testimonials__columns" aria-label="<?php esc_attr_e('Customer testimonials', 'echelon'); ?>">
			<?php foreach ($testimonial_columns as $column_index => $column) : ?>
				<div class="testimonials__column" style="--column-duration: <?php echo esc_attr(28 + ($column_index * 5)); ?>s;">
					<div class="testimonials__track">
						<?php for ($copy = 0; $copy < 2; $copy++) : ?>
							<div class="testimonials__group"<?php echo $copy ? ' aria-hidden="true"' : ''; ?>>
								<?php foreach ($column as $item) : ?>
									<?php echelon_testimonial_card($item['quote'], $item['name'], $item['title'], $item['rating'], $item['photo']); ?>
								<?php endforeach; ?>
							</div>
						<?php endfor; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
