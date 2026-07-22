<?php
/**
 * Home: "More Than a Rental" — featured service cards.
 */

$services = new WP_Query([
    'post_type'      => 'service',
    'posts_per_page' => 4,
    'orderby'        => ['menu_order' => 'ASC', 'date' => 'ASC'],
    'order'          => 'ASC',
    'no_found_rows'  => true,
]);

$eyebrow = echelon_field('occasions_eyebrow', get_the_ID(), 'Services');
$heading = echelon_field('occasions_heading', get_the_ID(), 'More Than a Rental');
$desc    = echelon_field('occasions_desc', get_the_ID(), 'Whatever the occasion — a first look at the aisle, a boardroom pull-up, or a shoot at golden hour — we tailor the car, the crew, and the moment.');
$cta     = echelon_field('occasions_cta', get_the_ID(), ['title' => 'View More', 'url' => get_post_type_archive_link('service')]);
$heading_parts = preg_split('/(rental)/i', $heading, -1, PREG_SPLIT_DELIM_CAPTURE);
$fallback_images = [
	'wedding'    => 'wedding.jpg',
	'prom'       => 'prom.jpg',
	'corporate'  => 'corporate.jpg',
	'photoshoot' => 'photoshoot.jpg',
];
?>
<section class="section more-than-rental" id="services" data-reveal>
	<div class="container">
		<header class="section-heading more-than-rental__header">
			<div>
				<p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
				<h2 class="section-heading__title">
					<?php foreach ($heading_parts as $part) : ?>
						<?php echo strcasecmp(trim($part), 'rental') === 0 ? '<span class="accent">' . esc_html($part) . '</span>' : esc_html($part); ?>
					<?php endforeach; ?>
				</h2>
			</div>
			<p class="more-than-rental__desc"><?php echo esc_html($desc); ?></p>
		</header>

		<?php if ($services->have_posts()) : ?>
			<div class="occasion-grid">
				<?php while ($services->have_posts()) : $services->the_post();
					$desc = echelon_field('service_kicker', get_the_ID(), get_the_excerpt());
					$thumbnail_id = get_post_thumbnail_id();
					$thumbnail_path = $thumbnail_id ? get_attached_file($thumbnail_id) : '';
					$fallback_image = 'wedding.jpg';
					$title_key = strtolower(get_the_title());
					foreach ($fallback_images as $keyword => $filename) {
						if (strpos($title_key, $keyword) !== false) {
							$fallback_image = $filename;
							break;
						}
					}
					?>
					<a class="occasion-card" href="<?php echo esc_url(get_permalink()); ?>">
						<?php if ($thumbnail_path && file_exists($thumbnail_path)) : ?>
							<?php echelon_media($thumbnail_id, 'content-card', 'occasion-card__media', 'check'); ?>
						<?php else : ?>
							<img class="occasion-card__media" src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/occasions/' . $fallback_image)); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
						<?php endif; ?>
						<div class="content-card__overlay">
							<h3 class="occasion-card__title"><?php the_title(); ?></h3>
							<p class="occasion-card__desc"><?php echo esc_html($desc); ?></p>
							<span class="occasion-card__link"><?php esc_html_e('Learn More', 'echelon'); ?></span>
						</div>
					</a>
				<?php endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<?php
			$fallback = [
                ['title' => 'Wedding Car Rental', 'desc' => 'Elegant arrival for your big day.', 'image' => 'wedding.jpg'],
                ['title' => 'Prom Night Rental', 'desc' => 'Make an entrance that gets remembered.', 'image' => 'prom.jpg'],
                ['title' => 'Corporate & Executive', 'desc' => 'White-glove transport for the boardroom.', 'image' => 'corporate.jpg'],
                ['title' => 'Photoshoot & Content', 'desc' => 'The right car for the right frame.', 'image' => 'photoshoot.jpg'],
            ];
			?>
			<div class="occasion-grid">
				<?php foreach ($fallback as $item) : ?>
					<div class="occasion-card">
						<img class="occasion-card__media" src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/occasions/' . $item['image'])); ?>" alt="<?php echo esc_attr($item['title']); ?>">
						<div class="content-card__overlay">
							<h3 class="occasion-card__title"><?php echo esc_html($item['title']); ?></h3>
							<p class="occasion-card__desc"><?php echo esc_html($item['desc']); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($cta['url'])) : ?>
			<div class="more-than-rental__footer">
				<a class="btn btn--outline" href="<?php echo esc_url($cta['url']); ?>">
					<?php echo esc_html($cta['title']); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
