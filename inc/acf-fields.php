<?php
/**
 * Local ACF field group registration. Guarded so the theme degrades
 * gracefully (falls back to sensible defaults via echelon_field()) if the
 * ACF plugin isn't installed/active yet.
 */

if (!defined('ABSPATH')) {
    exit;
}

function echelon_register_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // ---- Fleet Vehicle -----------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_vehicle',
        'title'  => 'Vehicle Details',
        'fields' => [
            ['key' => 'field_v_gallery', 'name' => 'gallery', 'label' => 'Gallery', 'type' => 'gallery'],
            ['key' => 'field_v_brand', 'name' => 'brand', 'label' => 'Brand (short label)', 'type' => 'text', 'placeholder' => 'e.g. Mercedes'],
            ['key' => 'field_v_price', 'name' => 'price_per_day', 'label' => 'Price / Day', 'type' => 'number', 'prepend' => '$'],
            ['key' => 'field_v_hp', 'name' => 'horsepower', 'label' => 'Horsepower', 'type' => 'number', 'append' => 'HP'],
            ['key' => 'field_v_060', 'name' => 'zero_to_sixty', 'label' => '0–60', 'type' => 'text', 'placeholder' => 'e.g. 4.9s'],
            ['key' => 'field_v_seats', 'name' => 'seats', 'label' => 'Seats', 'type' => 'number'],
            ['key' => 'field_v_tagline', 'name' => 'tagline', 'label' => 'Tagline', 'type' => 'text'],
            ['key' => 'field_v_year', 'name' => 'year', 'label' => 'Model Year', 'type' => 'number'],
            ['key' => 'field_v_doors', 'name' => 'doors', 'label' => 'Doors', 'type' => 'number'],
            ['key' => 'field_v_deposit', 'name' => 'security_deposit', 'label' => 'Security Deposit', 'type' => 'number', 'prepend' => '$'],
            ['key' => 'field_v_miles', 'name' => 'included_miles', 'label' => 'Included Miles / Day', 'type' => 'number'],
            ['key' => 'field_v_transmission', 'name' => 'transmission', 'label' => 'Transmission', 'type' => 'text'],
            ['key' => 'field_v_engine', 'name' => 'engine', 'label' => 'Engine', 'type' => 'text'],
            ['key' => 'field_v_drivetrain', 'name' => 'drivetrain', 'label' => 'Drivetrain', 'type' => 'text'],
            ['key' => 'field_v_exterior', 'name' => 'exterior_color', 'label' => 'Exterior Color', 'type' => 'text'],
            ['key' => 'field_v_interior', 'name' => 'interior_color', 'label' => 'Interior Color', 'type' => 'text'],
            ['key' => 'field_v_fuel', 'name' => 'fuel_type', 'label' => 'Fuel Type', 'type' => 'text'],
            ['key' => 'field_v_featured', 'name' => 'featured', 'label' => 'Featured in Hero', 'type' => 'true_false', 'ui' => 1],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'fleet_vehicle']]],
    ]);

    // ---- Testimonial ----------------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_testimonial',
        'title'  => 'Testimonial Details',
        'fields' => [
            ['key' => 'field_t_quote', 'name' => 'quote', 'label' => 'Quote', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_t_name', 'name' => 'author_name', 'label' => 'Author Name', 'type' => 'text'],
            ['key' => 'field_t_title', 'name' => 'author_title', 'label' => 'Author Title', 'type' => 'text'],
            ['key' => 'field_t_photo', 'name' => 'author_photo', 'label' => 'Author Photo', 'type' => 'image', 'preview_size' => 'thumbnail'],
            ['key' => 'field_t_rating', 'name' => 'rating', 'label' => 'Rating', 'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'testimonial']]],
    ]);

    // ---- Occasion --------------------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_occasion',
        'title'  => 'Occasion Details',
        'fields' => [
            ['key' => 'field_o_desc', 'name' => 'description', 'label' => 'Short Description', 'type' => 'text'],
            ['key' => 'field_o_link', 'name' => 'link', 'label' => 'Link', 'type' => 'link'],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'occasion']]],
    ]);

    // ---- Location --------------------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_location',
        'title'  => 'Location Details',
        'fields' => [
            ['key' => 'field_l_desc', 'name' => 'description', 'label' => 'Delivery Description', 'type' => 'text'],
            ['key' => 'field_l_address', 'name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'rows' => 2, 'new_lines' => 'br'],
            ['key' => 'field_l_phone', 'name' => 'phone', 'label' => 'Phone Number', 'type' => 'text', 'instructions' => 'Include the country/area code, for example +1 (212) 555-0147.'],
            ['key' => 'field_l_latitude', 'name' => 'latitude', 'label' => 'Latitude', 'type' => 'number', 'min' => -85, 'max' => 85, 'step' => 0.000001, 'instructions' => 'Used for Google Maps positioning, for example 40.7831.'],
            ['key' => 'field_l_longitude', 'name' => 'longitude', 'label' => 'Longitude', 'type' => 'number', 'min' => -180, 'max' => 180, 'step' => 0.000001, 'instructions' => 'Used for Google Maps positioning, for example -73.9712.'],
            ['key' => 'field_l_pinx', 'name' => 'pin_x', 'label' => 'Map Pin X (%)', 'type' => 'number', 'min' => 0, 'max' => 100, 'step' => 0.1, 'instructions' => 'Horizontal position on the map: 0 is the far left and 100 is the far right.'],
            ['key' => 'field_l_piny', 'name' => 'pin_y', 'label' => 'Map Pin Y (%)', 'type' => 'number', 'min' => 0, 'max' => 100, 'step' => 0.1, 'instructions' => 'Vertical position on the map: 0 is the top and 100 is the bottom.'],
            ['key' => 'field_l_active', 'name' => 'is_active', 'label' => 'Active Zone', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'location']]],
    ]);

    // ---- Curated Instagram feed -----------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_instagram_item',
        'title'  => 'Instagram Item Details',
        'fields' => [
            ['key' => 'field_ig_item_url', 'name' => 'instagram_url', 'label' => 'Instagram Post URL', 'type' => 'url', 'instructions' => 'Optional. The homepage image links to this Instagram post.'],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'instagram_item']]],
    ]);

    // ---- Home page content ------------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_home',
        'title'  => 'Home Page Content',
        'fields' => [
            // Hero
            ['key' => 'field_h_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            ['key' => 'field_h_eyebrow', 'name' => 'hero_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'New York · New Jersey · Connecticut'],
            ['key' => 'field_h_heading', 'name' => 'hero_heading', 'label' => 'Heading', 'type' => 'textarea', 'rows' => 2, 'default_value' => "Tri-State's Premier\nExotic Rental Experience"],
            ['key' => 'field_h_subtext', 'name' => 'hero_subtext', 'label' => 'Subtext', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'From Lamborghinis and Rolls-Royces to executive chauffeur service, we deliver luxury vehicles across Tri-State Areas. Premium cars, white-glove service, and a seamless booking experience from start to finish.'],
            ['key' => 'field_h_bg', 'name' => 'hero_background', 'label' => 'Background Image', 'type' => 'image', 'preview_size' => 'large'],
            ['key' => 'field_h_cta1', 'name' => 'hero_cta_primary', 'label' => 'Primary CTA', 'type' => 'link', 'default_value' => ['title' => 'Browse Our Fleet', 'url' => '/fleet']],
            ['key' => 'field_h_cta2', 'name' => 'hero_cta_secondary', 'label' => 'Secondary CTA', 'type' => 'link', 'default_value' => ['title' => 'How It Works', 'url' => '#']],
            [
                'key' => 'field_h_badges', 'name' => 'hero_badges', 'label' => 'Trust Badges', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 4,
                'sub_fields' => [
                    ['key' => 'field_h_badge_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_h_badge_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],

            // Stats
            ['key' => 'field_h_tab_stats', 'label' => 'Stats', 'type' => 'tab'],
            ['key' => 'field_h_stats_heading', 'name' => 'stats_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Why Clients Choose Us Over Anyone Else'],
            ['key' => 'field_h_stats_desc', 'name' => 'stats_desc', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'We measure ourselves on the things that actually matter to a client: how quickly the car arrives, how spotless it is when you step in, and how easy it is to reach a real human at 3 a.m. The numbers below are the proof.'],
            ['key' => 'field_h_stats_cta', 'name' => 'stats_cta', 'label' => 'CTA', 'type' => 'link', 'default_value' => ['title' => 'Learn More', 'url' => '/about']],
            [
                'key' => 'field_h_stats', 'name' => 'stats', 'label' => 'Stat Cards', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 4,
                'sub_fields' => [
                    ['key' => 'field_h_stat_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_h_stat_value', 'name' => 'value', 'label' => 'Value', 'type' => 'text'],
                    ['key' => 'field_h_stat_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],

            // Fleet brands
            ['key' => 'field_h_tab_brands', 'label' => 'Fleet Brands', 'type' => 'tab'],
            ['key' => 'field_h_brands_heading', 'name' => 'brands_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Luxury Fleet Brands'],
            ['key' => 'field_h_brands_desc', 'name' => 'brands_desc', 'label' => 'Description', 'type' => 'text', 'default_value' => 'Exotic, performance, and executive vehicles curated for high-presence arrivals.'],
            [
                'key' => 'field_h_brands', 'name' => 'brands', 'label' => 'Brand Logos', 'type' => 'repeater', 'layout' => 'table', 'min' => 0,
                'sub_fields' => [
                    ['key' => 'field_h_brand_logo', 'name' => 'logo', 'label' => 'Logo', 'type' => 'image', 'preview_size' => 'thumbnail', 'return_format' => 'array'],
                    ['key' => 'field_h_brand_name', 'name' => 'name', 'label' => 'Name', 'type' => 'text'],
                ],
            ],

            // Concierge
            ['key' => 'field_h_tab_concierge', 'label' => 'Concierge', 'type' => 'tab'],
            ['key' => 'field_h_concierge_heading', 'name' => 'concierge_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Your Personal Exotic Car Concierge'],
            ['key' => 'field_h_concierge_desc', 'name' => 'concierge_desc', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'We measure ourselves on the things that actually matter to a client: how quickly the car arrives, how spotless it is when you step in, and how easy it is to reach a real human at 3 a.m.'],
            [
                'key' => 'field_h_checklist', 'name' => 'concierge_checklist', 'label' => 'Checklist', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 4,
                'sub_fields' => [
                    ['key' => 'field_h_checklist_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_h_checklist_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],
            ['key' => 'field_h_concierge_cta', 'name' => 'concierge_cta', 'label' => 'Button', 'type' => 'link', 'default_value' => ['title' => 'Start With Dates', 'url' => '/fleet']],
            ['key' => 'field_h_chat_title', 'name' => 'concierge_chat_title', 'label' => 'Chat Widget Title', 'type' => 'text', 'default_value' => 'Exotic Rental Concierge'],
            [
                'key' => 'field_h_chat_messages', 'name' => 'concierge_chat_messages', 'label' => 'Chat Messages', 'type' => 'repeater', 'layout' => 'table', 'min' => 0,
                'sub_fields' => [
                    ['key' => 'field_h_chat_sender', 'name' => 'sender', 'label' => 'Sender', 'type' => 'select', 'choices' => ['agent' => 'Agent', 'user' => 'User']],
                    ['key' => 'field_h_chat_message', 'name' => 'message', 'label' => 'Message', 'type' => 'text'],
                ],
            ],

            // Rental terms
            ['key' => 'field_h_tab_terms', 'label' => 'Rental Terms', 'type' => 'tab'],
            ['key' => 'field_h_terms_heading', 'name' => 'terms_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Rental Terms'],
            ['key' => 'field_h_terms_desc', 'name' => 'terms_desc', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2, 'default_value' => "We're here for you to help find the perfect car that matches your needs."],
            ['key' => 'field_h_agent_name', 'name' => 'agent_name', 'label' => 'Agent Name', 'type' => 'text', 'default_value' => 'Marcus D.'],
            ['key' => 'field_h_agent_title', 'name' => 'agent_title', 'label' => 'Agent Title', 'type' => 'text', 'default_value' => 'Founder, Atlas Ventures'],
            ['key' => 'field_h_agent_photo', 'name' => 'agent_photo', 'label' => 'Agent Photo', 'type' => 'image', 'preview_size' => 'thumbnail'],
            ['key' => 'field_h_agent_phone', 'name' => 'agent_phone', 'label' => 'Agent Phone', 'type' => 'text'],
            [
                'key' => 'field_h_terms', 'name' => 'terms', 'label' => 'Requirement Cards', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 4,
                'sub_fields' => [
                    ['key' => 'field_h_term_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_h_term_value', 'name' => 'value', 'label' => 'Value', 'type' => 'text'],
                    ['key' => 'field_h_term_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],

            // Built for drivers
            ['key' => 'field_h_tab_features', 'label' => 'Built For Drivers', 'type' => 'tab'],
            ['key' => 'field_h_features_heading', 'name' => 'features_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Built For Drivers Who Notice Everything.'],
            [
                'key' => 'field_h_features', 'name' => 'features', 'label' => 'Features', 'type' => 'repeater', 'layout' => 'block', 'min' => 0, 'max' => 4,
                'sub_fields' => [
                    ['key' => 'field_h_feature_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_h_feature_title', 'name' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'field_h_feature_desc', 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2],
                ],
            ],

            // Instagram
            ['key' => 'field_h_tab_instagram', 'label' => 'Instagram', 'type' => 'tab'],
            ['key' => 'field_h_ig_handle', 'name' => 'instagram_handle', 'label' => 'Handle', 'type' => 'text', 'default_value' => '@echelonmotions'],
            ['key' => 'field_h_ig_link', 'name' => 'instagram_link', 'label' => 'Profile Link', 'type' => 'url'],
            [
                'key' => 'field_h_ig_images', 'name' => 'instagram_images', 'label' => 'Images', 'type' => 'gallery', 'min' => 0,
            ],

            // Occasions / services
            ['key' => 'field_h_tab_occasions', 'label' => 'More Than a Rental', 'type' => 'tab'],
            ['key' => 'field_h_occasions_eyebrow', 'name' => 'occasions_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Services'],
            ['key' => 'field_h_occasions_heading', 'name' => 'occasions_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'More Than a Rental'],
            ['key' => 'field_h_occasions_desc', 'name' => 'occasions_desc', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Whatever the occasion — a first look at the aisle, a boardroom pull-up, or a shoot at golden hour — we tailor the car, the crew, and the moment.'],
            ['key' => 'field_h_occasions_cta', 'name' => 'occasions_cta', 'label' => 'Button', 'type' => 'link', 'default_value' => ['title' => 'View More', 'url' => '/chauffeur-services']],

            // Service cities
            ['key' => 'field_h_tab_cities', 'label' => 'Service Cities', 'type' => 'tab'],
            ['key' => 'field_h_cities_eyebrow', 'name' => 'cities_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Where We Deliver'],
            ['key' => 'field_h_cities_heading', 'name' => 'cities_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Serving The Cities That Demand More'],
            ['key' => 'field_h_cities_intro', 'name' => 'cities_intro', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'From coast to coast, our concierge team routes the closest vehicle to your pickup point. If your city isn’t listed, ask — extended delivery is available on request.'],
            ['key' => 'field_h_cities_map', 'name' => 'cities_map', 'label' => 'Map Image', 'type' => 'image', 'preview_size' => 'large', 'instructions' => 'Optional. Upload a clean map backdrop to display the dynamic location pins.'],
            ['key' => 'field_h_cities_cta', 'name' => 'cities_cta', 'label' => 'Map Link', 'type' => 'link', 'default_value' => ['title' => 'Active Service Zones', 'url' => '/locations']],

            // Journal
            ['key' => 'field_h_tab_journal', 'label' => 'Journal', 'type' => 'tab'],
            ['key' => 'field_h_journal_eyebrow', 'name' => 'journal_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'The Journal'],
            ['key' => 'field_h_journal_heading', 'name' => 'journal_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Latest From Exotic Rental'],
            ['key' => 'field_h_journal_desc', 'name' => 'journal_desc', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Fleet drops, driving guides, and the occasional look behind the garage door.'],
            ['key' => 'field_h_journal_cta', 'name' => 'journal_cta', 'label' => 'Button', 'type' => 'link', 'default_value' => ['title' => 'View All Articles', 'url' => '/blog']],

            // Testimonials
            ['key' => 'field_h_tab_testimonials', 'label' => 'Testimonials', 'type' => 'tab'],
            ['key' => 'field_h_testimonials_eyebrow', 'name' => 'testimonials_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Clients'],
            ['key' => 'field_h_testimonials_heading', 'name' => 'testimonials_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Trusted, Quietly.'],

            // FAQ
            ['key' => 'field_h_tab_faq', 'label' => 'FAQ', 'type' => 'tab'],
            ['key' => 'field_h_faq_eyebrow', 'name' => 'faq_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'FAQ'],
            ['key' => 'field_h_faq_heading', 'name' => 'faq_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Questions, Answered.'],
            ['key' => 'field_h_faq_desc', 'name' => 'faq_desc', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Still curious? Our concierge team is available 24/7 to walk you through any detail.'],

            // CTA
            ['key' => 'field_h_tab_cta', 'label' => 'CTA', 'type' => 'tab'],
            ['key' => 'field_h_cta_eyebrow', 'name' => 'cta_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Reserve Today'],
            ['key' => 'field_h_cta_heading', 'name' => 'cta_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Ready To Redefine Your Drive?'],
            ['key' => 'field_h_cta_desc', 'name' => 'cta_desc', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Whatever the occasion — a first look at the aisle, a boardroom pull-up, or a shoot at golden hour — we tailor the car, the crew, and the moment.'],
            ['key' => 'field_h_cta_image', 'name' => 'cta_image', 'label' => 'Image', 'type' => 'image', 'preview_size' => 'large'],
            ['key' => 'field_h_cta_button', 'name' => 'cta_button', 'label' => 'Button', 'type' => 'link', 'default_value' => ['title' => 'Start Your Reservation', 'url' => '/fleet']],
        ],
        'location' => [[['param' => 'page_template', 'operator' => '==', 'value' => 'front-page.php']]],
    ]);
}
add_action('acf/init', 'echelon_register_acf_fields');

/**
 * Icon choices for ACF select fields, matching assets/images/icons/*.svg.
 */
function echelon_icon_choices() {
    return [
        'bolt'         => 'Bolt (performance)',
        'gauge'        => 'Gauge (speed)',
        'seat'         => 'Seat',
        'shield-check' => 'Shield Check (insured)',
        'star'         => 'Star (rating)',
        'headset'      => 'Headset (support)',
        'clock'        => 'Clock',
        'calendar'     => 'Calendar',
        'id-card'      => 'ID Card (documents)',
        'truck'        => 'Truck (logistics)',
        'wrench'       => 'Wrench (custom builds)',
        'pin'          => 'Pin (location)',
        'check'        => 'Check',
    ];
}
