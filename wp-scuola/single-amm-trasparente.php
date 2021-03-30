<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			single-amm-trasparente.php
 * Description:       	Template della pagina che visualizza il singolo contenuto dell'amministrazione trasparente. Pagina con sidebar Amministrazione Trasparente Widget Area
 *                      con padding sinistro
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
<?php if ( have_posts() ) : 
		while ( have_posts() ) : the_post(); 
			scuola_set_post_view();?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header>
					<h3 class="entry-title"><?php the_title(); ?></h3><?php edit_post_link(); ?>
<?php 				if ( ! post_password_required() ):?>
					<div class="row metarticoli">
<?php if(get_theme_mod('scuola_MTdata_attiva')):?>
						<span class="fas fa-calendar-alt pr-2"></span> 
<?php 	if(get_theme_mod('scuola_MTdataLink_attiva')){?>
			<a href="<?php echo esc_url(home_url('/')).get_the_time('Y')."/".get_the_time('m');?>"><?php the_time('j M y'); ?></a>
<?php 	}else{
			the_time('j M y');
	  	}
	  	endif;	  
	  	if(get_theme_mod('scuola_MTautore_attiva')):?>						
	  		<span class="fas fa-user-edit pr-2 pl-1"></span> 
<?php 	if(get_theme_mod('scuola_MTautoreLink_attiva')){?>
			<a href="<?php echo esc_url(home_url('/'))."author/".get_the_author_meta('user_nicename');?>"><?php the_author_meta('display_name'); ?></a>
<?php 	}else{
			the_author_meta('display_name');
	  	} 	
	  	endif;?>
					</div>
<?php				endif;?>	
				</header>
<?php		if ( ! post_password_required() ) get_template_part( 'template-parts/entry','excerpt' );
			get_template_part( 'template-parts/entry','content' ) ; ?>
			</article>	   
<?php 		if ( ! post_password_required() ) comments_template( '', true ); 
	  	endwhile; 
	  endif; ?>
	   </div>
      <div class="col-lg-3">
         <?php dynamic_sidebar( 'amm-trasparente-widget-area' ); ?>
      </div>
      </div>
   </div>
</section>
<?php get_footer(); ?>