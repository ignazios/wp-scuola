<?php 
 /**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			single.php
 * Description:       	Template della pagina dei servizi. Pagina senza sidebar senza padding sinistro e destro
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

      <div class="col-12 col-lg-9">
		   <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header>
					<h3 class="entry-title"><?php the_title(); ?></h3><?php edit_post_link(); ?>
<?php 				if ( ! post_password_required() ):?>
					<div class="row metarticoli">
								<i class="fas fa-calendar-alt pr-1"></i> <a href="<?php echo esc_url(home_url('/')).get_the_time('Y')."/".get_the_time('m');?>"><?php the_time('j M y'); ?></a>
								<i class="fas fa-user-edit pr-1 pl-1"></i> <a href="<?php echo esc_url(home_url('/'))."author/".get_the_author_meta('user_nicename');?>"><?php the_author_meta('display_name'); ?></a>
					</div>	
<?php		endif;?>				
				</header>
<?php			get_template_part( 'template-parts/entry','content' ) ; ?>
			</article>	   
<?php		endwhile; 
		endif;?>
	   	</div>
	    <div class="col-lg-3">
			<div class="link-list-wrapper shadow p-1">
				<div class="row p-2">
					<h6 class="TitoloArchivio"><i class="far fa-newspaper"></i> Servizio</h6>
				</div> 
				<ul class="link-list" id="ListaPagine">
		<?php
			$servizio_link_servizio = get_post_meta( get_the_ID(), 'servizio_link_servizio', true );
			$servizio_link_descrizione = get_post_meta( get_the_ID(), 'servizio_link_descrizione', true );
			$servizio_attivazione_servizio = get_post_meta( get_the_ID(), 'servizio_attivazione_servizio', true );
			$servizio_codice_ipa = get_post_meta( get_the_ID(), 'servizio_codice_ipa', true );

			// Set default values.
			if(!empty( $servizio_link_servizio)) echo '<li class="pb-2"><a href="'.$servizio_link_servizio.'" class="badge badge-primary" target="_blank">Erogazione</a></li>';
			if(!empty( $servizio_link_descrizione)) echo '<li class="pb-2"><a href="'.$servizio_link_descrizione.'" class="badge badge-primary" target="_blank">Descrizione</a></li>';
			if(!empty( $servizio_codice_ipa)) echo '<li class="pb-2"><a href="https://indicepa.gov.it/ricerca/n-lista-aoo.php?cod_amm='.$servizio_codice_ipa.'" class="badge badge-primary" target="_blank">Amministrazione</a></li>';?>
		  		</ul>
			 </div>
		</div>	         
	</div>
  </div>
</section>
<?php get_footer(); ?>