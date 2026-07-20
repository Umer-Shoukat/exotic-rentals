<?php
/**
 * Template Name: Front Page
 *
 * The Echelon Motions homepage. Content in each section falls back to
 * sensible defaults via echelon_field() when ACF fields are empty.
 */

get_header();
?>

<?php get_template_part('template-parts/home/hero'); ?>
<?php get_template_part('template-parts/home/booking-widget'); ?>
<?php get_template_part('template-parts/home/choose-ride'); ?>
<?php get_template_part('template-parts/home/built-for-drivers'); ?>
<?php get_template_part('template-parts/home/why-choose-us'); ?>
<?php get_template_part('template-parts/home/fleet-brands'); ?>
<?php get_template_part('template-parts/home/concierge'); ?>
<?php get_template_part('template-parts/home/instagram'); ?>
<?php get_template_part('template-parts/home/rental-terms'); ?>
<?php get_template_part('template-parts/home/more-than-rental'); ?>
<?php get_template_part('template-parts/home/serving-cities'); ?>
<?php get_template_part('template-parts/home/journal'); ?>
<?php get_template_part('template-parts/home/testimonials'); ?>
<?php get_template_part('template-parts/home/faq'); ?>
<?php get_template_part('template-parts/home/cta'); ?>

<?php get_footer(); ?>
