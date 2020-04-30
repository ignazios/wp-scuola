<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			page.php
 * Description:       	Template standard del tema con sidebar a destra
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
	      <div class="col-md-8 offset-sm-1">
	         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	         <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	            <header class="header mt-5">
	               <h1 class="entry-title"><?php the_title(); ?></h1>
	               <?php edit_post_link(); ?>
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
	      </div>     
	      <div class="col-sm-3">
<?php	if($Are_Child){
	    	get_template_part('template-parts/navigazione-page');
	    }
	   	if ( is_active_sidebar( 'page-widget-area' ) ) : ?>
   			<div class="container-fluid widget-area page-widget-area">
	   		   <ul class="xoxo">
	   		      <?php dynamic_sidebar( 'page-widget-area' ); ?>
	   		   </ul>
   			</div>
<?php endif; ?>
		</div>      
      </div>
   </div>
</section>

<?php get_footer(); ?>