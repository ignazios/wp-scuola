<?php
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			footer.php
 * Description:       	Codice del footer delle pagine del tema. Il footer prevede 3 Sezioni
 * 						1a- due righe. La prima riporta Logo e Nome del sito. La seconda riga è suddivisa in 4 colonne di cui le prime 3 riportano i dati della scuola e la 
 *                          terza il menu Footer Menu Ente
 * 						2a- tre colonne.La prima colonna riporta il menu Footer Menu. La seconda colonna riporta la mappa se inserita come Frame nelle personalizzazioni e/o
 * 						    il menu Footer Menu secondo. La terza colonna riporta i link ai Social
 * 						3a- una riga divisa in due parti alla sinistra viene riportato il menu Footer Menu Legale e sulla destra il Copyright
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
?>
			<div class="mx-auto text-center pb-3 pt-3" style="width: 200px;">
			  <a href="#" data-attribute="back-to-top" title="<?php _e('Torna ad inizio pagina','wpscuola');?>">
			    <span class="fas fa-arrow-circle-up fa-3x"></span>
			  </a>
			</div>
			<div class="clear"></div>
    </main>
	<footer class="it-footer" id="footer">
	  <div class="it-footer-main">
	    <div class="container">
	      <section>
	        <div class="row clearfix">
	          <div class="col-sm-12">
	            <div class="it-brand-wrapper">
	              <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_html( get_bloginfo( 'name' ) ); ?>" rel="home">
	              <?php 
					    $custom_logo_id = get_theme_mod('Scuola_Logo');
	                  	$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
					    if(FALSE!== $logo){
					    	$ImgLogo=esc_url( $logo[0] );
						} else {
							$ImgLogo=get_template_directory_uri() . '/images/logoStato.png';
				        } ?>
						<img class="icona" src="<?php echo $ImgLogo;?>" alt="<?php echo esc_html( get_bloginfo( 'name' ) );?>">
		                <div class="it-brand-text">
		                  <p class="h4"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
		                  <p class="h5"><?php bloginfo( 'description' ); ?></p>
		                </div>
		              </a>
	            </div>
	          </div>
	        </div>
	         <div class="row">
	          <div class="col-lg-3 col-md-6 col-sm-6 pb-2">
	            <h3><span class="fas fa-info-circle"></span> <?php _e( 'Informazioni', 'wpscuola' );?></h3>
	            <div class="link-list-wrapper">
				  <ul class="footer-list link-list clearfix">
	                <li><?php echo get_theme_mod('Scuola_Amministrazione_Indirizzo'); ?></li>
	                <li><?php echo get_theme_mod('Scuola_Amministrazione_CAP'); 
	                	if (get_theme_mod('Scuola_Amministrazione_CAP') And get_theme_mod('Scuola_Amministrazione_Citta')) echo ',';
	                	echo get_theme_mod('Scuola_Amministrazione_Citta'); ?></li>
	                <?php echo (get_theme_mod('Scuola_Amministrazione_CM')!=""?"<li>C.M. ".get_theme_mod('Scuola_Amministrazione_CM')."</li>":""); 
	                if (get_theme_mod('Scuola_Amministrazione_IPA')){
						$IPA="<li>Cod. IPA ";
						$linkIPA=get_theme_mod('Scuola_Amministrazione_LinkIndicePaByIPA');
						if($linkIPA){
							$IPA.='<a href="'.$linkIPA.'" style="display: inline;text-decoration-line: underline;" target="_blank" title="link al sito indicepa.gov.it con i dati dell\'ente">';
						}
						$IPA.=get_theme_mod('Scuola_Amministrazione_IPA'); 
						if($linkIPA){
							$IPA.='</a>';
						}
						$IPA.="</li>";
						echo $IPA;
					}
	                echo (get_theme_mod('Scuola_Amministrazione_CFPA')!=""?"<li>C.F. ".get_theme_mod('Scuola_Amministrazione_CFPA')."</li>":""); 
	                echo (get_theme_mod('Scuola_Amministrazione_PIVA')!=""?"<li>P.Iva ".get_theme_mod('Scuola_Amministrazione_PIVA')."</li>":""); 
	            	echo (get_theme_mod('Scuola_Amministrazione_CodUni')!=""?"<li>Cod. Univoco ".get_theme_mod('Scuola_Amministrazione_CodUni')."</li>":""); 
	                echo (get_theme_mod('Scuola_Amministrazione_IBAN')!=""?"<li>IBAN ".get_theme_mod('Scuola_Amministrazione_IBAN')."</li>":"");
					if(get_theme_mod('scuola_mappa_attiva')){?>
					<li> 
					<?php if(!get_theme_mod('scuola_mappa_frame') And get_theme_mod('scuola_mappa_attiva')){?> 
							<span class="fas fa-map-marked-alt"></span> <a href="<?php echo get_theme_mod('scuola_mappa_link'); ?>" target="_blank" title="<?php _e( "Mappa con la geolocalizzazione dell'Istituto", 'wpscuola' );?>" class="d-inline"><?php echo get_theme_mod('scuola_mappa_titolo'); ?></a>
					<?php } ?>
					</li>
			  <?php }?>
				  </ul>
	            </div>
	          </div>
	          <div class="col-lg-3 col-md-6 col-sm-6 pb-2">
	            <h3><span class="fas fa-address-book"></span> <?php _e( 'Recapiti', 'wpscuola' );?></h3>
	            <div class="link-list-wrapper">
	              <ul class="footer-list link-list clearfix">
	          <?php if (get_theme_mod('Scuola_Amministrazione_DesTelefono')!=""){
		          		echo "<li>".get_theme_mod('Scuola_Amministrazione_DesTelefono');
		          		if (get_theme_mod('Scuola_Amministrazione_Telefono')!=""){
		          			echo "<br />".get_theme_mod('Scuola_Amministrazione_Telefono');
	          			}
	          			echo "</li>";
					}	
	         		if (get_theme_mod('Scuola_Amministrazione_DesTelefono2')!=""){
	          			echo "<li>".get_theme_mod('Scuola_Amministrazione_DesTelefono2');
	          			if (get_theme_mod('Scuola_Amministrazione_Telefono2')!=""){
	          				echo "<br />".get_theme_mod('Scuola_Amministrazione_Telefono2');
	          			}
	          			echo "</li>";
					}
	        		if (get_theme_mod('Scuola_Amministrazione_DesTelefono3')!=""){
	          			echo "<li>".get_theme_mod('Scuola_Amministrazione_DesTelefono3');
	          			if (get_theme_mod('Scuola_Amministrazione_Telefono3')!=""){
	          				echo "<br />".get_theme_mod('Scuola_Amministrazione_Telefono3');
	          			}
	          			echo "</li>";
					}
	        		if (get_theme_mod('Scuola_Amministrazione_DesTelefono4')!=""){
	          			echo "<li>".get_theme_mod('Scuola_Amministrazione_DesTelefono4');
	          			if (get_theme_mod('Scuola_Amministrazione_Telefono4')!=""){
	          				echo "<br />".get_theme_mod('Scuola_Amministrazione_Telefono4');
	          			}
	          			echo "</li>";
	         		}?>
	            	</ul>
	            </div>
	          </div>
	          <div class="col-lg-3 col-md-6 col-sm-6 pb-2">
	            <h3><span class="fas fa-at"></span> <?php _e( 'Mail', 'wpscuola' );?></h3>
	            <div class="link-list-wrapper">
	              <ul class="footer-list link-list clearfix">
	       <?php	if (get_theme_mod('Scuola_Amministrazione_Email')!=""){
	          			echo "<li><a href=\"mailto:".get_theme_mod('Scuola_Amministrazione_Email')."\">".get_theme_mod('Scuola_Amministrazione_Email')."</a>
	          				  </li>";
	         		}
	 				if (get_theme_mod('Scuola_Amministrazione_Email2')!=""){
	          			echo "<li><a href=\"mailto:".get_theme_mod('Scuola_Amministrazione_Email2')."\">".get_theme_mod('Scuola_Amministrazione_Email2')."</a>
	          				  </li>";            
	         		}
	 				if (get_theme_mod('Scuola_Amministrazione_PEC')!=""){
	          			echo "<li><a href=\"mailto:".get_theme_mod('Scuola_Amministrazione_PEC')."\">".get_theme_mod('Scuola_Amministrazione_PEC')."</a>
	          				  </li>";          
	         		}?>
	          	 </ul>
	            </div>
	          </div>
	          <div class="col-lg-3 col-md-6 col-sm-6">
			<?php
				if ( has_nav_menu( 'menu-footer-ente' ) ) {
					$locations = get_nav_menu_locations();
					$menu = get_term( $locations["menu-footer-ente"], 'nav_menu' );
				?>
				<h3><?php echo htmlspecialchars_decode($menu->name); ?></h3><?php
				wp_nav_menu(array( 'theme_location' => 'menu-footer-ente', 'container' => 'ul', 'menu_class' => 'footer-list link-list clearfix' ));
			} ?>          
	          </div>
	        </div>
	      </section>
	      <section class="py-4 border-white border-top">
	        <div class="row">
	          <div class="col-lg-4 col-md-12 pb-2">
	  		<?php
				if ( has_nav_menu( 'menu-footer' ) ) {
					$locations = get_nav_menu_locations();
					$menu = get_term( $locations["menu-footer"], 'nav_menu' );
				?>
				<h4><?php echo htmlspecialchars_decode($menu->name); ?></h4><?php
				wp_nav_menu(array( 'theme_location' => 'menu-footer', 'container' => 'ul', 'menu_class' => 'footer-list link-list clearfix' ));
			} ?>   
	          </div>
	          <div class="col-lg-4 col-md-12 pb-2">
					<?php if(get_theme_mod('scuola_mappa_frame') And get_theme_mod('scuola_mappa_attiva')){?>
						<h4><span class="fas fa-map-marked"></span> <?php echo get_theme_mod('scuola_mappa_titolo');?></h4>
						<div id="Cartina">
					<?php	echo get_theme_mod('scuola_mappa_frame');?>
						</div>
					<?php }
					if ( has_nav_menu( 'menu-footer-secondo' ) ) {
						$locations = get_nav_menu_locations();
						$menu = get_term( $locations["menu-footer-secondo"], 'nav_menu' );?>
				<h3><?php echo htmlspecialchars_decode($menu->name); ?></h3><?php
				wp_nav_menu(array( 'theme_location' => 'menu-footer-secondo', 'container' => 'ul', 'menu_class' => 'footer-list link-list clearfix' ));
			} ?>   
	          </div>
	          <div class="col-lg-4 col-md-12 pb-2">
	          <?php if(HasSocial()) {?>
	            <h4><span class="fas fa-hashtag"></span> <?php echo get_theme_mod('scuola_social_footersectionTesto'  );?></h4>       	
	            <ul class="list-inline social">

	              <?php //
	              get_MenuSocial();?>
	            </ul>
	        <?php   } ?>
	         </div>
	        </div>
	      </section>
	 
	 		<?php if ( is_active_sidebar( 'footer-widget-area' ) ) : ?>
	      <section>
	        <div class="row">
					<div class="container-fluid widget-area">
					   <div class="row xoxo">
					      <?php dynamic_sidebar( 'footer-widget-area' ); ?>
					   </div>
					</div>
	        </div>
	      </section>
			<?php endif; ?>
			<?php if ( is_active_sidebar( 'footer-sub-widget-area' ) ) : ?>
	      <section class="py-4 border-white border-top">
	        <div class="row">
					<div class="container-fluid widget-area">
					   <div class="row xoxo">
					      <?php dynamic_sidebar( 'footer-sub-widget-area' ); ?>
					   </div>
					</div>
	        </div>
	      </section>
			<?php endif; ?>
	    </div>
	  </div>
	  <div class="it-footer-small-prints clearfix">
	    <div class="container">
			<div class="row">
				<div class="col-md-8">
			      <h3 class="sr-only"><?php _e( 'Sezione Legale', 'wpscuola' );?></h3>
			      <?php if ( has_nav_menu( 'menu-footer-legale' ) ) {
			      	 wp_nav_menu( array( 'theme_location' => 'menu-footer-legale', 'container' => 'ul', 'menu_class' => 'nav it-footer-small-prints-list list-inline mb-0 d-flex flex-column flex-md-row', 'menu_id' =>"menu_legal")); 
					}?>
			    </div>
				<div class="col-md-4 text-right copyright">
					<small><?php echo sprintf( __( 'Copyright %1$s %2$s %3$s', 'wppa' ), '<span class="far fa-copyright"></span>', date( 'Y' ), esc_html( get_bloginfo( 'name' ) ) ); ?></small>
				</div>
	 		</div>	
	    </div>
	   	</div>
	</footer>
</div>
<?php wp_footer(); ?>
    <!-- HTML5shim per Explorer 8 -->
    <script src="<?php bloginfo('template_url'); ?>/static/js/modernizr.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/static/js/jquery.min.js"></script>

    <script src="<?php bloginfo('template_url'); ?>/static/js/popper.min.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/static/js/bootstrap-italia.min.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/static/js/tema.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/static/js/jquery-ui.min.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/static/js/i18n/datepicker-it.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/static/js/owl.carousel.min.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/inc/magnific-popup/jquery.magnific-popup.min.js"></script>
    <script type='text/javascript'>
    	var wpscuolajaxsec = "<?php echo wp_create_nonce('WPScuolaSecret');?>";
    	var ajaxurl = '/wp-admin/admin-ajax.php';
	</script>
<?php	echo get_theme_mod('scuola_footer_script');?>

</body>
</html>
