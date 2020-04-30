<?php /**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			single-event.php
 * Description:       	Template della pagina che visualizza il singolo evento. Pagina con sidebar Event Sidebar Widget Area con padding sinistro e destro
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
get_header(); 
global $post;
$postcats = get_the_terms($post->ID,"event-categories");
$posttags = get_the_terms($post->ID,"event-tags");
?>
<section id="content" role="main" class="container-fluid">
   <div class="container-fluid">
      <div class="row">
      	<div class="col-12 col-lg-9 pl-5 pr-5 pt-3">
<?php if ( have_posts() ) :
		while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header>
					<h3 class="entry-title"><?php the_title(); ?></h3><?php edit_post_link(); ?>
<?php			if ( ! post_password_required() ) :?>
					<div class="row metarticoli">
						<i class="fas fa-calendar-alt pr-2"></i> <a href="<?php echo esc_url(home_url('/')).get_the_time('Y')."/".get_the_time('m');?>"><?php the_time('j M y'); ?></a>
						<i class="fas fa-user-edit pr-2 pl-1"></i> <a href="<?php echo esc_url(home_url('/'))."author/".get_the_author_meta('user_nicename');?>"><?php the_author_meta('display_name'); ?></a>
<?php	    				
							if ($postcats) :?>
						<br />
						<i class="fas fa-hashtag pr-2 pl-1" title="categorie"></i> <?php echo  get_the_category_list( " , ","",$post->ID ); ?>
						<br />
<?php		endif;	
			if ($posttags) :?>
						<i class="fas fa-tags pr-2 pl-1" title="tags"></i> <?php echo get_the_term_list($post->ID,"event-tags",'<span class="listCatTag">',', ','</span>'); 
			endif;?>
					</div>	
<?php		endif;?>				
				</header>
<?php			if ( ! post_password_required() ) get_template_part( 'template-parts/entry','excerpt' );
				get_template_part( 'template-parts/entry','content' ) ; ?>
			</article>		   
		   <?php if ( ! post_password_required() ) comments_template( '', true ); 
		endwhile; 
	endif; ?>
	   	</div>
      	<div class="col-lg-3">
 		<?php	if ( is_active_sidebar( 'event-widget-area' ) ) : ?>
   			<div class="container-fluid widget-area event-widget-area">
	   		   	<ul class="xoxo">
	   		      <?php dynamic_sidebar( 'event-widget-area' ); ?>
	   		   	</ul>
   			</div>
   		<?php endif; ?>
      	</div>  
      </div>
   </div>
</section>
<?php get_footer(); ?>