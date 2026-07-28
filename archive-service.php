<?php
/** Services archive: /services/. */
get_header();

$service_count = (int) wp_count_posts('service')->publish;
$vehicle_count = (int) wp_count_posts('fleet_vehicle')->publish;
$hero_eyebrow = echelon_field('services_hero_eyebrow', 'option', 'Chauffeur & Transportation Services');
$hero_title = echelon_field('services_hero_title', 'option', 'Luxury Service For Every Occasion');
$hero_description = echelon_field('services_hero_description', 'option', 'From landmark weddings and high-stakes business travel to content production, our concierge team coordinates every vehicle, detail, and arrival.');
$hero_image = echelon_field('services_hero_image', 'option', null);
$list_title = echelon_field('services_list_title', 'option', 'Luxury, Tailored To Every Occasion');
$list_description = echelon_field('services_list_description', 'option', 'Select the experience that matches the moment. Every service is tailored around your schedule, route, vehicle preferences, and guest requirements.');
$primary_cta = echelon_field('services_hero_primary_cta', 'option', ['title' => __('Explore Services', 'echelon'), 'url' => '#service-list', 'target' => '']);
$secondary_cta = echelon_field('services_hero_secondary_cta', 'option', ['title' => __('Plan Your Experience', 'echelon'), 'url' => home_url('/contact/'), 'target' => '']);
$proof_items = echelon_field('services_proof_items', 'option', [
	['value' => '5.0', 'label' => __('Google Reviews', 'echelon')],
	['value' => $vehicle_count ? $vehicle_count . '+' : '40+', 'label' => __('Vehicles', 'echelon')],
	['value' => $service_count ? $service_count . '+' : '6+', 'label' => __('Luxury Services', 'echelon')],
	['value' => '100%', 'label' => __('Insured', 'echelon')],
]);
$steps = echelon_field('services_steps', 'option', [
	['icon' => 'gauge', 'title' => __('Choose Your Vehicle', 'echelon'), 'description' => __('Browse the live fleet and select the vehicle for your occasion.', 'echelon')],
	['icon' => 'calendar', 'title' => __('Select Your Dates', 'echelon'), 'description' => __('Pick your timing, pickup location, and destination.', 'echelon')],
	['icon' => 'shield-check', 'title' => __('Confirm Reservation', 'echelon'), 'description' => __('Our concierge team confirms pricing, vehicle, and chauffeur details.', 'echelon')],
	['icon' => 'check', 'title' => __('Enjoy The Experience', 'echelon'), 'description' => __("Your chauffeur arrives on time and ready. That's the promise.", 'echelon')],
]);
if ($hero_eyebrow === 'Premium Automotive Services') {
	$hero_eyebrow = 'Chauffeur & Transportation Services';
}
foreach ($steps as &$step) {
	if (($step['description'] ?? '') === 'Choose your vehicle from the live fleet.') {
		$step['description'] = __('Browse the live fleet and select the vehicle for your occasion.', 'echelon');
	}
	if (($step['description'] ?? '') === 'Pick timing, location, and delivery details.') {
		$step['description'] = __('Pick your timing, pickup location, and destination.', 'echelon');
	}
	if (($step['description'] ?? '') === 'Review pricing, documents, and coverage.') {
		$step['description'] = __('Our concierge team confirms pricing, vehicle, and chauffeur details.', 'echelon');
	}
	if (($step['description'] ?? '') === 'We deliver. You drive. That is the promise.') {
		$step['description'] = __("Your chauffeur arrives on time and ready. That's the promise.", 'echelon');
	}
}
unset($step);
$hero_title_html = preg_replace('/(\S+)$/u', '<span>$1</span>', esc_html($hero_title));
$list_title_html = preg_replace('/(\S+)$/u', '<span>$1</span>', esc_html($list_title));
?>
<main class="services-page">
	<section class="services-hero">
		<div class="services-hero__media" aria-hidden="true">
			<?php if ($hero_image) : ?><?php echelon_media($hero_image, 'full', ''); ?><?php else : ?><img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/hero-homepage-v3.jpg')); ?>" alt=""><?php endif; ?>
			<span></span>
		</div>
		<div class="container services-hero__content">
			<nav class="services-breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'echelon'); ?>">
				<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'echelon'); ?></a><span aria-hidden="true">/</span><span><?php esc_html_e('Services', 'echelon'); ?></span>
			</nav>
			<p class="eyebrow eyebrow--flanked"><?php echo esc_html($hero_eyebrow); ?></p>
			<h1><?php echo wp_kses($hero_title_html, ['span' => []]); ?></h1>
			<p><?php echo esc_html($hero_description); ?></p>
			<div class="services-hero__actions">
				<a class="btn btn--primary" href="<?php echo esc_url($primary_cta['url'] ?? '#service-list'); ?>"<?php echo !empty($primary_cta['target']) ? ' target="' . esc_attr($primary_cta['target']) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html($primary_cta['title'] ?? __('Explore Services', 'echelon')); ?><?php echelon_icon('arrow-right'); ?></a>
				<a class="btn btn--outline" href="<?php echo esc_url($secondary_cta['url'] ?? home_url('/contact/')); ?>"<?php echo !empty($secondary_cta['target']) ? ' target="' . esc_attr($secondary_cta['target']) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html($secondary_cta['title'] ?? __('Plan Your Experience', 'echelon')); ?></a>
			</div>
		</div>
	</section>

	<section class="services-proof" aria-label="<?php esc_attr_e('Service summary', 'echelon'); ?>">
		<div class="container services-proof__grid">
			<?php foreach ($proof_items as $item) : ?><div><strong><?php echo esc_html($item['value'] ?? ''); ?></strong><span><?php echo esc_html($item['label'] ?? ''); ?></span></div><?php endforeach; ?>
		</div>
	</section>

	<section class="section services-list" id="service-list" data-reveal>
		<div class="container">
			<header class="services-list__header">
				<div><p class="eyebrow"><?php echo esc_html(echelon_field('services_list_eyebrow', 'option', __('Our Premium Services', 'echelon'))); ?></p><h2><?php echo wp_kses($list_title_html, ['span' => []]); ?></h2></div>
				<p><?php echo esc_html($list_description); ?></p>
			</header>
			<?php if (have_posts()) : ?>
				<div class="services-grid"><?php while (have_posts()) : the_post(); get_template_part('template-parts/services/card'); endwhile; ?></div>
				<?php the_posts_pagination(['prev_text' => __('Previous', 'echelon'), 'next_text' => __('Next', 'echelon')]); ?>
			<?php else : ?>
				<div class="services-empty"><h2><?php esc_html_e('Your services are ready to be added.', 'echelon'); ?></h2><p><?php esc_html_e('Publish Service entries in WordPress and they will appear here automatically.', 'echelon'); ?></p></div>
			<?php endif; ?>
		</div>
	</section>

	<section class="section service-steps" data-reveal>
		<div class="container">
			<?php
			$steps_heading = echelon_field('services_steps_heading', 'option', __('Booking Your Chauffeur Is Simple', 'echelon'));
			if ($steps_heading === 'Renting Your Dream Car Is Simple') {
				$steps_heading = __('Booking Your Chauffeur Is Simple', 'echelon');
			}
			?>
			<header><p class="eyebrow"><?php echo esc_html(echelon_field('services_steps_eyebrow', 'option', __('Simple From Start To Finish', 'echelon'))); ?></p><h2><?php echo wp_kses(echelon_accent_heading($steps_heading, __('Chauffeur', 'echelon')), ['span' => ['class' => true]]); ?></h2></header>
			<ol>
				<?php foreach ($steps as $index => $step) : ?>
					<li><b><?php echo esc_html(sprintf('%02d', $index + 1)); ?></b><?php echelon_icon($step['icon'] ?? 'check'); ?><h3><?php echo esc_html($step['title'] ?? ''); ?></h3><p><?php echo esc_html($step['description'] ?? ''); ?></p></li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<?php get_template_part('template-parts/home/serving-cities'); ?>
	<?php get_template_part('template-parts/home/faq'); ?>
	<?php get_template_part('template-parts/home/cta'); ?>
</main>
<?php get_footer(); ?>
