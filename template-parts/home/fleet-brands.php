<?php
/**
 * Home: "Luxury Fleet Brands" logo strip.
 */

$page_id = get_queried_object_id();
$heading = echelon_field('brands_heading', $page_id, 'Luxury Fleet Brands');
$desc    = echelon_field('brands_desc', $page_id, 'Exotic, performance, and executive vehicles selected
for weddings, corporate travel, and high-presence arrivals across New York City.');
$brands  = echelon_field('brands', $page_id, [
    ['logo' => null, 'name' => 'Audi', 'fallback' => 'audi.png'],
    ['logo' => null, 'name' => 'Bentley', 'fallback' => 'bentley.png'],
    ['logo' => null, 'name' => 'BMW', 'fallback' => 'bmw.png'],
    ['logo' => null, 'name' => 'Cadillac', 'fallback' => 'cadillac.png'],
    ['logo' => null, 'name' => 'Chevrolet', 'fallback' => 'chevrolet.png'],
    ['logo' => null, 'name' => 'Ferrari', 'fallback' => 'ferrari.png'],
]);
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
							<?php if (!empty($brand['logo'])) : ?>
								<?php echelon_media($brand['logo'], 'medium'); ?>
							<?php elseif (!empty($brand['fallback'])) : ?>
								<img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/brands/' . basename($brand['fallback']))); ?>" alt="<?php echo esc_attr($brand['name']); ?>" loading="lazy" decoding="async">
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
