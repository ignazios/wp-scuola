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
	if ($Posts ) {
		if($PostsInEvidenza){
			$Image=get_the_post_thumbnail_url($PostsInEvidenza->ID);
			$IDImgEvidenza=get_post_thumbnail_id($PostsInEvidenza->ID);
			$Title=$PostsInEvidenza->post_title;
			$PermaLink=get_permalink($PostsInEvidenza->ID);
		}else{
			$Image=get_the_post_thumbnail_url($Posts[0]->ID);
			$IDImgEvidenza=get_post_thumbnail_id($Posts[0]->ID);
			$Title=$Posts[0]->post_title;
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
	 <div class="it-carousel-wrapper it-carousel-landscape-abstract-three-cols">
	  <div class="it-header-block">
	    <div class="it-header-block-title">
	      <h2 class="no_toc pl-3">In evidenza</h2>
	    </div>
	  </div>
	   <div class="row">
	    <div class="col-sm-12 col-md-4 col-lg-3">
	         <div class="it-single-slide-wrapper">
		     <!--start card-->
		    <div class="card-wrapper card-space">
		      <div class="card card-bg card-big no-after border border-primary rounded">
		      <div class="flag-icon"></div>
		        <div class="card-body p-2">
		            <div class="img-responsive p-2">
		              <div class="img-wrapper img-carousel-evidenza">
	<?php if(isset($Image) And $Image!==FALSE) :?>
		              	<img src="<?php echo $Image;?>" title="<?php echo $ImageTitle;?>" alt="<?php echo $ImageAlt;?>" longdesc="<?php echo $ImageDescription;?>">
	<?php endif;?>
		              </div>
		            </div>
		            <div class="p-2">
			          <h5 class="card-title card-title-fix"><?php echo $Title;?></h5>
			          <div class="it-card-footer">
			          	<a class="read-more" href="<?php echo $PermaLink;?>">
			              <span class="text"><i class="fas fa-link p-1"></i> Leggi articolo</span>
			            </a>
			          </div>
			        </div>
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
				$PermaLink=get_permalink($Post->ID);
	?>
		    	<div class="it-single-slide-wrapper">
		      <!--start card-->
			    	<div class="card-wrapper card-space">
			      		<div class="card card-bg card-big no-after border-bottom-card">
			        		<div class="card-body p-2">
			            		<div class="img-responsive p-2">
			              			<div class="img-wrapper img-carousel">
	<?php if(isset($Image) And $Image!==FALSE) :?>
			              				<img src="<?php echo $Image;?>" title="<?php echo $ImageTitle;?>" alt="<?php echo $ImageAlt;?>" longdesc="<?php echo $ImageDescription;?>">
	<?php endif;?>		              				
			              			</div>
			            </div>
			            <div class="p-4">
				        	<h5 class="card-title card-title-fix"><?php echo $Title;?></h5>
				          	<div class="it-card-footer">
				          		<a class="read-more" href="<?php echo $PermaLink;?>">
				              		<span class="text"><i class="fas fa-link p-1"></i> Leggi articolo</span>
				            	</a>
					    	</div>
					    </div>
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