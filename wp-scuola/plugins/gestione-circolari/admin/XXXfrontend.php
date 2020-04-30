<?php
/**
 * Gestione Circolari
 * 
 * @package Gestione Circolari
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 2.7.3
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

$ret=Lista_Circolari($Anno,$Mese,$atts);

add_filter( 'posts_where' , 'posts_where' );

function posts_where( $where ) {

	global $wpdb;
	$DaEliminare="AND ( ( ".$wpdb->postmeta.".meta_key = '_visibilita' AND ".$wpdb->postmeta.".meta_value IN ('p') ))";
	$NewStrWhere=preg_replace('~[[:cntrl:]]~', '', $where);
	$NewStrWhere= str_replace("   "," ", $NewStrWhere);
	$NewStrWhere= str_replace($DaEliminare, "",$NewStrWhere);
	$NewStrWhere= preg_replace("/AND/", "OR (",$NewStrWhere,1);
//	echo $DaEliminare." <br /><br /> ".preg_replace('~[[:cntrl:]]~', '', $where)." <br /><br /> ". $where."<br /><br /> ".$NewStrWhere."<br /><br />"; die();
	$current_user = wp_get_current_user();
	if ( 0 == $current_user->ID ) {
		return $where;
	} 
	
	$GruppoUtente=get_user_meta($current_user->ID, "gruppo", true);
	
	if ( 0 == count($GruppoUtente)) {
		return $where;
	} 

	if (is_array($GruppoUtente)){
		$GruppiU="(";
		foreach($GruppoUtente as $Gruppo){
			$GruppiU.=$Gruppo.",";
		}
		$GruppoUtente= substr($GruppiU, 0, strlen($GruppiU)-1).")";		
	}
	$NewStrWhere.='AND ('.$wpdb->postmeta.'.meta_value IN(\'d\') AND EXISTS (SELECT '.$wpdb->terms.'.term_id FROM '.$wpdb->terms.' 
			inner join '.$wpdb->term_taxonomy.' on '.$wpdb->terms.'.term_id= '.$wpdb->term_taxonomy.'.term_id 
			inner join '.$wpdb->term_relationships.' on  '.$wpdb->term_relationships.'.term_taxonomy_id= '.$wpdb->term_taxonomy.'.term_taxonomy_id 
			inner join '.$wpdb->posts.' pts on '.$wpdb->term_relationships.'.object_id=pts.ID 
			WHERE  '.$wpdb->term_taxonomy.'.taxonomy="gruppiutenti" and pts.ID='.$wpdb->posts.'.ID and '.$wpdb->terms.'.term_id in ('.$GruppoUtente.'))))';
//	echo $where.$NewStrWhere;die();
	return $where.$NewStrWhere;
}

function Lista_Circolari($Anno,$Mese,$Attributi){
	ob_start();
	$mesecorrente = date('n');
	$annocorrente= date('y');
	$NumCircolari=-1;
	if(is_numeric($Attributi['numcircolari'])){
		$NumCircolari=$Attributi['numcircolari'];
	}
	$MQ=array(array('key' => '_visibilita',
					'value' => array('p'),
					'compare' => 'IN'));
	if($Attributi['archivio']=="Mese" Or (isset($_REQUEST['Anno']) or isset($_REQUEST['Mese']))){
		$args = array( 
					   'post_type' => array('circolari_scuola'),
					   'year' => $Anno,
					   'monthnum' => $Mese,
					   'posts_per_page'  => $NumCircolari,
					   'post_status' => 'publish',
						'meta_query' => $MQ);	
	}else{
		$args = array( 
					   'post_type' => array('circolari_scuola'),
					   'posts_per_page'  => $NumCircolari,
					   'post_status' => 'publish',
						'meta_query' => $MQ,
					   'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1 ));		
	}
	$Circolari = new WP_Query( $args );
	
	if ($Circolari->post_count==0){
		echo'<h3>Non risultano circolari per '.circ_MeseLettere($mesecorrente).' '.$annocorrente.' verranno visualizzate le ultime 5</h3>';
		$args = array(
		       'post_type' => array('circolari_scuola'),
			   'posts_per_page'  => 5,
			   'post_status' => 'publish',
			   'meta_query' => $MQ
			);	
		$Circolari = new WP_Query( $args );
	}
	/*
	echo "<pre>";
	print_r($Circolari);
	echo "</pre>";die();
	*/
	echo' <div>';
	foreach($Circolari->posts as $post) {
		$visibilita=get_post_meta($post->ID, "_visibilita");
	 	if(count($visibilita)==0)
	 		$visibilita="p";
	 	else 
	 		$visibilita=$visibilita[0];
	//	if ((Is_Circolare_per_User($post->ID) and $visibilita=="d") or $visibilita=="p"){
			$Elenco=GetEencoDestinatari($post->ID);
			echo'
			<div style="padding:3px;">';
			$numero=get_post_meta($post->ID, "_numero",TRUE);
			$anno=get_post_meta($post->ID, "_anno",TRUE);
			echo'
				<h3><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>
				</h3>
				<div style="font-weight: bold;font-size:0.8em;">Del '.FormatDataItaliano($post->post_date).' Numero '.$numero.'_'.$anno.'</div> 
				<i class="fa fa-folder-open" aria-hidden="true"></i> '.$post->post_type;	
			if ($post->post_type=="circolari")
				echo' <i class="fa fa-users" aria-hidden="true"></i> '.$Elenco;
			if (!post_password_required( $post->ID ))
				$riassunto=	$post->post_excerpt;
			else{
				$riassunto="";
			}
			if (!empty($post->post_password))
				echo' <i class="fa fa-lock" aria-hidden="true"></i> Contenuto Protetto';	
	//		if (Is_Circolare_per_User($post->ID))
	//	echo ">>>>>>>>>>>>>>>>>>>>>>>>>><<".$post->ID."<>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><br />";return;
			if (Is_Circolare_Da_Firmare($post->ID))
				if (!Is_Circolare_Firmata($post->ID)) {
					$ngiorni=Get_scadenzaCircolare($post->ID,"",True);					
					if(Is_Circolare_Scaduta($post->ID)){
						echo' <i class="fa fa-pencil" aria-hidden="true" style="color:red;"></i> Scaduta e non Firmata ';						
					}else{
						switch ($ngiorni){
							case -1:							
								$entro="";							
								break;													
							case 0:
								$entro="entro OGGI";
								break;
							case 1:
								$entro="entro DOMANI";
								break;
							default:
								$entro="entro $ngiorni giorni";
								break;
						}
						$sign=get_post_meta($post->ID, "_sign",TRUE);
						if ($sign!="Firma")
							$Tipo="Esprimere adesione $entro";
						else
							$Tipo="Firmare $entro";
						echo' <i class="fa fa-pencil" aria-hidden="true" style="color:red;"></i> '.$Tipo;	
				}			
			}else{
				echo' <i class="fa fa-pencil" aria-hidden="true" style="color:blue;"></i> Firmata';				
			}
			if(!Is_Circolare_Pubblica($post->ID)){
				echo' <span style="background-color: #FFFFB0;color:red;"><i class="fa fa-hand-paper-o" aria-hidden="true"></i> Riservato</span>';								
			}
				echo'	

				<div style="margin-bottom:5px;">
					<em>'.$riassunto .'</em>
				</div>
				<hr />
			</div>';
	//	}
	}
	echo '
			</div>
			<div style="clear:both"></div>';
	/*echo "<pre>";
	print_r($Circolari);
	echo "</pre>";die();
	*/
	if( $Circolari->max_num_pages <= 1 Or $Attributi['archivio']=="Mese")
		return ob_get_clean();
	$links	=array();
    $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
    $max   = intval( $Circolari->max_num_pages );
    /**    Add current page to the array */
	$cp=get_query_var( 'paged' )==0?1:get_query_var( 'paged' ) ;

	echo '<div class="circpaging"><ul class="paginazione">';

	/** Prima Pagina */
    if( $Circolari->max_num_pages > 2 and $cp>1){
		echo sprintf( '<li><a href="%s">%s</a></li>' , esc_url( get_pagenum_link( 1 ) ), '<i class="fa fa-fast-backward" aria-hidden="true"></i>' );	
	}else{
		echo sprintf( '<li>%s</li>', '<i class="fa fa-fast-backward disabled" aria-hidden="true"></i>' );			
	}
    /**    Pagina Precedente */
    if ( get_previous_posts_link() ){
        echo sprintf( '<li>%s</li>', get_previous_posts_link("<i class=\"fa fa-backward\" aria-hidden=\"true\"></i>") );		
	}else{
        echo sprintf( '<li>%s</li>', "<i class=\"fa fa-backward disabled\" aria-hidden=\"true\"></i>" );			
	}

    /**   Pagina Successiva */
	echo sprintf( '<li>%s/%s</li>', $cp, $Circolari->max_num_pages  );
    if ( get_query_var( 'paged' ) < $Circolari->max_num_pages  ){
        echo sprintf( '<li><a href="%s">%s</a></li>', esc_url( get_pagenum_link( $cp+1)),"<i class=\"fa fa-forward\" aria-hidden=\"true\"></i>" ); 		
	}else{
		echo sprintf( '<li>%s</li>', "<i class=\"fa fa-forward disabled\" aria-hidden=\"true\" style=\"margin-left:5px;\"></i>" ); 	
	}
	/**   Ultima Pagina */
	if( $Circolari->max_num_pages > 2 and $cp<$Circolari->max_num_pages){
        echo sprintf( '<li><a href="%s">%s</a></li>',  esc_url( get_pagenum_link( $max ) ), "<i class=\"fa fa-fast-forward\" aria-hidden=\"true\"></i>" );
    }else{
		echo sprintf( '<li>%s</a></li>', "<i class=\"fa fa-fast-forward disabled\" aria-hidden=\"true\"></i>" );
	}

    /**    Next Post Link */

    echo '</ul></div>';

return ob_get_clean();
}
?>