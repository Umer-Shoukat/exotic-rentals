<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
	<div class="field">
		<input class="field__control" type="search" name="s" placeholder="<?php esc_attr_e('Search…', 'echelon'); ?>" value="<?php echo esc_attr(get_search_query()); ?>">
	</div>
	<button type="submit" class="btn btn--primary btn--sm">
		<?php esc_html_e('Search', 'echelon'); ?>
	</button>
</form>
