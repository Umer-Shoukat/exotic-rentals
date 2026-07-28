<?php
/**
 * Template Name: About Page
 *
 * Brand story and service philosophy.
 */

get_header();

while (have_posts()) : the_post();
	$page_id = get_the_ID();
	$hero_image = echelon_field('about_hero_image', $page_id, get_post_thumbnail_id());
	$hero_description = echelon_field('about_hero_description', $page_id, '');
	if (!$hero_description || stripos($hero_description, 'Team Members') !== false || stripos($hero_description, 'web crawler expert') !== false) {
		$hero_description = __("Echelon Motions was built for clients who expect more than a pickup. Every reservation is handled by a professional chauffeur, in a vehicle prepared to the same standard, whether it's a wedding in Manhattan or a boardroom arrival in Midtown.", 'echelon');
	}
	if (stripos($hero_description, 'wedding arrival') !== false || stripos($hero_description, 'important business engagement') !== false) {
		$hero_description = __("Echelon Motions was built for clients who expect more than a pickup. Every reservation is handled by a professional chauffeur, in a vehicle prepared to the same standard, whether it's a wedding in Manhattan or a boardroom arrival in Midtown.", 'echelon');
	}
	$story_content = echelon_field('about_story_content', $page_id, '');
	if (!$story_content || stripos($story_content, 'Team Members') !== false || stripos($story_content, 'web crawler expert') !== false || stripos($story_content, 'Our Beloved Partners') !== false) {
		$story_content = '<p>' . __('Echelon Motions started with a simple observation: most car services treat the vehicle as the product. We treat the arrival as the product - the vehicle is just one part of getting that right.', 'echelon') . '</p>';
	}
	if (stripos($story_content, 'final destination') !== false || (stripos($story_content, 'client') !== false && stripos($story_content, 'occasion') !== false)) {
		$story_content = '<p>' . __('Echelon Motions started with a simple observation: most car services treat the vehicle as the product. We treat the arrival as the product - the vehicle is just one part of getting that right.', 'echelon') . '</p>';
	}
	$story_cta = echelon_field('about_story_cta', $page_id, ['title' => __('Explore Our Fleet', 'echelon'), 'url' => home_url('/fleet/'), 'target' => '']);
	$stats = echelon_field('about_stats', $page_id, [
		['value' => __('Curated', 'echelon'), 'label' => __('Exceptional Vehicles', 'echelon')],
		['value' => '100%', 'label' => __('Inspected & Detailed', 'echelon')],
		['value' => __('Planned', 'echelon'), 'label' => __('Carefully Scheduled Pickups', 'echelon')],
		['value' => '24/7', 'label' => __('Concierge Support', 'echelon')],
	]);
	foreach ($stats as &$stat) {
		if ('500+' === ($stat['value'] ?? '')) {
			$stat['value'] = __('Curated', 'echelon');
		}
		if ('45M' === ($stat['value'] ?? '')) {
			$stat['value'] = __('Planned', 'echelon');
			$stat['label'] = __('Carefully Scheduled Pickups', 'echelon');
		}
		if (stripos($stat['label'] ?? '', 'Average Delivery Time') !== false) {
			$stat['label'] = __('Carefully Scheduled Pickups', 'echelon');
		}
	}
	unset($stat);
	$values = echelon_field('about_values', $page_id, [
		['icon' => 'shield-check', 'title' => __('Uncompromising Quality', 'echelon'), 'description' => __('Every vehicle is selected, inspected, and meticulously prepared before it reaches your door.', 'echelon')],
		['icon' => 'headset', 'title' => __('Human Service', 'echelon'), 'description' => __('A knowledgeable concierge stays available before, during, and after every reservation.', 'echelon')],
		['icon' => 'bolt', 'title' => __('Effortless Execution', 'echelon'), 'description' => __('Clear communication, reliable pickups, and thoughtful coordination keep the experience seamless.', 'echelon')],
	]);
	$journey_steps = echelon_field('about_journey_steps', $page_id, [
		['title' => __('Tell Us The Occasion', 'echelon'), 'description' => __('Share the date, destination, preferences, and the kind of arrival you have in mind.', 'echelon')],
		['title' => __('We Curate The Details', 'echelon'), 'description' => __('Your concierge confirms the right vehicle and coordinates coverage, pickup, and timing.', 'echelon')],
		['title' => __('Enjoy The Drive', 'echelon'), 'description' => __('The vehicle arrives prepared and on time, with our team available whenever you need us.', 'echelon')],
	]);
	?>
	<section class="about-hero">
		<div class="about-hero__media" aria-hidden="true">
			<?php if ($hero_image) : ?>
				<?php echo wp_get_attachment_image($hero_image, 'vehicle-hero', false, ['class' => 'about-hero__image', 'loading' => 'eager', 'fetchpriority' => 'high']); ?>
			<?php else : ?>
				<img class="about-hero__image" src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/hero-homepage-v3.jpg')); ?>" alt="" loading="eager" fetchpriority="high">
			<?php endif; ?>
			<div class="about-hero__scrim"></div>
		</div>
		<div class="container about-hero__content">
			<p class="eyebrow eyebrow--flanked"><?php echo esc_html(echelon_field('about_hero_eyebrow', $page_id, __('Driven By The Experience', 'echelon'))); ?></p>
			<h1><?php echo esc_html(echelon_field('about_hero_title', $page_id, __('More Than A Car.', 'echelon'))); ?><br><span class="accent"><?php echo esc_html(echelon_field('about_hero_accent', $page_id, __('A Standard Of Service.', 'echelon'))); ?></span></h1>
			<p><?php echo esc_html($hero_description); ?></p>
		</div>
	</section>

	<section class="section about-story" data-reveal>
		<div class="container about-story__grid">
			<div class="about-story__media">
				<?php $story_image = echelon_field('about_story_image', $page_id, null); ?>
				<?php if ($story_image) : echelon_media($story_image, 'large', __('Luxury vehicle prepared for a client', 'echelon')); else : ?>
					<img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/occasions/corporate.jpg')); ?>" alt="<?php esc_attr_e('Luxury vehicle prepared for a client', 'echelon'); ?>" loading="lazy">
				<?php endif; ?>
				<div class="about-story__badge"><strong><?php echo esc_html(echelon_field('about_story_badge_value', $page_id, '24/7')); ?></strong><span><?php echo esc_html(echelon_field('about_story_badge_label', $page_id, __('Personal Concierge', 'echelon'))); ?></span></div>
			</div>
			<div class="about-story__content">
				<p class="eyebrow"><?php echo esc_html(echelon_field('about_story_eyebrow', $page_id, __('Our Story', 'echelon'))); ?></p>
				<h2><?php echo wp_kses(echelon_accent_heading(echelon_field('about_story_heading', $page_id, __('Luxury Should Feel Effortless', 'echelon')), echelon_field('about_story_accent', $page_id, __('Effortless', 'echelon'))), ['span' => ['class' => true]]); ?></h2>
				<?php if ($story_content) : ?>
					<div class="entry-content"><?php echo wp_kses_post(apply_filters('the_content', $story_content)); ?></div>
				<?php else : ?>
					<div class="entry-content">
						<p><?php esc_html_e('Echelon Motions started with a simple observation: most car services treat the vehicle as the product. We treat the arrival as the product - the vehicle is just one part of getting that right.', 'echelon'); ?></p>
					</div>
				<?php endif; ?>
				<a class="btn btn--primary" href="<?php echo esc_url($story_cta['url'] ?? home_url('/fleet/')); ?>"<?php echo !empty($story_cta['target']) ? ' target="' . esc_attr($story_cta['target']) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html($story_cta['title'] ?? __('Explore Our Fleet', 'echelon')); ?><?php echelon_icon('arrow-right'); ?></a>
			</div>
		</div>
	</section>

	<section class="about-proof" data-reveal>
		<div class="container about-proof__grid">
			<?php foreach ($stats as $stat) : ?>
				<div class="about-proof__item"><strong><?php echo esc_html($stat['value'] ?? ''); ?></strong><span><?php echo esc_html($stat['label'] ?? ''); ?></span></div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="section about-values" data-reveal>
		<div class="container">
			<header class="about-section-heading">
				<div><p class="eyebrow"><?php echo esc_html(echelon_field('about_values_eyebrow', $page_id, __('What Guides Us', 'echelon'))); ?></p><h2><?php echo wp_kses(echelon_accent_heading(echelon_field('about_values_heading', $page_id, __('The Echelon Standard', 'echelon')), echelon_field('about_values_accent', $page_id, __('Standard', 'echelon'))), ['span' => ['class' => true]]); ?></h2></div>
				<p><?php echo esc_html(echelon_field('about_values_description', $page_id, __('The details clients may never see are often the ones that matter most. These principles shape every booking.', 'echelon'))); ?></p>
			</header>
			<div class="about-values__grid">
				<?php foreach ($values as $index => $value) : ?>
					<article class="about-value-card"><span><?php echelon_icon($value['icon'] ?? 'check'); ?></span><small><?php echo esc_html(sprintf('%02d', $index + 1)); ?></small><h3><?php echo esc_html($value['title'] ?? ''); ?></h3><p><?php echo esc_html($value['description'] ?? ''); ?></p></article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section about-journey" data-reveal>
		<div class="container">
			<header class="about-section-heading">
				<div><p class="eyebrow"><?php echo esc_html(echelon_field('about_journey_eyebrow', $page_id, __('From Request To Road', 'echelon'))); ?></p><h2><?php echo wp_kses(echelon_accent_heading(echelon_field('about_journey_heading', $page_id, __('Your Journey, Handled', 'echelon')), echelon_field('about_journey_accent', $page_id, __('Handled', 'echelon'))), ['span' => ['class' => true]]); ?></h2></div>
				<p><?php echo esc_html(echelon_field('about_journey_description', $page_id, __('One dedicated team coordinates the complete experience around your schedule and destination.', 'echelon'))); ?></p>
			</header>
			<ol class="about-journey__steps">
				<?php foreach ($journey_steps as $index => $step) : ?>
					<li><span><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span><div><h3><?php echo esc_html($step['title'] ?? ''); ?></h3><p><?php echo esc_html($step['description'] ?? ''); ?></p></div></li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<?php get_template_part('template-parts/home/cta'); ?>
	<?php
endwhile;

get_footer();
