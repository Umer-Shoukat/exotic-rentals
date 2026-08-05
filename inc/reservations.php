<?php
/**
 * Reservation submission, admin review, status transitions, and email delivery.
 */

if (!defined('ABSPATH')) {
    exit;
}

const ECHELON_RESERVATION_META = [
    'reference', 'status', 'vehicle_id', 'pickup_date', 'return_date', 'pickup_time', 'return_time', 'pickup_at', 'return_at',
    'pickup_location_id', 'return_location_id', 'estimated_mileage', 'trip_type', 'hours_required', 'rental_service', 'billing_type',
    'duration_hours', 'duration_days', 'unit_rate',
    'customer_name', 'customer_email', 'customer_phone', 'licence_number',
    'date_of_birth', 'occasion', 'licence_front_file', 'licence_back_file', 'insurance_document_file', 'estimated_total', 'submitted_at',
];

function echelon_reservation_service_types() {
    return [
        'chauffeur' => __('Chauffeur Driven', 'echelon'),
        'self_drive' => __('Self Driven', 'echelon'),
    ];
}

function echelon_vehicle_supports_service($vehicle_id, $service) {
    $meta_key = $service === 'self_drive' ? 'self_drive_available' : 'chauffeur_available';
    $rate_key = $service === 'self_drive' ? 'daily_rental_price' : 'price_per_hour';
    // Existing vehicles remain available until an administrator explicitly
    // disables a service, making deployment backward compatible.
    $enabled = !metadata_exists('post', $vehicle_id, $meta_key) || (bool) get_post_meta($vehicle_id, $meta_key, true);
    return $enabled && (float) echelon_field($rate_key, $vehicle_id, 0) > 0;
}

function echelon_reservation_setting_defaults() {
    return [
        'minimum_chauffeur_hours' => 3,
        'minimum_self_drive_hours' => 24,
        'minimum_driver_age' => 25,
        'maximum_upload_mb' => 10,
        'admin_notification_email' => get_option('admin_email'),
        'required_customer_name' => 1,
        'required_customer_email' => 1,
        'required_customer_phone' => 1,
        'required_trip_type' => 1,
        'required_occasion' => 1,
        'required_estimated_mileage' => 1,
        'required_licence_number' => 1,
        'required_date_of_birth' => 1,
        'required_licence_front' => 1,
        'required_licence_back' => 1,
        'required_insurance_document' => 1,
    ];
}

function echelon_reservation_settings() {
    return wp_parse_args((array) get_option('echelon_reservation_settings', []), echelon_reservation_setting_defaults());
}

function echelon_reservation_setting($key) {
    $settings = echelon_reservation_settings();
    return $settings[$key] ?? null;
}

function echelon_reservation_field_required($field) {
    return (bool) echelon_reservation_setting('required_' . $field);
}

function echelon_sanitize_reservation_settings($input) {
    $defaults = echelon_reservation_setting_defaults();
    $clean = [];
    foreach ($defaults as $key => $default) {
        if (strpos($key, 'required_') === 0) {
            $clean[$key] = empty($input[$key]) ? 0 : 1;
        }
    }
    if (empty($clean['required_customer_email']) && empty($clean['required_customer_phone'])) {
        $clean['required_customer_email'] = 1;
        add_settings_error('echelon_reservation_settings', 'contact_required', __('Email was kept required because every reservation needs at least one reliable contact method.', 'echelon'));
    }
    $clean['minimum_chauffeur_hours'] = max(3, min(24, absint($input['minimum_chauffeur_hours'] ?? 3)));
    $clean['minimum_self_drive_hours'] = max(24, absint($input['minimum_self_drive_hours'] ?? 24));
    $clean['minimum_driver_age'] = max(18, min(100, absint($input['minimum_driver_age'] ?? 25)));
    $clean['maximum_upload_mb'] = max(1, min(50, absint($input['maximum_upload_mb'] ?? 10)));
    $email = sanitize_email($input['admin_notification_email'] ?? '');
    $clean['admin_notification_email'] = is_email($email) ? $email : get_option('admin_email');
    return $clean;
}

function echelon_register_reservation_settings() {
    register_setting('echelon_reservation_settings', 'echelon_reservation_settings', [
        'type' => 'array',
        'sanitize_callback' => 'echelon_sanitize_reservation_settings',
        'default' => echelon_reservation_setting_defaults(),
    ]);
}
add_action('admin_init', 'echelon_register_reservation_settings');

function echelon_add_reservation_settings_page() {
    add_submenu_page(
        'edit.php?post_type=rental_reservation',
        __('Reservation Form Settings', 'echelon'),
        __('Form Settings', 'echelon'),
        'manage_options',
        'echelon-reservation-settings',
        'echelon_render_reservation_settings_page'
    );
}
add_action('admin_menu', 'echelon_add_reservation_settings_page');

