<?php
/** Service archive card. */
$service_id = get_the_ID();
$summary = echelon_field('service_kicker', $service_id, get_the_excerpt());
$cta = echelon_field('service_cta_label', $service_id, __('Explore Service', 'echelon'));
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
			<h2><?php the_title(); ?></h2>
			<?php if ($summary) : ?><p><?php echo esc_html($summary); ?></p><?php endif; ?>
			<span><?php echo esc_html($cta); ?> <?php echelon_icon('arrow-right'); ?></span>
		</div>
	</a>
</article>
