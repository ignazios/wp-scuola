<?php
/**
 * Prenotazioni
 * Classe gestione Spazi
 * @package Prenotazioni
 * @author Scimone Ignazio
 * @copyright 2014-2099
 * @version 1.6.6
 */

class Spazi{
	private $Descrizione;
	private $Foto;
	private $IdPrimo;
	private $Riservato=array("Giorno" => 1,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 2,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 3,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 4,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 5,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 6,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 7,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)
							 );
	function __construct(){
		add_action( 'init', array(&$this,'crea_custom_spazi'));
		add_filter( 'post_updated_messages', array(&$this,'update_spazi_messages'));
		add_action( 'save_post', array(&$this,'save_spazi_dettagli'));
		add_action('add_meta_boxes',array(&$this,'new_spazi_box'));
	}
	function new_spazi_box(){
		add_meta_box('reserved', 'Impostazione Risorsa Riservata', array(&$this,'spazio_crea_box_riservato'), 'spazi', 'advanced', 'high');
		add_meta_box('sigla', 'Sigla Spazio', array(&$this,'spazio_crea_box_sigla'), 'spazi', 'advanced', 'high');
	}	
	function crea_custom_spazi() {
	register_post_type('spazi', array(
					   'labels' => array(
					   'name' => __( 'Spazi' ),
					   'singular_name' => __( 'Spazio' ),
					   'add_new' => __( 'Aggiungi Spazio' ),
					   'add_new_item' => 'Aggiungi nuovo Spazio',
					   'edit' => __( 'Modifica' ),
					   'edit_item' => __( 'Modifica Spazio' ),
					   'new_item' => __( 'Nuovo Spazio' ),
					   'items_archive' => __( 'Spazio Aggiornato' ),
					   'view' => __( 'Visualizza Spazio' ),
					   'view_item' => __( 'Visualizza' ),
					   'search_items' => __( 'Cerca Spazio' ),
					   'not_found' => __( 'Nessuno Spazio trovato' ),
					   'not_found_in_trash' => __( 'Nessuno Spazio trovato nel cestino' ),
					   'parent' => __( 'Spazio superiore' )),
					   'public' => true,
					   'show_ui' => true,
					   'menu_position' => 5,
					   'capability_type' => 'post',
					   'hierarchical' => false,
					   'has_archive' => true,
					   'show_in_menu' => 'Prenotazioni',
					   'menu_icon' => Prenotazioni_URL . 'img/spazi.png',
					//   'taxonomies' => array('category'),  
					   'supports' => array('title', 'editor', 'author','excerpt','thumbnail')));
	}
	function update_spazi_messages( $messages ) {
		global $post, $post_ID;
	    $messages['spazi'] = array(
		0 => '', 
		1 => sprintf('Spazio aggiornato. <a href="%s">Visualizza Spazio</a>', esc_url( get_permalink($post_ID) ) ),
		2 => 'Spazio aggiornato',
	/* translators: %s: date and time of the revision */
		3 => isset($_GET['spazi']) ? sprintf( 'Spazio ripristinata alla versione %s', wp_post_revision_title( (int) $_GET['spazi'], false ) ) : false,
		4 => sprintf( 'Spazio pubblicato. <a href="%s">Visualizza Spazio</a>', esc_url( get_permalink($post_ID) ) ),
		5 => 'Spazio memorizzato',
		6 => sprintf( 'Spazio inviato. <a target="_blank" href="%s">Anteprima Spazio</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		7 => sprintf( 'Spazio schedulato per: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Anteprima Spazio</a>',date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		8 => sprintf( 'Bozza Spazio aggiornato. <a target="_blank" href="%s">Anteprima Spazio</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) )
		);
		return $messages;
	}
	
	function save_spazi_dettagli( $post_id ){
		global $wpdb,$table_prefix;
		//print_r($_REQUEST);exit;
			if ( isset($_REQUEST['post_type']) And $_REQUEST['post_type'] == 'spazi' ) {
				for($giorno=1;$giorno<=7;$giorno++)	{
					for($ora=1;$ora<=24;$ora++){
						$indice=$ora."-0".$giorno;
						if (isset($_POST[$indice])){
						//	echo $indice."  ".$_POST[$indice]."   ";
							$riservato[$giorno][$ora]=$_POST[$indice];
						}else{
							$riservato[$giorno][$ora]=0;
						}
					}
					//echo "<br />";					
				}
				update_post_meta( $post_id, "_riservato",serialize($riservato));
				update_post_meta( $post_id, "_sigla",(isset($_POST['sigla'])?$_POST['sigla']:""));
			}
	}

	function spazio_crea_box_sigla( $post ){
// Recupero i dati dell'indisponibilità dello spazio
	$value=get_post_meta($post->ID, "_sigla",TRUE);
	echo '<label for="sigla">Sigla da associare allo spazio che verr&agrave; utilizzata, nelle tabelle riassuntive per periodo, al posto del Titolo nel caso in cui il numero degli spazi diventa elevato</label> 
	<br /><strong>Sigla</strong> <input type="text" id="sigla" name="sigla" value="' . esc_attr( $value ) . '" size="5" />';	
	}

	function spazio_crea_box_riservato( $post ){
// Recupero i dati dell'indisponibilità dello spazio
	$riserv=get_post_meta($post->ID, "_riservato",TRUE);
//	var_dump($riserv);
	if(strlen($riserv)>1){
		$RL=unserialize($riserv);	
	}else{
		$Rl=array();
	}
// Recupero i parametri della gestione
	$Parametri=get_Pre_Parametri();
	for($giorno=1;$giorno<=7;$giorno++)	
		for($ora=1;$ora<=24;$ora++){
			if($ora<$Parametri['OraInizio'] or $ora>$Parametri['OraFine'] or $Parametri['Giorni'][$giorno-1]==0)
				$Riservato[$giorno][$ora]=3;
			else
				if (!isset($RL[$giorno][$ora]) or $RL[$giorno][$ora]==0)
					$Riservato[$giorno][$ora]=0;
				else
					$Riservato[$giorno][$ora]=1;	
		}
	echo '
	<input type="hidden" id="ColRiservato" value="'.$Parametri['ColRiservato'].'" />
	<input type="hidden" id="ColPrenotato" value="'.$Parametri['ColPrenotato'].'" />
	<legend>Impostare le ore per ogni giorno della settimana in cui lo Spazio è riservato</legend>
	<table width="75%" class="settimanale">
     <caption>
          <div align="center"><strong>Occupazione Settimanale</strong></div>
     </caption>
     <thead>
          <tr>
                <th style="background-color:#00FFCC">Ora</th>
                <th style="background-color:#33CCFF">Lunedi</th>
                <th style="background-color:#00FFCC">Martedi</th>
                <th style="background-color:#33CCFF">Mercoledi</th>
                <th style="background-color:#00FFCC">Giovedi</th>
                <th style="background-color:#33CCFF">Venerdi</th>
                <th style="background-color:#00FFCC">Sabato</th>
                <th style="background-color:#00FFCC">Domenica</th>
          </tr>
     </thead>
     <tbody>';
     for($i=1;$i<=24;$i++){
     	echo '          <tr>
                <th style="background-color:#00FFCC">'.$i.'</th>';
     	for($g=1;$g<=7;$g++){
	    	switch ($Riservato[$g][$i]){
				case 1:
					${'bg'.$g}='style="background-color:'.$Parametri['ColRiservato'].'"';
					break;
				case 3:
					${'bg'.$g}='style="background-color:'.$Parametri['ColNonDisponibile'].'"';
					break;
				default:
					${'bg'.$g}='style="background-color:#FFFFFF"';
					break;				
			}
			echo '
		<td id="'.$i.'-0'.$g.'" class="preset" '.${'bg'.$g}.'><input type="hidden" id="v'.$i.'-0'.$g.'" name="'.$i.'-0'.$g.'" value="'.$Riservato[$g][$i].'"></td>';
		}
         echo '
          </tr>';	
	 }
	echo '
     </tbody>
</table> ';
	}
	
	function get_NomeSpazio($Id){
		$Spazio=get_post($Id);
		return $Spazio->post_title;
	}
	
	function get_ListaSpazi($select_name,$id_name,$class){
	$output = "<select name='$select_name' id='$id_name' class='$class'>\n";
//	$output .= "\t<option value='0' selected='selected'>----</option>\n";
	$myposts = get_posts(array('post_type'=> 'spazi','posts_per_page'   => -1));
	$Primo=true;
	if ( ! empty( $myposts ) ) {	
		foreach ( $myposts as $post ) {
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' );
			$urlIMG=$thumb['0'];
			if ($Primo){
				$this->Foto=$urlIMG;
				$this->IdPrimo=$post->ID;
				$Primo=false;
			}
			$output .= "\t<option value='$post->ID' title=\"$urlIMG\"";
			$output .=" >$post->post_title</option>\n";
		}
	}
	$output .= "</select>\n";
	return $output;
	}

	function get_ListaSpaziDiv(){
		$Html= "";
		$myposts = get_posts(array('post_type'=> 'spazi','posts_per_page'   => -1));
		foreach ( $myposts as $post ){
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium' );
			$Html.='
				<div style="clear:both;">
					<p>
					<img class="alignleft" src="'.$thumb['0'].'"  /><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a><br />'.$post->post_excerpt.'
					</p>
				</div>';	
		}
		$Html.='';
		return $Html;
	}	
	function get_Foto(){
		return $this->Foto;
	}
	function get_Foto_By_ID($Id){
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($Id), 'thumbnail' );
		return $thumb['0'];
	}
	function get_FirstID(){
		return $this->IdPrimo;
	}
}
?>