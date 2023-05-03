<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * Template Name: 		Pagina Nuvola di Tag
 * File Name:			tmpl_page_tagcloud.php
 * Description:       	Template di pagina che visualizza solo la nuvola di tag
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
get_header(); ?>
<section id="content" role="main" class="container-fluid">
   <div class="container">  
<?php if ( have_posts() ) : 
        while ( have_posts() ) : the_post(); scuola_set_post_view();?>
	            <header class="header">
	               <h2 class="entry-title"><?php the_title(); ?></h2>
            	</header>
<?php   endwhile; 
      endif; ?>
   
   
   
   
   <?php wp_tag_cloud( array(
   'smallest' => 1, // size of least used tag
   'largest'  => 1, // size of most used tag
   'unit'     => 'em', // unit for sizing the tags
   'number'   => 0, // displays at most 45 tags
   'orderby'  => 'count', // order tags alphabetically
   'order'    => 'DESC', // order tags by ascending order
   'taxonomy' => 'post_tag', // you can even make tags for custom taxonomies
   'show_count' => 1,
   'format'     => 'flat',
//	'separator'  => "<br />",
   ) ); ?>
   </div>
</section>
<?php get_footer(); ?>