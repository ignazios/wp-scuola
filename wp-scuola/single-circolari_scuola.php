<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			single-circolari_scuola.php
 * Description:       	Template della pagina che visualizza le singole circolari scolastiche. Pagina senza sidebar con padding sinistro e destro
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
	get_header(); 
	$PostID= get_the_ID();
	$visibilita=get_post_meta($PostID, "_visibilita");
	$seld="";
	if (count($visibilita)==0)
		$selp="Pb";
	else 
		if ($visibilita[0]=="p")
			$selp="Pb";
		else	
			$seld="Pr";
	if ((!is_user_logged_in() Or !wps_Is_Circolare_per_User($PostID)) And $seld=="Pr")	{?>
	<div class="alert alert-success mx-auto mt-5" style="width: 50%;" role="alert">
  		<h4 class="alert-heading">Avviso di sicurezza!</h4>
  		<p>Circolare riservata a specifici gruppi di utenti registrati.</p>
  		<hr>
  		<p class="mb-0">Loggati per accedere alla circolare.</p>
	</div>';
<?php }else{?>
<section id="content" role="main" class="container">
   <div class="container">
      <div class="row">

      <div class="col-12 pl-5 pr-5 pt-3">
<?php 	if ( have_posts() ) : 
			while ( have_posts() ) : the_post();
				scuola_set_post_view();
		   		$ID_post=get_the_ID();
				$numero=get_post_meta($ID_post, "_numero",TRUE);
				$anno=get_post_meta($ID_post, "_anno",TRUE);
				$Elenco=wps_GetEencoDestinatari($ID_post,FALSE,TRUE);
 ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header>
					<h3 class="entry-title"><?php the_title(); ?></h3><?php edit_post_link(); ?>
<?php			if ( ! post_password_required() ) :?>
					<div class="metarticoli">
						<p>
							<span class="fas fa-calendar-alt pr-1"></span> <strong><a href="<?php echo esc_url(home_url('/'))."circolari-scuola/".get_the_time('Y')."/".get_the_time('m');?>"><?php the_time('j M y'); ?></a> 
							<span class="fas fa-ticket-alt pl-2 pr-1"></span> <?php echo $numero."_".$anno;
				if(get_theme_mod('scuola_MTautore_attiva')):?>
							<span class="fas fa-user-edit pl-2 pr-1"></span> 
<?php 				if(get_theme_mod('scuola_MTautoreLink_attiva')){?>
				<a href="<?php echo esc_url(home_url('/'))."author/".get_the_author_meta('user_nicename');?>"><?php the_author_meta('display_name'); ?></a>
			<?php 	}else{
						the_author_meta('display_name');
				  	} 							
				endif;?>
				  		</p>
						<p>
							<span class="fa fa-users pr-1" aria-hidden="true"></span> <?php echo $Elenco;?>
				            <?php	if (wps_Is_Circolare_Da_Firmare($ID_post)){?>
				            	  <span class="card-firma pl-2 pr-1">
							<?php		if (!wps_Is_Circolare_Firmata($ID_post)) {
											$ngiorni=wps_Get_scadenzaCircolare($ID_post,"",True);					
											if(wps_Is_Circolare_Scaduta($ID_post)){
												echo' <span class="fas fa-pencil-alt" aria-hidden="true" style="color:red;"></span> Scaduta e non Firmata ';						
											}else{
												switch ($ngiorni){
													case -1:							
														$entro="";							
														break;													
													case 0:
														$entro=__("entro OGGI","wpscuola");
														break;
													case 1:
														$entro=__("entro DOMANI","wpscuola");"entro DOMANI";
														break;
													default:
														$entro=sprintf(__("entro %s giorni","wpscuola"),$ngiorni);
														break;
												}
												$sign=get_post_meta($ID_post, "_sign",TRUE);
												if ($sign!="Firma")
													$Tipo=sprintf(__("Esprimere adesione %s","wpscuola"),$entro);
												else
													$Tipo=sprintf(__("Firmare %s","wpscuola"),$entro);
												echo' <span class="fas fa-pencil-alt" style="color:red;"></span> '.$Tipo;	
										}			
									}else{
										echo' <span class="fas fa-pencil-alt" aria-hidden="true" style="color:blue;"></span> '.__("Firmata","wpscuola");				
									}?>
								</span>
						<?php }?>
						</p>
						</strong>
					</div>
<?php		endif;?>				
				</header>
<?php			if ( ! post_password_required() ) get_template_part( 'template-parts/entry','excerpt' );
				get_template_part( 'template-parts/entry','content' ) ; ?>
			</article>   
<?php 			if ( ! post_password_required() ) comments_template( '', true );
			endwhile; 
		endif;?>
	   </div>
       </div>
   </div>
</section>
<?php }
get_footer(); ?>