<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * Template Name: 		Pagina Due colonne NavPagina Contenuto
 * File Name:			tmpl_page-navcont.php
 * Description:       	Template di pagina. Pagina con sidebar Sinistra e margini
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
    
get_header(); 
?>
<section id="content" role="main" class="container">
   <div class="container">
      <div class="row">
	      <div class="pl-sm-2">
	         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); scuola_set_post_view();?>
	         <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	            <header class="header">
	               <h2 class="entry-title"><?php the_title(); ?></h2><?php edit_post_link(); 
            if ( ! post_password_required() ) get_template_part('template-parts/entry', 'meta'); ?>
	            </header>
	            <section class="entry-content">
	               <?php if ( has_post_thumbnail() ) { the_post_thumbnail(); } ?>
	               <?php the_content();
	                $LinkPages=wp_link_pages("echo=0");
	                if($LinkPages<>""){?>
	               <div class="entry-links"><?php echo $LinkPages; ?></div>
	               <?php }?>
	            </section>
	         </article>
	         <?php if ( ! post_password_required() ) comments_template( '', true ); ?>
	         <?php endwhile; endif; ?>
        <?php dynamic_sidebar("page-footer-widget-area"); ?>         
	      </div>     
      </div>
   </div>
</section>

<?php get_footer(); ?>