<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * Template Name: 		Pagina full-width
 * File Name:			tmpl_page-fullwidth.php
 * Description:       	Template di pagina a larghezza piena. Pagina senza sidebar e senza margini
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

<?php get_footer(); ?>