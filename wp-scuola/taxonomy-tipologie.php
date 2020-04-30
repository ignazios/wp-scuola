<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			taxonomy-tipologia.php
 * Description:       	Template della pagina che automaticamente crea l'archivio delle tipologie dell'amministrazione trasparente la pagina riporta anche la sidebar 
 *                      Amministrazione Trasparente (sulla destra per widht>767px o in basso per widht<768px )
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
		  		<h4 class="entry-title">Documenti sezione : <?php echo single_cat_title( '', false ) ; ?></h4>
		  	</header>
		  	<div class="container">
<?php	if (have_posts() ) :
		  while ( have_posts() ) : the_post(); 
		  	get_template_part( 'template-parts/section', 'art_list-item' ); 
		  endwhile; 
		endif; ?>
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