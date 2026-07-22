<?php
/** Single service detail: /services/{service-slug}/. */
get_header();

while (have_posts()) : the_post();
    $service_id = get_the_ID();
    $title = get_the_title();
    $hero_eyebrow = echelon_field('service_hero_eyebrow', $service_id, 'Luxury Transportation');
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
    $ancestors = array_reverse(get_post_ancestors($service_id));
    $title_words = preg_split('/\s+/', trim($title), 2);
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
            <h1><span><?php echo esc_html($title_words[0] ?? $title); ?></span><?php echo isset($title_words[1]) ? ' ' . esc_html($title_words[1]) : ''; ?></h1>
            <p class="service-detail__hero-description"><?php echo esc_html($hero_description); ?></p>
            <div class="service-detail__hero-actions">
                <a class="btn btn--primary" href="<?php echo esc_url($cta_url); ?>"><?php esc_html_e('Request Service Quote', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a>
                <a class="btn btn--outline" href="<?php echo esc_url(get_post_type_archive_link('fleet_vehicle')); ?>"><?php esc_html_e('Browse Fleet', 'echelon'); ?></a>
            </div>
        </div>
    </section>

    <section class="section service-detail__advantage" data-reveal>
        <div class="container service-detail__advantage-grid">
            <div class="service-detail__advantage-copy">
                <p class="eyebrow"><?php esc_html_e('Advantage', 'echelon'); ?></p>
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
                <p class="eyebrow"><?php esc_html_e('Recommended Fleet', 'echelon'); ?></p>
                <h2><?php echo esc_html($fleet_heading); ?></h2>
                <div class="service-detail__fleet-grid"><?php while ($service_fleet->have_posts()) : $service_fleet->the_post(); get_template_part('template-parts/fleet/card'); endwhile; ?></div>
            </div>
        </section>
    <?php endif; wp_reset_postdata(); ?>

    <section class="section service-detail__availability" data-reveal>
        <div class="container service-detail__availability-grid">
            <div>
                <p class="eyebrow"><?php esc_html_e('Availability Search', 'echelon'); ?></p>
                <h2><?php esc_html_e('Check The Fleet In', 'echelon'); ?> <span><?php esc_html_e('Real Time', 'echelon'); ?></span></h2>
                <p><?php esc_html_e('Choose your rental window and continue to the fleet with your dates already loaded.', 'echelon'); ?></p>
                <ul><li><?php esc_html_e('Live date validation', 'echelon'); ?></li><li><?php esc_html_e('Vehicle-specific availability', 'echelon'); ?></li><li><?php esc_html_e('Fast reservation handoff', 'echelon'); ?></li><li><?php esc_html_e('Concierge support when needed', 'echelon'); ?></li></ul>
            </div>
            <form action="<?php echo esc_url(get_post_type_archive_link('fleet_vehicle')); ?>" method="get">
                <input type="hidden" name="service" value="<?php echo esc_attr(get_post_field('post_name', $service_id)); ?>">
                <label class="service-detail__search"><span><?php esc_html_e('Search For A Car', 'echelon'); ?></span><input type="search" name="fleet_search" placeholder="<?php esc_attr_e('Search for a car…', 'echelon'); ?>"></label>
                <div><label><span><?php esc_html_e('Pick-up Date', 'echelon'); ?></span><input type="text" name="pickup_date" placeholder="dd/mm/yyyy" data-datepicker autocomplete="off"></label><label><span><?php esc_html_e('Pick-up Time', 'echelon'); ?></span><input type="time" name="pickup_time"></label></div>
                <div><label><span><?php esc_html_e('Return Date', 'echelon'); ?></span><input type="text" name="return_date" placeholder="dd/mm/yyyy" data-datepicker autocomplete="off"></label><label><span><?php esc_html_e('Return Time', 'echelon'); ?></span><input type="time" name="return_time"></label></div>
                <button class="btn btn--primary btn--block" type="submit"><?php esc_html_e('Check Availability', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></button>
            </form>
        </div>
    </section>

    <?php get_template_part('template-parts/home/faq'); ?>

    <section class="service-detail__cta">
        <?php if ($cta_image) : ?><?php echelon_media($cta_image, 'full', 'service-detail__cta-image'); ?><?php else : ?><img class="service-detail__cta-image" src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/reservation-cta.jpg')); ?>" alt="" loading="lazy" decoding="async"><?php endif; ?>
        <div class="service-detail__cta-scrim"></div>
        <div class="container service-detail__cta-content"><p class="eyebrow eyebrow--flanked"><?php esc_html_e('Reserve Today', 'echelon'); ?></p><h2><?php echo esc_html($cta_heading); ?></h2><p><?php echo esc_html($cta_description); ?></p><div><a class="btn btn--outline" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact Us', 'echelon'); ?></a><a class="btn btn--primary" href="<?php echo esc_url($cta_url); ?>"><?php esc_html_e('Check Availability', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a></div></div>
    </section>
</article>
<?php endwhile; get_footer();
