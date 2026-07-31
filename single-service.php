<?php
/** Single service detail: /services/{service-slug}/. */
get_header();

while (have_posts()) : the_post();
    $service_id = get_the_ID();
    $title = get_the_title();
    $hero_eyebrow = echelon_field('service_hero_eyebrow', $service_id, 'Luxury Transportation');
    $hero_heading = echelon_field('service_hero_heading', $service_id, $title);
    $hero_heading_words = preg_split('/\s+/', trim($hero_heading), 2);
    $hero_accent = echelon_field('service_hero_accent', $service_id, $hero_heading_words[0] ?? $hero_heading);
    $hero_description = echelon_field('service_hero_description', $service_id, has_excerpt() ? get_the_excerpt() : 'A tailored luxury vehicle experience, coordinated around your schedule, route, guests, and the moments that matter.');
    $hero_image = echelon_field('service_hero_image', $service_id, get_post_thumbnail_id($service_id));
    $hero_url = is_array($hero_image) ? wp_get_attachment_image_url($hero_image['ID'] ?? 0, 'full') : wp_get_attachment_image_url((int) $hero_image, 'full');
    $hero_url = $hero_url ?: get_theme_file_uri('assets/images/figma/hero-homepage-v3.jpg');
    $advantage_heading = echelon_field('service_advantage_heading', $service_id, 'Make Your Special Day Unforgettable');
    $advantage_body = echelon_field('service_advantage_body', $service_id, get_the_content());
    $advantages = echelon_field('service_advantages', $service_id, [
        ['icon' => 'truck', 'title' => 'Photo & Video Ready Vehicles'],
        ['icon' => 'shield-check', 'title' => 'Dedicated Event Coordination'],
        ['icon' => 'pin', 'title' => 'Chauffeured Luxury Options'],
    ]);
    $fleet_heading = echelon_field('service_fleet_heading', $service_id, 'Popular ' . $title . ' Vehicles');
    $vehicle_ids = array_values(array_filter(array_map('absint', (array) echelon_field('service_vehicles', $service_id, []))));
    $cta_heading = echelon_field('service_final_cta_heading', $service_id, 'Ready To Redefine Your Drive?');
    $cta_description = echelon_field('service_final_cta_description', $service_id, 'Tell us the occasion, timing, and preferred vehicle. Our concierge will coordinate the route, presentation, and every detail.');
    $cta_image = echelon_field('service_final_cta_image', $service_id, null);
    $cta_url = add_query_arg('service', get_post_field('post_name', $service_id), home_url('/reservation/'));
    $hero_primary_cta = echelon_field('service_hero_primary_cta', $service_id, ['title' => __('Request Service Quote', 'echelon'), 'url' => $cta_url, 'target' => '']);
    $hero_secondary_cta = echelon_field('service_hero_secondary_cta', $service_id, ['title' => __('Browse Fleet', 'echelon'), 'url' => get_post_type_archive_link('fleet_vehicle'), 'target' => '']);
    $availability_heading = echelon_field('service_availability_heading', $service_id, __('Check The Fleet In Real Time', 'echelon'));
    $availability_accent = echelon_field('service_availability_accent', $service_id, __('Real Time', 'echelon'));
    $availability_benefits = echelon_field('service_availability_benefits', $service_id, [
        ['label' => __('Live date validation', 'echelon')],
        ['label' => __('Vehicle-specific availability', 'echelon')],
        ['label' => __('Fast reservation handoff', 'echelon')],
        ['label' => __('Concierge support when needed', 'echelon')],
    ]);
    $search_vehicles = get_posts([
        'post_type' => 'fleet_vehicle',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => ['menu_order' => 'ASC', 'date' => 'DESC'],
    ]);
    $final_primary_cta = echelon_field('service_final_cta_primary', $service_id, ['title' => __('Check Availability', 'echelon'), 'url' => $cta_url, 'target' => '']);
    $final_secondary_cta = echelon_field('service_final_cta_secondary', $service_id, ['title' => __('Contact Us', 'echelon'), 'url' => home_url('/contact/'), 'target' => '']);
    $ancestors = array_reverse(get_post_ancestors($service_id));
    $service_schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Service',
        'name'        => $title,
        'description' => wp_strip_all_tags($hero_description),
        'url'         => get_permalink($service_id),
        'provider'    => [
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
            'url'   => home_url('/'),
        ],
        'areaServed'  => ['New York', 'New Jersey', 'Connecticut'],
    ];
