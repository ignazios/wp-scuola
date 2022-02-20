<?php
/*
 * ### SEZIONE Scuola ###
 * Mostra una sezione divisa in due parti uguali, il blocco di sinistra con i soggetti ed il blocco di destra con la presentazioen della scuola.

 *
 */
	 $Blocco1Immagine=get_theme_mod('scuola_scuola_Imgblocco1');
	 $Blocco1Titolo=get_theme_mod('scuola_scuola_Titoloblocco1');
	 $Blocco1Link=get_theme_mod('scuola_scuola_Linkblocco1');
	 $Blocco2Immagine=get_theme_mod('scuola_scuola_Imgblocco2');
	 $Blocco2Titolo=get_theme_mod('scuola_scuola_Titoloblocco2');
	 $Blocco2Link=get_theme_mod('scuola_scuola_Linkblocco2');
	 $Blocco3Immagine=get_theme_mod('scuola_scuola_Imgblocco3');
	 $Blocco3Titolo=get_theme_mod('scuola_scuola_Titoloblocco3');
	 $Blocco3Link=get_theme_mod('scuola_scuola_Linkblocco3');
	 $Blocco4Immagine=get_theme_mod('scuola_scuola_Imgblocco4');
	 $Blocco4Titolo=get_theme_mod('scuola_scuola_Titoloblocco4');
	 $Blocco4Link=get_theme_mod('scuola_scuola_Linkblocco4');
	 $ImmagineVideo=get_theme_mod('scuola_scuola_PrimaTabLinkVideoImg');
	 $Video=get_theme_mod('scuola_scuola_PrimaTabVideo');
	 $Didascalia=get_theme_mod('scuola_scuola_PrimaTabVideoDidascalia');
	 $ImgLinkPT=get_theme_mod('scuola_scuola_PrimaTabLinkImg');
	 $TestoLinkPT=get_theme_mod('scuola_scuola_PrimaTabLinkTesto');
	 $LinkPT=get_theme_mod('scuola_scuola_PrimaTabLink');
	 
	 $Eti_Prima=get_theme_mod('scuola_scuola_PrimaTabTesto');
	 $Pag_Prima=get_theme_mod('scuola_scuola_PrimaTabPagina');
	 $TestoLink_Prima=get_theme_mod('scuola_scuola_PrimaTabLeggiTutto');
	 if(!$Pag_Prima){
	 	$Link_Prima="#"; 
	 	$Testo_Prima="";	
	 }else{
	 	$Link_Prima=get_page_link($Pag_Prima);
	 	$Post=get_post( $Pag_Prima); 
	 	$Testo_Prima=apply_filters( 'the_content', $Post->post_content );
	 }
	 $Eti_Seconda=get_theme_mod('scuola_scuola_SecondaTabTesto');
	 $Pag_Seconda=get_theme_mod('scuola_scuola_SecondaTabPagina');
	 $TestoLink_Seconda=get_theme_mod('scuola_scuola_SecondaTabLeggiTutto');
	 if(!$Pag_Seconda){
	 	$Link_Seconda="#"; 
	 	$Testo_Seconda="";	 	
	 }else{
	 	$Link_Seconda=get_page_link($Pag_Seconda);
	 	$Post=get_post( $Pag_Seconda); 
	 	$Testo_Seconda=apply_filters( 'the_content', $Post->post_content );
	 }
	 $Eti_Terza=get_theme_mod('scuola_scuola_TerzaTabTesto');
	 $Pag_Terza=get_theme_mod('scuola_scuola_TerzaTabPagina');
	 $TestoLink_Terza=get_theme_mod('scuola_scuola_TerzaTabLeggiTutto');
	 if(!$Pag_Terza){
	 	$Link_Terza="#"; 
	 	$Testo_Terza="";	 	
	 }else{
	 	$Link_Terza=get_page_link($Pag_Terza);
	 	$Post=get_post( $Pag_Terza); 
	 	$Testo_Terza=apply_filters( 'the_content', $Post->post_content );
	 }
