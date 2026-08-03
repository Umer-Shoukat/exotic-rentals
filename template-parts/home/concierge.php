<?php
/**
 * Home: "Your Personal Exotic Car Concierge" — checklist + chat mockup.
 */

$page_id = get_queried_object_id();
$heading    = echelon_field('concierge_heading', $page_id, 'Your Personal Exotic Car Concierge');
$desc       = echelon_field('concierge_desc', $page_id, 'Our concierge team confirms availability,
coordinates pickup logistics, and stays reachable before, during, and after your reservation.');
$checklist  = echelon_field('concierge_checklist', $page_id, [
    ['icon' => 'clock', 'label' => 'Availability Checks'],
    ['icon' => 'shield-check', 'label' => 'Chauffeur Scheduling'],
    ['icon' => 'calendar', 'label' => 'Reservation Requests'],
    ['icon' => 'id-card', 'label' => 'Payment Link Support'],
]);
$cta        = echelon_field('concierge_cta', $page_id, ['title' => 'Start With Dates', 'url' => home_url('/fleet')]);
$chat_title = echelon_field('concierge_chat_title', $page_id, 'Echelon Concierge');
$messages   = echelon_field('concierge_chat_messages', $page_id, [
    ['sender' => 'user', 'message' => 'I need a Rolls-Royce with a driver this weekend.'],
    ['sender' => 'agent', 'message' => "Let me confirm availability — what's your pickup city and dates?"],
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

		<div class="concierge__chat" data-concierge-chat role="region" aria-label="<?php esc_attr_e('Concierge conversation preview', 'echelon'); ?>">
			<div class="concierge__chat-header">
				<?php echelon_icon('headset'); ?>
				<span><?php echo esc_html($chat_title); ?></span>
				<button type="button" class="concierge__chat-close" data-chat-replay aria-label="<?php esc_attr_e('Replay conversation', 'echelon'); ?>"><?php esc_html_e('Replay', 'echelon'); ?></button>
			</div>
			<div class="concierge__chat-body">
				<?php foreach ($messages as $msg) : ?>
					<div class="concierge__bubble concierge__bubble--<?php echo esc_attr($msg['sender']); ?>" data-chat-message>
						<?php echo esc_html($msg['message']); ?>
					</div>
				<?php endforeach; ?>
				<div class="concierge__typing" data-chat-typing aria-hidden="true"><span></span><span></span><span></span></div>
			</div>
		</div>
	</div>
</section>
