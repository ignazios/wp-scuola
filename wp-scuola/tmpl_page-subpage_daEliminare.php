<?php
 /**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			page-subpage.php
 * Description:       	Template di pagina che riporta le eventuali sottopagine nella sidebar destra che viene riportata solo se presenti.
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
$args = array(
    'posts_per_page' => -1,
    'child_of'    	 => $post->ID,
    'order'          => 'ASC',
    'orderby'        => 'post_title',
    'hierarchical'	 => TRUE,
    'echo'			 => FALSE,
    'title_li'		 => "");

$figli = wp_list_pages( $args );
$Are_Child=FALSE;
if ( $figli!="" ) $Are_Child=TRUE;
     
get_header(); 	
?>
<section id="content" role="main" class="container-fluid">
   <div class="container-fluid">
      <div class="row">
<?php	if($Are_Child){?>
	      <div class="col-md-8 offset-sm-1">
<?php 	}else{?>
	      <div class="col-lg-10 offset-lg-1">
<?php 	}
		if ( have_posts() ) : 
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
      endif; 
      if($Are_Child){?>
	      	</div>
	      	<div class="col-sm-3 pt-66">
<?php	get_template_part('template-parts/nav','subpage');
	    }?>
      	</div>
      </div>
   </div>
</section>
<?php get_footer(); ?>