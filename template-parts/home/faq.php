<?php
/**
 * Home: "Questions, Answered." — FAQ accordion.
 */

$faqs = get_posts([
    'post_type'      => 'faq',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'no_found_rows'  => true,
]);

$fallback = [
    ['q' => 'What Are The Age And License Requirements?', 'a' => 'Drivers must be 25 or older with a valid driver\'s license held for at least 3 years and a clean recent driving record.'],
    ['q' => 'How Does Insurance And The Security Deposit Work?', 'a' => 'Every rental includes full coverage. A refundable security deposit is authorized on your card at pickup and released after a clean return inspection.'],
    ['q' => 'Are There Mileage Limits?', 'a' => 'Standard rentals include 100 miles per day. Unlimited mileage packages are available for an additional fee.'],
    ['q' => 'Where Do You Deliver?', 'a' => 'We deliver across the Tri-State Area, including Manhattan, Brooklyn, New Jersey, and Connecticut, with extended delivery available on request.'],
    ['q' => 'What Is Your Cancellation Policy?', 'a' => 'Free cancellation up to 48 hours before pickup. Cancellations inside that window are subject to a one-day rental fee.'],
];

$page_id = get_queried_object_id();
$eyebrow = echelon_field('faq_eyebrow', $page_id, 'FAQ');
$heading = echelon_field('faq_heading', $page_id, 'Questions, Answered.');
$description = echelon_field('faq_desc', $page_id, 'Still curious? Our concierge team is available 24/7 to walk you through any detail.');
$heading_parts = preg_split('/(?=Answered\.)/i', $heading, 2);
$heading_primary = trim($heading_parts[0] ?? $heading);
$heading_accent = trim($heading_parts[1] ?? '');
?>
<section class="section faq" id="faq" data-reveal>
	<div class="container faq__grid">
		<div class="faq__intro">
			<p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
			<h2 class="section-heading__title"><?php echo esc_html($heading_primary); ?><?php if ($heading_accent) : ?><br><span class="accent"><?php echo esc_html($heading_accent); ?></span><?php endif; ?></h2>
			<p class="faq__description"><?php echo esc_html($description); ?></p>
		</div>

		<div class="accordion" data-accordion>
			<?php if ($faqs) : ?>
				<?php foreach ($faqs as $index => $faq) : ?>
					<div class="accordion__item">
						<button type="button" class="accordion__trigger" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="faq-panel-<?php echo esc_attr($faq->ID); ?>">
							<span><?php echo esc_html(get_the_title($faq)); ?></span>
							<span class="accordion__icon" aria-hidden="true"><img class="accordion__icon-closed" src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/faq-plus.svg'); ?>" alt=""><img class="accordion__icon-open" src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/faq-minus.svg'); ?>" alt=""></span>
						</button>
						<div class="accordion__panel" id="faq-panel-<?php echo esc_attr($faq->ID); ?>">
							<div class="accordion__panel-inner">
								<?php echo wp_kses_post(apply_filters('the_content', $faq->post_content)); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<?php foreach ($fallback as $index => $item) : ?>
					<div class="accordion__item">
						<button type="button" class="accordion__trigger" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="faq-panel-fallback-<?php echo esc_attr($index); ?>">
							<span><?php echo esc_html($item['q']); ?></span>
							<span class="accordion__icon" aria-hidden="true"><img class="accordion__icon-closed" src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/faq-plus.svg'); ?>" alt=""><img class="accordion__icon-open" src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/faq-minus.svg'); ?>" alt=""></span>
						</button>
						<div class="accordion__panel" id="faq-panel-fallback-<?php echo esc_attr($index); ?>">
							<div class="accordion__panel-inner">
								<p><?php echo esc_html($item['a']); ?></p>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
