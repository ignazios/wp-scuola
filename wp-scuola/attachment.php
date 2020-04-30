<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			attachment.php
 * Description:       	Template della pagina dell'archivio degli allegati
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
get_header();

global $post; 
?>
<section id="content" role="main" class="container">
   <div class="container">
      <div class="row">
	      <div class="col-12 col-lg-9 pl-5 pr-5 pt-3">
				<?php if (have_posts()): while (have_posts()): the_post(); ?>
					<header class="header">
						<h1 class="entry-title"><?php	the_title(); ?></h1>
						<p>
							<?php _e( 'Media collegato in: ', 'wpscuola' ); ?>
						<a href="<?php echo get_permalink($post->post_parent); ?>" title="<?php	printf(__('Vai a %s', 'wpscuola') , esc_attr(get_the_title($post->post_parent) , 1)); ?>" rev="attachment"> <?php echo get_the_title($post->post_parent); ?></a> <?php edit_post_link(); ?>
						</p>
						<?php get_template_part('template-parts/entry', 'meta'); ?>
					</header>
					
					<article id="post-<?php	the_ID(); ?>" <?php	post_class(); ?>>
						
						<header class="header">
							<nav id="nav-above" class="navigation" role="navigation">
								<div class="nav-previous"><?php	previous_image_link(false, '&larr;'); ?></div>
								<div class="nav-next"><?php	next_image_link(false, '&rarr;'); ?></div>
							</nav>
						</header>
						
						<section class="entry-content">
						<div class="entry-attachment">Allegato
						<?php	if (wp_attachment_is_image($post->ID)): $att_image = wp_get_attachment_image_src($post->ID, "large"); ?>
						<p class="attachment"><a href="<?php echo wp_get_attachment_url($post->ID); ?>" title="<?php the_title(); ?>" rel="attachment"><img src="<?php echo $att_image[0]; ?>" width="<?php echo $att_image[1]; ?>" height="<?php echo $att_image[2]; ?>" class="attachment-medium" alt="<?php $post->post_excerpt; ?>" /></a></p>
						<?php	else: ?>
						<a href="<?php echo wp_get_attachment_url($post->ID); ?>" title="<?php echo esc_attr(get_the_title($post->ID) , 1); ?>" rel="attachment"><?php echo basename($post->guid); ?></a>
						<?php	endif; ?>
						</div>
						<div class="entry-caption">
						<?php if (!empty($post->post_excerpt)) the_excerpt(); ?></div>
						<?php if (has_post_thumbnail()) { the_post_thumbnail(); } ?>
						</section>
					</article>
					
				<?php
					endwhile;
					endif; ?>

			</div>
		</div>
	</div>
</section>
<?php get_footer(); ?>