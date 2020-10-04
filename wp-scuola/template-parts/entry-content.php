<section class="entry-content clearfix pt-3">

	<?php if ( has_post_thumbnail() And  ! post_password_required()) { 
			the_post_thumbnail('medium', array( 'class' => 'float-left pr-2' )); 
		} ?>
	<?php the_content(); ?>
	<div>
		<?php 
		$args = array (
		    'before'            => '<ul class="page-numbers pagin-page">',
		    'after'             => '</ul>',
		    'link_before'       => '<li>',
		    'link_after'        => '</li>',
		    'separator'         => '',
		    'echo'				=> false
		);	
		$Intestazione="<li><span class='pagin-intest'>".__("Pagine: ", 'wpscuola' )."</span></li>";	
		$Paginazione=wp_link_pages($args );
		$Paginazione=str_replace( "<li>","",$Paginazione);
		$Paginazione=str_replace( "</li>","",$Paginazione);
		$Paginazione=str_replace( "<a","<li><a",$Paginazione);
		$Paginazione=str_replace( "</a>","</a></li>",$Paginazione);
		$Paginazione=str_replace( "post-page-numbers","page-numbers",$Paginazione);
		$Paginazione=str_replace( "<span","<li><span",$Paginazione);
		$Paginazione=str_replace( "</span>","</span></li>",$Paginazione);
		$Paginazione=str_replace( '<ul class="page-numbers pagin-page">','<ul class="page-numbers pagin-page">'.$Intestazione,$Paginazione);
		echo $Paginazione;
		?>
	</div>
</section>
