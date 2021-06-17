<?php
/*
 * ### SEZIONE Scuola ###
 * Mostra una sezione divisa in due parti uguali, il blocco di sinistra con i soggetti ed il blocco di destra con la presentazioen della scuola.

 *
 */
	$NumPost=(int)get_theme_mod( "scuola_eventi_numart","3");
	$args = array(	'posts_per_page'	  => $NumPost,
        			'post_type'			  => 'scuola_eventi',
        			'ignore_sticky_posts' => 1,
        			'meta_key'            => 'eventi_scadenza_evento',
        			'meta_value'          => date( "Y-m-d" ), 
        		    'meta_compare'        => '>',
					'orderby'     		  => 'date',
					'order'        		  => 'DESC');	
	$PostsEventi=get_posts($args);
	//echo "<pre>";var_dump($Posts);echo "</pre>";
	?>
	
<!-- Visit https://nicolaskadis.info/posts/pure-css-flip-cards-using-bootstrap-4-and-css-grid-no-js for an explanation -->
<?php if(count($PostsEventi)>0):?>
<!-- Eventi -->
<section id="eventi" class="pb-2">
<div id="newflip" class="container">
  <h2 class="widget-title pl-3 text-center"><?php _e('Eventi','wpscuola');?></h2>
  <div class="row">
<?php
	$Size="";
	$ColBreak=3;
	switch(count($PostsEventi)){
		case 1:?>
		   <div class="col-lg-4 card-container"></div>
		<?php 
			$ColBreak=4;		
			break;
		case 2:
			$ColBreak=6;
			$Size="w-50";
			break;
		case 3:
			$ColBreak=4;
			break;
 	}
	foreach($PostsEventi as $Post){
//	var_dump($Post);
			$Image=get_the_post_thumbnail_url($Post->ID);
			$IDImgEvidenza=get_post_thumbnail_id($Post->ID);
			$ImageTitle = get_post($IDImgEvidenza)->post_title; //The Title
			$ImageAlt = get_post_meta($IDImgEvidenza, '_wp_attachment_image_alt', TRUE); //The Caption
			$ImageDescription = get_post($IDImgEvidenza)->post_content;
			$eventi_link_titolo = get_post_meta( $Post->ID, 'eventi_link_titolo', true );
			$eventi_link_evento     = get_post_meta( $Post->ID, 'eventi_link_evento', true );
			$eventi_target_evento   = get_post_meta( $Post->ID, 'eventi_target_evento', true );?>
    <!-- Card -->
    <div class="col-lg-<?php echo $ColBreak;?> card-container">
      <div class="card-flip">
        <!-- Card Front -->
        <div class="card front shadow">
			<img src="<?php echo $Image;?>" title="<?php echo $ImageTitle;?>" alt="<?php echo $ImageAlt;?>"class="card-img-top rounded mx-auto d-block <?php echo $Size;?>">
        </div>
        <!-- End Card Front -->

        <!-- Card Back -->
        <div class="card back">
          <div class="card-block">
            <h4 class="card-title"><?php echo $Post->post_title;?></h4>
            <p class="card-text"><?php echo (isset($Post->post_excerpt)?$Post->post_excerpt:" ");?></p>
            <a href="<?php echo get_permalink($Post->ID);?>" class="btn btn-primary"><span class="fas fa-link p-1"></span> <?php _e('Dettagli evento','wpscuola');?></a>
<?php if( isset($eventi_link_titolo) And $eventi_link_titolo!=""):?>
            <a href="<?php echo $eventi_link_evento;?>" class="btn btn-primary" target="<?php echo $eventi_target_evento;?>"><?php echo $eventi_link_titolo;?></a>
<?php endif;?>
          </div>
        </div>
        <!-- End Card Back -->
      </div>
    </div>
    <!-- End Card -->
<?Php }?>
  </div>
</div>	
</section>
<?php 
	endif;