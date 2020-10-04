<?php
/*
 * ### SEZIONE Comunicazioni in evidenza ###
 * Mostra una sezione su unica colonna con le ultime comunicazioni.

 *
 */
?>
<section id="Comunicazioni">
	<div class="container shadow clearfix">
		<h3 class="widget-title"><?php _e('Ultime Comunicazioni', 'wpscuola');?></h3>
		<div class="col-12">
<?php
    $limit = get_theme_mod('ultime-comunicazioni_num_elementi');
    $showallarchivio = get_theme_mod('ultime-comunicazioni_leggiTutto');
	$NoCategorie=get_theme_mod('ultime-comunicazioni_CatEscludere');
			$Anno=date("Y");
			$Mese=date("m");
			$CA=array();
			$Escudere=false;
			if($NoCategorie!=""){
				$InfoCatDaEscludere=explode(";",$NoCategorie);
				foreach($InfoCatDaEscludere as $InfoRiga){
					$Riga=explode(",",$InfoRiga);
					$CA[]=$Riga[0];
					if ($Riga[1])
						$CA=array_merge($CA,get_term_children(intval($Riga[0]),'category'));
				}
			}
			if (count($CA)>0)
				$Escudere=true;
            global $post,$wpdb;
			if ($Escudere){
				$Param = array('year' => $Anno,'monthnum' => $Mese, 'category__not_in' => $CA, 'posts_per_page'=>$limit);
			}else
				$Param = array('year' => $Anno,'monthnum' => $Mese, 'posts_per_page'=>$limit);
			$myposts = get_posts($Param);
			if(count($myposts)==0){
				if ($Escudere)
					$Param = array('posts_per_page'=>$limit, 'category__not_in' => $CA ,'tag__not_in' => $TA );
				else
					$Param = array('posts_per_page'=>$limit);
			}
	$paged = array ( 'paged' => ( get_query_var('paged') ) ? get_query_var('paged') : 1);

	$Param = array_merge($Param,$paged);
    $postslist = new WP_Query( $Param );
    if ( $postslist->have_posts() ) :
        while ( $postslist->have_posts() ) : $postslist->the_post(); 
        	get_template_part( 'template-parts/section', 'art_list-item' );
         endwhile;  
        wp_reset_postdata();
    endif;
    if ($showallarchivio){
?>	<div class="row float-right">
			<a class="read-more" href="<?php echo esc_url(home_url()) .'/comunicazioni';?>"><span class="fas fa-link p-1"></span> <span class="text"><?php _e('Mostra tutte le comunicazioni', 'wpscuola');?></span>
          	</a>
    </div>						
<?php	} ?>				
		</div>
	</div>
</section>
