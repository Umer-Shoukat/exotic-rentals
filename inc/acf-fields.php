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

    $contact_locations = [[['param' => 'page_template', 'operator' => '==', 'value' => 'page-contact.php']]];
    $contact_page = get_page_by_path('contact');
    if ($contact_page) {
        $contact_locations[] = [['param' => 'page', 'operator' => '==', 'value' => (string) $contact_page->ID]];
    }

    $about_locations = [
        [['param' => 'page_template', 'operator' => '==', 'value' => 'page-about.php']],
        [['param' => 'page_template', 'operator' => '==', 'value' => 'page-about-us.php']],
    ];
    foreach (['about', 'about-us'] as $about_slug) {
        $about_page = get_page_by_path($about_slug);
        if ($about_page) {
            $about_locations[] = [['param' => 'page', 'operator' => '==', 'value' => (string) $about_page->ID]];
        }
    }

    // ---- Fleet archive -------------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_fleet_archive',
        'title'  => 'Fleet Archive Content',
        'fields' => [
            ['key' => 'field_fa_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            ['key' => 'field_fa_breadcrumb', 'name' => 'fleet_breadcrumb_label', 'label' => 'Breadcrumb Label', 'type' => 'text', 'default_value' => 'Fleet'],
            ['key' => 'field_fa_eyebrow', 'name' => 'fleet_hero_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Our Fleet'],
            ['key' => 'field_fa_title', 'name' => 'fleet_hero_title', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Luxury & Exotic Vehicle Fleet'],
            ['key' => 'field_fa_accent', 'name' => 'fleet_hero_accent', 'label' => 'Accent Text', 'type' => 'text', 'default_value' => 'Fleet', 'instructions' => 'Text within the heading that should use the accent style.'],
            ['key' => 'field_fa_desc', 'name' => 'fleet_hero_description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Browse our live collection, filter by body style, make, or rate, then request your exact dates directly from each vehicle page.'],
            ['key' => 'field_fa_tab_stats', 'label' => 'Summary', 'type' => 'tab'],
            ['key' => 'field_fa_vehicles_label', 'name' => 'fleet_vehicles_label', 'label' => 'Vehicles Label', 'type' => 'text', 'default_value' => 'Vehicles'],
            ['key' => 'field_fa_makes_label', 'name' => 'fleet_makes_label', 'label' => 'Makes Label', 'type' => 'text', 'default_value' => 'Makes'],
            ['key' => 'field_fa_availability_value', 'name' => 'fleet_availability_value', 'label' => 'Availability Value', 'type' => 'text', 'default_value' => 'Live'],
            ['key' => 'field_fa_availability_label', 'name' => 'fleet_availability_label', 'label' => 'Availability Label', 'type' => 'text', 'default_value' => 'Synced Availability'],
            ['key' => 'field_fa_tab_filters', 'label' => 'Filters & Empty State', 'type' => 'tab'],
            ['key' => 'field_fa_refine_label', 'name' => 'fleet_refine_label', 'label' => 'Filter Eyebrow', 'type' => 'text', 'default_value' => 'Refine'],
            ['key' => 'field_fa_filters_label', 'name' => 'fleet_filters_label', 'label' => 'Filter Button Label', 'type' => 'text', 'default_value' => 'Filters'],
            ['key' => 'field_fa_clear_label', 'name' => 'fleet_clear_label', 'label' => 'Clear Button Label', 'type' => 'text', 'default_value' => 'Clear all'],
            ['key' => 'field_fa_apply_label', 'name' => 'fleet_apply_label', 'label' => 'Apply Button Label', 'type' => 'text', 'default_value' => 'Apply filters'],
            ['key' => 'field_fa_empty_heading', 'name' => 'fleet_empty_heading', 'label' => 'No Results Heading', 'type' => 'text', 'default_value' => 'No vehicles match those filters.'],
            ['key' => 'field_fa_empty_desc', 'name' => 'fleet_empty_description', 'label' => 'No Results Description', 'type' => 'text', 'default_value' => 'Clear a filter or try a broader search.'],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'fleet_archive']]],
    ]);

    // ---- Services ------------------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_service',
        'title'  => 'Service Details',
        'fields' => [
            ['key' => 'field_s_kicker', 'name' => 'service_kicker', 'label' => 'Card Kicker', 'type' => 'text', 'placeholder' => 'e.g. A composed, on-time arrival for your ceremony and reception.'],
            ['key' => 'field_s_menu_icon', 'name' => 'service_menu_icon', 'label' => 'Mega Menu Icon', 'type' => 'select', 'choices' => echelon_icon_choices(), 'default_value' => 'star'],
            ['key' => 'field_s_menu_desc', 'name' => 'service_menu_description', 'label' => 'Mega Menu Description', 'type' => 'text', 'instructions' => 'Short supporting line shown in the desktop Services menu. Defaults to the card kicker or excerpt.'],
            ['key' => 'field_s_cta', 'name' => 'service_cta_label', 'label' => 'CTA Label', 'type' => 'text', 'default_value' => 'Explore Service'],
            ['key' => 'field_s_featured', 'name' => 'service_featured', 'label' => 'Featured Service', 'type' => 'true_false', 'ui' => 1],
            ['key' => 'field_s_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            ['key' => 'field_s_hero_eyebrow', 'name' => 'service_hero_eyebrow', 'label' => 'Hero Eyebrow', 'type' => 'text', 'default_value' => 'Luxury Transportation'],
            ['key' => 'field_s_hero_heading', 'name' => 'service_hero_heading', 'label' => 'Hero Heading', 'type' => 'text', 'instructions' => 'Optional. Defaults to the service title.'],
            ['key' => 'field_s_hero_accent', 'name' => 'service_hero_accent', 'label' => 'Hero Accent Text', 'type' => 'text', 'instructions' => 'Text within the heading that should use the accent color. Defaults to the first word.'],
            ['key' => 'field_s_hero_desc', 'name' => 'service_hero_description', 'label' => 'Hero Description', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_s_hero_image', 'name' => 'service_hero_image', 'label' => 'Hero Image', 'type' => 'image', 'preview_size' => 'large', 'instructions' => 'Defaults to the service featured image.'],
            ['key' => 'field_s_hero_primary_cta', 'name' => 'service_hero_primary_cta', 'label' => 'Primary Button', 'type' => 'link', 'instructions' => 'Defaults to Request Service Quote and links to the reservation page.'],
            ['key' => 'field_s_hero_secondary_cta', 'name' => 'service_hero_secondary_cta', 'label' => 'Secondary Button', 'type' => 'link', 'instructions' => 'Defaults to Browse Fleet.'],
            ['key' => 'field_s_tab_advantage', 'label' => 'Advantage', 'type' => 'tab'],
            ['key' => 'field_s_adv_eyebrow', 'name' => 'service_advantage_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Advantage'],
            ['key' => 'field_s_adv_heading', 'name' => 'service_advantage_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Make Your Special Day Unforgettable'],
            ['key' => 'field_s_adv_body', 'name' => 'service_advantage_body', 'label' => 'Supporting Copy', 'type' => 'wysiwyg', 'tabs' => 'visual', 'toolbar' => 'basic', 'media_upload' => 0],
            [
                'key' => 'field_s_advantages', 'name' => 'service_advantages', 'label' => 'Advantage Cards', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 5,
                'sub_fields' => [
                    ['key' => 'field_s_adv_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_s_adv_title', 'name' => 'title', 'label' => 'Title', 'type' => 'text'],
                ],
            ],
            ['key' => 'field_s_tab_fleet', 'label' => 'Recommended Fleet', 'type' => 'tab'],
            ['key' => 'field_s_fleet_eyebrow', 'name' => 'service_fleet_eyebrow', 'label' => 'Fleet Eyebrow', 'type' => 'text', 'default_value' => 'Recommended Fleet'],
            ['key' => 'field_s_fleet_heading', 'name' => 'service_fleet_heading', 'label' => 'Fleet Heading', 'type' => 'text', 'default_value' => 'Popular Vehicles'],
            ['key' => 'field_s_fleet', 'name' => 'service_vehicles', 'label' => 'Recommended Vehicles', 'type' => 'relationship', 'post_type' => ['fleet_vehicle'], 'filters' => ['search', 'taxonomy'], 'max' => 3, 'return_format' => 'id'],
            ['key' => 'field_s_tab_availability', 'label' => 'Availability', 'type' => 'tab'],
            ['key' => 'field_s_availability_eyebrow', 'name' => 'service_availability_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Availability Search'],
            ['key' => 'field_s_availability_heading', 'name' => 'service_availability_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Check The Fleet In Real Time'],
            ['key' => 'field_s_availability_accent', 'name' => 'service_availability_accent', 'label' => 'Accent Text', 'type' => 'text', 'default_value' => 'Real Time'],
            ['key' => 'field_s_availability_desc', 'name' => 'service_availability_description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3],
            [
                'key' => 'field_s_availability_benefits', 'name' => 'service_availability_benefits', 'label' => 'Benefits', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 6,
                'sub_fields' => [
                    ['key' => 'field_s_availability_benefit', 'name' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],
            ['key' => 'field_s_search_label', 'name' => 'service_search_label', 'label' => 'Search Field Label', 'type' => 'text', 'default_value' => 'Search the fleet'],
            ['key' => 'field_s_search_placeholder', 'name' => 'service_search_placeholder', 'label' => 'Search Placeholder', 'type' => 'text', 'default_value' => 'Search the fleet…'],
            ['key' => 'field_s_pickup_date_label', 'name' => 'service_pickup_date_label', 'label' => 'Pick-up Date Label', 'type' => 'text', 'default_value' => 'Pick-up Date'],
            ['key' => 'field_s_pickup_time_label', 'name' => 'service_pickup_time_label', 'label' => 'Pick-up Time Label', 'type' => 'text', 'default_value' => 'Pick-up Time'],
            ['key' => 'field_s_return_date_label', 'name' => 'service_return_date_label', 'label' => 'Drop-off Date Label', 'type' => 'text', 'default_value' => 'Drop-off Date'],
            ['key' => 'field_s_return_time_label', 'name' => 'service_return_time_label', 'label' => 'Return Time Label', 'type' => 'text', 'default_value' => 'Return Time'],
            ['key' => 'field_s_availability_button', 'name' => 'service_availability_button_label', 'label' => 'Submit Button Label', 'type' => 'text', 'default_value' => 'Check Availability'],
            ['key' => 'field_s_tab_cta', 'label' => 'Final CTA', 'type' => 'tab'],
            ['key' => 'field_s_cta_eyebrow', 'name' => 'service_final_cta_eyebrow', 'label' => 'CTA Eyebrow', 'type' => 'text', 'default_value' => 'Reserve Today'],
            ['key' => 'field_s_cta_heading', 'name' => 'service_final_cta_heading', 'label' => 'CTA Heading', 'type' => 'text', 'default_value' => 'Ready To Redefine Your Drive?'],
            ['key' => 'field_s_cta_desc', 'name' => 'service_final_cta_description', 'label' => 'CTA Description', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_s_cta_image', 'name' => 'service_final_cta_image', 'label' => 'CTA Image', 'type' => 'image', 'preview_size' => 'large'],
            ['key' => 'field_s_cta_primary', 'name' => 'service_final_cta_primary', 'label' => 'Primary Button', 'type' => 'link', 'instructions' => 'Defaults to Check Availability and links to this service reservation.'],
            ['key' => 'field_s_cta_secondary', 'name' => 'service_final_cta_secondary', 'label' => 'Secondary Button', 'type' => 'link', 'instructions' => 'Defaults to Contact Us.'],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'service']]],
    ]);

    acf_add_local_field_group([
        'key'    => 'group_echelon_service_archive',
        'title'  => 'Services Archive Content',
        'fields' => [
            ['key' => 'field_sa_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            ['key' => 'field_sa_eyebrow', 'name' => 'services_hero_eyebrow', 'label' => 'Hero Eyebrow', 'type' => 'text', 'default_value' => 'Chauffeur & Transportation Services'],
            ['key' => 'field_sa_title', 'name' => 'services_hero_title', 'label' => 'Hero Title', 'type' => 'text', 'default_value' => 'Luxury Service For Every Occasion'],
            ['key' => 'field_sa_desc', 'name' => 'services_hero_description', 'label' => 'Hero Description', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_sa_image', 'name' => 'services_hero_image', 'label' => 'Hero Image', 'type' => 'image', 'preview_size' => 'large'],
            ['key' => 'field_sa_primary_cta', 'name' => 'services_hero_primary_cta', 'label' => 'Primary Button', 'type' => 'link', 'default_value' => ['title' => 'Explore Services', 'url' => '#service-list']],
            ['key' => 'field_sa_secondary_cta', 'name' => 'services_hero_secondary_cta', 'label' => 'Secondary Button', 'type' => 'link', 'default_value' => ['title' => 'Plan Your Experience', 'url' => '/contact/']],
            ['key' => 'field_sa_tab_proof', 'label' => 'Proof Bar', 'type' => 'tab'],
            [
                'key' => 'field_sa_proof', 'name' => 'services_proof_items', 'label' => 'Proof Items', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 4,
                'instructions' => 'Leave empty to use the current automatic service and vehicle totals.',
                'sub_fields' => [
                    ['key' => 'field_sa_proof_value', 'name' => 'value', 'label' => 'Value', 'type' => 'text'],
                    ['key' => 'field_sa_proof_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],
            ['key' => 'field_sa_tab_list', 'label' => 'Service Listing', 'type' => 'tab'],
            ['key' => 'field_sa_list_eyebrow', 'name' => 'services_list_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Our Premium Services'],
            ['key' => 'field_sa_list_title', 'name' => 'services_list_title', 'label' => 'Services Section Title', 'type' => 'text', 'default_value' => 'Luxury, Tailored To Every Occasion'],
            ['key' => 'field_sa_list_desc', 'name' => 'services_list_description', 'label' => 'Services Section Description', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_sa_tab_steps', 'label' => 'Process', 'type' => 'tab'],
            ['key' => 'field_sa_steps_eyebrow', 'name' => 'services_steps_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Simple From Start To Finish'],
            ['key' => 'field_sa_steps_heading', 'name' => 'services_steps_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Booking Your Chauffeur Is Simple'],
            [
                'key' => 'field_sa_steps', 'name' => 'services_steps', 'label' => 'Steps', 'type' => 'repeater', 'layout' => 'block', 'min' => 0, 'max' => 4,
                'sub_fields' => [
                    ['key' => 'field_sa_step_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_sa_step_title', 'name' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'field_sa_step_desc', 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2],
                ],
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'service_archive']]],
    ]);

    // ---- Contact page -------------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_contact_page',
        'title'  => 'Contact Page Content',
        'fields' => [
            ['key' => 'field_cp_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            ['key' => 'field_cp_hero_eyebrow', 'name' => 'contact_hero_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Contact Us'],
            ['key' => 'field_cp_hero_title', 'name' => 'contact_hero_title', 'label' => 'Title', 'type' => 'text', 'default_value' => "Let's Plan Your Drive"],
            ['key' => 'field_cp_hero_accent', 'name' => 'contact_hero_accent', 'label' => 'Accent Text', 'type' => 'text', 'default_value' => 'Drive', 'instructions' => 'This text is highlighted when it appears in the title.'],
            ['key' => 'field_cp_hero_desc', 'name' => 'contact_hero_description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3, 'default_value' => "Tell our concierge what
you have in mind. We'll help with the vehicle, chauffeur, timing, and every detail in between."],
            ['key' => 'field_cp_hero_image', 'name' => 'contact_hero_image', 'label' => 'Hero Image', 'type' => 'image', 'preview_size' => 'large'],
            ['key' => 'field_cp_tab_intro', 'label' => 'Contact Details', 'type' => 'tab'],
            ['key' => 'field_cp_intro_eyebrow', 'name' => 'contact_intro_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Our Concierge'],
            ['key' => 'field_cp_intro_heading', 'name' => 'contact_intro_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Start The Conversation'],
            ['key' => 'field_cp_intro_desc', 'name' => 'contact_intro_description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_cp_phone_label', 'name' => 'contact_phone_label', 'label' => 'Phone Label', 'type' => 'text', 'default_value' => 'Call Us'],
            ['key' => 'field_cp_email_label', 'name' => 'contact_email_label', 'label' => 'Email Label', 'type' => 'text', 'default_value' => 'Email Us'],
            ['key' => 'field_cp_address_label', 'name' => 'contact_address_label', 'label' => 'Service Area Label', 'type' => 'text', 'default_value' => 'Where We Serve'],
            ['key' => 'field_cp_tab_form', 'label' => 'Inquiry Form', 'type' => 'tab'],
            ['key' => 'field_cp_form_eyebrow', 'name' => 'contact_form_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Send An Inquiry'],
            ['key' => 'field_cp_form_heading', 'name' => 'contact_form_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Contact Form'],
            ['key' => 'field_cp_form_desc', 'name' => 'contact_form_description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Submit your trip details and our concierge team will confirm vehicle availability, chauffeur scheduling, and pricing.'],
            ['key' => 'field_cp_form_button', 'name' => 'contact_form_button_label', 'label' => 'Submit Button Label', 'type' => 'text', 'default_value' => 'Email Concierge'],
        ],
        'location' => $contact_locations,
    ]);

    // ---- About page ---------------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_about_page',
        'title'  => 'About Page Content',
        'fields' => [
            ['key' => 'field_ap_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            ['key' => 'field_ap_hero_eyebrow', 'name' => 'about_hero_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Driven By The Experience'],
            ['key' => 'field_ap_hero_title', 'name' => 'about_hero_title', 'label' => 'Title', 'type' => 'text', 'default_value' => 'More Than A Car.'],
            ['key' => 'field_ap_hero_accent', 'name' => 'about_hero_accent', 'label' => 'Accent Title', 'type' => 'text', 'default_value' => 'A Standard Of Service.'],
            ['key' => 'field_ap_hero_desc', 'name' => 'about_hero_description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3, 'default_value' => "Echelon Motions was built for clients who expect more than a pickup. Every reservation is handled by a professional chauffeur, in a vehicle prepared to the same standard, whether it's a wedding in Manhattan or a boardroom arrival in Midtown."],
            ['key' => 'field_ap_hero_image', 'name' => 'about_hero_image', 'label' => 'Hero Image', 'type' => 'image', 'preview_size' => 'large', 'instructions' => 'Defaults to the page featured image.'],
            ['key' => 'field_ap_tab_story', 'label' => 'Story', 'type' => 'tab'],
            ['key' => 'field_ap_story_image', 'name' => 'about_story_image', 'label' => 'Image', 'type' => 'image', 'preview_size' => 'large'],
            ['key' => 'field_ap_badge_value', 'name' => 'about_story_badge_value', 'label' => 'Badge Value', 'type' => 'text', 'default_value' => '24/7'],
            ['key' => 'field_ap_badge_label', 'name' => 'about_story_badge_label', 'label' => 'Badge Label', 'type' => 'text', 'default_value' => 'Personal Concierge'],
            ['key' => 'field_ap_story_eyebrow', 'name' => 'about_story_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Our Story'],
            ['key' => 'field_ap_story_heading', 'name' => 'about_story_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Luxury Should Feel Effortless'],
            ['key' => 'field_ap_story_accent', 'name' => 'about_story_accent', 'label' => 'Accent Text', 'type' => 'text', 'default_value' => 'Effortless'],
            ['key' => 'field_ap_story_content', 'name' => 'about_story_content', 'label' => 'Story Copy', 'type' => 'wysiwyg', 'tabs' => 'visual', 'toolbar' => 'basic', 'media_upload' => 0, 'default_value' => '<p>Echelon Motions started with a simple observation: most car services treat the vehicle as the product. We treat the arrival as the product - the vehicle is just one part of getting that right.</p>', 'instructions' => 'Use this field for the company story. Legacy page-editor placeholder content is no longer displayed.'],
            ['key' => 'field_ap_story_cta', 'name' => 'about_story_cta', 'label' => 'Button', 'type' => 'link', 'default_value' => ['title' => 'Explore Our Fleet', 'url' => '/fleet/']],
            ['key' => 'field_ap_tab_stats', 'label' => 'Statistics', 'type' => 'tab'],
            [
                'key' => 'field_ap_stats', 'name' => 'about_stats', 'label' => 'Statistics', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 4,
                'sub_fields' => [
                    ['key' => 'field_ap_stat_value', 'name' => 'value', 'label' => 'Value', 'type' => 'text'],
                    ['key' => 'field_ap_stat_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],
            ['key' => 'field_ap_tab_values', 'label' => 'Values', 'type' => 'tab'],
            ['key' => 'field_ap_values_eyebrow', 'name' => 'about_values_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'What Guides Us'],
            ['key' => 'field_ap_values_heading', 'name' => 'about_values_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'The Echelon Standard'],
            ['key' => 'field_ap_values_accent', 'name' => 'about_values_accent', 'label' => 'Accent Text', 'type' => 'text', 'default_value' => 'Standard'],
            ['key' => 'field_ap_values_desc', 'name' => 'about_values_description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3],
            [
                'key' => 'field_ap_values', 'name' => 'about_values', 'label' => 'Value Cards', 'type' => 'repeater', 'layout' => 'block', 'min' => 0, 'max' => 6,
                'sub_fields' => [
                    ['key' => 'field_ap_value_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_ap_value_title', 'name' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'field_ap_value_desc', 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2],
                ],
            ],
            ['key' => 'field_ap_tab_journey', 'label' => 'Journey', 'type' => 'tab'],
            ['key' => 'field_ap_journey_eyebrow', 'name' => 'about_journey_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'From Request To Road'],
            ['key' => 'field_ap_journey_heading', 'name' => 'about_journey_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Your Journey, Handled'],
            ['key' => 'field_ap_journey_accent', 'name' => 'about_journey_accent', 'label' => 'Accent Text', 'type' => 'text', 'default_value' => 'Handled'],
            ['key' => 'field_ap_journey_desc', 'name' => 'about_journey_description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3],
            [
                'key' => 'field_ap_steps', 'name' => 'about_journey_steps', 'label' => 'Journey Steps', 'type' => 'repeater', 'layout' => 'block', 'min' => 0, 'max' => 6,
                'sub_fields' => [
                    ['key' => 'field_ap_step_title', 'name' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'field_ap_step_desc', 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2],
                ],
            ],
        ],
        'location' => $about_locations,
    ]);

    // ---- Fleet Vehicle -----------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_vehicle',
        'title'  => 'Vehicle Details',
        'fields' => [
            ['key' => 'field_v_brand', 'name' => 'brand', 'label' => 'Brand (short label)', 'type' => 'text', 'placeholder' => 'e.g. Mercedes'],
            ['key' => 'field_v_price_hour', 'name' => 'price_per_hour', 'label' => 'Price / Hour', 'type' => 'number', 'prepend' => '$', 'min' => 0],
            ['key' => 'field_v_daily_rental', 'name' => 'daily_rental_price', 'label' => 'Price / Day', 'type' => 'number', 'prepend' => '$', 'min' => 0],
            ['key' => 'field_v_min_hours', 'name' => 'minimum_booking_hours', 'label' => 'Minimum Booking', 'type' => 'number', 'append' => 'hours', 'min' => 3, 'default_value' => 3],
            ['key' => 'field_v_rate_note', 'name' => 'hourly_rate_note', 'label' => 'Hourly Rate Note', 'type' => 'text', 'placeholder' => 'e.g. Stars & ambient light'],
            ['key' => 'field_v_tab_addons', 'label' => 'Add-ons & Policies', 'type' => 'tab'],
            [
                'key' => 'field_v_addons', 'name' => 'vehicle_addons', 'label' => 'Add-ons', 'type' => 'repeater', 'layout' => 'table',
                'instructions' => 'Optional. Empty add-ons are not shown on the website.',
                'sub_fields' => [
                    ['key' => 'field_v_addon_name', 'name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => 1],
                    ['key' => 'field_v_addon_price', 'name' => 'price', 'label' => 'Price', 'type' => 'number', 'prepend' => '$', 'min' => 0, 'required' => 1],
                ],
            ],
            ['key' => 'field_v_toll_policy', 'name' => 'toll_policy', 'label' => 'Toll Policy', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_v_travel_policy', 'name' => 'travel_policy', 'label' => 'Travel Policy', 'type' => 'textarea', 'rows' => 4],
            ['key' => 'field_v_daily_deposit', 'name' => 'daily_rental_security_deposit', 'label' => 'Daily Rental Security Deposit', 'type' => 'number', 'prepend' => '$', 'min' => 0],
            ['key' => 'field_v_tab_specs', 'label' => 'Vehicle Specifications', 'type' => 'tab'],
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

    // ---- Location --------------------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_echelon_location',
        'title'  => 'Location Details',
        'fields' => [
            ['key' => 'field_l_hero_image', 'name' => 'hero_image', 'label' => 'Hero Image', 'type' => 'image', 'preview_size' => 'large'],
            ['key' => 'field_l_hero_heading', 'name' => 'hero_heading', 'label' => 'Hero Heading', 'type' => 'text', 'instructions' => 'Optional. Defaults to “Exotic Car Rentals Across {Location}”.'],
            ['key' => 'field_l_desc', 'name' => 'description', 'label' => 'Delivery Description', 'type' => 'text'],
            ['key' => 'field_l_menu_region', 'name' => 'menu_region', 'label' => 'Mega Menu Region', 'type' => 'select', 'choices' => ['New York' => 'New York', 'New Jersey' => 'New Jersey', 'Connecticut' => 'Connecticut', 'Long Island' => 'Long Island'], 'allow_null' => 1],
            ['key' => 'field_l_menu_desc', 'name' => 'menu_description', 'label' => 'Mega Menu Description', 'type' => 'text', 'instructions' => 'Short supporting line shown in the desktop Locations menu. Defaults to the delivery description or excerpt.'],
            ['key' => 'field_l_intro_heading', 'name' => 'intro_heading', 'label' => 'Support Section Heading', 'type' => 'text', 'instructions' => 'Optional. Defaults to “Premium Rental Support For {Location}”.'],
            ['key' => 'field_l_neighborhoods', 'name' => 'neighborhoods', 'label' => 'Service Areas', 'type' => 'text', 'instructions' => 'Comma-separated labels shown on the location card, for example Manhattan, Brooklyn, Bronx.'],
            ['key' => 'field_l_cta_heading', 'name' => 'cta_heading', 'label' => 'Reservation CTA Heading', 'type' => 'text', 'instructions' => 'Optional. Defaults to “Reserve A Premium Vehicle For {Location}.”'],
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

    acf_add_local_field_group([
        'key'    => 'group_echelon_location_archive',
        'title'  => 'Locations Archive Content',
        'fields' => [
            ['key' => 'field_la_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            ['key' => 'field_la_hero_eyebrow', 'name' => 'locations_hero_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Service Areas'],
            ['key' => 'field_la_hero_title', 'name' => 'locations_hero_title', 'label' => 'Title', 'type' => 'text', 'default_value' => 'Exotic Car Rentals Across New Jersey & Connecticut'],
            ['key' => 'field_la_hero_accent', 'name' => 'locations_hero_accent', 'label' => 'Accent Text', 'type' => 'text', 'default_value' => 'New Jersey & Connecticut'],
            ['key' => 'field_la_hero_desc', 'name' => 'locations_hero_description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_la_hero_image', 'name' => 'locations_hero_image', 'label' => 'Hero Image', 'type' => 'image', 'preview_size' => 'large'],
            ['key' => 'field_la_primary_cta', 'name' => 'locations_hero_primary_cta', 'label' => 'Primary Button', 'type' => 'link', 'default_value' => ['title' => 'Book Your Vehicle', 'url' => '/fleet/']],
            ['key' => 'field_la_secondary_cta', 'name' => 'locations_hero_secondary_cta', 'label' => 'Secondary Button', 'type' => 'link', 'default_value' => ['title' => 'View Our Fleet', 'url' => '/fleet/']],
            [
                'key' => 'field_la_trust', 'name' => 'locations_hero_trust_items', 'label' => 'Trust Items', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 4,
                'sub_fields' => [
                    ['key' => 'field_la_trust_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_la_trust_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],
            ['key' => 'field_la_tab_proof', 'label' => 'Proof Bar', 'type' => 'tab'],
            [
                'key' => 'field_la_proof', 'name' => 'locations_proof_items', 'label' => 'Proof Items', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 4,
                'sub_fields' => [
                    ['key' => 'field_la_proof_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_la_proof_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],
            ['key' => 'field_la_tab_list', 'label' => 'Location Listing', 'type' => 'tab'],
            ['key' => 'field_la_list_eyebrow', 'name' => 'locations_list_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Featured Locations'],
            ['key' => 'field_la_list_heading', 'name' => 'locations_list_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'White-Glove Access Where Luxury Clients Move'],
            ['key' => 'field_la_list_accent', 'name' => 'locations_list_accent', 'label' => 'Accent Text', 'type' => 'text', 'default_value' => 'Luxury Clients Move'],
            ['key' => 'field_la_view_label', 'name' => 'locations_view_label', 'label' => 'View Button Label', 'type' => 'text', 'default_value' => 'View'],
            ['key' => 'field_la_book_label', 'name' => 'locations_book_label', 'label' => 'Book Button Label', 'type' => 'text', 'default_value' => 'Book Now'],
            ['key' => 'field_la_tab_benefits', 'label' => 'Benefits', 'type' => 'tab'],
            ['key' => 'field_la_benefits_eyebrow', 'name' => 'locations_benefits_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Why Rent With Us'],
            ['key' => 'field_la_benefits_heading', 'name' => 'locations_benefits_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Built For Clients Who Expect The Details Handled'],
            ['key' => 'field_la_benefits_accent', 'name' => 'locations_benefits_accent', 'label' => 'Accent Text', 'type' => 'text', 'default_value' => 'Details Handled'],
            [
                'key' => 'field_la_benefits', 'name' => 'locations_benefits', 'label' => 'Benefit Cards', 'type' => 'repeater', 'layout' => 'block', 'min' => 0, 'max' => 6,
                'sub_fields' => [
                    ['key' => 'field_la_benefit_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_la_benefit_title', 'name' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'field_la_benefit_desc', 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2],
                ],
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'location_archive']]],
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
            ['key' => 'field_h_bg_mobile', 'name' => 'hero_background_mobile', 'label' => 'Mobile Background Image', 'type' => 'image', 'preview_size' => 'medium_large', 'instructions' => 'Optional portrait crop used below 768px.'],
            ['key' => 'field_h_cta1', 'name' => 'hero_cta_primary', 'label' => 'Primary CTA', 'type' => 'link', 'default_value' => ['title' => 'Browse Our Fleet', 'url' => '/fleet']],
            ['key' => 'field_h_cta2', 'name' => 'hero_cta_secondary', 'label' => 'Secondary CTA', 'type' => 'link', 'default_value' => ['title' => 'How It Works', 'url' => '#how-it-works']],
            [
                'key' => 'field_h_badges', 'name' => 'hero_badges', 'label' => 'Trust Badges', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 4,
                'sub_fields' => [
                    ['key' => 'field_h_badge_icon', 'name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'choices' => echelon_icon_choices()],
                    ['key' => 'field_h_badge_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],

            // Stats
            ['key' => 'field_h_tab_stats', 'label' => 'Stats', 'type' => 'tab'],
            ['key' => 'field_h_stats_heading', 'name' => 'stats_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'WHY CLIENTS CHOOSE ECHELON MOTIONS'],
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
            ['key' => 'field_h_chat_title', 'name' => 'concierge_chat_title', 'label' => 'Chat Widget Title', 'type' => 'text', 'default_value' => 'Echelon Concierge'],
            [
                'key' => 'field_h_chat_messages', 'name' => 'concierge_chat_messages', 'label' => 'Chat Messages', 'type' => 'repeater', 'layout' => 'table', 'min' => 0,
                'sub_fields' => [
                    ['key' => 'field_h_chat_sender', 'name' => 'sender', 'label' => 'Sender', 'type' => 'select', 'choices' => ['agent' => 'Agent', 'user' => 'User']],
                    ['key' => 'field_h_chat_message', 'name' => 'message', 'label' => 'Message', 'type' => 'text'],
                ],
            ],

            // Service details
            ['key' => 'field_h_tab_terms', 'label' => 'Service Details', 'type' => 'tab'],
            ['key' => 'field_h_terms_heading', 'name' => 'terms_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Service Details'],
            ['key' => 'field_h_terms_desc', 'name' => 'terms_desc', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Our concierge team is available 24/7 to help confirm the right vehicle, chauffeur, and schedule for your plans.'],
            ['key' => 'field_h_agent_name', 'name' => 'agent_name', 'label' => 'Agent Name', 'type' => 'text', 'default_value' => 'Echelon Concierge'],
            ['key' => 'field_h_agent_title', 'name' => 'agent_title', 'label' => 'Agent Title', 'type' => 'text', 'default_value' => 'Available 24/7'],
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
            ['key' => 'field_h_occasions_heading', 'name' => 'occasions_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Chauffeur Services For Every Occasion'],
            ['key' => 'field_h_occasions_desc', 'name' => 'occasions_desc', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Whatever the occasion - a wedding, a boardroom arrival, or a shoot at golden hour - we tailor the vehicle, the chauffeur, and the schedule.'],
            ['key' => 'field_h_occasions_cta', 'name' => 'occasions_cta', 'label' => 'Button', 'type' => 'link', 'default_value' => ['title' => 'View More', 'url' => '/chauffeur-services']],

            // Service cities
            ['key' => 'field_h_tab_cities', 'label' => 'Service Cities', 'type' => 'tab'],
            ['key' => 'field_h_cities_eyebrow', 'name' => 'cities_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'Where We Serve'],
            ['key' => 'field_h_cities_heading', 'name' => 'cities_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Serving New York City and Long Island'],
            ['key' => 'field_h_cities_intro', 'name' => 'cities_intro', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Across Manhattan, Brooklyn, Queens, the Bronx, and Long Island, our concierge team coordinates the closest available vehicle and chauffeur. Extended service to New Jersey, Connecticut, and Pennsylvania is available by request.'],
            ['key' => 'field_h_cities_map', 'name' => 'cities_map', 'label' => 'Map Image', 'type' => 'image', 'preview_size' => 'large', 'instructions' => 'Optional. Upload a clean map backdrop to display the dynamic location pins.'],
            ['key' => 'field_h_cities_cta', 'name' => 'cities_cta', 'label' => 'Map Link', 'type' => 'link', 'default_value' => ['title' => 'View All Locations', 'url' => '/locations']],

            // Journal
            ['key' => 'field_h_tab_journal', 'label' => 'Journal', 'type' => 'tab'],
            ['key' => 'field_h_journal_eyebrow', 'name' => 'journal_eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'default_value' => 'The Journal'],
            ['key' => 'field_h_journal_heading', 'name' => 'journal_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Latest From Echelon Motions'],
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
            ['key' => 'field_h_cta_heading', 'name' => 'cta_heading', 'label' => 'Heading', 'type' => 'text', 'default_value' => 'Ready To Plan Your Ride?'],
            ['key' => 'field_h_cta_desc', 'name' => 'cta_desc', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Whatever the occasion, our concierge team will confirm the right vehicle, chauffeur, and schedule for you.'],
            ['key' => 'field_h_cta_image', 'name' => 'cta_image', 'label' => 'Image', 'type' => 'image', 'preview_size' => 'large'],
            ['key' => 'field_h_cta_button', 'name' => 'cta_button', 'label' => 'Button', 'type' => 'link', 'default_value' => ['title' => 'Start Your Reservation', 'url' => '/reservation']],
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
