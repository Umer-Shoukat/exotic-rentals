<?php
/**
 * Single fleet vehicle detail.
 */
get_header();

while (have_posts()) : the_post();
    $vehicle_id = get_the_ID();
    $gallery = echelon_vehicle_gallery($vehicle_id);
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

    $title = get_the_title();
    $brand = echelon_field('brand', $vehicle_id, 'Lamborghini');
    $year = echelon_field('year', $vehicle_id, '2018');
    $doors = echelon_field('doors', $vehicle_id, '2');
    $price = echelon_field('price_per_hour', $vehicle_id, '');
    $minimum_hours = max(3, (int) echelon_field('minimum_booking_hours', $vehicle_id, 3));
    $rate_note = echelon_field('hourly_rate_note', $vehicle_id, '');
    $addons = array_values(array_filter((array) echelon_field('vehicle_addons', $vehicle_id, []), static function ($addon) {
        return !empty($addon['name']) && isset($addon['price']) && $addon['price'] !== '';
    }));
    if (!$addons) {
        $addon_count = absint(get_post_meta($vehicle_id, 'vehicle_addons', true));
        for ($addon_index = 0; $addon_index < $addon_count; $addon_index++) {
            $addon_name = get_post_meta($vehicle_id, "vehicle_addons_{$addon_index}_name", true);
            $addon_price = get_post_meta($vehicle_id, "vehicle_addons_{$addon_index}_price", true);
            if ($addon_name !== '' && $addon_price !== '') {
                $addons[] = ['name' => $addon_name, 'price' => $addon_price];
            }
        }
    }
    $toll_policy = echelon_field('toll_policy', $vehicle_id, '');
    $travel_policy = echelon_field('travel_policy', $vehicle_id, '');
    $daily_rental_price = echelon_field('daily_rental_price', $vehicle_id, '');
    $daily_deposit = echelon_field('daily_rental_security_deposit', $vehicle_id, '');
    $hp = echelon_field('horsepower', $vehicle_id, '630');
    $zero_to_sixty = echelon_field('zero_to_sixty', $vehicle_id, '2.9s');
    $seats = echelon_field('seats', $vehicle_id, '2');
    $engine = echelon_field('engine', $vehicle_id, 'V10');
    $exterior = echelon_field('exterior_color', $vehicle_id, 'Black');
    $interior = echelon_field('interior_color', $vehicle_id, 'Black Leather');
    $vehicle_categories = wp_get_post_terms($vehicle_id, 'vehicle_category', ['fields' => 'names']);
    $vehicle_category = !is_wp_error($vehicle_categories) && $vehicle_categories ? $vehicle_categories[0] : $doors . '-Door';
    $hero_rate = $daily_rental_price !== '' ? $daily_rental_price : $price;
    $hero_rate_period = $daily_rental_price !== '' ? __('day', 'echelon') : __('hour', 'echelon');
    $reserve_url = add_query_arg('vehicle', $vehicle_id, home_url('/reservation/'));
    $description = has_excerpt() ? get_the_excerpt() : __('Built for presence and engineered for exhilaration, this exotic delivers an unmistakable driving experience with concierge-level service from pickup to return.', 'echelon');
    $specs = [
        ['bolt', __('Horsepower', 'echelon'), $hp . ' HP'],
        ['gauge', __('Acceleration', 'echelon'), '0–60 in ' . $zero_to_sixty],
        ['seat', __('Seating', 'echelon'), $seats . ' ' . __('Seats', 'echelon')],
        ['wrench', __('Transmission', 'echelon'), echelon_field('transmission', $vehicle_id, 'Automatic')],
        ['bolt', __('Engine', 'echelon'), $engine],
        ['truck', __('Drivetrain', 'echelon'), echelon_field('drivetrain', $vehicle_id, 'All-Wheel Drive')],
        ['star', __('Exterior', 'echelon'), $exterior],
        ['star', __('Interior', 'echelon'), $interior],
        ['gauge', __('Fuel', 'echelon'), echelon_field('fuel_type', $vehicle_id, 'Premium')],
    ];
