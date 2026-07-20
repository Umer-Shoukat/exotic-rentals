<?php
/**
 * Home: "Your Personal Exotic Car Concierge" — checklist + chat mockup.
 */

$heading    = echelon_field('concierge_heading', get_the_ID(), 'Your Personal Exotic Car Concierge');
$desc       = echelon_field('concierge_desc', get_the_ID(), 'We measure ourselves on the things that actually matter to a client: how quickly the car arrives, how spotless it is when you step in, and how easy it is to reach a real human at 3 a.m.');
$checklist  = echelon_field('concierge_checklist', get_the_ID(), [
    ['icon' => 'clock', 'label' => 'Availability Checks'],
    ['icon' => 'shield-check', 'label' => 'Live Exotic Rentals'],
    ['icon' => 'calendar', 'label' => 'Booking-Ready Forms'],
    ['icon' => 'id-card', 'label' => 'Payment Link Support'],
]);
$cta        = echelon_field('concierge_cta', get_the_ID(), ['title' => 'Start With Dates', 'url' => home_url('/fleet')]);
$chat_title = echelon_field('concierge_chat_title', get_the_ID(), 'Exotic Rental Concierge');
$messages   = echelon_field('concierge_chat_messages', get_the_ID(), [
    ['sender' => 'user', 'message' => 'I need a Rolls-Royce or Lamborghini this weekend.'],
    ['sender' => 'agent', 'message' => "Let me check live availability. What's your pickup city and dates?"],
    ['sender' => 'user', 'message' => 'Manhattan, Saturday to Sunday.'],
]);

$heading_parts = preg_split('/(exotic car concierge)/i', $heading, -1, PREG_SPLIT_DELIM_CAPTURE);
?>
<section class="section concierge" id="concierge" data-reveal>
	<div class="container concierge__grid">
		<div class="concierge__intro">
			<p class="eyebrow"><?php esc_html_e('Concierge Powered', 'echelon'); ?></p>
			<h2 class="section-heading__title">
				<?php foreach ($heading_parts as $part) : ?>
					<?php echo strcasecmp(trim($part), 'exotic car concierge') === 0 ? '<span class="accent">' . esc_html($part) . '</span>' : esc_html($part); ?>
				<?php endforeach; ?>
			</h2>
			<p><?php echo esc_html($desc); ?></p>

			<?php if ($checklist) : ?>
				<ul class="concierge__checklist">
					<?php foreach ($checklist as $item) : ?>
						<li>
							<?php echelon_icon($item['icon'] ?? 'check'); ?>
							<span><?php echo esc_html($item['label']); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if (!empty($cta['url'])) : ?>
				<a class="btn btn--primary" href="<?php echo esc_url($cta['url']); ?>">
					<?php echo esc_html($cta['title']); ?>
					<?php echelon_icon('arrow-right'); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="concierge__chat" aria-hidden="true">
			<div class="concierge__chat-header">
				<?php echelon_icon('headset'); ?>
				<span><?php echo esc_html($chat_title); ?></span>
				<button type="button" class="concierge__chat-close">&times;</button>
			</div>
			<div class="concierge__chat-body">
				<?php foreach ($messages as $msg) : ?>
					<div class="concierge__bubble concierge__bubble--<?php echo esc_attr($msg['sender']); ?>">
						<?php echo esc_html($msg['message']); ?>
					</div>
				<?php endforeach; ?>
				<div class="concierge__typing" aria-hidden="true"><span></span><span></span><span></span></div>
			</div>
		</div>
	</div>
</section>
