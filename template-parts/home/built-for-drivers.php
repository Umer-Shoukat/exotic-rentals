<?php
/**
 * Home: "Built For Drivers Who Notice Everything" — 4-col numbered features.
 */

$heading  = echelon_field('features_heading', get_the_ID(), 'Built For Drivers Who Notice Everything.');
$features = echelon_field('features', get_the_ID(), [
    [
        'icon'        => 'gauge',
        'title'       => 'Performance Meets Luxury',
        'description' => 'Our collection brings together rare supercars and refined luxury vehicles, each selected for design, performance, and presence.',
    ],
    [
        'icon'        => 'headset',
        'title'       => 'Expert Support At Every Step',
        'description' => 'Real guidance from first inquiry to delivery — whether you\'re selecting a vehicle, arranging logistics, or customizing your build.',
    ],
    [
        'icon'        => 'truck',
        'title'       => 'Nationwide Logistics',
        'description' => 'We offer fully insured, enclosed transport across the country, delivering your vehicle wherever and whenever you need it.',
    ],
    [
        'icon'        => 'wrench',
        'title'       => 'Custom Builds',
        'description' => 'From wide-body kits to full custom interiors, our in-house team can transform any car in the collection to your exact spec.',
    ],
]);

$accent_position = stripos($heading, 'notice');
?>
<section class="section built-for-drivers" id="how-it-works" data-reveal>
	<div class="container">
		<header class="section-heading">
			<p class="eyebrow"><?php esc_html_e('Why Exotic Rental', 'echelon'); ?></p>
			<h2 class="section-heading__title">
				<?php if ($accent_position !== false) : ?>
					<?php echo esc_html(substr($heading, 0, $accent_position)); ?><span class="accent"><?php echo esc_html(substr($heading, $accent_position)); ?></span>
				<?php else : ?>
					<?php echo esc_html($heading); ?>
				<?php endif; ?>
			</h2>
		</header>

		<div class="feature-grid">
			<?php foreach ($features as $index => $feature) : ?>
				<div class="feature-card">
					<div class="feature-card__top">
						<span class="feature-card__number"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
						<img class="feature-card__brand" src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/feature-card-logo.png'); ?>" alt="" width="72" height="16">
					</div>
					<h3 class="feature-card__title"><?php echo esc_html($feature['title']); ?></h3>
					<p class="feature-card__desc"><?php echo esc_html($feature['description']); ?></p>
				</div>
			<?php endforeach; ?>
		</div>

		<a class="btn btn--primary built-for-drivers__cta" href="<?php echo esc_url(home_url('/about')); ?>">
			<?php esc_html_e('Read More', 'echelon'); ?>
			<?php echelon_icon('arrow-right'); ?>
		</a>
	</div>
</section>
