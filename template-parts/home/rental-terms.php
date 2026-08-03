<?php
/**
 * Home: chauffeur service details.
 */

$page_id = get_queried_object_id();
$heading     = echelon_field('terms_heading', $page_id, 'SERVICE DETAILS');
$desc        = echelon_field('terms_desc', $page_id, 'Our concierge team is available 24/7 to help confirm the right vehicle, chauffeur, and schedule for your plans.');
$agent_name  = echelon_field('agent_name', $page_id, 'Echelon Concierge');
$agent_title = echelon_field('agent_title', $page_id, 'Available 24/7');
$agent_photo = echelon_field('agent_photo', $page_id, null);
$agent_phone = echelon_field('agent_phone', $page_id, echelon_setting('contact_phone'));
$terms       = echelon_field('terms', $page_id, [
    ['icon' => 'headset', 'value' => '24/7', 'label' => 'Concierge Support'],
    ['icon' => 'shield-check', 'value' => 'Commercial', 'label' => 'Insurance Coverage'],
    ['icon' => 'clock', 'value' => 'Hourly', 'label' => 'Or Point-To-Point'],
    ['icon' => 'pin', 'value' => 'NYC & LI', 'label' => 'Extended Service By Request'],
]);

$heading_parts = preg_split('/(details)/i', $heading, -1, PREG_SPLIT_DELIM_CAPTURE);
?>
<section class="section section--alt rental-terms" id="rental-terms" data-reveal>
	<div class="container rental-terms__grid">
		<div class="rental-terms__intro">
			<p class="eyebrow"><?php esc_html_e('Good to Know', 'echelon'); ?></p>
			<h2 class="section-heading__title">
				<?php foreach ($heading_parts as $part) : ?>
					<?php echo strcasecmp(trim($part), 'details') === 0 ? '<span class="accent">' . esc_html($part) . '</span>' : esc_html($part); ?>
				<?php endforeach; ?>
			</h2>
			<p><?php echo esc_html($desc); ?></p>

			<div class="agent-card">
				<div class="agent-card__photo">
					<?php echelon_media($agent_photo, 'avatar-sm', '', 'headset'); ?>
				</div>
				<div class="agent-card__info">
					<span class="agent-card__name"><?php echo esc_html($agent_name); ?></span>
					<span class="agent-card__title"><?php echo esc_html($agent_title); ?></span>
				</div>
			</div>

			<?php if ($agent_phone) : ?>
				<a class="btn btn--primary" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $agent_phone)); ?>">
					<?php esc_html_e('Call Us Now', 'echelon'); ?>
					<?php echelon_icon('arrow-right'); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="rental-terms__requirements">
			<?php foreach ($terms as $index => $term) : ?>
				<div class="requirement-card<?php echo 0 === $index % 2 ? ' requirement-card--accent' : ''; ?>" style="--stack-index: <?php echo esc_attr($index); ?>;">
					<?php echelon_icon($term['icon'] ?? 'check', 'requirement-card__icon'); ?>
					<div class="requirement-card__value"><?php echo esc_html($term['value']); ?></div>
					<div class="requirement-card__label"><?php echo esc_html($term['label']); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
