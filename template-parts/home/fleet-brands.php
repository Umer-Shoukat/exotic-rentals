<?php
/**
 * Home: "Luxury Fleet Brands" logo strip.
 */

$page_id = get_queried_object_id();
$heading = echelon_field('brands_heading', $page_id, 'Luxury Fleet Brands');
$desc    = echelon_field('brands_desc', $page_id, 'Exotic, performance, and executive vehicles selected
for weddings, corporate travel, and high-presence arrivals across New York City.');
$default_brands = [
    ['logo' => null, 'name' => 'Audi', 'fallback' => 'audi.png'],
    ['logo' => null, 'name' => 'Bentley', 'fallback' => 'bentley.png'],
    ['logo' => null, 'name' => 'BMW', 'fallback' => 'bmw.png'],
    ['logo' => null, 'name' => 'Cadillac', 'fallback' => 'cadillac.png'],
    ['logo' => null, 'name' => 'Chevrolet', 'fallback' => 'chevrolet.png'],
    ['logo' => null, 'name' => 'Corvette', 'fallback' => 'corvette.svg'],
    ['logo' => null, 'name' => 'Ferrari', 'fallback' => 'ferrari.png'],
];
$configured_brands = echelon_field('brands', $page_id, []);
$brands = [];
$configured_names = [];

foreach ((array) $configured_brands as $brand) {
    $logo = $brand['logo'] ?? null;
    $name = trim((string) ($brand['name'] ?? ''));
    if ($name === '' && is_array($logo)) {
        $name = trim((string) ($logo['alt'] ?? $logo['title'] ?? ''));
        $name = trim((string) preg_replace('/\s+logo$/i', '', $name));
    }
    if ($name === '' && !$logo) {
        continue;
    }
    $fallback = '';
    foreach ($default_brands as $default_brand) {
        if ($name !== '' && strcasecmp($name, $default_brand['name']) === 0) {
            $fallback = $default_brand['fallback'];
            $configured_names[strtolower($default_brand['name'])] = true;
            break;
        }
    }
    $brands[] = ['logo' => $logo, 'name' => $name ?: __('Vehicle brand', 'echelon'), 'fallback' => $fallback];
}

foreach ($default_brands as $default_brand) {
    if (empty($configured_names[strtolower($default_brand['name'])])) {
        $brands[] = $default_brand;
    }
}
?>
<section class="section fleet-brands" id="fleet-brands" data-reveal>
	<div class="container fleet-brands__header">
		<div>
			<p class="eyebrow"><?php esc_html_e('VEHICLES WE PROVIDE', 'echelon'); ?></p>
			<h2 class="section-heading__title"><?php echo esc_html($heading); ?></h2>
		</div>
		<p class="fleet-brands__desc"><?php echo esc_html($desc); ?></p>
	</div>

	<div class="fleet-brands__marquee">
		<div class="fleet-brands__strip">
			<?php for ($copy = 0; $copy < 2; $copy++) : ?>
				<div class="fleet-brands__group"<?php echo $copy ? ' aria-hidden="true"' : ''; ?>>
					<?php foreach ($brands as $brand) : ?>
						<div class="fleet-brands__logo">
							<?php
                            $use_fallback = empty($brand['logo']);
                            if (!$use_fallback && !empty($brand['fallback']) && is_array($brand['logo'])) {
                                $legacy_filename = strtolower((string) ($brand['logo']['filename'] ?? ''));
                                $legacy_width = (int) ($brand['logo']['width'] ?? 0);
                                $use_fallback = $legacy_width > 0 && $legacy_width < 400 && $legacy_filename === sanitize_title($brand['name']) . '-logo.png';
                            }
                            ?>
							<?php if (!$use_fallback && !empty($brand['logo'])) : ?>
								<?php echelon_media($brand['logo'], 'medium'); ?>
							<?php elseif (!empty($brand['fallback'])) : ?>
								<img class="fleet-brands__fallback" src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/brands/' . basename($brand['fallback']))); ?>" alt="<?php echo esc_attr($brand['name']); ?> logo" loading="lazy" decoding="async">
							<?php else : ?>
								<span><?php echo esc_html($brand['name']); ?></span>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endfor; ?>
		</div>
	</div>
</section>
