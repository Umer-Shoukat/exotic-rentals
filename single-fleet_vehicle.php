<?php
/**
 * Single fleet vehicle detail.
 */
get_header();

while (have_posts()) : the_post();
    $vehicle_id = get_the_ID();
    $gallery = (array) echelon_field('gallery', $vehicle_id, []);
    $featured_id = get_post_thumbnail_id($vehicle_id);
    if (!$gallery && $featured_id) {
        $gallery = [$featured_id];
    }
    $fallbacks = array_map(static fn($name) => ECHELON_THEME_URI . '/assets/images/figma/car-detail/' . $name, ['hero.jpg', 'gallery-1.jpg', 'gallery-2.jpg', 'gallery-3.jpg', 'gallery-4.jpg']);
    $image_url = static function ($image, $size = 'full') {
        $id = is_array($image) ? ($image['ID'] ?? 0) : (int) $image;
        return $id ? wp_get_attachment_image_url($id, $size) : '';
    };
    $gallery_urls = array_values(array_filter(array_map($image_url, $gallery)));
    if (!$gallery_urls) {
        $gallery_urls = $fallbacks;
    }
    $gallery_urls = array_slice(array_pad($gallery_urls, 5, $gallery_urls[0]), 0, 5);

    $title = get_the_title();
    $brand = echelon_field('brand', $vehicle_id, 'Lamborghini');
    $year = echelon_field('year', $vehicle_id, '2018');
    $doors = echelon_field('doors', $vehicle_id, '2');
    $price = echelon_field('price_per_day', $vehicle_id, '1600');
    $hp = echelon_field('horsepower', $vehicle_id, '630');
    $zero_to_sixty = echelon_field('zero_to_sixty', $vehicle_id, '2.9s');
    $seats = echelon_field('seats', $vehicle_id, '2');
    $reserve_url = add_query_arg('vehicle', $vehicle_id, home_url('/reservation/'));
    $description = has_excerpt() ? get_the_excerpt() : __('Built for presence and engineered for exhilaration, this exotic delivers an unmistakable driving experience with concierge-level service from pickup to return.', 'echelon');
    $specs = [
        ['bolt', __('Horsepower', 'echelon'), $hp . ' HP'],
        ['gauge', __('Acceleration', 'echelon'), '0–60 in ' . $zero_to_sixty],
        ['seat', __('Seating', 'echelon'), $seats . ' ' . __('Seats', 'echelon')],
        ['wrench', __('Transmission', 'echelon'), echelon_field('transmission', $vehicle_id, 'Automatic')],
        ['bolt', __('Engine', 'echelon'), echelon_field('engine', $vehicle_id, 'V10')],
        ['truck', __('Drivetrain', 'echelon'), echelon_field('drivetrain', $vehicle_id, 'All-Wheel Drive')],
        ['star', __('Exterior', 'echelon'), echelon_field('exterior_color', $vehicle_id, 'Black')],
        ['star', __('Interior', 'echelon'), echelon_field('interior_color', $vehicle_id, 'Black Leather')],
        ['gauge', __('Fuel', 'echelon'), echelon_field('fuel_type', $vehicle_id, 'Premium')],
    ];
