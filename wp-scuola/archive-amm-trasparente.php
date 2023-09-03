<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			archive.php
 * Description:       	Template della pagina dell'archivio per i custom post type amm-trasparente creata dal plugin Amministrazione Trasparente. 
 *                      Il template riporta la Side Bar Amministrazione Trasparente sulla destra per widht > 767px, in basso per widht < 768px 
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
		  <div class="col-12 col-lg-9 pl-3">
		  	<header class="header">
		  		<h3 class="entry-title"><?php _e( 'Documenti Trasparenza:', 'wpscuola' ); ?></h3>
		  	</header>
		  	<div class="container">
<?php
		if (have_posts() ) :
		  	while (have_posts()):the_post(); 
		  		get_template_part( 'template-parts/section', 'art_list-item' );
		  	endwhile; 
		endif; 
		wp_reset_postdata();?>
		  	</div>
<?php get_template_part( 'template-parts/pagin' ); ?>
		  </div>
	      <div class="col-lg-3">
<?php dynamic_sidebar( 'amm-trasparente-widget-area' ); ?>
	      </div>
		</div>
	</div>
</section>
<?php get_footer(); ?>