?>
<article class="vehicle-detail">
    <section class="vehicle-detail__hero">
        <div class="container vehicle-detail__hero-content">
            <div class="vehicle-detail__hero-main">
                <p class="vehicle-detail__hero-brand"><?php echo esc_html($brand); ?></p>
                <h1><?php echo esc_html($title); ?></h1>
                <p class="vehicle-detail__hero-meta"><?php echo esc_html(implode(' · ', [$year, $vehicle_category, $exterior])); ?></p>
            </div>
            <div class="vehicle-detail__hero-stats" aria-label="<?php esc_attr_e('Vehicle highlights', 'echelon'); ?>">
                <div><strong><?php echo esc_html($year); ?></strong><span><?php esc_html_e('Year', 'echelon'); ?></span></div>
                <div><strong><?php echo esc_html($hp); ?> HP</strong><span><?php esc_html_e('Power', 'echelon'); ?></span></div>
                <div><strong><?php echo esc_html($engine); ?></strong><span><?php esc_html_e('Engine', 'echelon'); ?></span></div>
            </div>
            <div class="vehicle-detail__hero-summary">
                <div>
                    <span><?php esc_html_e('Rental', 'echelon'); ?></span>
                    <strong><?php echo esc_html(sprintf(_n('%d hour', '%d hours', $minimum_hours, 'echelon'), $minimum_hours)); ?></strong>
                </div>
                <div>
                    <span><?php esc_html_e('Starting At', 'echelon'); ?></span>
                    <strong><?php echo $hero_rate !== '' ? esc_html(echelon_price($hero_rate) . '/' . $hero_rate_period) : esc_html__('Contact Us', 'echelon'); ?></strong>
                </div>
                <a class="btn btn--primary" href="<?php echo esc_url($reserve_url); ?>">
                    <?php esc_html_e('Check Availability', 'echelon'); ?>
                    <?php echelon_icon('arrow-right'); ?>
                </a>
            </div>
        </div>
    </section>

    <div class="container vehicle-detail__gallery" data-vehicle-gallery>
        <div class="vehicle-detail__gallery-main">
            <button class="vehicle-detail__gallery-arrow vehicle-detail__gallery-arrow--prev" type="button" data-gallery-prev aria-label="<?php esc_attr_e('Previous vehicle image', 'echelon'); ?>"><?php echelon_icon('arrow-left'); ?></button>
            <button class="vehicle-detail__gallery-open" type="button" data-gallery-open aria-label="<?php echo esc_attr(sprintf(__('Open %s gallery', 'echelon'), $title)); ?>"><img data-gallery-hero src="<?php echo esc_url($gallery_urls[0]); ?>" alt="<?php echo esc_attr($title); ?>"></button>
            <button class="vehicle-detail__gallery-arrow vehicle-detail__gallery-arrow--next" type="button" data-gallery-next aria-label="<?php esc_attr_e('Next vehicle image', 'echelon'); ?>"><?php echelon_icon('arrow-right'); ?></button>
        </div>
        <div class="vehicle-detail__thumbs">
            <?php foreach ($gallery_urls as $index => $url) : ?>
                <button class="vehicle-detail__thumb<?php echo $index === 0 ? ' is-active' : ''; ?>" type="button" data-gallery-thumb data-gallery-src="<?php echo esc_url($url); ?>" aria-label="<?php echo esc_attr(sprintf(__('View %1$s image %2$d', 'echelon'), $title, $index + 1)); ?>"><img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($title); ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="vehicle-detail__lightbox" data-gallery-lightbox role="dialog" aria-modal="true" aria-label="<?php echo esc_attr(sprintf(__('%s image gallery', 'echelon'), $title)); ?>" hidden>
            <button class="vehicle-detail__lightbox-close" type="button" data-gallery-close aria-label="<?php esc_attr_e('Close gallery', 'echelon'); ?>"><span aria-hidden="true">&times;</span></button>
            <button class="vehicle-detail__lightbox-arrow vehicle-detail__lightbox-arrow--prev" type="button" data-gallery-prev aria-label="<?php esc_attr_e('Previous vehicle image', 'echelon'); ?>"><?php echelon_icon('arrow-left'); ?></button>
            <img data-gallery-lightbox-image src="<?php echo esc_url($gallery_urls[0]); ?>" alt="<?php echo esc_attr($title); ?>">
            <button class="vehicle-detail__lightbox-arrow vehicle-detail__lightbox-arrow--next" type="button" data-gallery-next aria-label="<?php esc_attr_e('Next vehicle image', 'echelon'); ?>"><?php echelon_icon('arrow-right'); ?></button>
            <p class="vehicle-detail__lightbox-count" data-gallery-count>1 / <?php echo esc_html((string) count($gallery_urls)); ?></p>
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
            <?php if ($price !== '' || $daily_rental_price !== '') : ?><div class="vehicle-detail__rate"><span><?php esc_html_e('Rates', 'echelon'); ?></span><strong><?php if ($price !== '') : ?><?php echo esc_html(echelon_price($price)); ?><small>/<?php esc_html_e('hour', 'echelon'); ?></small><?php endif; ?><?php if ($price !== '' && $daily_rental_price !== '') : ?><br><?php endif; ?><?php if ($daily_rental_price !== '') : ?><?php echo esc_html(echelon_price($daily_rental_price)); ?><small>/<?php esc_html_e('day', 'echelon'); ?></small><?php endif; ?></strong><?php if ($rate_note !== '') : ?><small><?php echo esc_html($rate_note); ?></small><?php endif; ?></div><?php endif; ?>
            <dl>
                <div><dt><?php esc_html_e('Minimum Booking', 'echelon'); ?></dt><dd><?php echo esc_html(sprintf(_n('%d hour', '%d hours', $minimum_hours, 'echelon'), $minimum_hours)); ?></dd></div>
                <?php $security_deposit = echelon_field('security_deposit', $vehicle_id, ''); if ($security_deposit !== '') : ?><div><dt><?php esc_html_e('Security Deposit', 'echelon'); ?></dt><dd><?php echo esc_html(echelon_price($security_deposit)); ?></dd></div><?php endif; ?>
                <div><dt><?php esc_html_e('Included Miles', 'echelon'); ?></dt><dd><?php echo esc_html(echelon_field('included_miles', $vehicle_id, 100)); ?>/<?php esc_html_e('day', 'echelon'); ?></dd></div>
                <div><dt><?php esc_html_e('Minimum Age', 'echelon'); ?></dt><dd>25+</dd></div>
            </dl>
            <a class="btn btn--primary btn--block" href="<?php echo esc_url($reserve_url); ?>"><?php esc_html_e('Book Now', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a>
            <small><?php esc_html_e('Final availability is confirmed by our concierge.', 'echelon'); ?></small>
        </aside>
    </section>

    <?php if ($addons || $toll_policy !== '' || $travel_policy !== '' || $daily_rental_price !== '') : ?>
        <section class="container vehicle-detail__benefits vehicle-detail__rental-details">
            <p class="eyebrow"><?php esc_html_e('Rental Details', 'echelon'); ?></p>
            <h2><?php esc_html_e('Options &', 'echelon'); ?> <span><?php esc_html_e('Policies', 'echelon'); ?></span></h2>
            <div class="vehicle-detail__benefit-grid">
                <?php if ($addons) : ?><div><?php echelon_icon('star'); ?><h3><?php esc_html_e('Add-ons', 'echelon'); ?></h3><?php foreach ($addons as $addon) : ?><p><?php echo esc_html($addon['name'] . ' — ' . echelon_price($addon['price'])); ?></p><?php endforeach; ?></div><?php endif; ?>
                <?php if ($toll_policy !== '') : ?><div><?php echelon_icon('truck'); ?><h3><?php esc_html_e('Toll Policy', 'echelon'); ?></h3><p><?php echo esc_html($toll_policy); ?></p></div><?php endif; ?>
                <?php if ($travel_policy !== '') : ?><div><?php echelon_icon('pin'); ?><h3><?php esc_html_e('Travel Policy', 'echelon'); ?></h3><p><?php echo esc_html($travel_policy); ?></p></div><?php endif; ?>
                <?php if ($daily_rental_price !== '') : ?><div><?php echelon_icon('calendar'); ?><h3><?php esc_html_e('Daily Rental Option', 'echelon'); ?></h3><p><?php echo esc_html(echelon_price($daily_rental_price)); ?> / <?php esc_html_e('day', 'echelon'); ?><?php if ($daily_deposit !== '') : ?><br><?php echo esc_html(sprintf(__('Security deposit: %s', 'echelon'), echelon_price($daily_deposit))); ?><?php endif; ?></p></div><?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="container vehicle-detail__specifications">
        <p class="eyebrow"><?php esc_html_e('At A Glance', 'echelon'); ?></p>
        <h2><?php echo esc_html($year . ' · ' . $doors . '-Door '); ?><span><?php echo esc_html($brand); ?></span></h2>
        <div class="vehicle-detail__spec-grid">
            <?php foreach ($specs as [$icon, $label, $value]) : ?><div class="vehicle-detail__spec"><?php echelon_icon($icon); ?><span><?php echo esc_html($label); ?></span><strong><?php echo esc_html($value); ?></strong></div><?php endforeach; ?>
        </div>
    </section>

    <section class="container vehicle-detail__benefits" data-reveal>
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
