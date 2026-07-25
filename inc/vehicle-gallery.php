<?php
/**
 * Native WordPress gallery management for fleet vehicles.
 */

if (!defined('ABSPATH')) {
    exit;
}

const ECHELON_VEHICLE_GALLERY_META = '_echelon_vehicle_gallery';

/**
 * Return ordered attachment IDs for a vehicle gallery.
 *
 * Native gallery data takes precedence. Existing ACF Pro gallery values are
 * retained as a read-only fallback so upgrading does not remove old images.
 */
function echelon_vehicle_gallery($vehicle_id) {
    $vehicle_id = absint($vehicle_id);
    $has_native_gallery = metadata_exists('post', $vehicle_id, ECHELON_VEHICLE_GALLERY_META);
    $gallery = $has_native_gallery ? get_post_meta($vehicle_id, ECHELON_VEHICLE_GALLERY_META, true) : null;

    if (!$has_native_gallery && function_exists('get_field')) {
        $gallery = get_field('gallery', $vehicle_id);
    }

    $attachment_ids = [];
    foreach ((array) $gallery as $image) {
        $attachment_id = is_array($image) ? absint($image['ID'] ?? 0) : absint($image);
        if ($attachment_id && get_post_type($attachment_id) === 'attachment') {
            $attachment_ids[] = $attachment_id;
        }
    }

    return array_values(array_unique($attachment_ids));
}

function echelon_add_vehicle_gallery_meta_box() {
    add_meta_box(
        'echelon-vehicle-gallery',
        __('Vehicle Gallery', 'echelon'),
        'echelon_render_vehicle_gallery_meta_box',
        'fleet_vehicle',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_fleet_vehicle', 'echelon_add_vehicle_gallery_meta_box');

function echelon_render_vehicle_gallery_meta_box($post) {
    $gallery = echelon_vehicle_gallery($post->ID);
    wp_nonce_field('echelon_save_vehicle_gallery', 'echelon_vehicle_gallery_nonce');
    ?>
    <div class="echelon-vehicle-gallery" data-vehicle-gallery-admin>
        <p><?php esc_html_e('Upload multiple images, drag them to change their order, or remove images. The first image is used as the gallery cover.', 'echelon'); ?></p>
        <input type="hidden" name="echelon_vehicle_gallery_ids" value="<?php echo esc_attr(implode(',', $gallery)); ?>" data-gallery-ids>
        <ul class="echelon-vehicle-gallery__items" data-gallery-items>
            <?php foreach ($gallery as $attachment_id) : ?>
                <li class="echelon-vehicle-gallery__item" data-attachment-id="<?php echo esc_attr($attachment_id); ?>" draggable="true">
                    <?php echo wp_get_attachment_image($attachment_id, 'thumbnail'); ?>
                    <button type="button" class="button-link-delete" data-gallery-remove aria-label="<?php esc_attr_e('Remove image', 'echelon'); ?>">&times;</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="button button-primary" data-gallery-add><?php esc_html_e('Add Gallery Images', 'echelon'); ?></button>
    </div>
    <?php
}

function echelon_save_vehicle_gallery($post_id) {
    if (
        !isset($_POST['echelon_vehicle_gallery_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['echelon_vehicle_gallery_nonce'])), 'echelon_save_vehicle_gallery') ||
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
        !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    $raw_ids = sanitize_text_field(wp_unslash($_POST['echelon_vehicle_gallery_ids'] ?? ''));
    $attachment_ids = array_values(array_filter(array_unique(array_map('absint', explode(',', $raw_ids))), static function ($attachment_id) {
        return $attachment_id && get_post_type($attachment_id) === 'attachment';
    }));

    update_post_meta($post_id, ECHELON_VEHICLE_GALLERY_META, $attachment_ids);
}
add_action('save_post_fleet_vehicle', 'echelon_save_vehicle_gallery');

function echelon_enqueue_vehicle_gallery_admin_assets($hook_suffix) {
    $screen = get_current_screen();
    if (!in_array($hook_suffix, ['post.php', 'post-new.php'], true) || !$screen || $screen->post_type !== 'fleet_vehicle') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_style(
        'echelon-vehicle-gallery-admin',
        ECHELON_THEME_URI . '/assets/admin/vehicle-gallery.css',
        [],
        echelon_asset_version('/assets/admin/vehicle-gallery.css')
    );
    wp_enqueue_script(
        'echelon-vehicle-gallery-admin',
        ECHELON_THEME_URI . '/assets/admin/vehicle-gallery.js',
        ['media-editor'],
        echelon_asset_version('/assets/admin/vehicle-gallery.js'),
        true
    );
}
add_action('admin_enqueue_scripts', 'echelon_enqueue_vehicle_gallery_admin_assets');
