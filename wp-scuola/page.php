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
	$args = array(
        'posts_per_page' => 1,
        'order'          => 'ASC',
        'post_parent'    => $post->ID,
        'post_status'	 => 'Publish',
        'post_type'		 => 'Page',
    );
 
    $padre = get_children( $args );
    
   	$Primo_Livello=get_ancestors( $post->ID, 'page' ); 	
//echo "<pre>";var_dump($padre);echo "</pre>";
//echo "<pre>";var_dump($Primo_Livello);echo "</pre>";
   	$Are_Child=FALSE;
	if(count($Primo_Livello)==0)
		$Partenza=$post->ID;
	else
		$Partenza=end($Primo_Livello);
   if (count($Primo_Livello)>0 or count($padre)>0){
	   $args = array(
	        'posts_per_page' => -1,
	        'child_of'    	 => $Partenza,
	        'sort_order'     => 'ASC',
	        'sort_column '   => 'menu_order,post_title',
	        'hierarchical'	 => TRUE,
	        'echo'			 => FALSE,
	        'title_li'		 => "",
	        'depth'			=>0,);
	    $figli = wp_list_pages( $args );
//		var_dump($figli);
	    $figli= '<li class="page_item page-item-234 page_item_has_children current_page_item">
	    	<a href="'.get_permalink(end($Primo_Livello)).'" aria-current="page">'.get_the_title(end($Primo_Livello)).'</a>
				<ul class="children">
				'.$figli.'
				</ul>
			</li>';
	    //echo "<pre>";var_dump($padre);echo "</pre>";
	    //echo "<pre>";var_dump($figli);echo "</pre>";
	    $Are_Child=TRUE; 
    }   
    
get_header(); 
?>
<section id="content" role="main" class="container-fluid">
   <div class="container-fluid">
      <div class="row">
<?php	if($Are_Child OR is_active_sidebar( 'page-widget-area' ) ) { ?>
	      <div class="col-lg-8 offset-sm-05">
<?php   }else{ ?>
		  <div class="col-lg-10 offset-sm-1">		      
<?php   }
		if (have_posts()) : while ( have_posts() ) : the_post(); scuola_set_post_view();?>
	         <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	            <header class="header">
	               <h2 class="entry-title"><?php the_title(); ?></h2><?php edit_post_link(); 
	                if ( ! post_password_required() ) get_template_part('template-parts/entry', 'meta'); ?>

	            </header>
	            <section class="entry-content pt-2">
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
<?php	if($Are_Child Or is_active_sidebar( 'page-widget-area' ) ) { ?>
	      <div class="col-lg-3 pt-66">		      
<?php		if($Are_Child){
		    	get_template_part('template-parts/nav',"subpage");
		    }
		   	if ( is_active_sidebar( 'page-widget-area' ) ) : ?>
	   			<div class="container-fluid widget-area page-widget-area">
		   		   <ul class="xoxo">
		   		      <?php dynamic_sidebar( 'page-widget-area' ); ?>
		   		   </ul>
	   			</div>
<?php 		endif; ?>
			</div>      
<?php 	} ?>
      </div>
   </div>
</section>

<?php get_footer(); ?>