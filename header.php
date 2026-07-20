<?php
/**
 * The header for the theme.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e('Skip to content', 'echelon'); ?></a>

<header class="site-header" data-site-header>
	<div class="site-header__inner">
		<?php if (has_custom_logo()) : ?>
			<div class="site-logo"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a class="site-logo" href="<?php echo esc_url(home_url('/')); ?>">
				<img class="site-logo__image" src="<?php echo esc_url(ECHELON_THEME_URI . '/assets/images/logo.png'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
			</a>
		<?php endif; ?>

		<?php get_template_part('template-parts/global/nav'); ?>

		<div class="site-header__actions">
			<a class="btn btn--primary btn--sm" href="<?php echo esc_url(home_url('/reservation/')); ?>">
				<?php esc_html_e('Book Now', 'echelon'); ?>
			</a>
			<button type="button" class="nav-toggle" data-nav-toggle aria-expanded="false" aria-controls="mobile-nav" aria-label="<?php esc_attr_e('Toggle menu', 'echelon'); ?>">
				<span></span>
			</button>
		</div>
	</div>
</header>

<?php get_template_part('template-parts/global/mobile-nav'); ?>

<main id="main">
