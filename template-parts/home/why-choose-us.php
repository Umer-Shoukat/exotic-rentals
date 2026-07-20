<?php
/**
 * Home: "Why Clients Choose Us" — intro + 4 stat cards.
 */

$heading = echelon_field('stats_heading', get_the_ID(), 'Why Clients Choose Us Over Anyone Else');
$desc    = echelon_field('stats_desc', get_the_ID(), 'We measure ourselves on the things that actually matter to a client: how quickly the car arrives, how spotless it is when you step in, and how easy it is to reach a real human at 3 a.m. The numbers below are the proof.');
$cta     = echelon_field('stats_cta', get_the_ID(), ['title' => 'Learn More', 'url' => home_url('/about')]);
$stats   = echelon_field('stats', get_the_ID(), [
    ['icon' => 'truck', 'value' => '500+', 'label' => 'Our fleet includes an array of high-performance, comfortable, and elegant cars tailored to different preferences.'],
    ['icon' => 'check', 'value' => '100%', 'label' => 'Every vehicle is inspected, detailed, and verified before it ever reaches a client.'],
    ['icon' => 'clock', 'value' => '45M', 'label' => 'Average time from confirmed booking to a car arriving at your door.'],
    ['icon' => 'headset', 'value' => '24/7', 'label' => 'A real concierge is always on call — not a bot, not a queue.'],
]);

$heading_parts = preg_split('/(over anyone else)/i', $heading, -1, PREG_SPLIT_DELIM_CAPTURE);
?>
<section class="section section--alt why-choose-us" id="why-choose-us" data-reveal>
	<div class="container why-choose-us__grid">
		<div class="why-choose-us__intro">
			<p class="eyebrow"><?php esc_html_e('The Exotic Rental Difference', 'echelon'); ?></p>
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

		<div class="stat-grid" data-parallax-stats>
			<?php foreach ($stats as $stat) : ?>
				<div class="stat-card">
					<?php echelon_icon($stat['icon'] ?? 'check', 'stat-card__icon'); ?>
					<div class="stat-card__value count-up" data-count-to="<?php echo esc_attr($stat['value']); ?>"><?php echo esc_html($stat['value']); ?></div>
					<p class="stat-card__label"><?php echo esc_html($stat['label']); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
