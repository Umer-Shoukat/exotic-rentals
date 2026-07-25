<?php
/**
 * Contact inquiry validation and email delivery.
 */

if (!defined('ABSPATH')) {
    exit;
}

function echelon_contact_page_url($args = []) {
    $page = get_page_by_path('contact');
    $url = $page ? get_permalink($page) : home_url('/contact/');
    return add_query_arg($args, $url);
}

function echelon_contact_error($code) {
    wp_safe_redirect(echelon_contact_page_url(['contact_error' => sanitize_key($code)]) . '#contact-form');
    exit;
}

function echelon_handle_contact_inquiry() {
    if (
        !isset($_POST['echelon_contact_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['echelon_contact_nonce'])), 'echelon_submit_contact')
    ) {
        echelon_contact_error('session');
    }

    if (!empty($_POST['website'])) {
        echelon_contact_error('invalid');
    }

    $name = trim(sanitize_text_field(wp_unslash($_POST['contact_name'] ?? '')));
    $phone = trim(sanitize_text_field(wp_unslash($_POST['contact_phone'] ?? '')));
    $email = sanitize_email(wp_unslash($_POST['contact_email'] ?? ''));
    $requested_date = sanitize_text_field(wp_unslash($_POST['requested_date'] ?? ''));
    $pickup_location = trim(sanitize_text_field(wp_unslash($_POST['pickup_location'] ?? '')));
    $destination = trim(sanitize_text_field(wp_unslash($_POST['destination'] ?? '')));
    $vehicle_id = absint($_POST['requested_vehicle'] ?? 0);
    $service_type = sanitize_key(wp_unslash($_POST['service_type'] ?? ''));
    $passengers = absint($_POST['passengers'] ?? 0);
    $details = trim(sanitize_textarea_field(wp_unslash($_POST['additional_details'] ?? '')));

    $service_types = [
        'wedding'    => __('Wedding', 'echelon'),
        'corporate'  => __('Corporate', 'echelon'),
        'airport'    => __('Airport', 'echelon'),
        'prom'       => __('Prom', 'echelon'),
        'photoshoot' => __('Photoshoot', 'echelon'),
        'other'      => __('Other', 'echelon'),
    ];

    if (strlen($name) < 2 || strlen($name) > 100) {
        echelon_contact_error('name');
    }
    if (!is_email($email) || strlen($email) > 254) {
        echelon_contact_error('email');
    }
    $phone_digits = preg_replace('/\D+/', '', $phone);
    if (strlen($phone_digits) < 7 || strlen($phone_digits) > 15) {
        echelon_contact_error('phone');
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $requested_date, wp_timezone());
    if (!$date || $date->format('Y-m-d') !== $requested_date || $date < new DateTimeImmutable('today', wp_timezone())) {
        echelon_contact_error('date');
    }
    if (
        strlen($pickup_location) < 2 || strlen($pickup_location) > 180 ||
        strlen($destination) < 2 || strlen($destination) > 180
    ) {
        echelon_contact_error('route');
    }
    if (!isset($service_types[$service_type])) {
        echelon_contact_error('service');
    }
    if ($passengers < 1 || $passengers > 99) {
        echelon_contact_error('passengers');
    }
    if (strlen($details) > 3000) {
        echelon_contact_error('details');
    }
    if ($vehicle_id && (get_post_type($vehicle_id) !== 'fleet_vehicle' || get_post_status($vehicle_id) !== 'publish')) {
        echelon_contact_error('vehicle');
    }

    $token = sanitize_key(wp_unslash($_POST['submission_token'] ?? ''));
    if (!$token || get_transient('echelon_contact_' . $token)) {
        echelon_contact_error('duplicate');
    }
    set_transient('echelon_contact_' . $token, 1, 10 * MINUTE_IN_SECONDS);

    $vehicle = $vehicle_id ? get_the_title($vehicle_id) : __('No preference', 'echelon');
    $summary = implode("\n", [
        'Name: ' . $name,
        'Phone: ' . $phone,
        'Email: ' . $email,
        'Requested date: ' . $requested_date,
        'Pickup location: ' . $pickup_location,
        'Destination: ' . $destination,
        'Requested vehicle: ' . $vehicle,
        'Service type: ' . $service_types[$service_type],
        'Passengers: ' . $passengers,
        'Additional details: ' . ($details ?: 'None provided'),
    ]);

    $configured_recipient = sanitize_email(echelon_setting('contact_email', ''));
    $recipient = is_email($configured_recipient) ? $configured_recipient : sanitize_email(get_option('admin_email'));
    $subject = sprintf(__('New chauffeur inquiry — %s', 'echelon'), $name);
    $headers = ['Reply-To: ' . $name . ' <' . $email . '>'];
    $sent = wp_mail($recipient, $subject, "A new contact inquiry has been received.\n\n" . $summary, $headers);

    if (!$sent) {
        delete_transient('echelon_contact_' . $token);
        error_log('Echelon contact inquiry email failed for ' . $email);
        echelon_contact_error('send');
    }

    wp_mail(
        $email,
        __('We received your Echelon Motions inquiry', 'echelon'),
        "Thank you for contacting Echelon Motions.\n\nWe received the following trip request:\n\n{$summary}\n\nThis is an inquiry only. A concierge will follow up to confirm availability, chauffeur scheduling, and pricing."
    );

    wp_safe_redirect(echelon_contact_page_url(['contact' => 'received']) . '#contact-form');
    exit;
}
add_action('admin_post_nopriv_echelon_submit_contact', 'echelon_handle_contact_inquiry');
add_action('admin_post_echelon_submit_contact', 'echelon_handle_contact_inquiry');

