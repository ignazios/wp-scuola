<?php
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * Template Name: 		Home page
 * File Name:			tmpl_page-home.php
 * Description:       	Template di pagina a larghezza piena. Pagina senza sidebar e senza margini
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
get_header();

if(get_theme_mod('Scuola_Hero_Active')){ 
	$IDPage=get_theme_mod('Scuola_Hero_Page');
	$Link=get_permalink($IDPage);
	$Titolo=get_the_title($IDPage);
	$Testo=get_the_excerpt($IDPage);
	$Image=get_the_post_thumbnail_url($IDPage);
	$ImageOverlay=get_theme_mod('Scuola_Hero_Overlay');
	$IDImgEvidenza=get_post_thumbnail_id($IDPage);
	$ImageTitle = get_post($IDImgEvidenza)->post_title; //The Title
	$ImageAlt = get_post_meta($IDImgEvidenza, '_wp_attachment_image_alt', TRUE); //The Caption
	$ImageDescription = get_post($IDImgEvidenza)->post_content; // The Description
?>
<section id="hero" role="main">    
	<div class="it-hero-wrapper <?php echo $ImageOverlay;?> it-overlay">
	  <!-- - img-->
	  <div class="img-responsive-wrapper">
	    <div class="img-responsive">
	        <div class="img-wrapper">
	        	<img src="<?php echo $Image;?>" title="<?php echo $ImageTitle;?>" alt="<?php echo $ImageAlt;?>" >
	        </div>
	    </div>
	  </div>
	  <!-- - texts-->
	  <div class="container-fluid">
	    <div class="row">
	        <div class="col-12">
	          <div class="it-hero-text-wrapper bg-dark">
	              <h2 class="no_toc"><?php echo $Titolo;?></h2>
	              <p class="d-none d-lg-block"><?php echo $Testo?></p>
	              <div class="it-btn-container"><a class="btn btn-sm btn-secondary" href="<?php echo $Link;?>"><?php echo get_theme_mod('Scuola_Hero_LeggiTutto');?></a></div>
	          </div>
	        </div>
	    </div>
	  </div>
	</div>         
</section>
<?php } ?>
<section id="content-home" role="main" class="container-fluid home-content">
   <div class="container-fluid">
<?php 	if ( has_post_thumbnail() ) {
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
	?>	
		<section ID="ContenutoPagina">
				<div class="container">
	<?php 	}
			if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		         <section class="entry-content">
		            <?php edit_post_link(); ?>
		            <?php the_content(); ?>
		            <div class="entry-links"><?php wp_link_pages(); ?></div>
		         </section>
		      </article>
		      <?php endwhile; endif; ?>

		   </div>
		</section> 
<?php 
	if(get_theme_mod('scuola_eventi_attiva')){
		get_template_part( 'template-parts/section', 'eventi' );
	}
	if(get_theme_mod('scuola_comeevidenza_attiva')){
		get_template_part( 'template-parts/section', 'in_evidenza_carousel' );
	}
 	if(get_theme_mod('active_slide_inevidenza')){
		get_template_part( 'template-parts/section', 'slider' );
	}
	if(get_theme_mod('scuola_scuola_attiva')){
		get_template_part( 'template-parts/section', 'scuola' );
	}
	if(get_theme_mod('UfficioStampa_active')){
		get_template_part( 'template-parts/section', 'ufficio_stampa' );
	}
	$args = array('numberposts' => -1,'post_type'   => 'servizio');
	$Servizi=get_posts($args);
//	var_dump($Servizi);
?>
		<section>
		      <?php if ( is_active_sidebar( 'home-widget-area' ) ) : ?>
	         <div class="row xoxo">
		            <?php dynamic_sidebar( 'home-widget-area' ); ?>
		      </div>
		      <?php endif; ?>
		</section>
	</div>
</section>
<?php	
get_footer();