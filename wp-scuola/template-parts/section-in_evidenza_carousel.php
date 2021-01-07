<?php
/*
 * ### SEZIONE Scuola ###
 * Mostra una sezione divisa in due parti uguali, il blocco di sinistra con i soggetti ed il blocco di destra con la presentazioen della scuola.

 *
 */
	$CatEv=get_theme_mod( "scuola_comeevidenza_categoria",get_option('default_category'));
	$NumPost=(int)get_theme_mod( "scuola_comeevidenza_numart","5");
	$args = array(	'posts_per_page'	  => 1,
        			'post__in' 			  => get_option( 'sticky_posts' ),
        			'category'	  		  => $CatEv,
        			'ignore_sticky_posts' => 1,
					'orderby'     		  => 'date',
					'order'        		  => 'DESC');	
	$PostsInEvidenza=get_posts($args);
	$PostsInEvidenza=$PostsInEvidenza[0];
	if($PostsInEvidenza){
		$args = array(	'numberposts' 	=> $NumPost,
						'category'	  	=> $CatEv,
						'post__not_in' 	=> array($PostsInEvidenza->ID),
						'orderby'     	=> 'date',
	        			'order'       	=> 'DESC');	
	}else{
		$args = array(	'numberposts' 	=> $NumPost+1,
						'category'	  	=> $CatEv,
						'orderby'     	=> 'date',
	        			'order'       	=> 'DESC');			
	}
	$Posts=get_posts($args);
	if ($Posts Or $PostsInEvidenza ) {
		if($PostsInEvidenza){
//			var_dump($PostsInEvidenza);
			$Image=get_the_post_thumbnail_url($PostsInEvidenza->ID);
			$IDImgEvidenza=get_post_thumbnail_id($PostsInEvidenza->ID);
			$Title=$PostsInEvidenza->post_title;
			$Except=($PostsInEvidenza->post_excerpt==""?get_the_excerpt($PostsInEvidenza->ID):$PostsInEvidenza->post_excerpt);
			$PermaLink=get_permalink($PostsInEvidenza->ID);
		}else{
			$Image=get_the_post_thumbnail_url($Posts[0]->ID);
			$IDImgEvidenza=get_post_thumbnail_id($Posts[0]->ID);
			$Title=$Posts[0]->post_title;
			$Except=($Posts[0]->post_excerpt==""?get_the_excerpt($Posts[0]->ID):$Posts[0]->post_excerpt);
			$PermaLink=get_permalink($Posts[0]->ID);
			array_shift($Posts);		
		}
		if(isset($Image) And $Image===FALSE){
			$IDImgEvidenza=get_theme_mod('scuola_DefautlImg');
			$Image=wp_get_attachment_url(get_theme_mod('scuola_DefautlImg'));
		}
		$ImageTitle = get_post($IDImgEvidenza)->post_title; //The Title
		$ImageAlt = get_post_meta($IDImgEvidenza, '_wp_attachment_image_alt', TRUE); //The Caption
		$ImageDescription = get_post($IDImgEvidenza)->post_content; // The Description	


	//echo "<pre>";var_dump($Posts);echo "</pre>";
	?>
<div>
	 <div class="it-carousel-wrapper it-carousel-landscape-abstract-three-cols">
	  <div class="it-header-block">
	    <div class="it-header-block-title">
	      <h2 class="widget-title pl-3"><?php _e('In evidenza', 'wpscuola');?></h2>
	    </div>
	  </div>
	   <div class="row">
	    <div class="col-sm-12 col-md-4 col-lg-3">
	         <div class="it-single-slide-wrapper">
		     <!--start card-->
		    <div class="card-wrapper card-space p-3">
		      <div class="card card-bg card-big no-after border border-primary rounded">
		      <div class="flag-icon"></div>
		        <div class="card-body p-2">
<?php if(get_theme_mod("scuola_comeevidenza_visimevidenza")):?>
		            <div class="img-responsive p-2">
		              <div class="img-wrapper img-carousel-evidenza">
	<?php if(isset($Image) And $Image!==FALSE) :?>
		              	<img src="<?php echo $Image;?>" title="<?php echo $ImageTitle;?>" alt="<?php echo $ImageAlt;?>"/>
	<?php endif;?>
		              </div>
		            </div>
		            <div class="p-2">
			          <h3 class="card-title card-title-fix h4"><?php echo $Title;?></h3>
			          <div class="it-card-footer">
			          	<a class="read-more" href="<?php echo $PermaLink;?>">
			              <span class="text"><span class="fas fa-link p-1"></span> <?php _e('Leggi articolo', 'wpscuola');?></span>
			            </a>
			          </div>
			        </div>
<?php else:?>
 					<div class="p-2" style="min-height: 75px;">
 						<h3 class="card-title h4"><?php echo $Title;?></h3>
 					</div>
		            <div class="p-2">
			          <p class="card-text" style="min-height: 100px;"><?php echo $Except;?></p>
			          <div class="it-card-footer">
			          	<a class="read-more" href="<?php echo $PermaLink;?>">
			              <span class="text"><span class="fas fa-link p-1"></span> <?php _e('Leggi articolo', 'wpscuola');?></span>
			            </a>
			          </div>
			        </div> 					
<?php endif;?>
		        </div>
		      </div>
		    </div>
		    <!--end card-->
		    </div>
		</div>
	    <div class="col-sm-12 col-md-8 col-lg-9">
			<div class="it-carousel-all owl-carousel">
	<?php
			foreach($Posts as $Post){
				$Image=get_the_post_thumbnail_url($Post->ID);
				if(isset($Image) And $Image===FALSE){
						$IDImgEvidenza=get_theme_mod('scuola_DefautlImg');
						$Image=wp_get_attachment_url(get_theme_mod('scuola_DefautlImg'));
				}else{
					$IDImgEvidenza=get_post_thumbnail_id($Post->ID);
				}
				$ImageTitle = get_post($IDImgEvidenza)->post_title; //The Title
				$ImageAlt = get_post_meta($IDImgEvidenza, '_wp_attachment_image_alt', TRUE); //The Caption
				$ImageDescription = get_post($IDImgEvidenza)->post_content; // The Description				
				$Title=$Post->post_title;
				$Except=($Post->post_excerpt==""?get_the_excerpt($Post->ID):$Post->post_excerpt);
				$PermaLink=get_permalink($Post->ID);
	?>
		    	<div class="it-single-slide-wrapper">
		      <!--start card-->
			    	<div class="card-wrapper card-space pl-1 pr-1">
			      		<div class="card card-bg card-big no-after border-bottom-card">
			        		<div class="card-body p-2">
<?php if(get_theme_mod("scuola_comeevidenza_visimevidenza")):?>
			            		<div class="img-responsive p-2">
			              			<div class="img-wrapper img-carousel">
	<?php if(isset($Image) And $Image!==FALSE) :?>
			              				<img src="<?php echo $Image;?>" title="<?php echo $ImageTitle;?>" alt="<?php echo $ImageAlt;?>"/>
			              				
	<?php endif;?>		              				
			              			</div>
			            </div>
			            <div class="p-4">
				        	<h4 class="card-title card-title-fix"><?php echo $Title;?></h4>
				          	<div class="it-card-footer">
				          		<a class="read-more" href="<?php echo $PermaLink;?>">
				              		<span class="text"><span class="fas fa-link p-1"></span> <?php _e('Leggi articolo', 'wpscuola');?></span>
				            	</a>
					    	</div>
					    </div>
<?php else:?>
 					<div class="pt-2 pl-2" style="min-height: 100px;">
 						<h3 class="card-title h4"><?php echo $Title;?></h3>
 					</div>
		            <div class="p-4">
			          <p class="card-text" style="min-height: 100px;"><?php echo $Except;?></p>
			          <div class="it-card-footer">
			          	<a class="read-more" href="<?php echo $PermaLink;?>">
			              <span class="text"><span class="fas fa-link p-1"></span> <?php _e('Leggi articolo', 'wpscuola');?></span>
			            </a>
			          </div>
			        </div> 					
<?php endif;?>
			        </div>
			      </div>
			    </div>
		    <!--end card-->
		       </div>
	<?php 		} ?>
			</div>
		</div>
	  </div>
	</div>
<?php }