<?php
/**
 * Shared hero for non-marketing inner pages.
 *
 * Optional arguments: eyebrow, title, description, modifier, image.
 */

$args = wp_parse_args($args ?? [], [
    'eyebrow'    => '',
    'title'      => '',
    'description'=> '',
    'modifier'   => '',
    'image'      => null,
]);

$classes = trim('inner-hero ' . $args['modifier']);
?>
<section class="<?php echo esc_attr($classes); ?>">
	<?php if ($args['image']) : ?>
		<div class="inner-hero__media" aria-hidden="true">
			<?php echelon_media($args['image'], 'full', ''); ?>
			<span></span>
		</div>
	<?php endif; ?>
	<div class="container">
		<?php if ($args['eyebrow']) : ?>
			<p class="eyebrow"><?php echo esc_html($args['eyebrow']); ?></p>
		<?php endif; ?>
		<h1 class="inner-hero__title"><?php echo wp_kses_post($args['title']); ?></h1>
		<?php if ($args['description']) : ?>
			<div class="inner-hero__description"><?php echo wp_kses_post(wpautop($args['description'])); ?></div>
		<?php endif; ?>
	</div>
</section>
