<?php
/** Single location service-area page. */
get_header();

while (have_posts()) : the_post();
    $location_id = get_the_ID();
    $location_name = get_the_title();
    $description = echelon_field('description', $location_id, get_the_excerpt() ?: sprintf(__('Concierge delivery, premium vehicles, and white-glove support throughout %s.', 'echelon'), $location_name));
    $hero_heading = echelon_field('hero_heading', $location_id, sprintf(__('Exotic Car Rentals Across %s', 'echelon'), $location_name));
    $intro_heading = echelon_field('intro_heading', $location_id, sprintf(__('Premium Rental Support For %s', 'echelon'), $location_name));
    $cta_heading = echelon_field('cta_heading', $location_id, sprintf(__('Reserve A Premium Vehicle For %s.', 'echelon'), $location_name));
    $hero_image = echelon_field('hero_image', $location_id, null);
    $areas = array_filter(array_map('trim', explode(',', (string) echelon_field('neighborhoods', $location_id, ''))));
    $intro_content = trim((string) get_the_content());
    $intro_content = $intro_content ? apply_filters('the_content', $intro_content) : wpautop($description);
    $heading_parts = preg_split('/\s+/', trim($hero_heading));
    $accent_start = max(1, count($heading_parts) - 3);
    $hero_primary = implode(' ', array_slice($heading_parts, 0, $accent_start));
    $hero_accent = implode(' ', array_slice($heading_parts, $accent_start));
    $nearby = get_posts([
        'post_type'      => 'location',
        'posts_per_page' => 4,
        'post__not_in'   => [$location_id],
        'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ]);
    $fallback_nearby = [
        ['title' => 'New York', 'description' => 'Manhattan arrivals, penthouse deliveries, five-borough coverage.', 'image' => 'new-york.jpg'],
        ['title' => 'New Jersey', 'description' => 'Hoboken skylines to Jersey Shore weekends — handled.', 'image' => 'new-jersey.jpg'],
        ['title' => 'Connecticut', 'description' => 'Gold Coast estates, Greenwich drives, Yale Bowl events.', 'image' => 'connecticut.jpg'],
        ['title' => 'Nassau County', 'description' => 'Manhasset, Great Neck, Old Westbury delivery.', 'image' => 'nassau-county.jpg'],
    ];
    ?>
    <article class="location-single">
        <section class="location-single-hero">
            <div class="location-single-hero__media" aria-hidden="true">
                <?php if ($hero_image) : echelon_media($hero_image, 'full', ''); else : ?><img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/locations/hero.jpg')); ?>" alt=""><?php endif; ?>
                <span></span>
            </div>
            <div class="container location-single-hero__content">
                <p class="eyebrow"><?php esc_html_e('Service Areas', 'echelon'); ?></p>
                <h1><?php echo esc_html($hero_primary); ?><br><span><?php echo esc_html($hero_accent); ?></span></h1>
                <p><?php echo esc_html($description); ?></p>
                <div class="location-single-hero__actions">
                    <a class="btn btn--primary" href="<?php echo esc_url(home_url('/fleet/')); ?>"><?php esc_html_e('Browse Fleet', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a>
                    <a class="btn btn--outline" href="<?php echo esc_url(home_url('/reservation/?location=' . get_post_field('post_name', $location_id))); ?>"><?php esc_html_e('Plan Delivery', 'echelon'); ?></a>
                </div>
            </div>
        </section>

        <section class="section location-support" data-reveal>
            <div class="container location-support__grid">
                <div class="location-support__intro">
                    <p class="eyebrow"><?php echo esc_html($location_name); ?></p>
                    <h2><?php echo esc_html($intro_heading); ?></h2>
                    <div class="location-support__copy"><?php echo wp_kses_post($intro_content); ?></div>
                    <?php if ($areas) : ?><ul class="location-support__areas"><?php foreach ($areas as $area) : ?><li><?php echo esc_html($area); ?></li><?php endforeach; ?></ul><?php endif; ?>
                </div>
                <div class="location-support__benefits">
                    <?php foreach ([
                        ['truck', __('Curated Fleet', 'echelon'), __('Detailed, fueled, delivered — every single time.', 'echelon')],
                        ['pin', __('Location Planning', 'echelon'), __('No hidden fees. What you see is what you pay.', 'echelon')],
                        ['calendar', __('Availability Flow', 'echelon'), __('Real people, always on, from booking to return.', 'echelon')],
                        ['shield-check', __('Approval Ready', 'echelon'), __('Comprehensive coverage included on every trip.', 'echelon')],
                    ] as $benefit) : ?>
                        <article><?php echelon_icon($benefit[0]); ?><h3><?php echo esc_html($benefit[1]); ?></h3><p><?php echo esc_html($benefit[2]); ?></p></article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section nearby-locations" data-reveal>
            <div class="container">
                <header class="section-heading"><p class="eyebrow"><?php esc_html_e('Nearby Service Areas', 'echelon'); ?></p><h2 class="section-heading__title"><?php esc_html_e('Explore More', 'echelon'); ?> <span class="accent"><?php esc_html_e('Locations', 'echelon'); ?></span></h2></header>
                <div class="nearby-locations__grid">
                    <?php if ($nearby) : foreach ($nearby as $nearby_location) : ?>
                        <a class="nearby-location-card" href="<?php echo esc_url(get_permalink($nearby_location)); ?>">
                            <?php if (has_post_thumbnail($nearby_location)) : echo get_the_post_thumbnail($nearby_location, 'large'); else : ?><img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/locations/new-york.jpg')); ?>" alt=""><?php endif; ?>
                            <span class="nearby-location-card__scrim"></span><span class="nearby-location-card__content"><strong><?php echo esc_html(get_the_title($nearby_location)); ?></strong><span><?php echo esc_html(echelon_field('description', $nearby_location->ID, get_the_excerpt($nearby_location))); ?></span></span>
                        </a>
                    <?php endforeach; else : foreach ($fallback_nearby as $item) : ?>
                        <a class="nearby-location-card" href="<?php echo esc_url(get_post_type_archive_link('location')); ?>"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/locations/' . $item['image'])); ?>" alt="<?php echo esc_attr($item['title']); ?>"><span class="nearby-location-card__scrim"></span><span class="nearby-location-card__content"><strong><?php echo esc_html($item['title']); ?></strong><span><?php echo esc_html($item['description']); ?></span></span></a>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </section>

        <?php get_template_part('template-parts/home/faq'); ?>

        <section class="location-reserve" data-reveal>
            <div class="location-reserve__media" aria-hidden="true"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/reservation-cta.jpg')); ?>" alt=""><span></span></div>
            <div class="container location-reserve__content">
                <p class="eyebrow eyebrow--flanked"><?php esc_html_e('Reserve Today', 'echelon'); ?></p>
                <h2><?php echo esc_html($cta_heading); ?></h2>
                <p><?php esc_html_e('Choose your vehicle, lock in the rental window, and let us coordinate the approval and handoff details.', 'echelon'); ?></p>
                <div><a class="btn btn--outline" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact Us', 'echelon'); ?></a><a class="btn btn--primary" href="<?php echo esc_url(home_url('/fleet/')); ?>"><?php esc_html_e('Browse Fleet', 'echelon'); ?><?php echelon_icon('arrow-right'); ?></a></div>
            </div>
        </section>
    </article>
    <?php
endwhile;
get_footer();
