<?php
/**
 * Gestione Circolari - Funzioni Gestione Gruppi
 * 
 * @package Gestione Circolari
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @ver 2.7.3
 */
 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
  die('You are not allowed to call this page directly.'); 
}

//Gestione Gruppi Utenti
add_action( 'init', 						'wps_Crea_tassonomia_GruppoUtenti');
add_filter('manage_users_sortable_columns', 'wps_gruppi_user_sortable_columns' );
add_filter('request', 						'wps_gruppi_user_column_orderby' );
add_action('manage_users_custom_column', 	'wps_gruppi_add_custom_user_columns', 15, 3);
add_filter('manage_users_columns', 			'wps_gruppi_add_user_columns', 15, 1);
add_action( 'show_user_profile', 			'wps_visualizza_gruppo_utenti' );
add_action( 'edit_user_profile', 			'wps_visualizza_gruppo_utenti' );
add_action( 'personal_options_update', 		'wps_memorizza_gruppo_utenti' );
add_action( 'edit_user_profile_update', 	'wps_memorizza_gruppo_utenti' );

function wps_memorizza_gruppo_utenti( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ){
		return false;
	}
	$GruppiArray=array();
	foreach($_POST['Gruppo'] as $Gruppo){
		$GruppiArray[]=$Gruppo;
	}
	if (isset($GruppiArray)){
		update_user_meta( $user_id, 'gruppo', $GruppiArray);
	}
}
function wps_sort_terms_hierarchicaly(Array &$cats, Array &$into, $parentId = 0)
{
    foreach ($cats as $i => $cat) {
        if ($cat->parent == $parentId) {
            $into[$cat->term_id] = $cat;
            unset($cats[$i]);
        }
    }

    foreach ($into as $topCat) {
        $topCat->children = array();
        wps_sort_terms_hierarchicaly($cats, $topCat->children, $topCat->term_id);
    }
}
function wps_displayLivelloGruppo($gruppi,$GruppiUtente,$Livello){
	foreach($gruppi as $K=>$gruppo){
		if (in_array($K, $GruppiUtente)){
			$Selezionato= "checked";
		}else{
			$Selezionato="";
		}	

		echo '<p style="margin-bottom:10px;">'
		. '<input type="checkbox" name="Gruppo['.$K.']" value="'.$K.'" '.$Selezionato.' style="margin-left:'.($Livello*20).'px;">'.$gruppo->name
		.'</p>';
		if (count($gruppo->children)>0){
			wps_displayLivelloGruppo($gruppo->children,$GruppiUtente,$Livello+1);
		}
	}
}
function wps_visualizza_gruppo_utenti( $user ) { 
 $gruppiutenti=get_terms('gruppiutenti', array('hide_empty' => false));
 $gruppi = array();
 wps_sort_terms_hierarchicaly($gruppiutenti, $gruppi);
//  echo "<pre>";print_r($gruppi);echo "</pre>";
 $GruppiUtente=get_the_author_meta( 'gruppo', $user->ID );
  if(!is_array( $GruppiUtente )){
	 $GruppiUtente=array($GruppiUtente);
 }
?>
	<h3>Informazioni aggiuntive</h3>
	<table class="form-table">
		<tr>
			<th><label for="gruppo">Gruppi Utente</label></th>
			<td>
<?php	     if (current_user_can('create_users'))
     	{
				wps_displayLivelloGruppo($gruppi,$GruppiUtente,0);
			?>
				<span class="description">Per favore seleziona il gruppo di appartenenza dell'utente.</span>
<?php
		}else
			foreach($gruppi as $K=>$gruppo){
				if (in_array($K, $GruppiUtente)) 
					echo $gruppo->name."\n";
			}
?>
			</td>
		</tr>
	</table>
<?php }

