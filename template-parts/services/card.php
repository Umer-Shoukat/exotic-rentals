<?php
/** Service archive card. */
$service_id = get_the_ID();
$summary = echelon_field('service_kicker', $service_id, get_the_excerpt());
$cta = echelon_field('service_cta_label', $service_id, __('Explore Service', 'echelon'));
$card_title = get_the_title();
$title_key = strtolower($card_title);
if (strpos($title_key, 'wedding') !== false) {
	$card_title = __('Wedding Transportation', 'echelon');
	$summary = __('A composed, on-time arrival for your ceremony and reception.', 'echelon');
} elseif (strpos($title_key, 'prom') !== false) {
	$card_title = __('Prom Transportation', 'echelon');
	$summary = __('A safe, chauffeured ride your group will remember.', 'echelon');
}
?>
<article class="service-card">
	<a class="service-card__link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(sprintf(__('Explore %s', 'echelon'), get_the_title())); ?>">
		<div class="service-card__media">
			<?php if (has_post_thumbnail()) : ?>
				<?php the_post_thumbnail('large', ['loading' => 'lazy', 'decoding' => 'async']); ?>
			<?php else : ?>
				<?php echelon_media(null, 'large', 'service-card__placeholder', 'star'); ?>
			<?php endif; ?>
		</div>
		<div class="service-card__scrim" aria-hidden="true"></div>
		<div class="service-card__content">
			<h2><?php echo esc_html($card_title); ?></h2>
			<?php if ($summary) : ?><p><?php echo esc_html($summary); ?></p><?php endif; ?>
			<span><?php echo esc_html($cta); ?> <?php echelon_icon('arrow-right'); ?></span>
		</div>
	</a>
</article>
