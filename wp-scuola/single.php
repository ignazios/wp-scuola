<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			single.php
 * Description:       	Template della pagina che visualizza il singolo articolo. Pagina senza sidebar con padding sinistro e destro
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
get_header(); 
?>
<section id="content" role="main" class="container-fluid">
   <div class="container-fluid">
      <div class="row">

      <div class="col-12 col-lg-9 pl-5 pr-5 pt-3">
<?php 	if ( have_posts() ) : 
			while ( have_posts() ) : the_post();?> 
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header>
					<h3 class="entry-title"><?php the_title(); ?></h3><?php edit_post_link(); ?>
					<?php if ( ! post_password_required() ) get_template_part('template-parts/entry', 'meta'); ?>
				</header>
<?php			if ( ! post_password_required() ) get_template_part( 'template-parts/entry','excerpt' );
				get_template_part( 'template-parts/entry','content' ) ; ?>
			</article>	   
<?php 				if ( ! post_password_required() ) comments_template( '', true );
			endwhile; 
		endif;?>
	   </div>

      <div class="col-lg-3">
         <?php get_sidebar(); ?>
      </div>
      
      </div>
   </div>
</section>
<?php get_footer(); ?>