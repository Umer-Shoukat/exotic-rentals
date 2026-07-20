<?php
/**
 * A single vehicle card. Expects the loop to be positioned on a
 * fleet_vehicle post (called via the_post() from the parent query).
 */

$vehicle_id = get_the_ID();
$gallery    = echelon_field('gallery', $vehicle_id, []);
$cover      = $gallery[0] ?? get_post_thumbnail_id($vehicle_id);
$price      = echelon_field('price_per_day', $vehicle_id, '');
$hp         = echelon_field('horsepower', $vehicle_id, '');
$zero_sixty = echelon_field('zero_to_sixty', $vehicle_id, '');
$seats      = echelon_field('seats', $vehicle_id, '');
$featured   = echelon_field('featured', $vehicle_id, false);
?>
<article class="vehicle-card<?php echo $featured ? ' is-featured' : ''; ?>">
	<a class="vehicle-card__media" href="<?php the_permalink(); ?>" tabindex="-1">
		<?php echelon_media($cover, 'vehicle-card', '', 'bolt'); ?>
	</a>
	<div class="vehicle-card__body">
		<div class="vehicle-card__top">
			<h3 class="vehicle-card__name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<?php if ($price !== '') : ?>
				<span class="vehicle-card__price">
					<span class="vehicle-card__price-label"><?php esc_html_e('From', 'echelon'); ?></span>
					<span class="vehicle-card__price-value"><?php echo esc_html(echelon_price($price)); ?>/<?php esc_html_e('day', 'echelon'); ?></span>
				</span>
			<?php endif; ?>
		</div>

		<div class="vehicle-card__meta">
			<?php if ($hp !== '') : ?>
				<div class="vehicle-card__meta-item">
					<?php echelon_icon('bolt'); ?>
					<span class="vehicle-card__meta-value"><?php echo esc_html($hp); ?> HP</span>
				</div>
			<?php endif; ?>
			<?php if ($zero_sixty !== '') : ?>
				<div class="vehicle-card__meta-item">
					<?php echelon_icon('gauge'); ?>
					<span class="vehicle-card__meta-value">0–60 in <?php echo esc_html($zero_sixty); ?></span>
				</div>
			<?php endif; ?>
			<?php if ($seats !== '') : ?>
				<div class="vehicle-card__meta-item">
					<?php echelon_icon('seat'); ?>
					<span class="vehicle-card__meta-value"><?php echo esc_html($seats); ?> Seats</span>
				</div>
			<?php endif; ?>
		</div>

		<a class="btn <?php echo $featured ? 'btn--primary' : 'btn--outline'; ?> btn--block" href="<?php the_permalink(); ?>">
			<?php esc_html_e('Reserve', 'echelon'); ?>
			<?php echelon_icon('arrow-right'); ?>
		</a>
	</div>
</article>