?>
<article class="service-detail">
    <script type="application/ld+json"><?php echo wp_json_encode($service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
    <section class="service-detail__hero">
        <img src="<?php echo esc_url($hero_url); ?>" alt="<?php echo esc_attr($title); ?>">
        <div class="service-detail__hero-scrim"></div>
        <div class="container service-detail__hero-content">
            <nav class="service-detail__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'echelon'); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'echelon'); ?></a><span>/</span>
                <a href="<?php echo esc_url(get_post_type_archive_link('service')); ?>"><?php esc_html_e('Services', 'echelon'); ?></a><span>/</span>
                <?php foreach ($ancestors as $ancestor_id) : ?><a href="<?php echo esc_url(get_permalink($ancestor_id)); ?>"><?php echo esc_html(get_the_title($ancestor_id)); ?></a><span>/</span><?php endforeach; ?>
                <span><?php echo esc_html($title); ?></span>
            </nav>
            <p class="eyebrow eyebrow--flanked"><?php echo esc_html($hero_eyebrow); ?></p>
            <h1><?php echo wp_kses(echelon_accent_heading($hero_heading, $hero_accent), ['span' => ['class' => true]]); ?></h1>
            <p class="service-detail__hero-description"><?php echo esc_html($hero_description); ?></p>
            <div class="service-detail__hero-actions">
                <a class="btn btn--primary" href="<?php echo esc_url($hero_primary_cta['url'] ?? $cta_url); ?>"<?php echo !empty($hero_primary_cta['target']) ? ' target="' . esc_attr($hero_primary_cta['target']) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html($hero_primary_cta['title'] ?? __('Request Service Quote', 'echelon')); ?><?php echelon_icon('arrow-right'); ?></a>
                <a class="btn btn--outline" href="<?php echo esc_url($hero_secondary_cta['url'] ?? get_post_type_archive_link('fleet_vehicle')); ?>"<?php echo !empty($hero_secondary_cta['target']) ? ' target="' . esc_attr($hero_secondary_cta['target']) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html($hero_secondary_cta['title'] ?? __('Browse Fleet', 'echelon')); ?></a>
            </div>
        </div>
    </section>

    <section class="section service-detail__advantage" data-reveal>
        <div class="container service-detail__advantage-grid">
            <div class="service-detail__advantage-copy">
                <p class="eyebrow"><?php echo esc_html(echelon_field('service_advantage_eyebrow', $service_id, __('Advantage', 'echelon'))); ?></p>
                <h2><?php echo esc_html($advantage_heading); ?></h2>
                <?php if ($advantage_body) : ?><div class="service-detail__richtext"><?php echo wp_kses_post(apply_filters('the_content', $advantage_body)); ?></div><?php endif; ?>
            </div>
            <div class="service-detail__advantage-list">
                <?php foreach ((array) $advantages as $advantage) : ?>
                    <div><?php echelon_icon($advantage['icon'] ?? 'check'); ?><h3><?php echo esc_html($advantage['title'] ?? ''); ?></h3></div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php
    $fleet_args = ['post_type' => 'fleet_vehicle', 'post_status' => 'publish', 'posts_per_page' => 3, 'no_found_rows' => true];
    if ($vehicle_ids) {
        $fleet_args['post__in'] = $vehicle_ids;
        $fleet_args['orderby'] = 'post__in';
    } else {
        $fleet_args['orderby'] = ['menu_order' => 'ASC', 'date' => 'DESC'];
    }
    $service_fleet = new WP_Query($fleet_args);
    if ($service_fleet->have_posts()) : ?>
        <section class="section service-detail__fleet" data-reveal>
            <div class="container">
                <p class="eyebrow"><?php echo esc_html(echelon_field('service_fleet_eyebrow', $service_id, __('Recommended Fleet', 'echelon'))); ?></p>
                <h2><?php echo esc_html($fleet_heading); ?></h2>
                <div class="service-detail__fleet-grid"><?php while ($service_fleet->have_posts()) : $service_fleet->the_post(); get_template_part('template-parts/fleet/card'); endwhile; ?></div>
            </div>
        </section>
    <?php endif; wp_reset_postdata(); ?>

    <section class="section service-detail__availability" data-reveal>
        <div class="container service-detail__availability-grid">
            <div>
                <p class="eyebrow"><?php echo esc_html(echelon_field('service_availability_eyebrow', $service_id, __('Availability Search', 'echelon'))); ?></p>
                <h2><?php echo wp_kses(echelon_accent_heading($availability_heading, $availability_accent), ['span' => ['class' => true]]); ?></h2>
                <p><?php echo esc_html(echelon_field('service_availability_description', $service_id, __('Choose your rental window and continue to the fleet with your dates already loaded.', 'echelon'))); ?></p>
                <ul><?php foreach ($availability_benefits as $benefit) : ?><li><?php echo esc_html($benefit['label'] ?? ''); ?></li><?php endforeach; ?></ul>
            </div>
            <form action="<?php echo esc_url(get_post_type_archive_link('fleet_vehicle')); ?>" method="get" data-availability-form>
                <input type="hidden" name="service" value="<?php echo esc_attr(get_post_field('post_name', $service_id)); ?>">
                <div class="service-detail__search" data-vehicle-combobox>
                    <label for="service-fleet-search"><span><?php echo esc_html(echelon_field('service_search_label', $service_id, __('Search the fleet', 'echelon'))); ?></span></label>
                    <input id="service-fleet-search" type="search" name="fleet_search" placeholder="<?php echo esc_attr(echelon_field('service_search_placeholder', $service_id, __('Search the fleet…', 'echelon'))); ?>" autocomplete="off" role="combobox" aria-autocomplete="list" aria-controls="service-fleet-search-results" aria-expanded="false" data-vehicle-search>
                    <div class="vehicle-search-results" id="service-fleet-search-results" role="listbox" aria-label="<?php esc_attr_e('Matching fleet vehicles', 'echelon'); ?>" data-vehicle-results hidden>
                        <div class="vehicle-search-results__items">
                            <?php foreach ($search_vehicles as $index => $vehicle) :
                                $vehicle_id = $vehicle->ID;
                                $brand = echelon_field('brand', $vehicle_id, '');
                                $gallery = echelon_vehicle_gallery($vehicle_id);
                                $cover = $gallery[0] ?? get_post_thumbnail_id($vehicle_id);
                                $search_text = trim($brand . ' ' . get_the_title($vehicle_id) . ' ' . echelon_field('tagline', $vehicle_id, ''));
                                ?>
                                <button type="button" class="vehicle-search-option" id="service-vehicle-option-<?php echo esc_attr($vehicle_id); ?>" role="option" aria-selected="false" data-vehicle-option data-vehicle-id="<?php echo esc_attr($vehicle_id); ?>" data-vehicle-label="<?php echo esc_attr(get_the_title($vehicle_id)); ?>" data-vehicle-search-text="<?php echo esc_attr(strtolower($search_text)); ?>"<?php echo $index >= 4 ? ' hidden' : ''; ?>>
                                    <span class="vehicle-search-option__image"><?php echelon_media($cover, 'thumbnail', '', 'bolt'); ?></span>
                                    <span class="vehicle-search-option__copy"><strong><?php echo esc_html($brand ?: get_the_title($vehicle_id)); ?></strong><?php if ($brand) : ?><small><?php echo esc_html(get_the_title($vehicle_id)); ?></small><?php endif; ?></span>
                                    <span class="vehicle-search-option__arrow" aria-hidden="true">→</span>
                                </button>
                            <?php endforeach; ?>
                            <p class="vehicle-search-results__empty" data-vehicle-empty hidden><?php esc_html_e('No vehicles match your search.', 'echelon'); ?></p>
                        </div>
                        <a class="vehicle-search-results__all" href="<?php echo esc_url(get_post_type_archive_link('fleet_vehicle')); ?>"><?php esc_html_e('Browse Full Fleet', 'echelon'); ?><span aria-hidden="true">→</span></a>
                    </div>
                </div>
                <div><label><span><?php echo esc_html(echelon_field('service_pickup_date_label', $service_id, __('Pick-up Date', 'echelon'))); ?></span><input type="text" name="pickup_date" placeholder="dd/mm/yyyy" data-datepicker autocomplete="off" required></label><label><span><?php echo esc_html(echelon_field('service_pickup_time_label', $service_id, __('Pick-up Time', 'echelon'))); ?></span><input type="time" name="pickup_time" required></label></div>
                <div><label><span><?php echo esc_html(echelon_field('service_return_date_label', $service_id, __('Drop-off Date', 'echelon'))); ?></span><input type="text" name="return_date" placeholder="dd/mm/yyyy" data-datepicker autocomplete="off" required></label><label><span><?php echo esc_html(echelon_field('service_return_time_label', $service_id, __('Return Time', 'echelon'))); ?></span><input type="time" name="return_time" required></label></div>
                <button class="btn btn--primary btn--block" type="submit"><?php echo esc_html(echelon_field('service_availability_button_label', $service_id, __('Check Availability', 'echelon'))); ?><?php echelon_icon('arrow-right'); ?></button>
            </form>
        </div>
    </section>

    <?php get_template_part('template-parts/home/faq'); ?>

    <section class="service-detail__cta">
        <?php if ($cta_image) : ?><?php echelon_media($cta_image, 'full', 'service-detail__cta-image'); ?><?php else : ?><img class="service-detail__cta-image" src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/reservation-cta.jpg')); ?>" alt="" loading="lazy" decoding="async"><?php endif; ?>
        <div class="service-detail__cta-scrim"></div>
        <div class="container service-detail__cta-content"><p class="eyebrow eyebrow--flanked"><?php echo esc_html(echelon_field('service_final_cta_eyebrow', $service_id, __('Reserve Today', 'echelon'))); ?></p><h2><?php echo esc_html($cta_heading); ?></h2><p><?php echo esc_html($cta_description); ?></p><div><a class="btn btn--outline" href="<?php echo esc_url($final_secondary_cta['url'] ?? home_url('/contact/')); ?>"<?php echo !empty($final_secondary_cta['target']) ? ' target="' . esc_attr($final_secondary_cta['target']) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html($final_secondary_cta['title'] ?? __('Contact Us', 'echelon')); ?></a><a class="btn btn--primary" href="<?php echo esc_url($final_primary_cta['url'] ?? $cta_url); ?>"<?php echo !empty($final_primary_cta['target']) ? ' target="' . esc_attr($final_primary_cta['target']) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html($final_primary_cta['title'] ?? __('Check Availability', 'echelon')); ?><?php echelon_icon('arrow-right'); ?></a></div></div>
    </section>
</article>
<?php endwhile; get_footer();