function wps_gruppi_add_user_columns( $defaults ) {
 	$defaults['gruppo'] = "Gruppo/i";
     return $defaults;
}
function wps_gruppi_add_custom_user_columns($value, $column_name, $id) {
      if( $column_name == 'gruppo' ) {	
      	$IDGruppo=array();
	  	if(($IDG=get_the_author_meta( 'gruppo', $id ))==NULL)
	  		$IDGruppo[]=-1;
	  	else
	  		$IDGruppo=$IDG;
//	  	echo "U=".$id." - g=".get_the_author_meta( 'gruppo', $id )."<br />";
//echo "<pre>";var_dump($IDGruppo);echo "</pre>";
		$gruppiutenti=get_terms('gruppiutenti', array('hide_empty' => FALSE,'include'=>$IDGruppo));
//		echo "<pre>";var_dump($gruppiutenti);echo "</pre>";
		$GruppiUtente="";
		foreach($gruppiutenti as $gruppo){
			$GruppiUtente.=$gruppo->name.", ";
		}
		return $GruppiUtente==""?"--":substr($GruppiUtente,0,-2);
      }
 }

function wps_gruppi_user_sortable_columns( $columns ) {
	$columns['gruppo'] = 'Gruppo';
	return $columns;
}

function wps_gruppi_user_column_orderby( $vars ) {
 if ( isset( $vars['orderby'] ) && 'gruppo' == $vars['orderby'] ) {
 			$vars = array_merge( $vars, array(
			'meta_key' => 'gruppo',
			'orderby' => 'meta_value',
			'order'     => 'asc'
		) );
	}
	return $vars;
}
/**
* Tassonomia personalizzata Gruppi Utenti
* 
*/
function wps_Crea_tassonomia_GruppoUtenti() 
{
	// Register Custom Taxonomy
	$labels = array(
		'name'                       => _x( 'Gruppi Utenti', 'Taxonomy General Name', 'wpscuola' ),
		'singular_name'              => _x( 'Gruppo Utenti', 'Taxonomy Singular Name', 'wpscuola' ),
		'menu_name'                  => __( 'Gruppi', 'wpscuola' ),
		'all_items'                  => __( 'Tutti i Gruppi', 'wpscuola' ),
		'parent_item'                => __( 'Gruppo padre', 'wpscuola' ),
		'parent_item_colon'          => __( 'Gruppo padre:', 'wpscuola' ),
		'new_item_name'              => __( 'Nuovo Gruppo', 'wpscuola' ),
		'add_new_item'               => __( 'Aggiungi nuovo Gruppo', 'wpscuola' ),
		'edit_item'                  => __( 'Modifica Gruppo', 'wpscuola' ),
		'update_item'                => __( 'Aggiorna Gruppo', 'wpscuola' ),
		'view_item'                  => __( 'Visualizza Gruppo', 'wpscuola' ),
		'separate_items_with_commas' => __( 'Gruppi separati da virgola', 'wpscuola' ),
		'add_or_remove_items'        => __( 'Aggiungi o rimuovi Gruppi', 'wpscuola' ),
		'choose_from_most_used'      => __( 'Scegli tra i più usati', 'wpscuola' ),
		'popular_items'              => __( 'Gruppi popolari', 'wpscuola' ),
		'search_items'               => __( 'Cerca Gruppi', 'wpscuola' ),
		'not_found'                  => __( 'Gruppo non trovato', 'wpscuola' ),
		'no_terms'                   => __( 'Nessun Gruppo', 'wpscuola' ),
		'items_list'                 => __( 'Elenco Gruppi', 'wpscuola' ),
		'items_list_navigation'      => __( 'Navigazione elenco Gruppi', 'wpscuola' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest' 				 => true,
		'rewrite' => array('with_front' => true,'slug' => 'destinatari'),
		'capabilities' => array(
			'manage_terms' => 'edit_users', // Using 'edit_users' cap to keep this simple.
			'edit_terms'   => 'edit_users',
			'delete_terms' => 'edit_users',
			'assign_terms' => 'read')
	);
	register_taxonomy( 'gruppiutenti', array( 'circolari_scuola' ), $args );

}
?>