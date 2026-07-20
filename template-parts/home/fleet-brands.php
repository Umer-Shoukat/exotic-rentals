<?php
/**
 * Home: "Luxury Fleet Brands" logo strip.
 */

$heading = echelon_field('brands_heading', get_the_ID(), 'Luxury Fleet Brands');
$desc    = echelon_field('brands_desc', get_the_ID(), 'Exotic, performance, and executive vehicles curated for high-presence arrivals.');
$brands  = echelon_field('brands', get_the_ID(), [
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
			<p class="eyebrow"><?php esc_html_e('Makes We Move', 'echelon'); ?></p>
			<h2 class="section-heading__title"><?php echo esc_html($heading); ?></h2>
		</div>
		<p class="fleet-brands__desc"><?php echo esc_html($desc); ?></p>
	</div>

	<div class="fleet-brands__strip">
		<?php foreach ($brands as $brand) : ?>
			<div class="fleet-brands__logo">
				<?php if (!empty($brand['logo'])) : ?>
					<?php echelon_media($brand['logo'], 'medium'); ?>
				<?php elseif (!empty($brand['fallback'])) : ?>
					<img src="<?php echo esc_url(get_theme_file_uri('assets/images/figma/brands/' . basename($brand['fallback']))); ?>" alt="<?php echo esc_attr($brand['name']); ?>">
				<?php else : ?>
					<span><?php echo esc_html($brand['name']); ?></span>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
