<?php
/**
 * Home: Instagram feed carousel.
 */

$handle = echelon_field('instagram_handle', get_the_ID(), '@echelonmotions');
$link   = echelon_field('instagram_link', get_the_ID(), 'https://instagram.com/');
$images = echelon_field('instagram_images', get_the_ID(), array_fill(0, 5, null));
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
			<?php foreach ($images as $image) : ?>
				<div class="swiper-slide instagram-feed__slide">
					<?php echelon_media($image, 'large', '', 'instagram'); ?>
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
