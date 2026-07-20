<?php
/**
 * Home: final CTA band — "Ready To Redefine Your Drive?"
 */

$page_id = get_queried_object_id();
$eyebrow = echelon_field('cta_eyebrow', $page_id, 'Reserve Today');
$heading = echelon_field('cta_heading', $page_id, 'Ready To Redefine Your Drive?');
$desc    = echelon_field('cta_desc', $page_id, 'Whatever the occasion — a first look at the aisle, a boardroom pull-up, or a shoot at golden hour — we tailor the car, the crew, and the moment.');
$image   = echelon_field('cta_image', $page_id, null);
$button  = echelon_field('cta_button', $page_id, ['title' => 'Start Your Reservation', 'url' => home_url('/fleet')]);
?>
<section class="cta-band" data-reveal>
	<div class="cta-band__media" aria-hidden="true">
		<?php if ($image) : ?>
			<?php echelon_media($image, 'full', 'cta-band__img'); ?>
		<?php else : ?>
			<img class="cta-band__img" src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/reservation-cta.jpg'); ?>" alt="" loading="lazy" decoding="async">
		<?php endif; ?>
		<div class="cta-band__scrim"></div>
	</div>
	<div class="container cta-band__content">
		<p class="eyebrow eyebrow--flanked"><?php echo esc_html($eyebrow); ?></p>
		<h2 class="cta-band__heading"><?php echo esc_html($heading); ?></h2>
		<p class="cta-band__desc"><?php echo esc_html($desc); ?></p>
		<?php if (!empty($button['url'])) : ?>
			<a class="btn btn--primary" href="<?php echo esc_url($button['url']); ?>"<?php echo !empty($button['target']) ? ' target="' . esc_attr($button['target']) . '" rel="noopener"' : ''; ?>>
				<?php echo esc_html($button['title']); ?>
				<?php echelon_icon('arrow-right'); ?>
			</a>
		<?php endif; ?>
	</div>
</section>