function echelon_render_reservation_settings_page() {
    if (!current_user_can('manage_options')) return;
    $settings = echelon_reservation_settings();
    $required_fields = [
        'customer_name' => __('Full Name', 'echelon'), 'customer_email' => __('Email', 'echelon'),
        'customer_phone' => __('Phone', 'echelon'), 'trip_type' => __('Trip Type (chauffeur)', 'echelon'),
        'occasion' => __('Occasion', 'echelon'), 'estimated_mileage' => __('Estimated Mileage (self-drive)', 'echelon'),
        'licence_number' => __('Driver’s License Number (self-drive)', 'echelon'), 'date_of_birth' => __('Date of Birth (self-drive)', 'echelon'),
        'licence_front' => __('Driver’s License Front (self-drive)', 'echelon'), 'licence_back' => __('Driver’s License Back (self-drive)', 'echelon'),
        'insurance_document' => __('Insurance Document (self-drive)', 'echelon'),
    ];
    ?>
    <div class="wrap"><h1><?php esc_html_e('Reservation Form Settings', 'echelon'); ?></h1>
        <p><?php esc_html_e('These rules are shared by the form, server validation, reservation records, and notification emails. Vehicle, service, schedule, locations, and terms always remain required.', 'echelon'); ?></p>
        <form method="post" action="options.php">
            <?php settings_fields('echelon_reservation_settings'); ?>
            <h2><?php esc_html_e('Required Fields', 'echelon'); ?></h2>
            <table class="form-table" role="presentation"><tbody>
                <?php foreach ($required_fields as $key => $label) : ?><tr><th scope="row"><?php echo esc_html($label); ?></th><td><label><input type="checkbox" name="echelon_reservation_settings[required_<?php echo esc_attr($key); ?>]" value="1" <?php checked(!empty($settings['required_' . $key])); ?>> <?php esc_html_e('Required', 'echelon'); ?></label></td></tr><?php endforeach; ?>
            </tbody></table>
            <h2><?php esc_html_e('Business Rules', 'echelon'); ?></h2>
            <table class="form-table" role="presentation"><tbody>
                <tr><th scope="row"><label for="minimum-chauffeur-hours"><?php esc_html_e('Minimum chauffeur hours', 'echelon'); ?></label></th><td><input id="minimum-chauffeur-hours" class="small-text" type="number" min="3" max="24" name="echelon_reservation_settings[minimum_chauffeur_hours]" value="<?php echo esc_attr($settings['minimum_chauffeur_hours']); ?>"></td></tr>
                <tr><th scope="row"><label for="minimum-self-drive-hours"><?php esc_html_e('Minimum self-drive hours', 'echelon'); ?></label></th><td><input id="minimum-self-drive-hours" class="small-text" type="number" min="24" name="echelon_reservation_settings[minimum_self_drive_hours]" value="<?php echo esc_attr($settings['minimum_self_drive_hours']); ?>"></td></tr>
                <tr><th scope="row"><label for="minimum-driver-age"><?php esc_html_e('Minimum self-drive age', 'echelon'); ?></label></th><td><input id="minimum-driver-age" class="small-text" type="number" min="18" max="100" name="echelon_reservation_settings[minimum_driver_age]" value="<?php echo esc_attr($settings['minimum_driver_age']); ?>"></td></tr>
                <tr><th scope="row"><label for="maximum-upload-mb"><?php esc_html_e('Maximum document size (MB)', 'echelon'); ?></label></th><td><input id="maximum-upload-mb" class="small-text" type="number" min="1" max="50" name="echelon_reservation_settings[maximum_upload_mb]" value="<?php echo esc_attr($settings['maximum_upload_mb']); ?>"></td></tr>
                <tr><th scope="row"><label for="reservation-admin-email"><?php esc_html_e('Admin notification email', 'echelon'); ?></label></th><td><input id="reservation-admin-email" class="regular-text" type="email" name="echelon_reservation_settings[admin_notification_email]" value="<?php echo esc_attr($settings['admin_notification_email']); ?>"></td></tr>
            </tbody></table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function echelon_reservation_trip_types() {
    return [
        'airport_arrival' => __('Airport Arrival', 'echelon'), 'airport_departure' => __('Airport Departure', 'echelon'),
        'point_to_point' => __('Point-to-Point', 'echelon'), 'hourly' => __('Hourly / As Directed', 'echelon'),
        'birthday_party' => __('Birthday Party', 'echelon'), 'charter' => __('Charter', 'echelon'),
        'round_trip' => __('Round Trip', 'echelon'), 'other' => __('Other', 'echelon'),
    ];
}

function echelon_store_private_reservation_upload($field, $reference, $allowed_extensions) {
    $file = $_FILES[$field] ?? null;
    if (!$file || !isset($file['error'], $file['tmp_name'], $file['name']) || (int) $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) return new WP_Error('upload_missing');
    $maximum_bytes = (int) echelon_reservation_setting('maximum_upload_mb') * MB_IN_BYTES;
    if ((int) ($file['size'] ?? 0) <= 0 || (int) $file['size'] > $maximum_bytes) return new WP_Error('upload_size');
    $extension = strtolower(pathinfo(sanitize_file_name($file['name']), PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_extensions, true)) return new WP_Error('upload_extension');
    $checked = wp_check_filetype_and_ext($file['tmp_name'], $file['name']);
    if (empty($checked['type']) || empty($checked['ext']) || !in_array(strtolower($checked['ext']), $allowed_extensions, true)) return new WP_Error('upload_type');

    $base = apply_filters('echelon_private_reservation_upload_dir', dirname(ABSPATH) . '/echelon-private-reservations');
    $directory = trailingslashit($base) . sanitize_file_name($reference);
    if (!wp_mkdir_p($directory)) return new WP_Error('upload_directory');
    $filename = sanitize_key($field) . '-' . wp_generate_password(20, false, false) . '.' . $extension;
    $path = trailingslashit($directory) . $filename;
    if (!move_uploaded_file($file['tmp_name'], $path)) return new WP_Error('upload_move');
    @chmod($path, 0640);
    return ['path' => $path, 'name' => sanitize_file_name($file['name']), 'type' => $checked['type']];
}

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

function echelon_parse_reservation_datetime($date_value, $time_value) {
    $date_value = sanitize_text_field(wp_unslash($date_value));
    $time_value = sanitize_text_field(wp_unslash($time_value));
    foreach (['Y-m-d H:i', 'd/m/Y H:i'] as $format) {
        $value = $date_value . ' ' . $time_value;
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
    $pickup_date = echelon_parse_reservation_datetime($_POST['pickup_date'] ?? '', $_POST['pickup_time'] ?? '');
    $return_date = echelon_parse_reservation_datetime($_POST['return_date'] ?? '', $_POST['return_time'] ?? '');
    $email = sanitize_email(wp_unslash($_POST['customer_email'] ?? ''));
    $name = trim(sanitize_text_field(wp_unslash($_POST['customer_name'] ?? '')));
    $phone = trim(sanitize_text_field(wp_unslash($_POST['customer_phone'] ?? '')));
    $licence = trim(sanitize_text_field(wp_unslash($_POST['licence_number'] ?? '')));
    $date_of_birth_raw = sanitize_text_field(wp_unslash($_POST['date_of_birth'] ?? ''));
    $date_of_birth = $date_of_birth_raw !== '' ? echelon_parse_reservation_date($date_of_birth_raw) : false;
    $trip_type = sanitize_key(wp_unslash($_POST['trip_type'] ?? ''));
    $rental_service = sanitize_key(wp_unslash($_POST['rental_service'] ?? ''));
    $billing_type = sanitize_key(wp_unslash($_POST['billing_type'] ?? ''));
    $occasion = sanitize_text_field(wp_unslash($_POST['occasion'] ?? ''));
    $estimated_mileage = sanitize_text_field(wp_unslash($_POST['estimated_mileage'] ?? ''));

    if (get_post_type($vehicle_id) !== 'fleet_vehicle' || get_post_status($vehicle_id) !== 'publish') {
        echelon_reservation_error('vehicle');
    }
    $expected_billing_type = $rental_service === 'chauffeur' ? 'hourly' : ($rental_service === 'self_drive' ? 'daily' : '');
    if (!$expected_billing_type || $billing_type !== $expected_billing_type || !echelon_vehicle_supports_service($vehicle_id, $rental_service)) {
        echelon_reservation_error('service');
    }
    if (get_post_type($pickup_location_id) !== 'location' || get_post_status($pickup_location_id) !== 'publish' ||
        get_post_type($return_location_id) !== 'location' || get_post_status($return_location_id) !== 'publish') {
        echelon_reservation_error('location');
    }
    $minimum_hours = $rental_service === 'self_drive'
        ? (int) echelon_reservation_setting('minimum_self_drive_hours')
        : max((int) echelon_reservation_setting('minimum_chauffeur_hours'), (int) echelon_field('minimum_booking_hours', $vehicle_id, 3));
    $duration_seconds = $pickup_date && $return_date ? $return_date->getTimestamp() - $pickup_date->getTimestamp() : 0;
    if (!$pickup_date || !$return_date || $return_date <= $pickup_date || $pickup_date < new DateTimeImmutable('now', wp_timezone()) || $duration_seconds < $minimum_hours * HOUR_IN_SECONDS) {
        echelon_reservation_error('dates');
    }
    if ((echelon_reservation_field_required('customer_name') && strlen($name) < 2) || strlen($name) > 100 || ($name !== '' && strlen($name) < 2)) echelon_reservation_error('name');
    if ((echelon_reservation_field_required('customer_email') && !is_email($email)) || ($email !== '' && !is_email($email)) || strlen($email) > 254) echelon_reservation_error('email');
    $phone_digits = preg_replace('/\D+/', '', $phone);
    if ((echelon_reservation_field_required('customer_phone') && strlen($phone_digits) < 7) || strlen($phone_digits) > 15 || ($phone !== '' && strlen($phone_digits) < 7)) echelon_reservation_error('phone');
    if ($rental_service === 'chauffeur' && ((echelon_reservation_field_required('trip_type') && !isset(echelon_reservation_trip_types()[$trip_type])) || ($trip_type && !isset(echelon_reservation_trip_types()[$trip_type])))) echelon_reservation_error('trip');
    if ($rental_service === 'self_drive' && ((echelon_reservation_field_required('licence_number') && strlen($licence) < 4) || strlen($licence) > 50 || ($licence !== '' && strlen($licence) < 4))) echelon_reservation_error('licence');
    if (echelon_reservation_field_required('occasion') && $occasion === '') echelon_reservation_error('details');
    if ($rental_service === 'self_drive' && echelon_reservation_field_required('estimated_mileage') && !in_array($estimated_mileage, ['150', '250', 'unlimited'], true)) echelon_reservation_error('details');
    if ($rental_service === 'self_drive' && echelon_reservation_field_required('date_of_birth') && !$date_of_birth) echelon_reservation_error('age');
    if ($rental_service === 'self_drive' && $date_of_birth_raw !== '') {
        $today = new DateTimeImmutable('today', wp_timezone());
        if (!$date_of_birth || $date_of_birth >= $today || $date_of_birth->diff($today)->y < (int) echelon_reservation_setting('minimum_driver_age')) echelon_reservation_error('age');
    }
    if (empty($_POST['terms_accepted'])) echelon_reservation_error('terms');

    $hours = (int) ceil($duration_seconds / HOUR_IN_SECONDS);
    $days = (int) ceil($hours / 24);
    $unit_rate = (float) echelon_field($billing_type === 'daily' ? 'daily_rental_price' : 'price_per_hour', $vehicle_id, 0);
    if ($unit_rate <= 0) echelon_reservation_error('vehicle');
    $estimated_total = $unit_rate * ($billing_type === 'daily' ? $days : $hours);

    $token = sanitize_key(wp_unslash($_POST['submission_token'] ?? ''));
    if (!$token || get_transient('echelon_reservation_' . $token)) {
        echelon_reservation_error('duplicate');
    }
    set_transient('echelon_reservation_' . $token, 1, 10 * MINUTE_IN_SECONDS);

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

    $uploads = ['licence_front' => '', 'licence_back' => '', 'insurance_document' => ''];
    $document_fields = ['licence_front' => ['jpg', 'jpeg', 'png'], 'licence_back' => ['jpg', 'jpeg', 'png'], 'insurance_document' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']];
    foreach ($rental_service === 'self_drive' ? $document_fields : [] as $field => $extensions) {
        $has_upload = isset($_FILES[$field]['error']) && (int) $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE;
        if (!$has_upload && !echelon_reservation_field_required($field)) continue;
        $uploads[$field] = echelon_store_private_reservation_upload($field, $reference, $extensions);
        if (is_wp_error($uploads[$field])) {
            foreach ($uploads as $stored) if (is_array($stored) && !empty($stored['path'])) wp_delete_file($stored['path']);
            wp_delete_post($post_id, true);
            delete_transient('echelon_reservation_' . $token);
            error_log('Echelon reservation upload failed for ' . $field . ': ' . $uploads[$field]->get_error_code());
            echelon_reservation_error('documents');
        }
    }

    $data = [
        'reference'            => $reference,
        'status'               => 'pending',
        'vehicle_id'           => $vehicle_id,
        'pickup_date'          => $pickup_date->format('Y-m-d'),
        'return_date'          => $return_date->format('Y-m-d'),
        'pickup_time'          => $pickup_date->format('H:i'),
        'return_time'          => $return_date->format('H:i'),
        'pickup_at'            => $pickup_date->format('Y-m-d H:i:s'),
        'return_at'            => $return_date->format('Y-m-d H:i:s'),
        'pickup_location_id'   => $pickup_location_id,
        'return_location_id'   => $return_location_id,
        'estimated_mileage'    => $rental_service === 'self_drive' && in_array($estimated_mileage, ['150', '250', 'unlimited'], true) ? $estimated_mileage : '',
        'trip_type'            => $rental_service === 'chauffeur' ? $trip_type : '',
        'hours_required'       => $billing_type === 'hourly' ? $hours : '',
        'rental_service'       => $rental_service,
        'billing_type'         => $billing_type,
        'duration_hours'       => $hours,
        'duration_days'        => $days,
        'unit_rate'            => $unit_rate,
        'customer_name'        => $name,
        'customer_email'       => $email,
        'customer_phone'       => $phone,
        'licence_number'       => $licence,
        'date_of_birth'        => $rental_service === 'self_drive' && $date_of_birth ? $date_of_birth->format('Y-m-d') : '',
        'occasion'             => $occasion,
        'licence_front_file'   => $uploads['licence_front'],
        'licence_back_file'    => $uploads['licence_back'],
        'insurance_document_file' => $uploads['insurance_document'],
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
    $service = $get('rental_service');
    $lines = [
        'Reference: ' . $get('reference'),
        'Vehicle: ' . $vehicle,
        'Service: ' . (echelon_reservation_service_types()[$service] ?? ($service ?: 'Legacy hourly booking')),
        'Billing: ' . ucfirst($get('billing_type') ?: 'hourly'),
        $get('unit_rate') !== '' ? 'Rate: ' . echelon_price($get('unit_rate')) . ' per ' . (($get('billing_type') ?: 'hourly') === 'daily' ? 'day' : 'hour') : '',
        'Schedule: ' . $get('pickup_date') . ' ' . $get('pickup_time') . ' to ' . $get('return_date') . ' ' . $get('return_time'),
        'Pick-up: ' . $pickup,
        'Return: ' . $return,
        'Customer: ' . $get('customer_name'),
        'Email: ' . $get('customer_email'),
        'Phone: ' . $get('customer_phone'),
        'Occasion: ' . $get('occasion'),
        'Estimated total: ' . echelon_price($get('estimated_total')),
    ];
    $duration = $service === 'self_drive'
        ? ['Duration: ' . $get('duration_days') . ' day(s)', 'Estimated mileage: ' . $get('estimated_mileage')]
        : ['Trip type: ' . (echelon_reservation_trip_types()[$get('trip_type')] ?? $get('trip_type')), 'Duration: ' . ($get('duration_hours') ?: $get('hours_required')) . ' hour(s)'];
    array_splice($lines, 4, 0, $duration);
    return implode("\n", array_filter($lines, static fn($line) => substr($line, -2) !== ': '));
}

function echelon_reservation_email_details($post_id) {
    $get = static fn($key) => get_post_meta($post_id, '_echelon_' . $key, true);
    $service = $get('rental_service');
    $details = [
        __('Reference', 'echelon') => $get('reference'),
        __('Status', 'echelon') => ucfirst($get('status') ?: 'pending'),
        __('Vehicle', 'echelon') => get_the_title((int) $get('vehicle_id')),
        __('Service', 'echelon') => echelon_reservation_service_types()[$service] ?? ($service ?: __('Legacy hourly booking', 'echelon')),
        __('Billing', 'echelon') => ucfirst($get('billing_type') ?: 'hourly'),
        __('Rate', 'echelon') => $get('unit_rate') !== '' ? echelon_price($get('unit_rate')) . ' / ' . (($get('billing_type') ?: 'hourly') === 'daily' ? __('day', 'echelon') : __('hour', 'echelon')) : '',
        __('Schedule', 'echelon') => $get('pickup_date') . ' ' . $get('pickup_time') . ' — ' . $get('return_date') . ' ' . $get('return_time'),
        __('Pick-up', 'echelon') => get_the_title((int) $get('pickup_location_id')),
        __('Return', 'echelon') => get_the_title((int) $get('return_location_id')),
        __('Customer', 'echelon') => $get('customer_name'),
        __('Email', 'echelon') => $get('customer_email'),
        __('Phone', 'echelon') => $get('customer_phone'),
        __('Occasion', 'echelon') => $get('occasion'),
        __('Estimated Total', 'echelon') => echelon_price($get('estimated_total')),
    ];
    if ($service === 'self_drive') {
        $days = (int) $get('duration_days');
        $details[__('Duration', 'echelon')] = sprintf(_n('%s day', '%s days', $days, 'echelon'), $days);
        $details[__('Estimated Mileage', 'echelon')] = $get('estimated_mileage');
    } else {
        $hours = $get('duration_hours') ?: $get('hours_required');
        $details[__('Trip Type', 'echelon')] = echelon_reservation_trip_types()[$get('trip_type')] ?? $get('trip_type');
        $details[__('Duration', 'echelon')] = sprintf(_n('%s hour', '%s hours', (int) $hours, 'echelon'), $hours);
    }
    return array_filter($details, static fn($value) => $value !== '' && $value !== null);
}

function echelon_render_reservation_email($post_id, $status, $audience = 'customer') {
    $get = static fn($key) => get_post_meta($post_id, '_echelon_' . $key, true);
    $status_content = [
        'pending' => [__('Request received', 'echelon'), __('Your drive is in motion.', 'echelon'), __('Our concierge is reviewing availability and will contact you within one hour to finalize the details.', 'echelon')],
        'confirmed' => [__('Reservation confirmed', 'echelon'), __('Your vehicle is reserved.', 'echelon'), __('Your reservation is confirmed. Our concierge will contact you with the remaining delivery and payment details.', 'echelon')],
        'cancelled' => [__('Reservation cancelled', 'echelon'), __('Your request has been cancelled.', 'echelon'), __('This reservation is no longer active. If you did not request this change or would like to rebook, please contact our concierge.', 'echelon')],
    ];
    [$eyebrow, $heading, $message] = $status_content[$status] ?? $status_content['pending'];
    if ($audience === 'admin') {
        $eyebrow = $status === 'pending' ? __('New booking request', 'echelon') : __('Booking status updated', 'echelon');
        $heading = $status === 'pending' ? __('A new reservation needs review.', 'echelon') : sprintf(__('Reservation marked %s.', 'echelon'), $status);
        $message = sprintf(__('Review the complete request%s in WordPress. Customer: %s.', 'echelon'), $get('rental_service') === 'self_drive' ? __(' and verification documents', 'echelon') : '', $get('customer_name'));
    }
    $rows = '';
    foreach (echelon_reservation_email_details($post_id) as $label => $value) {
        $rows .= '<tr><td style="padding:12px 0;border-bottom:1px solid #2a2a2a;color:#8f8f8f;font-size:12px;letter-spacing:.05em;text-transform:uppercase;vertical-align:top">' . esc_html($label) . '</td><td style="padding:12px 0;border-bottom:1px solid #2a2a2a;color:#fff;font-size:14px;text-align:right;vertical-align:top">' . esc_html($value) . '</td></tr>';
    }
    $cta = $audience === 'admin' ? '<tr><td style="padding:28px 32px 0"><a href="' . esc_url(admin_url('post.php?post=' . $post_id . '&action=edit')) . '" style="display:inline-block;padding:14px 22px;background:#d40924;color:#fff;font-size:12px;font-weight:700;letter-spacing:.06em;text-decoration:none;text-transform:uppercase">' . esc_html__('Review reservation', 'echelon') . '</a></td></tr>' : '';
    return '<!doctype html><html><body style="margin:0;padding:0;background:#080808;color:#fff;font-family:Arial,sans-serif"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#080808"><tr><td align="center" style="padding:32px 12px"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#121212;border-top:3px solid #d40924"><tr><td style="padding:30px 32px 18px"><div style="color:#fff;font-size:22px;font-weight:800;letter-spacing:.08em;text-transform:uppercase">ECHELON <span style="color:#d40924">MOTIONS</span></div></td></tr><tr><td style="padding:22px 32px;background:#181818"><div style="margin-bottom:10px;color:#d40924;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase">' . esc_html($eyebrow) . '</div><h1 style="margin:0 0 14px;color:#fff;font-size:30px;line-height:1.15;text-transform:uppercase">' . esc_html($heading) . '</h1><p style="margin:0;color:#b8b8b8;font-size:15px;line-height:1.65">' . esc_html($message) . '</p></td></tr><tr><td style="padding:20px 32px 0"><table role="presentation" width="100%" cellspacing="0" cellpadding="0">' . $rows . '</table></td></tr>' . $cta . '<tr><td style="padding:30px 32px;color:#777;font-size:12px;line-height:1.6">' . esc_html__('Echelon Motions · Concierge-supported luxury transportation', 'echelon') . '<br><a href="' . esc_url(home_url('/')) . '" style="color:#d40924;text-decoration:none">' . esc_html(home_url('/')) . '</a></td></tr></table></td></tr></table></body></html>';
}

function echelon_send_reservation_email($to, $subject, $html) {
    return wp_mail($to, $subject, $html, ['Content-Type: text/html; charset=UTF-8']);
}

/**
 * Confirmed reservations are the source of truth for availability. Pending
 * requests may overlap because a concierge still needs to approve them.
 */
function echelon_reservation_has_confirmed_conflict($vehicle_id, $pickup_date, $return_date, $exclude_id = 0, $pickup_time = '00:00', $return_time = '23:59') {
    $pickup_at = $pickup_date . ' ' . ($pickup_time ?: '00:00');
    $return_at = $return_date . ' ' . ($return_time ?: '23:59');
    $conflicts = get_posts([
        'post_type' => 'rental_reservation', 'post_status' => 'publish',
        'posts_per_page' => 1, 'fields' => 'ids', 'post__not_in' => $exclude_id ? [$exclude_id] : [],
        'meta_query' => [
            'relation' => 'AND',
            ['key' => '_echelon_status', 'value' => 'confirmed'],
            ['key' => '_echelon_vehicle_id', 'value' => (int) $vehicle_id, 'type' => 'NUMERIC'],
            ['key' => '_echelon_pickup_at', 'value' => $return_at, 'compare' => '<', 'type' => 'DATETIME'],
            ['key' => '_echelon_return_at', 'value' => $pickup_at, 'compare' => '>', 'type' => 'DATETIME'],
        ],
    ]);
    return !empty($conflicts);
}

function echelon_send_reservation_received_emails($post_id) {
    $email = get_post_meta($post_id, '_echelon_customer_email', true);
    $reference = get_post_meta($post_id, '_echelon_reference', true);
    if (is_email($email)) echelon_send_reservation_email($email, sprintf(__('Reservation request received — %s', 'echelon'), $reference), echelon_render_reservation_email($post_id, 'pending', 'customer'));
    echelon_send_reservation_email(echelon_reservation_setting('admin_notification_email'), sprintf(__('New reservation request — %s', 'echelon'), $reference), echelon_render_reservation_email($post_id, 'pending', 'admin'));
}

function echelon_add_reservation_meta_box() {
    add_meta_box('echelon-reservation-details', __('Reservation Details', 'echelon'), 'echelon_render_reservation_meta_box', 'rental_reservation', 'normal', 'high');
}
add_action('add_meta_boxes', 'echelon_add_reservation_meta_box');

function echelon_reservation_admin_columns($columns) {
    return [
        'cb' => $columns['cb'] ?? '<input type="checkbox">',
        'title' => __('Reservation', 'echelon'),
        'reservation_service' => __('Service', 'echelon'),
        'reservation_vehicle' => __('Vehicle', 'echelon'),
        'reservation_schedule' => __('Schedule', 'echelon'),
        'reservation_total' => __('Estimated Total', 'echelon'),
        'reservation_status' => __('Status', 'echelon'),
        'date' => $columns['date'] ?? __('Date', 'echelon'),
    ];
}
add_filter('manage_rental_reservation_posts_columns', 'echelon_reservation_admin_columns');

function echelon_reservation_admin_column($column, $post_id) {
    $get = static fn($key) => get_post_meta($post_id, '_echelon_' . $key, true);
    if ($column === 'reservation_service') {
        $service = $get('rental_service');
        echo esc_html(echelon_reservation_service_types()[$service] ?? ($service ?: __('Legacy hourly', 'echelon')));
    } elseif ($column === 'reservation_vehicle') {
        echo esc_html(get_the_title((int) $get('vehicle_id')) ?: '—');
    } elseif ($column === 'reservation_schedule') {
        echo esc_html(trim($get('pickup_date') . ' ' . $get('pickup_time')) . ' → ' . trim($get('return_date') . ' ' . $get('return_time')));
    } elseif ($column === 'reservation_total') {
        echo esc_html(echelon_price($get('estimated_total')) ?: '—');
    } elseif ($column === 'reservation_status') {
        echo esc_html(ucfirst($get('status') ?: 'pending'));
    }
}
add_action('manage_rental_reservation_posts_custom_column', 'echelon_reservation_admin_column', 10, 2);

function echelon_render_reservation_meta_box($post) {
    wp_nonce_field('echelon_save_reservation', 'echelon_reservation_admin_nonce');
    $status = get_post_meta($post->ID, '_echelon_status', true) ?: 'pending';
    if (get_transient('echelon_reservation_conflict_' . get_current_user_id() . '_' . $post->ID)) {
        delete_transient('echelon_reservation_conflict_' . get_current_user_id() . '_' . $post->ID);
        echo '<div class="notice notice-error inline"><p>' . esc_html__('This reservation remains pending because the vehicle already has a confirmed reservation for overlapping dates.', 'echelon') . '</p></div>';
    }
    echo '<div style="display:grid;grid-template-columns:minmax(160px,220px) minmax(0,1fr);gap:24px;align-items:start">';
    echo '<div><label for="echelon-status" style="display:block;margin-bottom:8px;font-weight:600">' . esc_html__('Reservation Status', 'echelon') . '</label>';
    echo '<select id="echelon-status" name="echelon_status" style="width:100%;min-height:40px">';
    foreach (['pending' => __('Pending', 'echelon'), 'confirmed' => __('Confirmed', 'echelon'), 'cancelled' => __('Cancelled', 'echelon')] as $value => $label) {
        echo '<option value="' . esc_attr($value) . '"' . selected($status, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select><p style="margin:10px 0 0;color:#646970;font-size:12px;line-height:1.5">' . esc_html__('Changing the status sends a branded update email to the customer and administrator.', 'echelon') . '</p></div>';
    echo '<div style="overflow:hidden;border:1px solid #dcdcde;border-radius:4px;background:#fff"><table class="widefat striped" style="border:0;box-shadow:none"><tbody>';
    foreach (echelon_reservation_email_details($post->ID) as $label => $value) {
        echo '<tr><th scope="row" style="width:180px;padding:11px 14px;font-weight:600;color:#3c434a">' . esc_html($label) . '</th><td style="padding:11px 14px;color:#1d2327">' . esc_html($value ?: '—') . '</td></tr>';
    }
    $service = get_post_meta($post->ID, '_echelon_rental_service', true);
    if ($service === 'self_drive' || $service === '') {
        $licence_number = get_post_meta($post->ID, '_echelon_licence_number', true);
        $date_of_birth = get_post_meta($post->ID, '_echelon_date_of_birth', true);
        echo '<tr><th scope="row" style="padding:11px 14px;font-weight:600">' . esc_html__('License Number', 'echelon') . '</th><td style="padding:11px 14px">' . esc_html($licence_number ?: '—') . '</td></tr>';
        echo '<tr><th scope="row" style="padding:11px 14px;font-weight:600">' . esc_html__('Date of Birth', 'echelon') . '</th><td style="padding:11px 14px">' . esc_html($date_of_birth ?: '—') . '</td></tr>';
    }
    echo '</tbody></table></div></div>';
    if ($service === 'chauffeur') return;
    echo '<hr style="margin:24px 0;border:0;border-top:1px solid #dcdcde"><h3 style="margin:0 0 14px">' . esc_html__('Verification Documents', 'echelon') . '</h3>';
    echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px">';
    foreach (['licence_front_file' => __('Driver’s License — Front', 'echelon'), 'licence_back_file' => __('Driver’s License — Back', 'echelon'), 'insurance_document_file' => __('Insurance Document', 'echelon')] as $key => $label) {
        $file = get_post_meta($post->ID, '_echelon_' . $key, true);
        if (is_array($file) && !empty($file['path'])) {
            $download_url = wp_nonce_url(admin_url('admin-post.php?action=echelon_download_reservation_document&reservation_id=' . $post->ID . '&document=' . $key), 'echelon_download_reservation_document_' . $post->ID);
            $preview_url = add_query_arg('preview', '1', $download_url);
            $is_image = strpos($file['type'] ?? '', 'image/') === 0;
            echo '<article style="overflow:hidden;border:1px solid #dcdcde;border-radius:4px;background:#fff">';
            if ($is_image) {
                echo '<a href="' . esc_url($download_url) . '" style="display:block;height:180px;background:#f0f0f1" title="' . esc_attr(sprintf(__('Download %s', 'echelon'), $label)) . '"><img src="' . esc_url($preview_url) . '" alt="' . esc_attr($label) . '" style="display:block;width:100%;height:100%;object-fit:contain"></a>';
            } else {
                $extension = strtoupper(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
                echo '<div style="display:grid;place-items:center;height:180px;background:#f0f0f1;color:#50575e"><span style="padding:12px 18px;border:2px solid #8c8f94;border-radius:4px;font-size:24px;font-weight:700">' . esc_html($extension ?: __('FILE', 'echelon')) . '</span></div>';
            }
            echo '<div style="padding:13px 14px"><strong style="display:block;margin-bottom:5px">' . esc_html($label) . '</strong><small style="display:block;overflow:hidden;margin-bottom:10px;color:#646970;text-overflow:ellipsis;white-space:nowrap" title="' . esc_attr($file['name'] ?? '') . '">' . esc_html($file['name'] ?? '') . '</small><a class="button button-secondary" href="' . esc_url($download_url) . '">' . esc_html__('Download', 'echelon') . '</a></div></article>';
        }
    }
    echo '</div>';
}

function echelon_download_reservation_document() {
    $post_id = absint($_GET['reservation_id'] ?? 0);
    $key = sanitize_key(wp_unslash($_GET['document'] ?? ''));
    $allowed = ['licence_front_file', 'licence_back_file', 'insurance_document_file'];
    if (!$post_id || !current_user_can('edit_post', $post_id) || !in_array($key, $allowed, true)) wp_die(esc_html__('You are not allowed to access this document.', 'echelon'), '', ['response' => 403]);
    check_admin_referer('echelon_download_reservation_document_' . $post_id);
    $file = get_post_meta($post_id, '_echelon_' . $key, true);
    $path = is_array($file) ? ($file['path'] ?? '') : '';
    if (!$path || !is_file($path) || !is_readable($path)) wp_die(esc_html__('The requested document could not be found.', 'echelon'), '', ['response' => 404]);
    nocache_headers();
    header('Content-Type: ' . sanitize_mime_type($file['type'] ?? 'application/octet-stream'));
    $is_preview = !empty($_GET['preview']) && strpos($file['type'] ?? '', 'image/') === 0;
    header('Content-Disposition: ' . ($is_preview ? 'inline' : 'attachment') . '; filename="' . sanitize_file_name($file['name'] ?? basename($path)) . '"');
    header('X-Content-Type-Options: nosniff');
    header("Content-Security-Policy: default-src 'none'; img-src 'self'; sandbox");
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}
add_action('admin_post_echelon_download_reservation_document', 'echelon_download_reservation_document');

function echelon_delete_reservation_documents($post_id) {
    if (get_post_type($post_id) !== 'rental_reservation') return;
    foreach (['licence_front_file', 'licence_back_file', 'insurance_document_file'] as $key) {
        $file = get_post_meta($post_id, '_echelon_' . $key, true);
        if (is_array($file) && !empty($file['path']) && is_file($file['path'])) wp_delete_file($file['path']);
    }
}
add_action('before_delete_post', 'echelon_delete_reservation_documents');

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
        $post_id,
        get_post_meta($post_id, '_echelon_pickup_time', true),
        get_post_meta($post_id, '_echelon_return_time', true)
    )) {
        set_transient('echelon_reservation_conflict_' . get_current_user_id() . '_' . $post_id, 1, MINUTE_IN_SECONDS);
        $new_status = 'pending';
    }
    update_post_meta($post_id, '_echelon_status', $new_status);
    if ($new_status !== $old_status) {
        $email = get_post_meta($post_id, '_echelon_customer_email', true);
        $reference = get_post_meta($post_id, '_echelon_reference', true);
        $status_label = ucfirst($new_status);
        if (is_email($email)) echelon_send_reservation_email($email, sprintf(__('Reservation %1$s — %2$s', 'echelon'), $status_label, $reference), echelon_render_reservation_email($post_id, $new_status, 'customer'));
        echelon_send_reservation_email(echelon_reservation_setting('admin_notification_email'), sprintf(__('Reservation %1$s — %2$s', 'echelon'), $status_label, $reference), echelon_render_reservation_email($post_id, $new_status, 'admin'));
        update_post_meta($post_id, '_echelon_status_email_sent_at', current_time('mysql', true));
    }
}
add_action('save_post_rental_reservation', 'echelon_save_reservation_status');
