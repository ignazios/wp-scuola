<?php
	$prev_arrow = is_rtl() ? '<svg class="icon"><use xlink:href="'.get_bloginfo('template_url').'/static/svg/sprite.svg#it-chevron-right"></use></svg>':'<svg class="icon"><use xlink:href="'.get_bloginfo('template_url').'/static/svg/sprite.svg#it-chevron-left"></use></svg>';
	$next_arrow = is_rtl() ? '<svg class="icon"><use xlink:href="'.get_bloginfo('template_url').'/static/svg/sprite.svg#it-chevron-left"></use></svg>':'<svg class="icon"><use xlink:href="'.get_bloginfo('template_url').'/static/svg/sprite.svg#it-chevron-right"></use></svg>';
	
	global $wp_query;
	$total = $wp_query->max_num_pages;
	$big = 999999999; // need an unlikely integer
	if( $total > 1 )  {
		 if( !$current_page = get_query_var('paged') )
			 $current_page = 1;
		 if( get_option('permalink_structure') ) {
			 $format = 'page/%#%/';
		 } else {
			 $format = '&paged=%#%';
		 }
		echo paginate_links(array(
			'base'			=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'		=> $format,
			'current'		=> max( 1, get_query_var('paged') ),
			'total' 		=> $total,
			'mid_size'		=> 2,
			'type' 			=> 'list',
			'prev_text'		=> $prev_arrow,
			'next_text'		=> $next_arrow,
		 ) );
	}
?>