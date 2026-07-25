<?php
/**
 * Home: "Why Clients Choose Us" — intro + 4 stat cards.
 */

$heading = echelon_field('stats_heading', get_the_ID(), 'WHY CLIENTS CHOOSE ECHELON MOTIONS');
$desc    = echelon_field('stats_desc', get_the_ID(), 'We measure
ourselves on what actually matters to a client: how carefully the pickup is scheduled, how
presentation-ready the vehicle is, and how easy it is to reach a real person when plans change.
The numbers below reflect that standard.');
$cta     = echelon_field('stats_cta', get_the_ID(), ['title' => 'Learn More', 'url' => home_url('/about')]);
$stats   = echelon_field('stats', get_the_ID(), [
    ['icon' => 'truck', 'value' => 'LUXURY AND EXOTIC VEHICLES', 'label' => 'Our fleet includes high-performance, comfortable, and
elegant vehicles suited to different occasions.'],
    ['icon' => 'check', 'value' => '100%', 'label' => 'Every vehicle is inspected, detailed, and verified before it ever reaches a client.'],
    ['icon' => 'clock', 'value' => 'CAREFULLY
SCHEDULED PICKUPS', 'label' => 'Vehicles are coordinated and confirmed well ahead of your requested
time.'],
    ['icon' => 'headset', 'value' => '24/7', 'label' => 'A real concierge is always on call — not a bot, not a queue.'],
]);

$heading_parts = preg_split('/(over anyone else)/i', $heading, -1, PREG_SPLIT_DELIM_CAPTURE);
?>
<section class="section section--alt why-choose-us" id="why-choose-us" data-reveal data-scroll-progress-section>
	<div class="container why-choose-us__grid">
		<div class="why-choose-us__intro">
			<p class="eyebrow"><?php esc_html_e('THE EXOTIC RENTAL DIFFERENCE', 'echelon'); ?></p>
			<h2 class="section-heading__title">
				<?php foreach ($heading_parts as $part) : ?>
					<?php echo strcasecmp(trim($part), 'over anyone else') === 0 ? '<span class="accent">' . esc_html($part) . '</span>' : esc_html($part); ?>
				<?php endforeach; ?>
			</h2>
			<p><?php echo esc_html($desc); ?></p>
			<?php if (!empty($cta['url'])) : ?>
				<a class="btn btn--primary" href="<?php echo esc_url($cta['url']); ?>">
					<?php echo esc_html($cta['title']); ?>
					<?php echelon_icon('arrow-right'); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="stat-grid" data-stat-stack>
			<div class="stat-grid__progress" data-scroll-progress role="progressbar" aria-label="<?php esc_attr_e('Section progress', 'echelon'); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
			<?php foreach ($stats as $index => $stat) : ?>
				<div class="stat-card" style="--stack-index: <?php echo esc_attr($index); ?>;">
					<?php echelon_icon($stat['icon'] ?? 'check', 'stat-card__icon'); ?>
					<div class="stat-card__value count-up" data-count-to="<?php echo esc_attr($stat['value']); ?>"><?php echo esc_html($stat['value']); ?></div>
					<p class="stat-card__label"><?php echo esc_html($stat['label']); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
