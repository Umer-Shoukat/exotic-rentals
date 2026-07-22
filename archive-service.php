<?php
/** Services archive: /services/. */
get_header();

$service_count = (int) wp_count_posts('service')->publish;
$vehicle_count = (int) wp_count_posts('fleet_vehicle')->publish;
$hero_eyebrow = echelon_field('services_hero_eyebrow', 'option', 'Premium Automotive Services');
$hero_title = echelon_field('services_hero_title', 'option', 'Luxury Service For Every Occasion');
$hero_description = echelon_field('services_hero_description', 'option', 'From landmark weddings and high-stakes business travel to content production, our concierge team coordinates every vehicle, detail, and arrival.');
$hero_image = echelon_field('services_hero_image', 'option', null);
$list_title = echelon_field('services_list_title', 'option', 'Luxury, Tailored To Every Occasion');
$list_description = echelon_field('services_list_description', 'option', 'Select the experience that matches the moment. Every service is tailored around your schedule, route, vehicle preferences, and guest requirements.');
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
				<a class="btn btn--primary" href="#service-list"><?php esc_html_e('Explore Services', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a>
				<a class="btn btn--outline" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Plan Your Experience', 'echelon'); ?></a>
			</div>
		</div>
	</section>

	<section class="services-proof" aria-label="<?php esc_attr_e('Service summary', 'echelon'); ?>">
		<div class="container services-proof__grid">
			<div><strong>5.0</strong><span><?php esc_html_e('Google Reviews', 'echelon'); ?></span></div>
			<div><strong><?php echo esc_html($vehicle_count ? $vehicle_count . '+' : '40+'); ?></strong><span><?php esc_html_e('Vehicles', 'echelon'); ?></span></div>
			<div><strong><?php echo esc_html($service_count ? $service_count . '+' : '6+'); ?></strong><span><?php esc_html_e('Luxury Services', 'echelon'); ?></span></div>
			<div><strong>100%</strong><span><?php esc_html_e('Insured', 'echelon'); ?></span></div>
		</div>
	</section>

	<section class="section services-list" id="service-list" data-reveal>
		<div class="container">
			<header class="services-list__header">
				<div><p class="eyebrow"><?php esc_html_e('Our Premium Services', 'echelon'); ?></p><h2><?php echo wp_kses($list_title_html, ['span' => []]); ?></h2></div>
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
			<header><p class="eyebrow"><?php esc_html_e('Simple From Start To Finish', 'echelon'); ?></p><h2><?php esc_html_e('Renting Your', 'echelon'); ?> <span><?php esc_html_e('Dream Car', 'echelon'); ?> </span><?php esc_html_e('Is Simple', 'echelon'); ?></h2></header>
			<ol>
				<li><b>01</b><?php echelon_icon('gauge'); ?><h3><?php esc_html_e('Choose Your Vehicle', 'echelon'); ?></h3><p><?php esc_html_e('Choose your vehicle from the live fleet.', 'echelon'); ?></p></li>
				<li><b>02</b><?php echelon_icon('calendar'); ?><h3><?php esc_html_e('Select Your Dates', 'echelon'); ?></h3><p><?php esc_html_e('Pick timing, location, and delivery details.', 'echelon'); ?></p></li>
				<li><b>03</b><?php echelon_icon('shield-check'); ?><h3><?php esc_html_e('Confirm Reservation', 'echelon'); ?></h3><p><?php esc_html_e('Review pricing, documents, and coverage.', 'echelon'); ?></p></li>
				<li><b>04</b><?php echelon_icon('check'); ?><h3><?php esc_html_e('Enjoy The Experience', 'echelon'); ?></h3><p><?php esc_html_e('We deliver. You drive. That is the promise.', 'echelon'); ?></p></li>
			</ol>
		</div>
	</section>

	<?php get_template_part('template-parts/home/serving-cities'); ?>
	<?php get_template_part('template-parts/home/faq'); ?>
	<?php get_template_part('template-parts/home/cta'); ?>
</main>
<?php get_footer(); ?>
