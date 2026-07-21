<article <?php post_class('content-card'); ?>>
	<?php if (has_post_thumbnail()) : ?>
		<a class="content-card__media" href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail('content-card'); ?>
		</a>
	<?php endif; ?>
	<div class="content-card__body">
		<div class="content-card__meta">
			<time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
			<?php $categories = get_the_category(); if ($categories) : ?>
				<span><?php echo esc_html($categories[0]->name); ?></span>
			<?php endif; ?>
		</div>
		<h2 class="content-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<div class="content-card__excerpt"><?php the_excerpt(); ?></div>
		<a class="btn btn--ghost" href="<?php the_permalink(); ?>">
			<?php esc_html_e('Read More', 'echelon'); ?>
			<?php echelon_icon('arrow-right'); ?>
		</a>
	</div>
</article>
