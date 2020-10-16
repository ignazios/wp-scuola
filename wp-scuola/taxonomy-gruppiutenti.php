<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			archive.php
 * Description:       	Template della pagina dell'archivio standard che automaticamente crea l'archivio per data sulla destra per widht > 767px, in basso per widht < 768px 
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
global $query_string,$mesi;
wp_parse_str( $query_string, $search_query );
if(isset($search_query['author'])){
	echo '<meta http-equiv="refresh" content="0;URL='.esc_url(home_url('/'))."author/".$search_query['author'].'">';
	die();
} 
get_header();
 $gruppiutenti=get_terms('gruppiutenti', array('hide_empty' => false));
 $Gruppi = array();
 wps_sort_terms_hierarchicaly($gruppiutenti, $Gruppi);
 $tax = $wp_query->get_queried_object();
 ?>
<section id="content" role="main" class="container-fluid">
   <div class="container">
      <div class="row">
		  <div class="col-12 col-lg-9">
		  	<header class="header">
		  		<h3 class="entry-title"><?php echo __( 'Circolari', 'wpscuola' )." ". $tax->name ; ?></h3>
		  	</header>
		  	<div class="container">
<?php	if (have_posts() ):
			while ( have_posts() ):the_post(); 
		  		get_template_part( 'template-parts/section', 'art_list-item' );
			endwhile; 
		endif;
		wp_reset_postdata(); ?>
		  	</div>
<?php get_template_part( 'template-parts/pagin' ); ?>
		  </div>
		  <div class="col-12 col-lg-3">
		  	<div class="container">
			  	<div class="link-list-wrapper shadow p-1">
				  	<div class="row">
						<div class="col mb-0">
							<h3 class="h5"><span class="fas fa-users"></span> <?php _e( 'Destinatari', 'wpscuola' ); ?></h3>
						</div>
					</div> 
					<div class="row">
						<div class="col ml-3" id="archdest">	
							<ul class="link-list">
				<?php foreach($Gruppi as $Gruppo):?>
						  		<li>
					  			<a class="list-item" href="<?php echo esc_url(home_url('/')).'destinatari/'.$Gruppo->slug;?>"><?php echo $Gruppo->name;?></a>
					  	<?php	if ($Gruppo->children){
					  				$SottoGruppi=$Gruppo->children;?>
						  			<ul>
						  			<?php foreach($SottoGruppi as $SottoGruppo):?>
						  				<li>
						  				<a class="list-item" href="<?php echo esc_url(home_url('/')).'destinatari/'.$SottoGruppo->slug;?>"><?php echo $SottoGruppo->name;?></a>
						  				</li>
						  			<?php endforeach;?>
						  			</ul>
						  		<?php } ?>
								</li>
				<?php endforeach;?>
						  	</ul>
						</div>
					</div>
			   	</div>		  
			</div>
		 </div>	
		</div>
	  </div>
	</div>
</section>
<?php get_footer(); ?>