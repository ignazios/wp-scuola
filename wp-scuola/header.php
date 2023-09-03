<!DOCTYPE html>
<html lang="it" >
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="<?php echo get_bloginfo('description'); ?>">
    <meta name="referrer" content="same-origin">
	<?php $Script=get_theme_mod('scuola_header_script');if($Script!="") echo $Script.PHP_EOL;?>
    <script>
        window.__PUBLIC_PATH__ = "<?php bloginfo('template_url'); ?>/static/fonts";
        theme_directory = "<?php echo get_template_directory_uri() ?>";
    </script>
    <?php
    	$logo_scuola = wp_get_attachment_url(get_theme_mod('Scuola_Logo'));
	    if ( $logo_scuola ) {
		    	$ImgLogo=esc_url( $logo_scuola);
			} else {
				$ImgLogo=get_template_directory_uri() . '/images/logoStato.png';
	        } ?>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
    <link rel="icon" type="image/png" href="<?php echo esc_url($ImgLogo); ?>">  
    <?php wp_head(); ?>
 </head>

<body class="t-Pac" id="main">
    
<?php	if(get_theme_mod('scuola_cookies_attiva')){ ?>
		<div class="cookiebar">
		    <p><?php echo (get_theme_mod('scuola_cookies_testoMSG')!=""?get_theme_mod('scuola_cookies_testoMSG'):__( 'Questo sito utilizza cookie tecnici, analytics e di terze parti. <br>Proseguendo nella navigazione accetti l’utilizzo dei cookie.', 'wpscuola' ));?></p>
		    <div class="cookiebar-buttons">
		        <a href="<?php echo get_permalink(get_theme_mod('scuola_cookies_pulsSXlink'));?>" class="cookiebar-btn"><?php echo get_theme_mod('scuola_cookies_pulsSXText');?></a>
		        <button data-accept="cookiebar" title="Pulsante accettazione cookies" class="cookiebar-btn cookiebar-confirm"><?php echo get_theme_mod('scuola_cookies_accettaText');?><span class="sr-only"> i cookies</span></button>
		    </div>
		</div>
 <?php }?>
    
