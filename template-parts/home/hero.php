<?php
/**
 * Home: hero section with trust badges and featured-vehicle strip.
 */

$eyebrow    = echelon_field('hero_eyebrow', get_the_ID(), 'New York · New Jersey · Connecticut');
$heading    = echelon_field('hero_heading', get_the_ID(), "Tri-State's Premier\nExotic Rental Experience");
$subtext    = echelon_field('hero_subtext', get_the_ID(), 'From Lamborghinis and Rolls-Royces to executive chauffeur service, we deliver luxury vehicles across Tri-State Areas. Premium cars, white-glove service, and a seamless booking experience from start to finish.');
$bg         = echelon_field('hero_background', get_the_ID(), null);
$bg_mobile  = echelon_field('hero_background_mobile', get_the_ID(), null);
$cta1       = echelon_field('hero_cta_primary', get_the_ID(), ['title' => 'Browse Our Fleet', 'url' => home_url('/fleet')]);
$cta2       = echelon_field('hero_cta_secondary', get_the_ID(), ['title' => 'How It Works', 'url' => '#how-it-works']);
$badges     = echelon_field('hero_badges', get_the_ID(), [
    ['icon' => 'star', 'label' => '5-Star Rated'],
    ['icon' => 'shield-check', 'label' => 'Fully Insured'],
    ['icon' => 'headset', 'label' => '24/7 Concierge'],
]);

$featured = new WP_Query([
    'post_type'      => 'fleet_vehicle',
    'posts_per_page' => 6,
    'meta_key'       => 'featured',
    'meta_value'     => '1',
    'no_found_rows'  => true,
]);

if (!$featured->have_posts()) {
    $featured = new WP_Query([
        'post_type'      => 'fleet_vehicle',
        'posts_per_page' => 6,
        'no_found_rows'  => true,
    ]);
}
?>
<section class="hero" data-reveal-group>
	<div class="hero__media" aria-hidden="true">
		<?php
		$desktop_url = is_array($bg) && !empty($bg['ID']) ? wp_get_attachment_image_url($bg['ID'], 'full') : (is_numeric($bg) ? wp_get_attachment_image_url((int) $bg, 'full') : '');
		$mobile_url = is_array($bg_mobile) && !empty($bg_mobile['ID']) ? wp_get_attachment_image_url($bg_mobile['ID'], 'full') : (is_numeric($bg_mobile) ? wp_get_attachment_image_url((int) $bg_mobile, 'full') : '');
		$desktop_url = $desktop_url ?: ECHELON_THEME_URI . '/assets/images/generated/home-hero-desktop.webp';
		$mobile_url = $mobile_url ?: ECHELON_THEME_URI . '/assets/images/generated/home-hero-mobile.webp';
		?>
		<picture>
			<source media="(max-width: 767px)" srcset="<?php echo esc_url($mobile_url); ?>">
			<img class="hero__bg-img" src="<?php echo esc_url($desktop_url); ?>" alt="" width="1672" height="941" fetchpriority="high" decoding="async">
		</picture>
		<div class="hero__scrim"></div>
	</div>

	<div class="container hero__content">
		<p class="eyebrow" data-reveal><?php echo esc_html($eyebrow); ?></p>
		<h1 class="hero__heading" data-reveal><?php echo wp_kses_post(nl2br(esc_html($heading))); ?></h1>
		<p class="hero__subtext" data-reveal><?php echo esc_html($subtext); ?></p>

		<div class="hero__ctas" data-reveal>
			<?php if (!empty($cta1['url'])) : ?>
				<a class="btn btn--primary" href="<?php echo esc_url($cta1['url']); ?>">
					<?php echo esc_html($cta1['title']); ?>
					<?php echelon_icon('arrow-right'); ?>
				</a>
			<?php endif; ?>
			<?php if (!empty($cta2['url'])) : ?>
				<a class="btn btn--outline" href="<?php echo esc_url($cta2['url']); ?>">
					<?php echo esc_html($cta2['title']); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php if ($badges) : ?>
			<ul class="hero__badges" data-reveal>
				<?php foreach ($badges as $badge) : ?>
					<li>
						<?php echelon_icon($badge['icon'] ?? 'check'); ?>
						<span><?php echo esc_html($badge['label']); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<?php if ($featured->have_posts()) :
		$vehicle_links = array_map(static function ($vehicle) {
			return add_query_arg('vehicle', $vehicle->ID, home_url('/reservation/'));
		}, $featured->posts);
		?>
		<div class="hero__strip" data-hero-strip data-vehicle-links='<?php echo esc_attr(wp_json_encode($vehicle_links)); ?>'>
			<div class="hero__strip-slides" data-hero-strip-slides>
				<?php while ($featured->have_posts()) : $featured->the_post(); ?>
					<div class="hero__strip-slide">
						<span class="hero__strip-stat">
							<span class="hero__strip-label"><?php echo esc_html(echelon_field('brand', get_the_ID(), 'Vehicle')); ?></span>
							<span class="hero__strip-value"><?php the_title(); ?></span>
						</span>
						<span class="hero__strip-stat">
							<span class="hero__strip-label"><?php esc_html_e('Starting At', 'echelon'); ?></span>
							<?php $hero_hourly_rate = echelon_field('price_per_hour', get_the_ID(), ''); $hero_daily_rate = echelon_field('daily_rental_price', get_the_ID(), ''); ?>
							<?php if ($hero_hourly_rate !== '' || $hero_daily_rate !== '') : ?><span class="hero__strip-value"><?php if ($hero_hourly_rate !== '') : ?><?php echo esc_html(echelon_price($hero_hourly_rate)); ?>/<?php esc_html_e('hour', 'echelon'); ?><?php endif; ?><?php if ($hero_hourly_rate !== '' && $hero_daily_rate !== '') : ?> · <?php endif; ?><?php if ($hero_daily_rate !== '') : ?><?php echo esc_html(echelon_price($hero_daily_rate)); ?>/<?php esc_html_e('day', 'echelon'); ?><?php endif; ?></span><?php endif; ?>
						</span>
					</div>
				<?php endwhile; ?>
			</div>
			<a class="btn btn--primary btn--sm hero__strip-cta" href="<?php echo esc_url($vehicle_links[0] ?? home_url('/reservation/')); ?>">
				<?php esc_html_e('Rent This Car', 'echelon'); ?>
				<?php echelon_icon('arrow-right'); ?>
			</a>
			<div class="hero__strip-controls">
				<button type="button" class="slider-arrow" data-hero-prev aria-label="<?php esc_attr_e('Previous vehicle', 'echelon'); ?>"><?php echelon_icon('arrow-left'); ?></button>
				<button type="button" class="slider-arrow" data-hero-next aria-label="<?php esc_attr_e('Next vehicle', 'echelon'); ?>"><?php echelon_icon('arrow-right'); ?></button>
			</div>
			<div class="hero__strip-progress" aria-hidden="true"><span data-hero-progress></span></div>
		</div>
	<?php
	wp_reset_postdata();
	endif;
	?>
</section>
