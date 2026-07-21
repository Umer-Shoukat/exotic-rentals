<?php
/**
 * Reservation submission, admin review, status transitions, and email delivery.
 */

if (!defined('ABSPATH')) {
    exit;
}

const ECHELON_RESERVATION_META = [
    'reference', 'status', 'vehicle_id', 'pickup_date', 'return_date',
    'pickup_location_id', 'return_location_id', 'estimated_mileage',
    'customer_name', 'customer_email', 'customer_phone', 'licence_number',
    'date_of_birth', 'occasion', 'estimated_total', 'submitted_at',
];

function echelon_reservation_page_url($args = []) {
    return add_query_arg($args, home_url('/reservation/'));
}

function echelon_parse_reservation_date($value) {
    $value = sanitize_text_field(wp_unslash($value));
    foreach (['Y-m-d', 'd/m/Y'] as $format) {
        $date = DateTimeImmutable::createFromFormat('!' . $format, $value, wp_timezone());
        if ($date && $date->format($format) === $value) {
            return $date;
        }
    }
    return false;
}

function echelon_reservation_error($code) {
    wp_safe_redirect(echelon_reservation_page_url(['reservation_error' => sanitize_key($code)]));
    exit;
}

function echelon_handle_reservation_submission() {
    if (!isset($_POST['echelon_reservation_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['echelon_reservation_nonce'])), 'echelon_submit_reservation')) {
        echelon_reservation_error('session');
    }

    if (!empty($_POST['company'])) {
        echelon_reservation_error('invalid');
    }

    $vehicle_id = isset($_POST['vehicle_id']) ? absint($_POST['vehicle_id']) : 0;
    $pickup_location_id = isset($_POST['pickup_location_id']) ? absint($_POST['pickup_location_id']) : 0;
    $return_location_id = isset($_POST['return_location_id']) ? absint($_POST['return_location_id']) : 0;
    $pickup_date = echelon_parse_reservation_date($_POST['pickup_date'] ?? '');
    $return_date = echelon_parse_reservation_date($_POST['return_date'] ?? '');
    $email = sanitize_email(wp_unslash($_POST['customer_email'] ?? ''));
    $name = trim(sanitize_text_field(wp_unslash($_POST['customer_name'] ?? '')));
    $phone = trim(sanitize_text_field(wp_unslash($_POST['customer_phone'] ?? '')));
    $licence = trim(sanitize_text_field(wp_unslash($_POST['licence_number'] ?? '')));
    $date_of_birth_raw = sanitize_text_field(wp_unslash($_POST['date_of_birth'] ?? ''));
    $date_of_birth = $date_of_birth_raw !== '' ? echelon_parse_reservation_date($date_of_birth_raw) : false;

    if (get_post_type($vehicle_id) !== 'fleet_vehicle' || get_post_status($vehicle_id) !== 'publish') {
        echelon_reservation_error('vehicle');
    }
    if (get_post_type($pickup_location_id) !== 'location' || get_post_status($pickup_location_id) !== 'publish' ||
        get_post_type($return_location_id) !== 'location' || get_post_status($return_location_id) !== 'publish') {
        echelon_reservation_error('location');
    }
    if (!$pickup_date || !$return_date || $return_date <= $pickup_date || $pickup_date < new DateTimeImmutable('today', wp_timezone())) {
        echelon_reservation_error('dates');
    }
    if (strlen($name) < 2 || strlen($name) > 100) echelon_reservation_error('name');
    if (!is_email($email) || strlen($email) > 254) echelon_reservation_error('email');
    $phone_digits = preg_replace('/\D+/', '', $phone);
    if (strlen($phone_digits) < 7 || strlen($phone_digits) > 15) echelon_reservation_error('phone');
    if (strlen($licence) < 4 || strlen($licence) > 50) echelon_reservation_error('licence');
    if ($date_of_birth_raw !== '') {
        $today = new DateTimeImmutable('today', wp_timezone());
        if (!$date_of_birth || $date_of_birth >= $today || $date_of_birth->diff($today)->y < 25) echelon_reservation_error('age');
    }
    if (empty($_POST['terms_accepted'])) echelon_reservation_error('terms');

    $token = sanitize_key(wp_unslash($_POST['submission_token'] ?? ''));
    if (!$token || get_transient('echelon_reservation_' . $token)) {
        echelon_reservation_error('duplicate');
    }
    set_transient('echelon_reservation_' . $token, 1, 10 * MINUTE_IN_SECONDS);

    $days = max(1, (int) $pickup_date->diff($return_date)->days);
    $daily_rate = (float) echelon_field('price_per_day', $vehicle_id, 0);
    $estimated_total = $daily_rate * $days;
    $reference = 'ER-' . gmdate('ymd') . '-' . strtoupper(wp_generate_password(6, false, false));
    $post_id = wp_insert_post([
        'post_type'   => 'rental_reservation',
        'post_status' => 'publish',
        'post_title'  => sprintf('%s — %s', $reference, $name),
    ], true);

    if (is_wp_error($post_id)) {
        delete_transient('echelon_reservation_' . $token);
        error_log('Echelon reservation insert failed: ' . $post_id->get_error_message());
        echelon_reservation_error('save');
    }

    $data = [
        'reference'            => $reference,
        'status'               => 'pending',
        'vehicle_id'           => $vehicle_id,
        'pickup_date'          => $pickup_date->format('Y-m-d'),
        'return_date'          => $return_date->format('Y-m-d'),
        'pickup_location_id'   => $pickup_location_id,
        'return_location_id'   => $return_location_id,
        'estimated_mileage'    => in_array(wp_unslash($_POST['estimated_mileage'] ?? '150'), ['150', '250', 'unlimited'], true) ? wp_unslash($_POST['estimated_mileage']) : '150',
        'customer_name'        => $name,
        'customer_email'       => $email,
        'customer_phone'       => $phone,
        'licence_number'       => $licence,
        'date_of_birth'        => $date_of_birth ? $date_of_birth->format('Y-m-d') : '',
        'occasion'             => sanitize_text_field(wp_unslash($_POST['occasion'] ?? '')),
        'estimated_total'      => $estimated_total,
        'submitted_at'         => current_time('mysql', true),
    ];
    foreach ($data as $key => $value) {
        update_post_meta($post_id, '_echelon_' . $key, $value);
    }

    echelon_send_reservation_received_emails($post_id);
    wp_safe_redirect(echelon_reservation_page_url(['reservation' => 'received', 'reference' => $reference]));
    exit;
}
add_action('admin_post_nopriv_echelon_submit_reservation', 'echelon_handle_reservation_submission');
add_action('admin_post_echelon_submit_reservation', 'echelon_handle_reservation_submission');

function echelon_reservation_email_summary($post_id) {
    $get = static fn($key) => get_post_meta($post_id, '_echelon_' . $key, true);
    $vehicle = get_the_title((int) $get('vehicle_id'));
    $pickup = get_the_title((int) $get('pickup_location_id'));
    $return = get_the_title((int) $get('return_location_id'));
    return implode("\n", [
        'Reference: ' . $get('reference'),
        'Vehicle: ' . $vehicle,
        'Dates: ' . $get('pickup_date') . ' to ' . $get('return_date'),
        'Pick-up: ' . $pickup,
        'Return: ' . $return,
        'Customer: ' . $get('customer_name'),
        'Email: ' . $get('customer_email'),
        'Phone: ' . $get('customer_phone'),
        'Estimated total: ' . echelon_price($get('estimated_total')),
    ]);
}

/**
 * Confirmed reservations are the source of truth for availability. Pending
 * requests may overlap because a concierge still needs to approve them.
 */
function echelon_reservation_has_confirmed_conflict($vehicle_id, $pickup_date, $return_date, $exclude_id = 0) {
    $conflicts = get_posts([
        'post_type' => 'rental_reservation', 'post_status' => 'publish',
        'posts_per_page' => 1, 'fields' => 'ids', 'post__not_in' => $exclude_id ? [$exclude_id] : [],
        'meta_query' => [
            'relation' => 'AND',
            ['key' => '_echelon_status', 'value' => 'confirmed'],
            ['key' => '_echelon_vehicle_id', 'value' => (int) $vehicle_id, 'type' => 'NUMERIC'],
            ['key' => '_echelon_pickup_date', 'value' => $return_date, 'compare' => '<', 'type' => 'DATE'],
            ['key' => '_echelon_return_date', 'value' => $pickup_date, 'compare' => '>', 'type' => 'DATE'],
        ],
    ]);
    return !empty($conflicts);
}

function echelon_send_reservation_received_emails($post_id) {
    $email = get_post_meta($post_id, '_echelon_customer_email', true);
    $reference = get_post_meta($post_id, '_echelon_reference', true);
    $summary = echelon_reservation_email_summary($post_id);
    $customer_message = "Thank you for your reservation request.\n\n{$summary}\n\nA concierge will contact you within one hour to confirm availability and finalize the details.";
    $admin_message = "A new reservation request has been received.\n\n{$summary}\n\nReview it in WordPress: " . admin_url('post.php?post=' . $post_id . '&action=edit');
    wp_mail($email, sprintf(__('Reservation request received — %s', 'echelon'), $reference), $customer_message);
    wp_mail(get_option('admin_email'), sprintf(__('New reservation request — %s', 'echelon'), $reference), $admin_message);
}

function echelon_add_reservation_meta_box() {
    add_meta_box('echelon-reservation-details', __('Reservation Details', 'echelon'), 'echelon_render_reservation_meta_box', 'rental_reservation', 'normal', 'high');
}
add_action('add_meta_boxes', 'echelon_add_reservation_meta_box');

function echelon_render_reservation_meta_box($post) {
    wp_nonce_field('echelon_save_reservation', 'echelon_reservation_admin_nonce');
    $status = get_post_meta($post->ID, '_echelon_status', true) ?: 'pending';
    if (get_transient('echelon_reservation_conflict_' . get_current_user_id() . '_' . $post->ID)) {
        delete_transient('echelon_reservation_conflict_' . get_current_user_id() . '_' . $post->ID);
        echo '<div class="notice notice-error inline"><p>' . esc_html__('This reservation remains pending because the vehicle already has a confirmed reservation for overlapping dates.', 'echelon') . '</p></div>';
    }
    echo '<p><label for="echelon-status"><strong>' . esc_html__('Status', 'echelon') . '</strong></label><br>';
    echo '<select id="echelon-status" name="echelon_status">';
    foreach (['pending' => __('Pending', 'echelon'), 'confirmed' => __('Confirmed', 'echelon'), 'cancelled' => __('Cancelled', 'echelon')] as $value => $label) {
        echo '<option value="' . esc_attr($value) . '"' . selected($status, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select></p><pre style="white-space:pre-wrap">' . esc_html(echelon_reservation_email_summary($post->ID)) . '</pre>';
}

function echelon_save_reservation_status($post_id) {
    if (!isset($_POST['echelon_reservation_admin_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['echelon_reservation_admin_nonce'])), 'echelon_save_reservation') ||
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id)) {
        return;
    }
    $new_status = sanitize_key(wp_unslash($_POST['echelon_status'] ?? 'pending'));
    if (!in_array($new_status, ['pending', 'confirmed', 'cancelled'], true)) {
        return;
    }
    $old_status = get_post_meta($post_id, '_echelon_status', true) ?: 'pending';
    if ($new_status === 'confirmed' && echelon_reservation_has_confirmed_conflict(
        get_post_meta($post_id, '_echelon_vehicle_id', true),
        get_post_meta($post_id, '_echelon_pickup_date', true),
        get_post_meta($post_id, '_echelon_return_date', true),
        $post_id
    )) {
        set_transient('echelon_reservation_conflict_' . get_current_user_id() . '_' . $post_id, 1, MINUTE_IN_SECONDS);
        $new_status = 'pending';
    }
    update_post_meta($post_id, '_echelon_status', $new_status);
    if ($new_status === 'confirmed' && $old_status !== 'confirmed' && !get_post_meta($post_id, '_echelon_confirmation_sent', true)) {
        $email = get_post_meta($post_id, '_echelon_customer_email', true);
        $reference = get_post_meta($post_id, '_echelon_reference', true);
        $message = "Your reservation has been confirmed.\n\n" . echelon_reservation_email_summary($post_id) . "\n\nOur concierge will contact you with the remaining delivery and payment details.";
        if (wp_mail($email, sprintf(__('Reservation confirmed — %s', 'echelon'), $reference), $message)) {
            update_post_meta($post_id, '_echelon_confirmation_sent', current_time('mysql', true));
        }
    }
}
add_action('save_post_rental_reservation', 'echelon_save_reservation_status');
