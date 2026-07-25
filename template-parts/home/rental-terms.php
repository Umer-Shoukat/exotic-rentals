<?php
/**
 * Home: "Rental Terms" — agent card + requirement stat boxes.
 */

$heading     = echelon_field('terms_heading', get_the_ID(), 'SERVICE REQUIREMENTS');
$desc        = echelon_field('terms_desc', get_the_ID(), "Our team is here to help you find the right vehicle and the right
chauffeur for your plans.");
$agent_name  = echelon_field('agent_name', get_the_ID(), 'Marcus D.');
$agent_title = echelon_field('agent_title', get_the_ID(), 'Founder, Atlas Ventures');
$agent_photo = echelon_field('agent_photo', get_the_ID(), null);
$agent_phone = echelon_field('agent_phone', get_the_ID(), echelon_setting('contact_phone'));
$terms       = echelon_field('terms', get_the_ID(), [
    ['icon' => 'id-card', 'value' => '21 Years', 'label' => 'Minimum Age'],
    ['icon' => 'shield-check', 'value' => '2 Documents', 'label' => "Passport And Driver's License"],
    ['icon' => 'clock', 'value' => '1 Year', 'label' => 'Of Driving Experience'],
    ['icon' => 'check', 'value' => 'Form 1000$', 'label' => 'Security Deposit'],
]);

$heading_parts = preg_split('/(terms)/i', $heading, -1, PREG_SPLIT_DELIM_CAPTURE);
?>
<section class="section section--alt rental-terms" id="rental-terms" data-reveal>
	<div class="container rental-terms__grid">
		<div class="rental-terms__intro">
			<p class="eyebrow"><?php esc_html_e('Good to Know', 'echelon'); ?></p>
			<h2 class="section-heading__title">
				<?php foreach ($heading_parts as $part) : ?>
					<?php echo strcasecmp(trim($part), 'terms') === 0 ? '<span class="accent">' . esc_html($part) . '</span>' : esc_html($part); ?>
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
				<div class="requirement-card<?php echo 0 === $index % 2 ? ' requirement-card--accent' : ''; ?>">
					<?php echelon_icon($term['icon'] ?? 'check', 'requirement-card__icon'); ?>
					<div class="requirement-card__value"><?php echo esc_html($term['value']); ?></div>
					<div class="requirement-card__label"><?php echo esc_html($term['label']); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
