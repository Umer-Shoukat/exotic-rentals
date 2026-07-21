<?php
/**
 * Template Name: About Page
 *
 * Brand story and service philosophy.
 */

get_header();

while (have_posts()) : the_post();
	$hero_image = get_post_thumbnail_id();
	$editor_content = trim(get_the_content());
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
			<p class="eyebrow eyebrow--flanked"><?php esc_html_e('Driven By The Experience', 'echelon'); ?></p>
			<h1><?php esc_html_e('More Than A Car.', 'echelon'); ?><br><span class="accent"><?php esc_html_e('A Standard Of Service.', 'echelon'); ?></span></h1>
			<p><?php echo esc_html(get_the_excerpt() ?: __('We pair an exceptional fleet with personal, detail-driven service to make every journey feel effortless.', 'echelon')); ?></p>
		</div>
	</section>

	<section class="section about-story" data-reveal>
		<div class="container about-story__grid">
			<div class="about-story__media">
				<img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/occasions/corporate.jpg')); ?>" alt="<?php esc_attr_e('Luxury vehicle prepared for a client', 'echelon'); ?>" loading="lazy">
				<div class="about-story__badge"><strong><?php esc_html_e('24/7', 'echelon'); ?></strong><span><?php esc_html_e('Personal Concierge', 'echelon'); ?></span></div>
			</div>
			<div class="about-story__content">
				<p class="eyebrow"><?php esc_html_e('Our Story', 'echelon'); ?></p>
				<h2><?php esc_html_e('Luxury Should Feel', 'echelon'); ?> <span class="accent"><?php esc_html_e('Effortless', 'echelon'); ?></span></h2>
				<?php if ($editor_content) : ?>
					<div class="entry-content"><?php the_content(); ?></div>
				<?php else : ?>
					<div class="entry-content">
						<p><?php esc_html_e('Echelon Motions was built around a simple belief: an extraordinary vehicle deserves an equally extraordinary experience. From the first conversation to final collection, every detail should feel considered.', 'echelon'); ?></p>
						<p><?php esc_html_e('Our team brings together a carefully selected fleet, uncompromising preparation, and a real concierge who understands the occasion behind every booking.', 'echelon'); ?></p>
					</div>
				<?php endif; ?>
				<a class="btn btn--primary" href="<?php echo esc_url(home_url('/fleet/')); ?>"><?php esc_html_e('Explore Our Fleet', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a>
			</div>
		</div>
	</section>

	<section class="about-proof" data-reveal>
		<div class="container about-proof__grid">
			<div class="about-proof__item"><strong>500+</strong><span><?php esc_html_e('Exceptional Vehicles', 'echelon'); ?></span></div>
			<div class="about-proof__item"><strong>100%</strong><span><?php esc_html_e('Inspected & Detailed', 'echelon'); ?></span></div>
			<div class="about-proof__item"><strong>45M</strong><span><?php esc_html_e('Average Delivery Time', 'echelon'); ?></span></div>
			<div class="about-proof__item"><strong>24/7</strong><span><?php esc_html_e('Concierge Support', 'echelon'); ?></span></div>
		</div>
	</section>

	<section class="section about-values" data-reveal>
		<div class="container">
			<header class="about-section-heading">
				<div><p class="eyebrow"><?php esc_html_e('What Guides Us', 'echelon'); ?></p><h2><?php esc_html_e('The Echelon', 'echelon'); ?> <span class="accent"><?php esc_html_e('Standard', 'echelon'); ?></span></h2></div>
				<p><?php esc_html_e('The details clients may never see are often the ones that matter most. These principles shape every booking.', 'echelon'); ?></p>
			</header>
			<div class="about-values__grid">
				<article class="about-value-card"><span><?php echelon_icon('shield-check'); ?></span><small>01</small><h3><?php esc_html_e('Uncompromising Quality', 'echelon'); ?></h3><p><?php esc_html_e('Every vehicle is selected, inspected, and meticulously prepared before it reaches your door.', 'echelon'); ?></p></article>
				<article class="about-value-card"><span><?php echelon_icon('headset'); ?></span><small>02</small><h3><?php esc_html_e('Human Service', 'echelon'); ?></h3><p><?php esc_html_e('A knowledgeable concierge stays available before, during, and after every reservation.', 'echelon'); ?></p></article>
				<article class="about-value-card"><span><?php echelon_icon('bolt'); ?></span><small>03</small><h3><?php esc_html_e('Effortless Execution', 'echelon'); ?></h3><p><?php esc_html_e('Clear communication, reliable delivery, and thoughtful coordination keep the experience seamless.', 'echelon'); ?></p></article>
			</div>
		</div>
	</section>

	<section class="section about-journey" data-reveal>
		<div class="container">
			<header class="about-section-heading">
				<div><p class="eyebrow"><?php esc_html_e('From Request To Road', 'echelon'); ?></p><h2><?php esc_html_e('Your Journey,', 'echelon'); ?> <span class="accent"><?php esc_html_e('Handled', 'echelon'); ?></span></h2></div>
				<p><?php esc_html_e('One dedicated team coordinates the complete experience around your schedule and destination.', 'echelon'); ?></p>
			</header>
			<ol class="about-journey__steps">
				<li><span>01</span><div><h3><?php esc_html_e('Tell Us The Occasion', 'echelon'); ?></h3><p><?php esc_html_e('Share the date, destination, preferences, and the kind of arrival you have in mind.', 'echelon'); ?></p></div></li>
				<li><span>02</span><div><h3><?php esc_html_e('We Curate The Details', 'echelon'); ?></h3><p><?php esc_html_e('Your concierge confirms the right vehicle and coordinates coverage, delivery, and timing.', 'echelon'); ?></p></div></li>
				<li><span>03</span><div><h3><?php esc_html_e('Enjoy The Drive', 'echelon'); ?></h3><p><?php esc_html_e('The vehicle arrives prepared and on time, with our team available whenever you need us.', 'echelon'); ?></p></div></li>
			</ol>
		</div>
	</section>

	<?php get_template_part('template-parts/home/cta'); ?>
	<?php
endwhile;

get_footer();
