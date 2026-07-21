<?php
/**
 * Default page template.
 */

get_header();

while (have_posts()) : the_post();
	get_template_part('template-parts/global/inner-hero', null, [
		'eyebrow' => __('Echelon Motions', 'echelon'),
		'title'   => esc_html(get_the_title()),
	]);
	?>
	<article class="section static-page">
		<div class="container container--narrow">
			<div class="entry-content"><?php the_content(); ?></div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
