<?php
/**
 * Home: "Built For Drivers Who Notice Everything" - 4-col numbered features.
 */

$heading  = echelon_field('features_heading', get_the_ID(), 'Built For Drivers Who Notice Everything.');
$features = [
    [
        'icon'        => 'gauge',
        'title'       => 'PERFORMANCE MEETS PRESENCE',
        'description' => 'Our fleet pairs supercars with refined luxury vehicles, each selected for design, performance, and arrival presence.',
    ],
    [
        'icon'        => 'headset',
        'title'       => 'Expert Support At Every Step',
        'description' => "Real guidance from first inquiry to pickup - whether you're selecting a vehicle, coordinating a route, or planning a full event.",
    ],
    [
        'icon'        => 'truck',
        'title'       => 'TRI-STATE LOGISTICS',
        'description' => 'We coordinate fully insured transportation across New York City and Long Island, with extended service to New Jersey, Connecticut, and Pennsylvania by request.',
    ],
    [
        'icon'        => 'wrench',
        'title'       => 'TAILORED EXPERIENCES',
        'description' => 'From a single airport pickup to a full weekend itinerary, our team tailors the vehicle and the schedule to your event.',
    ],
];

$accent_position = stripos($heading, 'notice');
?>
<section class="section built-for-drivers" id="how-it-works" data-reveal>
	<div class="container">
		<header class="section-heading">
			<p class="eyebrow"><?php esc_html_e('Why Echelon Motions', 'echelon'); ?></p>
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
				<div class="feature-card" style="--stack-index: <?php echo esc_attr($index); ?>;">
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
			<?php esc_html_e('LEARN MORE ABOUT US', 'echelon'); ?>
			<?php echelon_icon('arrow-right'); ?>
		</a>
	</div>
</section>
