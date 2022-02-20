<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * Template Name: 		Pagina full-width Servizi
 * File Name:			tmpl_page-servizi.php
 * Description:       	Template di pagina a larghezza piena. Pagina senza sidebar e senza margini. Pagina che Visualizza i *                      Servizi
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
 get_header(); 
 

/**
* 
* @param {object} $Servizi
* Funzione che stampa il codice per visualizzare i blocchi dei servizi
* @return
*/
	function tmpl_ps_Crea_blocchi($Servizi,$ClassRow=""){?>
		<div class="container">
	<div class="row<?php echo $ClassRow;?>">
<?php 
		foreach($Servizi as $Servizio){
			$Servizio_Image=get_the_post_thumbnail_url($Servizio->ID);
			$Servizio_Link=get_permalink($Servizio->ID);?>
		<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 pb-2">
		    <div class="hovereffect">
		        <img class="img-responsive" src="<?php echo $Servizio_Image;?>" alt="logo servizo <?php $Servizio->post_title;?>" width="100%" height="200">
		            <div class="overlay">
		                <h2><?php echo $Servizio->post_title;?></h2>
						<p>
							<a href="<?php echo $Servizio_Link;?>" class="text-white">Vai al Servizio</a>
						</p>
		            </div>
		    </div>
		</div>
		<div class="visible-sm-block"></div>
<?php } ?>		
	</div>
  </div>
<?php
	} 
 ?>
<section id="content" role="main" class="container-fluid">
   <div class="container-fluid">
      <div class="row">
      	<div class="col-12 pl-5 pr-5">
<?php if ( have_posts() ) : 
         while ( have_posts() ) : the_post(); scuola_set_post_view();?>
	         <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	            <header class="header">
	               <h2 class="entry-title"><?php the_title(); ?></h2><?php edit_post_link(); 
            if ( ! post_password_required() ) get_template_part('template-parts/entry', 'meta'); ?>
            	</header>
             <?php get_template_part( 'template-parts/entry','content' ) ;?>
         	</article>
<?php if ( ! post_password_required() ) comments_template( '', true ); 
         endwhile; 
      endif; ?>
      	</div>      
      </div>
   </div>
</section>
<section id="servizi"  class="home-widget container">
<?php 
	$Tipologie=get_categories(array('taxonomy'  	=>"tiposervizio",
									'hide_empty' 	=> true,
									'order' 		=> 'ASC',
									'meta_key'		=> 'ordine',
									'orderby' 		=> 'meta_value_num'));
	if (count($Tipologie)>0){
		foreach($Tipologie as $Tipologia){?>
			<h3><?php echo $Tipologia->name;?></h3>
<?php
			$argServizi = array('numberposts' 	=> -1,
								'post_type'   	=> 'servizio',
								'order' 		=> 'ASC',
								'meta_key'		=> 'servizio_ordine',
								'orderby' 		=> 'meta_value_num',
								'meta_query'	=> array(array('key'	   => 'servizio_attivazione_servizio',
																'value'	   => 'si',
																'compare'  => '=')),
    							'tax_query' 	=> array(array( 'taxonomy' => 'tiposervizio',
											                	'field'    => 'slug',
											                	'terms'    => $Tipologia->slug)));
				$Servizi=get_posts($argServizi);?>
				<div class="mb-5">
					<?php tmpl_ps_Crea_blocchi($Servizi);?>
				</div>
<?php	}
	}?>
</section>	
<?php get_footer(); ?>