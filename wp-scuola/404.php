<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			404.php
 * Description:       	Template della pagina 404 pagina non trovata
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
get_header(); ?>
<section id="content" class="container">
	<div class="container">
		<div class="row">
  	  		<div class="col-lg-8 offset-lg-2 pt-4 pb-4">
	   		<article id="post-0" class="post not-found">
				<div class="alert  alert-danger" role="alert">
			      <header class="header">
					  <h1 class="alert-heading"><?php _e( 'Si è verificato il seguente errore:', 'wpscuola' ); ?></h1>
				  </header>
				 <p><?php _e( '404 Pagina non trovata', 'wpscuola' ); ?></p>
				 <hr />
				 <p><?php _e( 'La pagina che stavi cercando non esiste.', 'wpscuola' ); ?></p>
				</div>
			</article>
	 		</div>
 		</div>
	</div>
</section>
<?php get_footer(); ?>