?>
<section class="scuola">
	<div id="Scuola" class="container mt-5">
	<h3 class="widget-title"><?php echo get_theme_mod('scuola_scuola_titolosezione');?></h3>
    <div class="row">
  		<div class="col-12 col-lg-3">
	  		<div class="row p-md-5">
	  			<div class="col-lg-12 col-6 col-sm-6">
	  				<a href="<?php echo $Blocco1Link;?>">
						<figure class="figure bg-primary rounded pb-2 text-white">
						  <img src="<?php echo wp_get_attachment_url($Blocco1Immagine);?>" alt="Logo <?php echo $Blocco1Titolo;?>" class="figure-img img-fluid rounded">
						  <figcaption class="figure-caption text-center font-weight-bold text-white"><?php echo $Blocco1Titolo;?></figcaption>
						</figure>	
					</a>			        
				</div>  			
	  			<div class="col-lg-12 col-6 col-sm-6">
	  				<a href="<?php echo $Blocco2Link;?>">
						<figure class="figure bg-primary rounded pb-2 text-white">
						  <img src="<?php echo wp_get_attachment_url($Blocco2Immagine);?>" alt="Logo <?php echo $Blocco2Titolo;?>" class="figure-img img-fluid rounded">
						  <figcaption class="figure-caption text-center font-weight-bold text-white"><?php echo $Blocco2Titolo;?></figcaption>
						</figure>
					</a>				        
				</div>  			
	  			<div class="col-lg-12 col-6 col-sm-6">
	  				<a href="<?php echo $Blocco3Link;?>">
						<figure class="figure bg-primary rounded pb-2 text-white">
						  <img src="<?php echo wp_get_attachment_url($Blocco3Immagine);?>" alt="Logo <?php echo $Blocco3Titolo;?>" class="figure-img img-fluid rounded">
						  <figcaption class="figure-caption text-center font-weight-bold text-white"><?php echo $Blocco3Titolo;?></figcaption>
						</figure>
					</a>				        
				</div>  			
	  			<div class="col-lg-12 col-6 col-sm-6">
	  				<a href="<?php echo $Blocco4Link;?>">
						<figure class="figure bg-primary rounded pb-2 text-white">
						  <img src="<?php echo wp_get_attachment_url($Blocco4Immagine);?>" alt="Logo <?php echo $Blocco4Titolo;?>" class="figure-img img-fluid rounded">
						  <figcaption class="figure-caption text-center font-weight-bold text-white"><?php echo $Blocco4Titolo;?></figcaption>
						</figure>
					</a>				        
				</div>  			
	  		</div>
  		</div>
 		<div class="col-12 col-lg-9">
			<nav>
			  <div class="nav nav-tabs auto" id="nav-tab" role="tablist">
			    <a class="nav-item nav-link active nav-item-ufficio" id="nav-tab1-tab" data-toggle="tab" href="#nav-tab1" role="tab" aria-controls="nav-tab1" aria-selected="true"><?php echo $Eti_Prima;?></a>
			    <a class="nav-item nav-link nav-item-ufficio" id="nav-tab2-tab" data-toggle="tab" href="#nav-tab2" role="tab" aria-controls="nav-tab2" aria-selected="false"><?php echo $Eti_Seconda; ?></a>
			    <a class="nav-item nav-link nav-item-ufficio" id="nav-tab3-tab" data-toggle="tab" href="#nav-tab3" role="tab" aria-controls="nav-tab3" aria-selected="false"><?php echo $Eti_Terza;?></a>
			  </div>
			</nav>
			<div class="tab-content" id="nav-tabContent">
			  <div class="tab-pane p-4 fade show active shadow" id="nav-tab1" role="tabpanel" aria-labelledby="nav-tab1-tab">
			  	<div class="BloccoScuola">
			  		<?php 	if($Video!=""){
			  			if ($ImmagineVideo!=""){?>
			  				<figure class="aligncenter size-full">
			  					<a href="https://www.youtube.com/watch?v=<?php echo $Video;?>" target="_blank" rel="noopener">
			  						<img loading="lazy" width="429" height="200" src="<?php echo wp_get_attachment_url($ImmagineVideo);?>" alt="" sizes="(max-width: 386px) 100vw, 429px" style="display: block;margin-left: auto!important;margin-right: auto!important;">
			  					</a>
			  				</figure>
			  		<?php }else{?>
			  		<div class="embed-responsive embed-responsive-16by9">
  						<iframe class="embed-responsive-item" title="Video YouTube" src="https://www.youtube.com/embed/<?php echo $Video;?>" allowfullscreen></iframe>
 					</div>
 					<?php }?>
  					<p class="text-center font-italic font-weight-semibold"><?php echo $Didascalia;?></p>
					<?php if($LinkPT!=""){?>
					<div>
					<?php if($ImgLinkPT!=""){?>
					
					<figure class="aligncenter size-full">
			  			<a href="<?php echo $LinkPT;?>" target="_blank" rel="noopener">
			  				<img loading="lazy" width="429" height="200" src="<?php echo wp_get_attachment_url($ImgLinkPT);?>" alt="" sizes="(max-width: 600px) 100vw, 429px" alt="Logo <?php echo $TestoLinkPT;?>" style="display: block;margin-left: auto!important;margin-right: auto!important;">
			  			</a>
			  		</figure>
					<p class="text-center font-italic font-weight-semibold"><?php echo $TestoLinkPT;?></p>
					<?php }else{?>
						<a href="<?php echo $LinkPT;?>" target="_blank"><?php echo $TestoLinkPT;?></a>
					<?php	}?>					
						</div>
					<?php	}
						}else 
								echo $Testo_Prima;?>
			  	</div>
			  	<a class="read-more" href="<?php echo $Link_Prima;?>">
            		<span class="fas fa-link p-1"></span>
            		<span class="text"><?php echo $TestoLink_Prima;?></span>
          		</a>
	        </div>
			  <div class="tab-pane p-4 fade shadow" id="nav-tab2" role="tabpanel" aria-labelledby="nav-tab2-tab">
			  	<div class="BloccoScuola">
			  		<?php echo $Testo_Seconda;?>
			  	</div>
			  	<a class="read-more" href="<?php echo $Link_Seconda;?>">
            		<span class="fas fa-link p-1"></span>
            		<span class="text"><?php echo $TestoLink_Seconda;?></span>
          		</a>
	        </div>
			  <div class="tab-pane p-4 fade shadow" id="nav-tab3" role="tabpanel" aria-labelledby="nav-tab3-tab">
			  	<div class="BloccoScuola">
			  		<?php echo $Testo_Terza;?>
			  	</div>
			  	<a class="read-more" href="<?php echo $Link_Terza;?>">
            		<span class="fas fa-link p-1"></span>
            		<span class="text"><?php echo $TestoLink_Terza;?></span>
          		</a>
	          </div>
			</div>
  		</div>
  	</div>
	</div>
</section>
