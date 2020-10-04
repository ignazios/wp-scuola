<?php
 /**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			single.php
 * Template Name:		Articolo full-width
 * Template Post Type: 	post, product
 * Description:       	Template della pagina a pagina piena per visualizza il singolo articolo. Pagina senza sidebar senza padding sinistro e destro
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
 get_header(); ?>
<section id="content" role="main" class="container mt-4 mb-4">
	<div class="container">
    	<div class="row">
	    	<div class="col-12">
<?php 	if ( have_posts() ) : 
			while ( have_posts() ) : the_post();
				scuola_set_post_view();?> 
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header>
						<h2 class="entry-title"><?php the_title(); ?></h2><?php edit_post_link(); ?>
					<?php if ( ! post_password_required() ) get_template_part('template-parts/entry', 'meta'); ?>
					</header>
<?php	if ( ! post_password_required()) get_template_part( 'template-parts/entry','excerpt' );
				get_template_part( 'template-parts/entry','content' ) ; ?>
				</article>	   
<?php 				if ( ! post_password_required() ) comments_template( '', true );
			endwhile; 
		endif;?>
	   		</div>
			<?php dynamic_sidebar("single-footer-widget-area"); ?>
		</div>
	</div>
</section>
<?php get_footer(); ?>