<div class="body_wrapper push_container clearfix" id="page_top">
	<div class="skiplinks">
		<a accesskey="2" class="sr-only sr-only-focusable" href="#main"><?php _e( 'Vai al contenuto', 'wpscuola' );?></a>
  		<a accesskey="3" class="sr-only sr-only-focusable" href="#sub_nav"><?php echo __('Vail al menu di navigazione','wpscuola'); ?></a>
  		<a accesskey="4" class="sr-only sr-only-focusable" href="#footer"><?php echo __('Vai al footer','wpscuola'); ?></a>
	</div>
         
    <header id="mainheader">
		<!-- Fascia piccola Amministrazione Afferente -->
		<section class="preheader">
          <div class="it-header-slim-wrapper" id="header-superiore">
             <div class="container">
               <div class="row">
                 <div class="col">
                   <div class="it-header-slim-wrapper-content">
                     <a class="d-lg-block navbar-brand" href="<?php echo get_theme_mod('Scuola_AmmAfferente_URL');?>" title="<?php echo __( 'Sito', 'wpscuola' )." ".get_theme_mod('Scuola_AmmAfferente_Nome');?>" target="_blank"> 
                        <img class="header-slim-img" alt="<?php echo  __( 'Logo', 'wpscuola' )." ".get_theme_mod('Scuola_AmmAfferente_Nome');?>" src="<?php echo wp_get_attachment_url(get_theme_mod('Scuola_AmmAfferente_Logo')); ?>">
                     </a>
                	</div>
                </div>
                <div class="col">
                <?php if(!get_theme_mod('Scuola_Login')):?>
                	<div class="it-header-slim-wrapper-content float-right">
					<?php
					 
						if(is_user_logged_in()){
							echo '<span class="fas fa-sign-out-alt pr-2"></span> ';
						}else{
							echo '<span class="fas fa-sign-in-alt pr-2"></span> ';
						}
						echo wp_loginout($_SERVER['REQUEST_URI'],FALSE );?>
					</div>
				<?php endif;?>
                 </div>
               </div>
             </div>
          </div>
		</section>
		<!-- Fine Fascia piccola Amministrazione Afferente -->

		<!-- Button Menu -->
		<button class="navbar-toggle menu-btn pull-left menu-left push-body jPushMenuBtn" title="navigazione mobile">
		    <span class="sr-only"><?php echo __('Attiva disattiva la navigazione','wpscuola'); ?></span>
		    <span class="icon-bar icon-bar1"></span>
		    <span class="icon-bar icon-bar2"></span>
		    <span class="icon-bar icon-bar3"></span>
		</button>
		<!-- Fine Button Menu -->

		<!-- Inizio Menu Mobile -->
		<nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left" id="menup">
		    <div class="cbp-menu-wrapper clearfix">
		        <div class="logo-burger">
		            <div class="logotxt-burger">
		                <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo bloginfo('name'); ?>"><?php echo bloginfo('name'); ?></a>
		            </div>
		        </div>

		        <p style="h2" class="sr-only"><?php echo __('Menu principale','wpscuola'); ?></p>

		            <?php   if(has_nav_menu('menu-principale')) {
		                        $menu = array(
		                            'theme_location' => 'menu-principale',
		                            'menu_id' => 'site-navigation',
		                            'menu_class' => 'nav navmenu'
		                        );
		                        echo strip_tags(wp_nav_menu($menu));
		                    }
		             ?>
		        
		        <!-- pulsante ricerca mobile -->
		        <div class="p_cercaMobile">
			        <div class="cerca">
						<form class="Form" method="get" role="search" action="<?php echo esc_url(home_url('/')); ?>">
						    <label class="sr-only" for="search_text_mobile"><?php _e("Ricerca all'interno del sito","wpscuola");?></label>
						    <input type="text" value="" name="s" required id="search_text_mobile" autocomplete="off">
						    <button class="btn btn-default btn-cerca pull-right" name="submit" title="<?php echo __('Avvia la ricerca','wpscuola'); ?>" aria-label="<?php echo __('Avvia la ricerca','wpscuola'); ?>">
						        <svg class="icona">
						        <use xlink:href="<?php bloginfo('template_url'); ?>/static/img/ponmetroca.svg#ca-search"></use>
						        </svg>
						    </button>
						</form>
					</div>
		        </div>
		        <!-- pulsante ricerca mobile -->

		        <ul class="list-inline socialmobile">
		            <?php if(HasSocial()): ?>
		                <li class="small list-inline-item"><?php echo get_theme_mod('scuola_social_footersectionTesto'); ?></li>
						<?php get_MenuSocial();?>
		            <?php endif; ?>
		        </ul>
		    </div>
		</nav>
		<!-- Fine Menu Mobile -->

		<!-- Inizio Intestazione -->
		<div class="container header">
		    <div class="row clearfix header-tablet">
		        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-7 amministrazione">
		            <div class="logoprint">
		                <h1>
							<img class="custom-logo" src="<?php echo $ImgLogo;?>"  alt="<?php echo esc_html( get_bloginfo( 'name' ) );?>">
		                    <?php echo bloginfo('name'); ?>
		                </h1>
		            </div>
		            <div class="logoimg">
		                <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo bloginfo('name'); ?>">
							<img class="custom-logo" src="<?php echo $ImgLogo;?>" alt="<?php echo esc_html( get_bloginfo( 'name' ) );?>">
		                </a>
		            </div>
		            <div class="logotxt">
		                <h1 class="pt-4 pt-md-4 pt-lg-3 pt-xl-0">
		                    <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo bloginfo('name'); ?>">
		                        <?php echo bloginfo('name'); ?>
		                    </a>
		                </h1>
		                <p class="d-none d-xl-block"><?php echo get_bloginfo('description');?></p>
		            </div>
		        </div>

		        <div class="header-social col-xl-4 col-lg-4 d-none d-lg-block d-md-none pull-right text-right">
				<!-- Inizio Social-->
		            <?php if(HasSocial()): ?>
		            <ul class="list-inline text-right social">
		            	<li class="small list-inline-item"><?php echo get_theme_mod('scuola_social_footersectionTesto'); ?></li>
		            		<?php get_MenuSocial();?>
		                           
		            </ul>
		            <?php endif; ?>
		            <!-- Fine Social-->
		        </div>

		        <div class="header-cerca col-xl-2 col-lg-2 col-md-4 col-sm-5 col-5 d-none d-lg-block d-md-none text-right">
		            <!-- Inizio Ricerca -->
		            <div class="cerca">
						<form class="Form" method="get" role="search" action="<?php echo esc_url(home_url('/')); ?>">
						    <label class="sr-only" for="search_text"><?php _e("Ricerca all'interno del sito","wpscuola");?></label>
						    <input type="text" value="" name="s" required id="search_text" autocomplete="off">
						    <button class="btn btn-default btn-cerca pull-right" name="submit" title="<?php echo __('Avvia la ricerca','wpscuola'); ?>" aria-label="<?php echo __('Avvia la ricerca','wpscuola'); ?>">
						        <svg class="icona">
						        <use xlink:href="<?php bloginfo('template_url'); ?>/static/img/ponmetroca.svg#ca-search"></use>
						        </svg>
						    </button>
						</form>
					</div>
		            <!-- Fine Ricerca -->
		        </div>
		    </div>
		</div>
		<!-- Fine Intestazione -->

		<section class="hidden-xs" id="sub_nav">

		    <div class="container">
		        <div class="row">
		            <div class="col-12">
		            
		                <nav class="navbar navbar-expand-sm has-megamenu">
		                    <div class="navbar-collapsable">
		                        <div class="close-div sr-only">
		                            <button class="btn close-menu" type="button">
		                                <svg class="icon icon-sm icon-light">
		                                <use xlink:href="<?php bloginfo('template_url'); ?>/static/svg/sprite.svg#it-close"></use>
		                                </svg><?php echo __('close','italiawp2'); ?>
		                            </button>
		                        </div>

		                        <div class="menu-wrapper">
		                        <?php 
		                            wp_nav_menu(array(
		                                'theme_location' => 'menu-principale',
		                                'menu_id' => 'menu-principale',
		                                'depth' => 4,
		                                'container' => '',
		                                'container_class' => '',
		                                'menu_class' => 'nav navbar-nav',
		                                'fallback_cb' => 'wp_bootstrap_navwalker::fallback',
		                                'walker' => new wp_bootstrap_navwalker()
		                            ));
		                         ?>
		                        </div>
		                    </div>
		                </nav>
		            </div>
		        </div>
		    </div>

		</section>
    </header>

    <main id="main_container">
<?php if ( !is_front_page() ) :?>
		<section id="breadcrumbs" role="main" class="container-fluid">
		   <div class="container-fluid">
		      <div class="row">
			      <div class="col-md-12">
	         		<?php custom_breadcrumbs();?>
				  </div>
			  </div>
			</div>
		</section>
<?php endif;?>
        