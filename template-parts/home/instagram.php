<?php
/**
 * Home: Instagram feed carousel.
 */

$handle = echelon_field('instagram_handle', get_the_ID(), '@echelonmotions');
$link   = echelon_field('instagram_link', get_the_ID(), 'https://instagram.com/');
$images = echelon_field('instagram_images', get_the_ID(), array_fill(0, 5, null));
$feed_items = [];
$instagram_posts = get_posts([
	'post_type' => 'instagram_item', 'post_status' => 'publish', 'posts_per_page' => 12,
	'orderby' => ['menu_order' => 'ASC', 'date' => 'DESC'],
]);

foreach ($instagram_posts as $instagram_post) {
	$feed_items[] = [
		'image' => get_post_thumbnail_id($instagram_post),
		'url' => echelon_field('instagram_url', $instagram_post->ID, $link),
		'label' => get_the_title($instagram_post),
	];
}

if (!$feed_items) {
	foreach ($images as $index => $image) {
		$feed_items[] = ['image' => $image, 'url' => $link, 'label' => sprintf(__('Instagram photo %d', 'echelon'), $index + 1)];
	}
}

$real_feed_count = count($feed_items);
if ($real_feed_count > 1 && $real_feed_count < 12) {
	$loop_feed_items = $feed_items;
	while (count($feed_items) < 12) {
		$feed_items[] = $loop_feed_items[count($feed_items) % $real_feed_count];
	}
}
?>
<section class="section instagram-feed" id="instagram" data-reveal>
	<div class="container">
		<header class="section-heading instagram-feed__header">
			<p class="eyebrow eyebrow--center eyebrow--flanked"><?php esc_html_e('Follow The Fleet', 'echelon'); ?></p>
			<h2 class="section-heading__title"><span class="accent"><?php echo esc_html($handle); ?></span> <?php esc_html_e('On Instagram', 'echelon'); ?></h2>
		</header>
	</div>

	<div class="instagram-feed__slider" data-swiper data-swiper-centered>
		<div class="swiper-wrapper">
			<?php foreach ($feed_items as $item) : ?>
				<div class="swiper-slide instagram-feed__slide">
					<?php if (!empty($item['url'])) : ?><a href="<?php echo esc_url($item['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($item['label']); ?>"><?php endif; ?>
						<?php echelon_media($item['image'], 'large', '', 'instagram'); ?>
					<?php if (!empty($item['url'])) : ?></a><?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="container instagram-feed__footer">
		<a class="btn btn--primary" href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer">
			<?php echelon_icon('instagram'); ?>
			<?php esc_html_e('Follow Us On Instagram', 'echelon'); ?>
		</a>
	</div>
</section>