?>
<article class="vehicle-detail">
    <section class="vehicle-detail__hero">
        <img src="<?php echo esc_url($gallery_urls[0]); ?>" alt="<?php echo esc_attr($title); ?>">
        <div class="vehicle-detail__hero-scrim"></div>
        <div class="container vehicle-detail__hero-content">
            <h1><?php echo esc_html($title); ?></h1>
            <p><?php echo esc_html($year); ?> · <?php echo esc_html($doors); ?>-<?php esc_html_e('Door', 'echelon'); ?></p>
            <a class="btn btn--primary" href="<?php echo esc_url($reserve_url); ?>"><?php esc_html_e('Book Now', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a>
        </div>
    </section>

    <div class="container vehicle-detail__gallery" data-vehicle-gallery>
        <div class="vehicle-detail__gallery-main"><img data-gallery-hero src="<?php echo esc_url($gallery_urls[0]); ?>" alt="<?php echo esc_attr($title); ?>"></div>
        <div class="vehicle-detail__thumbs">
            <?php foreach ($gallery_urls as $index => $url) : ?>
                <button class="vehicle-detail__thumb<?php echo $index === 0 ? ' is-active' : ''; ?>" type="button" data-gallery-thumb data-gallery-src="<?php echo esc_url($url); ?>" aria-label="<?php echo esc_attr(sprintf(__('View %1$s image %2$d', 'echelon'), $title, $index + 1)); ?>"><img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($title); ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>

    <section class="container vehicle-detail__overview">
        <div class="vehicle-detail__copy">
            <p class="eyebrow"><?php echo esc_html($brand); ?></p>
            <h2><?php echo esc_html($year . ' · ' . $doors . '-Door '); ?><span><?php echo esc_html($brand); ?></span></h2>
            <p><?php echo esc_html($description); ?></p>
            <p><?php esc_html_e('Every rental is prepared by our team, inspected before delivery, and supported by a dedicated concierge throughout your reservation.', 'echelon'); ?></p>
        </div>
        <aside class="vehicle-detail__booking" aria-label="<?php esc_attr_e('Vehicle reservation summary', 'echelon'); ?>">
            <div class="vehicle-detail__rate"><span><?php esc_html_e('From', 'echelon'); ?></span><strong><?php echo esc_html(echelon_price($price)); ?><small>/<?php esc_html_e('day', 'echelon'); ?></small></strong></div>
            <dl>
                <div><dt><?php esc_html_e('Security Deposit', 'echelon'); ?></dt><dd><?php echo esc_html(echelon_price(echelon_field('security_deposit', $vehicle_id, 2500))); ?></dd></div>
                <div><dt><?php esc_html_e('Included Miles', 'echelon'); ?></dt><dd><?php echo esc_html(echelon_field('included_miles', $vehicle_id, 100)); ?>/<?php esc_html_e('day', 'echelon'); ?></dd></div>
                <div><dt><?php esc_html_e('Minimum Age', 'echelon'); ?></dt><dd>25+</dd></div>
            </dl>
            <a class="btn btn--primary btn--block" href="<?php echo esc_url($reserve_url); ?>"><?php esc_html_e('Book Now', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a>
            <small><?php esc_html_e('Final availability is confirmed by our concierge.', 'echelon'); ?></small>
        </aside>
    </section>

    <section class="container vehicle-detail__specifications">
        <p class="eyebrow"><?php esc_html_e('At A Glance', 'echelon'); ?></p>
        <h2><?php echo esc_html($year . ' · ' . $doors . '-Door '); ?><span><?php echo esc_html($brand); ?></span></h2>
        <div class="vehicle-detail__spec-grid">
            <?php foreach ($specs as [$icon, $label, $value]) : ?><div class="vehicle-detail__spec"><?php echelon_icon($icon); ?><span><?php echo esc_html($label); ?></span><strong><?php echo esc_html($value); ?></strong></div><?php endforeach; ?>
        </div>
    </section>

    <section class="container vehicle-detail__benefits">
        <p class="eyebrow"><?php esc_html_e('Why This Car', 'echelon'); ?></p>
        <h2><?php esc_html_e('Built For The', 'echelon'); ?> <span><?php esc_html_e('Moment', 'echelon'); ?></span></h2>
        <div class="vehicle-detail__benefit-grid">
            <?php foreach ([['star', 'Head-Turning Presence', 'Arrive with unmistakable road presence.'], ['gauge', 'Performance & Sound', 'Immediate response and an unforgettable soundtrack.'], ['shield-check', 'Fully Insured', 'Coverage and preparation are handled before delivery.'], ['headset', 'Concierge Support', 'A real person is available throughout your rental.'], ['truck', 'Airport Transfers', 'Flexible delivery and collection across service areas.']] as [$icon, $heading, $copy]) : ?><div><?php echelon_icon($icon); ?><h3><?php echo esc_html($heading); ?></h3><p><?php echo esc_html($copy); ?></p></div><?php endforeach; ?>
        </div>
    </section>

    <section class="container vehicle-detail__steps">
        <p class="eyebrow"><?php esc_html_e('Pickup & Return', 'echelon'); ?></p>
        <h2><?php esc_html_e('How To Get', 'echelon'); ?> <span><?php esc_html_e('Your Car', 'echelon'); ?></span></h2>
        <div class="vehicle-detail__step-grid">
            <?php foreach ([['01', 'Choose & Book', 'Select your dates and send your reservation request.'], ['02', 'Verify & Prepare', 'Our concierge confirms availability, documents, and delivery.'], ['03', 'Delivery & Drive', 'Meet your prepared vehicle and enjoy the experience.']] as [$number, $heading, $copy]) : ?><div><strong><?php echo esc_html($number); ?></strong><h3><?php echo esc_html($heading); ?></h3><p><?php echo esc_html($copy); ?></p><span><?php esc_html_e('Concierge supported', 'echelon'); ?></span></div><?php endforeach; ?>
        </div>
    </section>

    <?php get_template_part('template-parts/home/faq'); ?>

    <?php
    $related = new WP_Query(['post_type' => 'fleet_vehicle', 'post_status' => 'publish', 'posts_per_page' => 3, 'post__not_in' => [$vehicle_id], 'orderby' => 'rand', 'no_found_rows' => true]);
    if ($related->have_posts()) : ?>
        <section class="container vehicle-detail__related">
            <p class="eyebrow"><?php esc_html_e('You May Also Like', 'echelon'); ?></p>
            <h2><?php esc_html_e('Similar', 'echelon'); ?> <span><?php esc_html_e('Vehicles', 'echelon'); ?></span></h2>
            <div class="vehicle-detail__related-grid"><?php while ($related->have_posts()) : $related->the_post(); get_template_part('template-parts/fleet/card'); endwhile; ?></div>
            <a class="vehicle-detail__view-more" href="<?php echo esc_url(get_post_type_archive_link('fleet_vehicle')); ?>"><?php esc_html_e('View More', 'echelon'); ?></a>
        </section>
    <?php endif; wp_reset_postdata(); ?>

    <section class="vehicle-detail__cta">
        <img src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/figma/car-detail/cta.jpg'); ?>" alt="" loading="lazy">
        <div class="container"><p class="eyebrow"><?php esc_html_e('Reserve Today', 'echelon'); ?></p><h2><?php esc_html_e('Ready To Drive', 'echelon'); ?> <span><?php echo esc_html($brand); ?></span></h2><p><?php esc_html_e('Send your dates and our concierge will confirm the vehicle, delivery, and every detail.', 'echelon'); ?></p><a class="btn btn--primary" href="<?php echo esc_url($reserve_url); ?>"><?php esc_html_e('Reserve', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a></div>
    </section>
</article>
<?php endwhile; get_footer();
