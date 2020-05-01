<?php
/*
Plugin Name:Gestione Circolari
Plugin URI: http://www.sisviluppo.info
Description: Plugin che implementa la gestione delle circolari scolastiche
Version:3.0
Author: Scimone Ignazio
Author URI: http://www.sisviluppo.info
License: GPL2
    Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : info@sisviluppo.info)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
  die('You are not allowed to call this page directly.'); 
}
global $wpdb,$table_prefix;
$wpdb->table_firme_circolari = $table_prefix . "firme_circolari";

define("Circolari_URL",get_template_directory_uri()."/plugins/gestione-circolari");
define("Circolari_DIR",dirname (__FILE__));
define("Circolari_Dir_Servizio",WP_CONTENT_DIR."/Circolari");
include_once(Circolari_DIR."/admin/gruppi.php");
include_once(Circolari_DIR."/admin/firme.php");
include_once(Circolari_DIR."/functions.inc.php");
include_once(Circolari_DIR."/GestioneCircolari.widget.php");
include_once(Circolari_DIR."/GestioneNavigazioneCircolari.widget.php");
include_once(Circolari_DIR."/GestioneCircolari_CustomPostType.php");
include_once(Circolari_DIR."/admin/testi.php");
if((get_option('Circolari_ModuloCircolari')!= 'Si'||!get_option('Circolari_ModuloCircolari')) And get_theme_mod("scuola_circolari_attiva")){
	circolari_activate();
	update_option('Circolari_ModuloCircolari', 'Si');
}
if(get_option('Circolari_ModuloCircolari')== 'Si' And !get_theme_mod("scuola_circolari_attiva")){
	circolari_uninstall();
	update_option('Circolari_ModuloCircolari', 'No');
}
$msg="";
$TestiRisposte="";
$Testi="";
require_once(ABSPATH . 'wp-includes/pluggable.php'); 
if(isset($_REQUEST["op"])){
	if (isset($_REQUEST['circoFir'])) 
		if (wp_verify_nonce($_REQUEST['circoFir'],'FirmaCircolare'))
			switch ($_REQUEST["op"]){
				case "Firma":
					global $msg;
					$msg=FirmaCircolare((int)$_REQUEST["pid"],4);
					break;
				case "Adesione":
					global $msg;
					$msg=FirmaCircolare((int)$_REQUEST["pid"],$_REQUEST["scelta"]);
					wp_redirect( $_SERVER['HTTP_REFERER'] );
					break;	
			}
	if (isset($_REQUEST['circoRmFir'])){
		if (wp_verify_nonce($_REQUEST['circoRmFir'],'RmFirmaCircolare') And $_REQUEST["op"]=="RemoveFirma"){
			global $msg;
			$msg=RimuoviFirmaCircolare($_REQUEST["pid"]);
			wp_redirect( $_SERVER['HTTP_REFERER'] );
		}	
	}
}

function post_type_archive( $query ) {  
	if(current_user_can('administrator')) return $where;
	if($query->is_post_type_archive('circolari_scuola' ) and ($query->is_admin==FALSE)){  
		$current_user = wp_get_current_user();
		if ( 0 == $current_user->ID ) {
		   $meta_query=array(
		   		array(
		        'key'=>'_visibilita',
		        'value'=>'d',
		        'compare'=>'!=',
		     ));
	         $query->set('meta_query',$meta_query);
		}else{
			$GruppoUtente=get_user_meta($current_user->ID, "gruppo", true);
			if (count($GruppoUtente)>0 AND is_array($GruppoUtente)){
					$GruppiU=array();
					foreach($GruppoUtente as $Gruppo){
						$GruppiU[]=$Gruppo;
					}
			$taxquery =array(
			        array(	'taxonomy' => 'gruppiutenti',
					        'field' => 'id',
					        'terms' => $GruppiU,
					        'operator'=> 'IN',
					        'include_children' =>false),
			);	
		    $query->set( 'tax_query', $taxquery);
		    $meta_query=array(
		   		array(
		        'key'	  => '_visibilita',
		        'value'   => 'd',
                'compare' => '!=',		        
		     ));
	         $query->set('meta_query',$meta_query);
			}	   
		}
	 }  
}  
add_action( 'pre_get_posts', 'post_type_archive' );  


add_filter('posts_where', function ($where, $query) {
	if(current_user_can('administrator') Or $query->query_vars['post_type']!='circolari_scuola' Or $query->is_admin==true) return $where;
		$current_user = wp_get_current_user();
		if ( 0 == $current_user->ID ) return $where;
	if(strpos($where, "term_taxonomy_id")===FALSE) return $where;
		
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
	global $wpdb;
//	echo ">".$where."<<br /><br /><br />";
//	AND ( iswplia_term_relationships.term_taxonomy_id IN (2599,2600,2606) )
//	AND ( iswplia_term_relationships.term_taxonomy_id IN (2600,2599,2606) )
	$CondPrivati= " OR ( ".$wpdb->term_relationships.".term_taxonomy_id IN ".$GruppoUtente." And ".$wpdb->postmeta.".meta_value ='d' )";
	$where=substr($where,strpos($where, "AND",3));
//	echo $where."<br /><br /><br />";
	$where=$where." ".$CondPrivati;
//	echo $where."<br /><br /><br />";wp_die();
//	var_dump($where);	wp_die();
	return $where;
}, 10, 2);

function circolari_scuola_rewrite_rules(){

    add_rewrite_rule(
        'circolari_scuola/([0-9]{4})/([0-9]{1,2})/?$',
        'index.php?post_type=circolari_scuola&year=$matches[1]&monthnum=$matches[2]',
        'top'
    );

    add_rewrite_rule(
        'circolari_scuola/([0-9]{4})/?$',
        'index.php?post_type=circolari_scuola&year=$matches[1]',
        'top'
    );

}
add_action( 'init', 'circolari_scuola_rewrite_rules' );


if (isset($_GET['update']) And $_GET['update'] == 'true')
	$stato="<div id='setting-error-settings_updated' class='updated settings-error'> 
			<p><strong>Impostazioni salvate.</strong></p></div>";
add_filter('post_updated_messages', 'circolari_updated_messages');
add_action('save_post', 'circolari_salva_dettagli');
add_action('add_meta_boxes','circolari_crea_box');
add_filter('manage_posts_columns', 'circolari_NuoveColonne');  
add_action('manage_posts_custom_column', 'circolari_NuoveColonneContenuto', 10, 2); 
add_action('admin_menu', 'circolari_add_menu' ); 
//add_action('publish_circolari','AC_OnPublishPost' );
add_filter('post_row_actions','remove_quick_edit',10,2);
add_filter('wp_get_attachment_url', 'getFileUrl', 10, 2);

function circolari_Inizializzazione(){
	global $TestiRisposte,$Testi;
/**
*  Creazione del custom post type Circolari
*/    
	crea_custom_circolari();
/**
* Impostazione dei tipi di circolari e dei testi delle risposte 
*/
	if(FALSE!==($TestiRisposte=get_option('Circolari_TestiRisposte'))){
    	$TestiRisposte= unserialize($TestiRisposte);
    }else{
    	$TestiRisposte=circolari_CreaTestiRisposta();
    }
   	if(FALSE!==($Testi=get_option('Circolari_Tipo'))){
    	$Testi=unserialize($Testi);
    }else{
    	$Testi=circolari_CreaTesti();
    }
/**
* Operazione eseguite per la memorizzazione dei parametri delle Circoalri
*/
    if(isset($_POST['Circolari_submit_button']) And 
       $_POST['Circolari_submit_button'] == 'Salva Modifiche'){
       	if (isset($_REQUEST['circoPar'])) 
			if (wp_verify_nonce($_REQUEST['circoPar'],'ParametriCircolare')){
			    update_option('Circolari_Visibilita_Pubblica',$_POST['pubblica'] );
			    update_option('Circolari_GGScadenza',(int)$_POST['GGScadenza'] );
				update_option('Circolari_NotificaFirma',$_POST['NotificaFirma'] );  		
				update_option('Circolari_From_NotificaFirma',$_POST['FromNotificaFirma'] );  		
				update_option('Circolari_Oggetto_NotificaFirma',$_POST['OggettoNotificaFirma'] );  		
				update_option('Circolari_Messaggio_NotificaFirma',$_POST['MessaggioNotifica'] );  		
				update_option('Circolari_GestPerm',$_POST['GestPerm'] );     
				header('Location: '.get_bloginfo('wpurl').'/wp-admin/edit.php?post_type=circolari_scuola'); 
			}
	}
}
add_action('init', "circolari_Inizializzazione");

/**
* *************************************************
* 
* Disattivate le notifiche per rallentamento BackEnd
*/
add_action( 'wp_before_admin_bar_render', 'circolari_admin_bar_render' );
add_action( 'admin_menu', 'add_circolari_menu_bubble' );
/**
* *************************************************
*/
add_filter( 'the_content', 'FiltroVisualizzaCircolare');
add_filter( 'get_the_excerpt', 'FiltroVisualizzaRiassuntoCircolare' );
//add_shortcode('VisCircolari', 'VisualizzaCircolari');
//add_shortcode('VisCircolariHome', 'VisualizzaCircolariHome');
add_action('wp_head', 'TestataCircolari' );
add_action( 'admin_enqueue_scripts',  'Circolari_Admin_Enqueue_Scripts' );
add_action( 'wp_enqueue_scripts', 'Circolari_FrontEnd_Enqueue_Scripts' );

add_action('do_feed', 'circolari_disable_feed', 1);
add_action('do_feed_rdf', 'circolari_disable_feed', 1);
add_action('do_feed_rss', 'circolari_disable_feed', 1);
add_action('do_feed_rss2', 'circolari_disable_feed', 1);
add_action('do_feed_atom', 'circolari_disable_feed', 1);
//register_uninstall_hook(__FILE__,  'circolari_uninstall' );
//register_activation_hook( plugin_basename(__FILE__), 'circolari_activate');
//add_action( 'plugins_loaded', 'circolari_activate');
//circolari_activate();
function getFileUrl($url, $id){
    if (get_post_type($id)=="circolari_scuola") {
		$visibilita=get_post_meta($id, "_visibilita");
		if (count($visibilita)==0)
			$selp="Pb";
		else 
			if ($visibilita[0]=="p")
				$selp="Pb";
			else	
				$seld="Pr";
		if ((!is_user_logged_in() Or !Is_Circolare_per_User($id)) And $seld=="Pr")	
			$url = get_home_url().'?circolarifiletype=attachment&circolarigetfile='.$url;		
		}
        return $url;
    }
//removes quick edit from custom post type list
function remove_quick_edit( $actions ) {
	global $post;
    if( $post->post_type == 'circolari_scuola' ) {
 		unset($actions['inline hide-if-no-js']);
	}
    return $actions;
}

function circolari_disable_feed() {
	if ( get_post_type()=='circolari_scuola') {
        wp_die( 'Non ci sono feed disponibili per le Circolari, per visualizzarle naviga la  <a href="'. get_bloginfo('url') .'">Home</a> del sito!');
    }
}

function Circolari_FrontEnd_Enqueue_Scripts() {
//	wp_enqueue_style( 'circolari_styles-fonts', Circolari_URL.'/font/css/font-awesome.min.css',  array(), null, 'all' );
//	wp_enqueue_style( 'circolari_styles', Circolari_URL.'/css/style.css',  array(), null, 'all' );
	}
function Circolari_Admin_Enqueue_Scripts($hook) {
//	if($hook == 'edit.php' And (isset($_GET['post_type']) And $_GET['post_type']=="circolari")) {
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'jquery-ui-datepicker', '', array('jquery'),false,true);
		wp_enqueue_script( 'Circolari-admin', Circolari_URL.'/js/Circolari.js', array(), null);
		wp_enqueue_style( 'jquery.ui.theme', Circolari_URL.'/css/jquery-ui-custom.css');
		if($hook == 'edit.php' And (isset($_GET['post_type']) And $_GET['post_type']=="circolari_scuola")) {
        	wp_enqueue_script( 'jquery-ui-tooltip', '', array('jquery'),false,true);
        	wp_enqueue_script( 'Circolari-admin-tooltip', Circolari_URL.'/js/Circolari_tooltip.js', array(), null);
		}
		wp_enqueue_script( 'Circolari-DataTable', Circolari_URL.'/js/jquery.dataTables.js');
		wp_enqueue_script( 'Circolari-DataTable-Tools', Circolari_URL.'/js/dataTables.tableTools.js');

		wp_enqueue_style( 'circolari_styles-fonts', Circolari_URL.'/font/css/font-awesome.min.css',  array(), null, 'all' );
		wp_enqueue_style( 'Circolari-DataTable-theme', Circolari_URL.'/css/jquery.dataTables.css' );
		wp_enqueue_style( 'Circolari-DataTable-theme-Tools', Circolari_URL.'/css/dataTables.tableTools.css' );
		wp_enqueue_style( 'Circolari-style', Circolari_URL.'/css/style.css' );
//    }
}

function search_filter($query) {
if (get_post_type()=='newsletter' ) {
    	      $query->set('post_type', array( 'post', 'circolari_scuola' ) );
}
	return $query;
}

add_action('pre_get_posts','search_filter');

function VisualizzaCircolari($atts){
	$ret="";
	if (isset($_REQUEST['Anno']))
		$Anno = (int)$_REQUEST['Anno'];
	else
		$Anno = date('Y');
	if (isset($_REQUEST['Mese']))
		$Mese=(int)$_REQUEST['Mese'];
	elseif(isset($_REQUEST['Anno']))
		$Mese="";
	else
		$Mese=date('n');
	$atts = shortcode_atts(
			array(
				'archivio' => 'Mese',
				'numcircolari' => 'All',
			), $atts, 'VisCircolari' );
	require_once ( dirname (__FILE__) . '/admin/frontend.php' );
	return $ret;
}

function VisualizzaCircolariHome(){
	$ret="";
	require_once ( dirname (__FILE__) . '/admin/frontendhome.php' );
	return $ret;
}

function FiltroVisualizzaCircolare( $content ){
global $TestiRisposte,$Testi;
$PostID= get_the_ID();
/*
 * Se l'articolo non appartiene al CustomPostType circolari_scuola rimando il contenuto
 */
	if (get_post_type( $PostID) !="circolari_scuola")
		return $content;
/*
 * Se non c'è un utente loggato verifico se la circolare è pubblica. 
 * Se è pubblica la rimando per la visualizzazione
 * Altrimenti rimando il messaggio di risorsa riservata
 */
	$visibilita=get_post_meta($PostID, "_visibilita");
	$seld="";
	if (count($visibilita)==0)
		$selp="Pb";
	else 
		if ($visibilita[0]=="p")
			$selp="Pb";
		else	
			$seld="Pr";
	if ((!is_user_logged_in() Or !Is_Circolare_per_User($PostID)) And $seld=="Pr")	
		return '
	<div class="alert alert-success" role="alert">
  		<h4 class="alert-heading">Avviso di sicurezza!</h4>
  		<p>Circolare riservata a specifici gruppi di utenti registrati.</p>
  		<hr>
  		<p class="mb-0">Loggati per accedere alla circolare.</p>
	</div>';
/*
 * Se la circolare è per l'utente ed è da firmare visualizzo la gestione della firma 
 * 
 * Altrimenti rimando il messaggio di risorsa riservata
 */
	if (!Is_Circolare_Da_Firmare($PostID) or !Is_Circolare_per_User($PostID))
		return $content;
	if (strlen(stristr($_SERVER["HTTP_REFERER"],"wp-admin/edit.php?post_type=circolari_scuola&page=Firma"))>0)
		return "<br />
		<button style=' outline: none;
 cursor: pointer;
 text-align: center;
 text-decoration: none;
 font: bold 12px Arial, Helvetica, sans-serif;
 color: #fff;
 padding: 10px 20px;
 border: solid 1px #0076a3;
 background: #0095cd;' onclick='javascript:history.back()'>Torna alla Firma</button>".$content;
	else{ 
		$Campo_Firma="";
		if (Is_Circolare_per_User($PostID)){	
			$sign=get_post_meta($PostID, "_sign",TRUE);
			if($sign=="NoFirma"){
				return $content;
			}else{
				$BaseUrl=admin_url()."edit.php";
				if (Is_Circolare_Firmata($PostID)){
					//$Campo_Firma="Firmata".$TestiRisposte[get_Circolare_Adesione($PostID)]->get_Risposta;
					return  $content;
				}else{
					if ($sign!="Firma"){
							//$TipoC=new Circolari_Tipo();
//							echo "<pre>";var_dump(Circolari_find_Tipo($sign));	echo "</pre>";
						$Campo_Firma='<form action=""  method="get" style="display:inline;">
							<div>
								<div class="row">
									<input type="hidden" name="op" value="Adesione" />
									<input type="hidden" name="pid" value="'.$PostID.'" />
									<input type="hidden" name="circoFir" value="'.wp_create_nonce('FirmaCircolare').'" />';
					$TipoC=Circolari_find_Tipo($sign);
					$Primo=true;
					foreach($TipoC->get_Risposte() as $Risposta){
						$Campo_Firma.='    <div class="form-check form-check-inline">
      <input name="scelta" type="radio" id="radio'.$Risposta.'" class="s'.$Risposta.'-'.$PostID.'" value="'.$Risposta.'" '.($Primo?"checked":"").'>
      <label for="radio'.$Risposta.'">'.Circolari_find_Risposta($Risposta)->get_Risposta().'</label>
    </div>';
    				$Primo=false;
					}
					$Campo_Firma.='
									<button class="btn btn-primary btn-icon inviaadesione" type="submit" name="inviaadesione" id="'.$PostID.'" value="Firma" rel="'.get_the_title($PostID).'"><i class="fas fa-signature pr-2"></i> <span>Esprimi scelta</span>
								</div>
							</div>
						</form>';
					}else
						$Campo_Firma='<button class="btn btn-primary btn-icon" onclick="window.location.href=\''.get_permalink($PostID).'?op=Firma&pid='.$PostID.'&circoFir='.wp_create_nonce('FirmaCircolare').'\'"><i class="fas fa-signature pr-2"></i> <span>Firma la Circolare</span>';					
				}
			}
		}
	return $content." <br />".$Campo_Firma;
	}	
}

function FiltroVisualizzaRiassuntoCircolare( $excerpt ){

	$PostID= get_the_ID();
/*
 * Se l'articolo richiede la password rimando tutto il contenuto con la richiesta della password
 */
	if (post_password_required( $PostID ))
		return "Contenuto protetto da Password";
/*
 * Se l'articolo non appartiene al CustomPostType Circolari rimando il contenuto
 */
	if (get_post_type( $PostID) !="circolari_scuola")
		return $excerpt;
/*
 * Se non c'è un utente loggato verifico se la circolare è pubblica. 
 * Se è pubblica la rimando per la visualizzazione
 * Altrimenti rimando il messaggio di risorsa riservata
 */
	$visibilita=get_post_meta($PostID, "_visibilita");
	if (count($visibilita)==0)
		$selp="Pb";
	else 
		if ($visibilita[0]=="p")
			$selp="Pb";
		else	
			$seld="Pr";
			if ((!is_user_logged_in() Or !Is_Circolare_per_User($PostID)) And $seld=="Pr")	
		return "Contenuto riservato a specifici gruppi di utenti registrati";
	else
		return $excerpt;
//	return $excerpt;
}
function circolari_activate() {
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	require_once(ABSPATH . 'wp-admin/includes/plugin.php'); 
	$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->table_firme_circolari." (
  			post_ID  bigint(20) NOT NULL,
  			user_ID bigint(20) NOT NULL,
  			datafirma timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  			ip varchar(16) DEFAULT NULL,
			adesione smallint(6) NOT NULL DEFAULT '-1',
  			PRIMARY KEY (post_ID,user_ID));";
	dbDelta($sql);
	if(get_option('Circolari_GestPerm')== ''||!get_option('Circolari_GestPerm')){
		add_option('Circolari_GestPerm', 'int');
	}
	if(get_option('Circolari_Visibilita_Pubblica')== ''||!get_option('Circolari_Visibilita_Pubblica')){
		add_option('Circolari_Visibilita_Pubblica', '0');
	}
	if(get_option('Circolari_GGScadenza')== ''||!get_option('Circolari_GGScadenza')){
		add_option('Circolari_GGScadenza', '30');
	}
	if(get_option('Circolari_NotificaFirma')== ''||!get_option('Circolari_NotificaFirma')){
		add_option('Circolari_NotificaFirma', 'No');
	}
	if(get_option('Circolari_From_NotificaFirma')== ''||!get_option('Circolari_From_NotificaFirma')){
		add_option('Circolari_From_NotificaFirma', 'Servizio di Notifica firma delle circolari <indirizzo_email@dominio.it>');
	}
	if(get_option('Circolari_Oggetto_NotificaFirma')== ''||!get_option('Circolari_Oggetto_NotificaFirma')){
		add_option('Circolari_Oggetto_NotificaFirma', 'Notifica firma circolare');
	}
	if(get_option('Circolari_Messaggio_NotificaFirma')== ''||!get_option('Circolari_Messaggio_NotificaFirma')){
		add_option('Circolari_Messaggio_NotificaFirma', 'Caro  {Dati_Utente}

con la presente email ti confermiamo di aver registrato, in data {Data},  la tua scelta di {Operazione} circolare {Link_Circolare}.

Grazie per la collaborazione      ');
	}
	if(get_option('Circolari_TestiRisposte')== ''||!get_option('Circolari_TestiRisposte') ){			
		circolari_CreaTestiRisposta();
	}
	if(get_option('Circolari_Tipo')== ''||!get_option('Circolari_Tipo')){
		circolari_CreaTesti();
	}
}
function circolari_CreaTestiRisposta(){
$TestiRisposte=array(new Circolari_Risposta(0,"Non Firmata","Non Firmare la"),
				 new Circolari_Risposta(1,"Si","Aderire alla"),
                 new Circolari_Risposta(2,"No","Non Aderire alla"),
                 new Circolari_Risposta(3,"Presa Visione","Prendere Visione in merito alla"),
                 new Circolari_Risposta(4,"Firma","Firmare la"));
add_option('Circolari_TestiRisposte', serialize($TestiRisposte));
return $TestiRisposte;
}
function circolari_CreaTesti(){
$Testi=array(new Circolari_Tipo("NoFirma","Informativa","","La circolare non richiede conferma","Firma non prevista","",array()),
		new Circolari_Tipo("Sciopero","Adesioni allo sciopero","Sciopero","Adesione","La circolare si riferisce ad uno sciopero.<br />Bisogna indicare Si/No/Presa Visione","Adesione allo sciopero",array(1,2,3)),
        new Circolari_Tipo("Firma","Firme","Circolare ordinaria","Da Firmare","&Egrave; richiesta la firma alla circolare ordinaria","Firma la circolare ordinaria",array(4)),
        new Circolari_Tipo("Assemblea","Partecipazioni all\'assemblea","Assembea Sindacale","Partecipazione","La circolare si riferisce ad una assemblea sindacale.<br />Bisogna indicare Si/No","Partecipazione all\'assemblea",array(1,2)));
add_option('Circolari_Tipo', serialize($Testi));
return $Testi;
}
function circolari_create_Testi_Risposte(){
	global $TestiRisposte,$Testi;
	if(isset($_REQUEST['MemoRisposta']) And $_REQUEST['MemoRisposta'] == 'Crea Nuova Risposta'){
		$nuova=new Circolari_Risposta(Circolari_Get_New_Numero_Risposta(),$_REQUEST['risposta'],$_REQUEST['testomail']);
		$TestiRisposte[]=$nuova;
		update_option('Circolari_TestiRisposte', serialize($TestiRisposte));			
	}
	if(isset($_REQUEST['MemoTesto']) And $_REQUEST['MemoTesto'] == 'Creazione Tipo Circolare'){
		$num=0;
		$suff="";
		while (Circolari_is_set_Tipo($_REQUEST['tipo'].$suff)){
			$num++;
			$suff="-".$num;
		}
		$nuova=new Circolari_Tipo($_REQUEST['tipo'].$suff,
								  $_REQUEST["popup"],
								  $_REQUEST["des_tipo"],
								  $_REQUEST["pref_risposta"],
								  $_REQUEST["descrizione"],
								  $_REQUEST["testo_elenco"],
								  array());
		$Testi[]=$nuova;
		update_option('Circolari_Tipo', serialize($Testi));			
	}
}
function circolari_update_Testi_Risposte(){
	global $TestiRisposte,$Testi;
	if(filter_input(INPUT_GET,'MemoTesto') == 'Memorizza Modifiche'){		
			foreach($Testi as $Testo)
				if ($Testo->get_Tipo()==$_REQUEST['tipo']){
					if(isset($_REQUEST['popup'])){$Testo->set_Popup($_REQUEST['popup']);}
					if(isset($_REQUEST['des_tipo'])){$Testo->set_DescrizioneTipo($_REQUEST['des_tipo']);}
					if(isset($_REQUEST['pref_risposta'])){$Testo->set_Prefisso($_REQUEST['pref_risposta']);}
					if(isset($_REQUEST['descrizione'])){$Testo->set_Descrizione($_REQUEST['descrizione']);}
					if(isset($_REQUEST['testo_elenco'])){$Testo->set_TestoElenco($_REQUEST['testo_elenco']);}						
					$risposte=array();
					foreach($_REQUEST as $key => $value){
						if (substr($key,0,2)=="R-"){
							$risposte[]=substr($key,2,strlen($key)-1);
						}
					}
					$Testo->set_Risposte($risposte);
				}		
		update_option('Circolari_Tipo', serialize($Testi));			
	}
	if(filter_input(INPUT_GET,'MemoRisposta') == 'Memorizza Modifiche'){
			foreach($TestiRisposte as $Risposta)
				if ($Risposta->get_IDRisposta()==(int)$_REQUEST['id']){
					$Risposta->set_Risposta($_REQUEST['risposta']);
					$Risposta->set_RispostaMail($_REQUEST['rispostamail']);
				}		
		update_option('Circolari_TestiRisposte', serialize($TestiRisposte));			
	}
}
function add_circolari_menu_bubble() {
  global $menu,$DaFirmare;
//	$NumCircolari=GetCircolariDaFirmare("N");
$DaFirmare=GetCircolariDaFirmare("N");
  if ($DaFirmare==0)
	return;
// echo "<pre>";var_dump($menu);echo "</pre>";
  foreach($menu as $i=>$m){
  	if ($m[0]=="Circolari"){
		$menu[$i][0] .= "<span class='update-plugins count-1'><span class='update-count'>$DaFirmare</span></span>";
		return;
	}
 }
}

function circolari_add_menu(){
   add_submenu_page( 'edit.php?post_type=circolari_scuola', 'Parametri',  'Parametri', 'edit_others_posts', 'circolari', 'circolari_MenuPagine');
   $pageFirma=add_submenu_page( 'edit.php?post_type=circolari_scuola', 'Firma',  'Firma', 'read', 'Firma', 'circolari_GestioneFirme');
   add_action( 'admin_head-'. $pageFirma, 'TestataCircolari' );
//   add_action( 'admin_head-'. $pagenFirmate, 'TestataCircolari' );
   $pageArchivio=add_submenu_page( 'edit.php?post_type=circolari_scuola', 'Archivio Circolari', 'Archivio Circolari', 'read', 'Archivio', 'circolari_VisualizzaArchivio');
   add_action( 'admin_head-'. $pageArchivio, 'TestataCircolari' );
   $gestionetesti=add_submenu_page( 'edit.php?post_type=circolari_scuola', 'Testi',  'Tipi di circolari', 'edit_published_posts', 'Testi', 'circolari_MenuTesti');
   add_action( 'admin_head-'. $gestionetesti, 'TestataCircolari' );  
   $utility=add_submenu_page( 'edit.php?post_type=circolari_scuola', 'Utility',  'Utility', 'edit_others_posts', 'Utility', 'circolari_Utility');
   add_action( 'admin_head-'. $utility, 'TestataCircolari' );
}
function circolari_MenuTesti(){
	global $TestiRisposte,$Testi;	
	if (isset($_REQUEST['opT'])){
		switch ($_REQUEST['opT']){
			case "Edit":
				circolari_GestioneTesti();
				break;
			case "Risp":
				circolari_GestioneRisposteTesti();
				break;
			case "NewTipo":
				circolari_NewTesto();
				break;
			case "Canc":
				if (($Num=Circolari_IsUsed_TipoCircolare($_REQUEST['id']))==0 And !in_array($_REQUEST['id'],array("NoFirma","Sciopero","Firma","Assemblea"))){
					unset($Testi[Circolari_find_Index_Tipo($_REQUEST['id'])]);
					update_option('Circolari_Tipo', serialize($Testi));
				echo'<div class="updated">
	<p></p><em><strong>Cancellazione avvenuta con successo!</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="5;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
				}else{
				echo'<div class="updated">
	<p></p><em><strong>Cancellazione non effettuata:<br />Ci sono '.$Num.' Circolari che utilizzano questo Tipo<br />Bisogna prima disassociare le circolari e poi si può cancellare il Tipo</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';				
				}
				break;
			case "MemorizzaNew":
				if (isset($_REQUEST["circoTesti"])) {
					if (wp_verify_nonce($_REQUEST["circoTesti"],'GestioneTestiCircolari')){
						circolari_create_Testi_Risposte();
						echo'<div class="updated">
	<p></p><em><strong>Creazione avvenuta con successo!</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
					}
				}
				break;			
			case "Memorizza":
				if (isset($_REQUEST["circoTesti"])) {
					if (wp_verify_nonce($_REQUEST["circoTesti"],'GestioneTestiCircolari')){
						circolari_update_Testi_Risposte();
						echo'<div class="updated">
	<p></p><em><strong>Memorizzazione avvenuta con successo!</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
					}
				}
				break;
			default:
				echo'<div class="updated">
	<p></p><em><strong>Operazione non eseguita!</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
				break;
		}
		return;
	}
	if (isset($_REQUEST['opR'])){
		switch ($_REQUEST['opR']){
			case "Edit":
				circolari_GestioneRisposte();
				break;
			case "Canc":
				if (($Num=Circolari_IsUsed_Risposta($_REQUEST['id']))==0){
					unset($TestiRisposte[Circolari_find_Index_Risposta($_REQUEST['id'])]);
					update_option('Circolari_TestiRisposte', serialize($TestiRisposte));
				echo'<div class="updated">
	<p></p><em><strong>Cancellazione avvenuta con successo!</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="5;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
				}else{
				echo'<div class="updated">
	<p></p><em><strong>Cancellazione non effettuata:<br />Ci sono '.$Num.' di Tipi di Circolare che utilizzano questa Risposta<br />Bisogna prima disassociare la risposta dalle circolari e poi si può cancellare la risposta</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';				
				}		
				break;
			case "NewRisp":
				circolari_NewRisposta();
				break;
			case "MemorizzaNew":
				if (isset($_REQUEST["circoTesti"])) {
					if (wp_verify_nonce($_REQUEST["circoTesti"],'GestioneTestiCircolari')){
						circolari_create_Testi_Risposte();
						echo'<div class="updated">
	<p></p><em><strong>Creazione avvenuta con successo!</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
					}
				}
				break;				
			case "Memorizza":
				if (isset($_REQUEST["circoTesti"])) {
					if (wp_verify_nonce($_REQUEST["circoTesti"],'GestioneTestiCircolari')){
						circolari_update_Testi_Risposte();
						echo'<div class="updated">
	<p></p><em><strong>Memorizzazione avvenuta con successo!</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
					}
				}
				break;
		}
		return;
	}
	circolari_Testi();
}
function circolari_GestioneRisposteTesti(){
	global $TestiRisposte,$Testi;
	foreach($Testi as $Testo){
		if($Testo->get_Tipo()==$_GET['id'])
			break;
	}	
	$UrlB=get_site_url()."/wp-admin/edit.php";
echo '<div class="wrap">
	<i class="fa fa-pencil-square-o fa-3x" aria-hidden="true"></i>
		<h2 style="display:inline;margin-left:10px;vertical-align:super;">Gestione Risposte</h2>
	</div> 
		<br />
		<table class="widefat">
			<tr class="intestariga">
				<th width="20%" class="intestariga">Tipo</th>
				<td>'.$Testo->get_Tipo().'</td>
			</tr>
			<tr class="intestariga">
				<th class="intestariga">Descrizione Tipo</th>
				<td>'.$Testo->get_DescrizioneTipo().'</td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Prefisso risposta</th>
				<td>'.$Testo->get_Prefisso().'</td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Descrizione</th>
				<td>'.$Testo->get_Descrizione().'</td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Testo Elenco</th>
				<td>'.$Testo->get_TestoElenco().'</td>
			</tr>
		</table>
		<form action="'.$UrlB.'" name="MTesti" metod="post">
		<input type="hidden" name="opT" value="Memorizza"/>
		<input type="hidden" name="post_type" value="circolari_scuola"/>
		<input type="hidden" name="page" value="Testi"/>
		<input type="hidden" name="tipo" value="'.$Testo->get_Tipo().'"/>
		<input type="hidden" name="circoTesti" value="'.wp_create_nonce('GestioneTestiCircolari').'"/>
		<div style="margin:20px;border: thin groove Blue;background-color: #ECECEC;padding:20px;width:400px;margin-left:auto;margin-right:auto;">
		<p style="font-weight: bold;font-size: 1.3em;text-align: center;margin-bottom: 15px;">Risposte assegnate a questo tipo di corcolare</p>';	
		foreach($TestiRisposte as $TRisposte){
				if($Testo->is_set_Risposta($TRisposte->get_IDRisposta()))
					$chk="checked='checked'";
				else
					$chk="";
			if ($TRisposte->get_IDRisposta()>0)
				echo "<input type='checkbox' name='R-".$TRisposte->get_IDRisposta()."' value='".$TRisposte->get_Risposta()."' ".$chk."/> ".$TRisposte->get_Risposta()."<br />";
		}
		echo '
		</div>
		<p style="text-align:center;">
<input type="submit" value="Memorizza Modifiche" name="MemoTesto"/>
<a href=\''.$UrlB.'?post_type=circolari_scuola&page=Testi'.'\'>Annulla Modifiche</a>
		</p>
		</form>';
							
}
function circolari_GestioneTesti(){
	global $TestiRisposte,$Testi;
	foreach($Testi as $Testo){
		if($Testo->get_Tipo()==$_GET['id'])
			break;
	}	
	if($_GET['opT']=="Edit")
		$Operazione="Modifica";
	else 
		$Operazione="Nuovo";
	$UrlB=get_site_url()."/wp-admin/edit.php";
echo '<div class="wrap">
	<i class="fa fa-pencil-square-o fa-3x" aria-hidden="true"></i><h2 style="display:inline;margin-left:10px;vertical-align:super;">'.$Operazione.'Testo Circolare</h2>
	</div> 
		<form action="'.$UrlB.'" name="MTesti" metod="post">
		<input type="hidden" name="opT" value="Memorizza"/>
		<input type="hidden" name="post_type" value="circolari_scuola"/>
		<input type="hidden" name="page" value="Testi"/>
		<input type="hidden" name="tipo" value="'.$Testo->get_Tipo().'"/>
		<input type="hidden" name="circoTesti" value="'.wp_create_nonce('GestioneTestiCircolari').'"/>
		<p style="text-align:left;font-size:1em;font-style: italic;margin-top:30px;">
		<table class="widefat">
			<tr class="intestariga">
				<th width="20%" class="intestariga">Tipo</th>
				<td>'.$Testo->get_Tipo().'</td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Popup</th>
				<td><input type="text" name="popup" value="'.$Testo->get_Popup().'"" size="100" id="popup"/><br />
				<span style="font-style: italic;font-weight: bold;">Testo che viene riportato nel Popup dell\'elenco delle circolari</span></td>
			</tr>
			<tr class="intestariga">
				<th class="intestariga">Descrizione Tipo</th>
				<td><input type="text" name="des_tipo" value="'.$Testo->get_DescrizioneTipo().'"" size="100" id="descrizioneTipo"/><br />
				<span style="font-style: italic;font-weight: bold;">Testo che viene riportato nella seconda parte del messaggio di richiesta di adesione da parte dell\'utente</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Prefisso risposta</th>
				<td><input type="text" name="pref_risposta" value="'.$Testo->get_Prefisso().'" size="100" id="descrizioneTipo"/><br />
				<span style="font-style: italic;font-weight: bold;">Testo che viene riportato nella prima parte del messaggio di richiesta di adesione da parte dell\'utente</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Descrizione</th>
				<td><input type="text" name="descrizione" value="'.$Testo->get_Descrizione().'" size="100" id="descrizione"/><br /><span style="font-style: italic;font-weight: bold;">Testo che viene riportato nel Box di Creazione/Modifica delle circolari in cui si selezione il tipo di circolare</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Testo Elenco</th>
				<td><input type="text" name="testo_elenco" value="'.$Testo->get_TestoElenco().'" size="100" id="testoelenco"/><br /><span style="font-style: italic;font-weight: bold;">Testo che viene riportato nell\'intestazione della colonna che riporta la scelta dell\'utente nel report delle Firme/Adesioni</span></td>
			</tr>
		</table>
		<p style="text-align:center;">
<input type="submit" value="Memorizza Modifiche" name="MemoTesto"/>
<a href=\''.$UrlB.'?post_type=circolari_scuola&page=Testi'.'\'>Annulla Modifiche</a>
		</p>
		</form>';
							
}
function circolari_GestioneRisposte(){
	global $TestiRisposte;
	foreach($TestiRisposte as $Risposta){
		if($Risposta->get_IDRisposta()==(int)$_GET['id'])
			break;
	}	
	if($_GET['opR']=="Edit")
		$Operazione="Modifica";
	else 
		$Operazione="Nuovo";
	$UrlB=get_site_url()."/wp-admin/edit.php";
echo '<div class="wrap">
	<i class="fa fa-pencil-square-o fa-3x" aria-hidden="true"></i><h2 style="display:inline;margin-left:10px;vertical-align:super;">'.$Operazione.' Risposta Circolare</h2>
	</div> 
		<form action="'.$UrlB.'" name="MRisposte" metod="post">
		<input type="hidden" name="opR" value="Memorizza"/>
		<input type="hidden" name="post_type" value="circolari"/>
		<input type="hidden" name="page" value="Testi"/>
		<input type="hidden" name="id" value="'.$Risposta->get_IDRisposta().'"/>
		<input type="hidden" name="circoTesti" value="'.wp_create_nonce('GestioneTestiCircolari').'"/>
		<p style="text-align:left;font-size:1em;font-style: italic;margin-top:30px;">
		<table class="widefat">
			<tr class="intestariga">
				<th width="20%" class="intestariga">Risposta</th>
				<td><input type="text" name="risposta" value="'.$Risposta->get_Risposta().'" size="100" id="risposta"/></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Testo email</th>
				<td><input type="text" name="rispostamail" value="'.$Risposta->get_RispostaMail().'" size="100" id="rispostamail"/></td>
			</tr>
		</table>
		<p style="text-align:center;">
<input type="submit" value="Memorizza Modifiche" name="MemoRisposta"/>&nbsp;
<a href=\''.$UrlB.'?post_type=circolari_scuola&page=Testi'.'\'>Annulla Modifiche</a>
		</p>
		</form>';
							
}
function circolari_NewTesto(){
	$UrlB=get_site_url()."/wp-admin/edit.php";
echo '<div class="wrap">
	<i class="fa fa-pencil-square-o fa-3x" aria-hidden="true"></i><h2 style="display:inline;margin-left:10px;vertical-align:super;">Nuovo Tipo Circolare</h2>
	</div> 
		<form action="'.$UrlB.'" name="MTesti" metod="post">
		<input type="hidden" name="opT" value="MemorizzaNew"/>
		<input type="hidden" name="post_type" value="circolari_scuola"/>
		<input type="hidden" name="page" value="Testi"/>
		<input type="hidden" name="circoTesti" value="'.wp_create_nonce('GestioneTestiCircolari').'"/>
		<p style="text-align:left;font-size:1em;font-style: italic;margin-top:30px;">
		<table class="widefat">
			<tr class="intestariga">
				<th width="20%" class="intestariga">Tipo</th>
				<td><input type="text" name="tipo" size="10" id="Tipo"/><br />
				<span style="font-style: italic;font-weight: bold;">Codice Univoco che identificher&agrave; il tipo di circolare</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Popup</th>
				<td><input type="text" name="popup" size="10" id="Popup"/><br />
				<span style="font-style: italic;font-weight: bold;">Testo del Popup dell\'elenco delle circolari</span></td>
			</tr>
			<tr class="intestariga">
				<th class="intestariga">Descrizione Tipo</th>
				<td><input type="text" name="des_tipo" size="100" id="descrizioneTipo"/><br />
				<span style="font-style: italic;font-weight: bold;">Testo che viene riportato nella seconda parte del messaggio di richiesta di adesione da parte dell\'utente</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Prefisso risposta</th>
				<td><input type="text" name="pref_risposta" size="100" id="descrizioneTipo"/><br />
				<span style="font-style: italic;font-weight: bold;">Testo che viene riportato nella prima parte del messaggio di richiesta di adesione da parte dell\'utente</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Descrizione</th>
				<td><input type="text" name="descrizione" size="100" id="descrizione"/><br /><span style="font-style: italic;font-weight: bold;">Testo che viene riportato nel Box di Creazione/Modifica delle circolari in cui si selezione il tipo di circolare</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Testo Elenco</th>
				<td><input type="text" name="testo_elenco" size="100" id="testoelenco"/><br /><span style="font-style: italic;font-weight: bold;">Testo che viene riportato nell\'intestazione della colonna che riporta la scelta dell\'utente nel report delle Firme/Adesioni</span></td>
			</tr>
		</table>
		<p style="text-align:center;">
<input type="submit" value="Creazione Tipo Circolare" name="MemoTesto"/>
<a href=\''.$UrlB.'?post_type=circolari_scuola&page=Testi'.'\'>Annulla Creazione</a>
		</p>
		</form>';							
}
function circolari_NewRisposta(){
	global $TestiRisposte;
	$UrlB=get_site_url()."/wp-admin/edit.php";
echo '<div class="wrap">
	<i class="fa fa-pencil-square-o fa-3x" aria-hidden="true"></i><h2 style="display:inline;margin-left:10px;vertical-align:super;">Nuova Risposta Circolare</h2>
	</div> 
		<form action="'.$UrlB.'" name="MRisposte" metod="post">
		<input type="hidden" name="opR" value="MemorizzaNew"/>
		<input type="hidden" name="post_type" value="circolari_scuola"/>
		<input type="hidden" name="page" value="Testi"/>
		<input type="hidden" name="id" value="'.Circolari_Get_New_Numero_Risposta().'"/>
		<input type="hidden" name="circoTesti" value="'.wp_create_nonce('GestioneTestiCircolari').'"/>
		<p style="text-align:left;font-size:1em;font-style: italic;margin-top:30px;">
		<table class="widefat">
			<tr class="intestariga">
				<th width="20%" class="intestariga">Risposta</th>
				<td><input type="text" name="risposta" size="100" id="risposta"/></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">Testo email</th>
				<td><input type="text" name="testomail" size="100" id="testomail"/></td>
			</tr>
		</table>
		<p style="text-align:center;">
<input type="submit" value="Crea Nuova Risposta" name="MemoRisposta"/>&nbsp;
<a href=\''.$UrlB.'?post_type=circolari_scuola&page=Testi'.'\'>Annulla Creazione</a>
		</p>
		</form>';
							
}

function circolari_Testi(){
	global $TestiRisposte,$Testi;
	$UrlB=get_site_url()."/wp-admin/edit.php?post_type=circolari_scuola&page=Testi";
echo '<div class="wrap">
	<i class="fa fa-pencil-square-o fa-3x" aria-hidden="true"></i><h2 style="display:inline;margin-left:10px;vertical-align:super;">Tipi di Circolari</h2>
	</div> 
		<p></p>
		<div class="widefat">
<div style="width:100px;margin:0 auto;font-size:1.5em;font-weight: bold;">
			Tipi <a href="'.$UrlB.'&opT=NewTipo"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
			</div>
			<p style="text-align:left;font-size:1em;font-style: italic;">
			<table class="widefat">
				<tr class="border_bottom">
					<th>Tipo</th>
					<th>Popup</th>
					<th>Descrizione Tipo</th>
					<th>Prefisso risposta</th>
					<th>Descrizione</th>
					<th>Testo Elenco</th>
					<th>Operazioni</th>
				</tr>';
	foreach($Testi as $Testo){
		$RisposteModifica="<a href='".$UrlB."&opT=Edit&id=".$Testo->get_Tipo()."' title='Modifica Testi Tipo Circolare'><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></a> ";
		$RisposteCancella="<a href='".$UrlB."&opT=Canc&id=".$Testo->get_Tipo()."' title='Cancella Tipo Circolare'><i class=\"fa fa-eraser\" aria-eraser=\"true\" style=\"color:red;\"></i></a> ";
		$Risposte="<a href='".$UrlB."&opT=Risp&id=".$Testo->get_Tipo()."' title='Modifica le risposte'><i class=\"fa fa-child\" aria-eraser=\"true\" style=\"color:green;\"></i></a> ";
		if($Testo->get_Tipo()=="NoFirma"){
			$RisposteCancella=$Risposte="";
		}
		if(in_array($Testo->get_Tipo(),array("NoFirma","Sciopero","Firma","Assemblea"))){
			$RisposteCancella="";
		}
		echo "			<tr class='border_bottom'>
		<td>".$Testo->get_Tipo()."</td>
		<td>".$Testo->get_Popup()."</td>
		<td>".$Testo->get_DescrizioneTipo()."</td>
		<td>".$Testo->get_Prefisso()."</td>
		<td>".$Testo->get_Descrizione()."</td>
		<td>".$Testo->get_TestoElenco()."</td>
		<td width='130px;'>".$RisposteModifica.$RisposteCancella.$Risposte."</td>
	</tr>";
	}	
echo '
			</table>
			</p>
			<div style="width:150px;margin:0 auto;font-size:1.5em;font-weight: bold;">
			Risposte <a href="'.$UrlB.'&amp;opR=NewRisp"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
			</div>
			<p style="text-align:left;font-size:1em;font-style: italic;">
			<table class="widefat">
				<tr class="border_bottom">
					<th>ID</th>
					<th>Testo</th>
					<th>Testo Mail</th>
					<th>Operazioni</th>
				</tr>';
	foreach($TestiRisposte as $TestoRisposta){
		$RisposteModifica="
		<a href='".$UrlB."&opR=Edit&id=".$TestoRisposta->get_IDRisposta()."'><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></a>
		";
		if ($TestoRisposta->get_IDRisposta()>4)
			$RisposteCancella="
		<a href='".$UrlB."&opR=Canc&id=".$TestoRisposta->get_IDRisposta()."'><i class=\"fa fa-eraser\" aria-eraser=\"true\" style=\"color:red;\"></i></a>
		";
		else
			$RisposteCancella="";
		echo "			<tr class='border_bottom'>
		<td>".$TestoRisposta->get_IDRisposta()."</td>
		<td>".$TestoRisposta->get_Risposta()."</td>
		<td>".$TestoRisposta->get_RispostaMail()."</td>
		<td>".$RisposteModifica.$RisposteCancella."</td>
	</tr>";
	}	
echo '
			</table>
			</p>
</div>';	
}
function circolari_Utility($Stato=""){
echo '<div class="wrap">
		<i class="fa fa-cogs fa-3x" aria-hidden="true"></i> <h2 style="display:inline;margin-left:10px;vertical-align:super;">Utility Circolari</h2>';
$lista="";
$azione= filter_input(INPUT_GET, "action");
if($azione){
	switch ($azione){
		case "versca":			
			echo "<p style='text-align:center;font-size:1.5em;font-weight: bold;'>Stato Operazioni:</p>";
				$GGscadenza=get_option("Circolari_GGScadenza");
				$Posts = get_posts('post_type=circolari_scuola&numberposts=-1');
				foreach($Posts as $post){
					$Adesione=get_post_meta($post->ID, "_sciopero",TRUE);
					$firma=get_post_meta($post->ID, "_sign",TRUE);
					var_dump($firma);wp_die();
					if (($firma!="NoFirma") and empty($scadenza)){
						$lista.="			<li>$post->ID $post->post_title $post->post_date_gmt";
						if (isset($_GET['opt']) && $_GET['opt']=="aggsca"){
							$Scadenza=date('Y-m-d', strtotime(substr($post->post_date_gmt,0,10). " + $GGscadenza days"));
							if (update_post_meta($post->ID,"_scadenza",$Scadenza ))
								$lista.= ' <img src="'.Circolari_URL.'/img/verificato.png" alt="Icona Permessi" style="display:inline;margin-left:5px;"/>';
						}else
						$lista.=" la data verrà aggiornata a:".date('Y-m-d', strtotime(substr($post->post_date_gmt,0,10). " + $GGscadenza days"));
					$lista.= "</li>";					
					}
				}
				if (!empty($lista)){
					echo "<h4>Circolari da firmare senza data di scadenza</h4>
			<ul>
					$lista
			</ul>";
			if (isset($_GET['opt']) && $_GET['opt']=="aggsca")
				echo "Aggiornamento effettuato";
			else
				echo '<p style="text-align:left;font-size:1em;font-style: italic;">Aggiorna la Data entro cui firmare = aggiunngendo '.$GGscadenza.' giorni alla data di pubblicazione <spam style="text-align:center;font-size:1.5em;font-weight: bold;"> <a href="edit.php?post_type=circolari_scuola&page=Utility&action=versca&opt=aggsca">Aggiorna</a></spam>
	</p>
	</div>';			
			}else
				echo "<h4>Tutte le circolari da firmare hanno la data di scadenza</h4>";
			break;
		case "verforsca":
				echo "<p style='text-align:center;font-size:1.5em;font-weight: bold;'>Stato Operazioni:</p>";
				$Posts = get_posts('post_type=circolari_scuola&numberposts=-1');
				foreach($Posts as $post){
					$scadenza=get_post_meta($post->ID, "_scadenza",TRUE);
					$Data=explode("-",$scadenza);
					if (!empty($scadenza) And (strlen($Data[0])<4 or strlen($Data[1])<2 or strlen($Data[2])<2)){
						$lista.="			<li>$post->ID $post->post_title ";
						if (isset($_GET['opt']) && $_GET['opt']=="aggsca"){
							if (update_post_meta($post->ID,"_scadenza",NormalData($scadenza)))
								$lista.= ' <img src="'.Circolari_URL.'/img/verificato.png" alt="Icona Permessi" style="display:inline;margin-left:5px;"/>';
						}else
						$lista.="<spam style='font-weight: bold;'>".$scadenza ."</spam> la data verrà aggiornata a:<spam style='font-weight: bold;'>".NormalData($scadenza)."</spam>";
					$lista.= "</li>";					
					}
				}
				if (!empty($lista)){
					echo "<h4>Circolari da firmare con formato data di scadenza errata</h4>
			<ul>
					$lista
			</ul>";
			if (isset($_GET['opt']) && $_GET['opt']=="aggsca")
				echo "Aggiornamento effettuato";
			else
				echo '<p style="text-align:left;font-size:1em;font-style: italic;">Aggiorna il formato della Data entro cui firmare <spam style="text-align:center;font-size:1.5em;font-weight: bold;"> <a href="edit.php?post_type=circolari_scuola&page=Utility&action=verforsca&opt=aggsca">Aggiorna</a></spam>
	</p>
	</div>';			
			}else
				echo "<h4>Tutte le circolari da firmare hanno la data di scadenza in formato corretto</h4>";
			break;
		case "updfirme":
			if (!isset($_GET["CircoUtility"])) {
				header('Location: '.get_bloginfo('wpurl').'/wp-admin/edit.php?post_type=circolari_scuola&page=Utility');    
				exit;	
			}
			if (!wp_verify_nonce($_GET["CircoUtility"],'AggCampiFirma')){
				header('Location: '.get_bloginfo('wpurl').'/wp-admin/edit.php?post_type=circolari_scuola&page=Utility');
				exit;
			}		
			$Circolari = get_posts("post_type=circolari_scuola&orderby=date&posts_per_page=-1&post_status=any");
			foreach($Circolari as $post) {
				$firma=get_post_meta($post->ID,"_firma");
				$sciopero=get_post_meta($post->ID,"_sciopero");
				$sign=get_post_meta($post->ID,"_sign");
				$NC1=get_post_meta($post->ID,"_numero");
				$NC2=get_post_meta($post->ID,"_anno");
				if (isset($sign[0]))
					echo "<strong><em>Circolare ".$post->ID."  N. ".$NC1[0]."_".$NC2[0]." gi&aacute; Aggiornato</strong></em> ";
				else{
					echo "Circolare ".$post->ID."  N. ".$NC1[0]."_".$NC2[0]." Aggiornata a:";
					if ($sciopero[0]=="Si"){
						update_post_meta($post->ID,"_sign","Sciopero" );
						echo "Sciopero";
					}else
						if($firma[0]=="Si"){
							update_post_meta($post->ID,"_sign","Firma" );
							echo "Firma";				
						}
						else{
							update_post_meta($post->ID,"_sign","NoFirma" );
							echo "NoFirma";
						}
					$Calcellata="";
					if(delete_post_meta($post->ID, "_firma")==TRUE)
						$Calcellata= " Firma ";
					if(delete_post_meta($post->ID, "_sciopero")==TRUE)
						$Calcellata= " Sciopero";
					if($Calcellata!="")
						echo "<strong> Cancellata per questa circolare l'impostazione ".$Calcellata."</strong>"; 
				}
				echo "<br />";
			}
			echo "<div class='update-nag'><em><strong>Operazione conclusa con successo!</em></strong></div>";
			echo '<meta http-equiv="refresh" content="5;url=edit.php?post_type=circolari_scuola&page=Utility"/>';
			break;
		}
	return;	
}
	if ($Stato!="") 
		echo '<div id="message" class="updated"><p>'.str_replace("%%br%%","<br />",$Stato).'</p></div>
		  <meta http-equiv="refresh" content="5;url=admin.php?page=utility"/>';
	echo '		</div> 
			<p></p>
			<div class="widefat" style="padding:10px;">
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">Verifica procedura</p>
				<ul>
					<li> Questa procedura esegue un test generale della procedura e riporta eventuali anomalie nei dati e nelle impostazioni.</spam><br /><spam style="font-size:1em;font-style: italic;margin-left:10px;font-weight: bold;">
		Verifica Presenza data scadenza firma <spam style="text-align:center;font-size:1.5em;font-weight: bold;"> <a href="edit.php?post_type=circolari_scuola&page=Utility&action=versca">Verifica</a></spam>
					</li>
					<li style="text-align:left;font-size:1em;">Questa procedura verifica il formato delle date di scadenza.<br /><spam style="font-size:1em;font-style: italic;margin-left:10px;font-weight: bold;">
		Verifica Formato data scadenza firma <spam style="text-align:center;font-size:1.5em;font-weight: bold;"> <a href="edit.php?post_type=circolari_scuola&page=Utility&action=verforsca">Verifica</a></spam>
					</li>
				</ul>
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">Importa Gruppi ed impostazioni da Circolari Groups</p>
					<ul>
						<li style="text-align:left;font-size:1em;">
						<p>Questa procedura deve essere eseguita solo una volta dopo la migrazione da Circolari Groups.</p>
						<p>Azioni da eseguire per la migrazione:
						<ol>
							<li><span style="color:red;font-weight: bold;">Aggiornare il plugin Circolari Groups</span></li>
							<li>Disinstallare Circolari Groups</li>
							<li>Installare Circolari</li>
							<li>Lanciare la seguente procedura di importazione dei dati da Circolari Groups</li>
							<li>A questo punto, se non utilizzato per altri scopi, si può disinstallare il plugin Groups</li>
						</ol>
						</p>
						<p>
						Le oprazioni che verranno eseguite sono:
						<ol>
							<li>Importazione dei Gruppi</li>
							<li>Aggiornamento delle impostazioni delle circolari</li>
						</ol>
						</li>
						</p>
					</ul>
					<spam style="text-align:center;font-size:1.5em;font-weight: bold;">
						<a href="edit.php?post_type=circolari_scuola&page=Utility&action=importa">Importa da Circolari Groups</a>
					</spam>	
	';
}
function TestataCircolari() {
global $TestiRisposte,$post;
	
if( isset($post->post_type) And $post->post_type != 'circolari_scuola' ) {
	return;
}
$sele="";
if($TestiRisposte){
	foreach($TestiRisposte as $Risp){
		$sele.='if ($(".s'.$Risp->get_IDRisposta().'-"+$(this).attr(\'id\')).attr(\'checked\')=="checked")	s="'.$Risp->get_Risposta().'";
		';
	}	
}
?>
<script type='text/javascript'>
jQuery.noConflict();
(function($) {
	$(function() {
		$('.inviaadesione').click(function(){
			<?php echo $sele;?>
			var to=$("#to").val();
			var answer = confirm("Circolare "+$(this).attr('rel') +"\nConfermi la scelta: " + s +"?")
			if (answer){
				return true;
			}
			else{
				return false;
			}					
		});
 });
})(jQuery);
</script>
<?php
}

function circolari_MenuPagine(){
	switch (filter_input(INPUT_GET,"op")){
		case "Firme":
			circolari_VisualizzaFirme((int)$_REQUEST["post_id"]);
			break;
		case "Adesioni":
			circolari_VisualizzaFirme((int)$_REQUEST["post_id"],1);
			break;
		case "email":
			circolari_SpostainNewsletter((int)$_REQUEST["post_id"]);
			break;
		case "Log":
			circolari_VisualizzaLog((int)$_REQUEST["post_id"]);
			break;
		default:
			circolari_Parametri();	
	}
}
function circolari_uninstall() {
	global $wpdb;
// Eliminazione Tabelle data Base
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_firme_circolari);
	$Circolari = get_posts( "post_type=circolari_scuola" );
	foreach ( $Circolari as $Circolare )
		set_post_type( $Circolare );	
	delete_option('Circolari_Versione');
	delete_option('Circolari_GestPerm');
	delete_option('Circolari_Visibilita_Pubblica');
	delete_option('Circolari_GGScadenza');
	delete_option('Circolari_NotificaFirma');
	delete_option('Circolari_From_NotificaFirma');
	delete_option('Circolari_Tipo');
	delete_option('Circolari_TestiRisposte');
}

function circolari_VisualizzaLog($IDPost){
	global $TestiRisposte;
	MakeDir();
	$numero=get_post_meta($IDPost, "_numero");
	$anno=get_post_meta($IDPost, "_anno");
	$circolare=get_post($IDPost);
	$Dati=Get_Log_Circolare($IDPost);
?>	
	<div class="wrap">
		<i class="fa fa-th-list fa-3x" aria-hidden="true"></i>
		<h2 style="display:inline;margin-left:10px;vertical-align:super;">Log Firme Circolare n° <?php echo $numero[0].'_'.$anno[0];?></h2>
		<h3><?php echo $circolare->post_title;?></h3>
			<table  id="TabellaCircolari" class="widefat"  cellspacing="0" width="99%">
			<thead>
				<th>Utente</th>
				<th>Data Ora</th>
				<th>Operazione</th>
				<th>Espressione</th>
			</thead>
			<tbody>
<?php foreach($Dati as $Riga){ ?>
				<tr>
					<td><?php echo $Riga['Utente']?></td>
					<td><?php echo $Riga['Data']?></td>
					<td><?php echo $Riga['Operazione']?></td>
					<td><?php echo $Riga['Espressione']?></td>
				</tr>
<?php } ?>
			</tbody>
			</table>
		</div>
<?php
}

function circolari_SpostainNewsletter($IDPost){
$DatiPost=get_post( $IDPost,  ARRAY_A);
		$args = array(
			'post_type' => 'attachment',
			'numberposts' => null,
			'post_status' => null,
			'post_parent' => $IDPost); 
		$attachments = get_posts($args);
		$LinkAllegati="";
		if ($attachments) {
			$LinkAllegati.="<p>Allegati
			<ul>";
			foreach ($attachments as $attachment) {
				$LinkAllegati.="		<li><a href='$attachment->guid'>$attachment->post_title</a></li>";
			}
			$LinkAllegati.="</p>
			</ul>";	
		}
$my_post = array(
  		'post_title'    => $DatiPost['post_title'],
  		'post_content'  => "<p>Ciao [USER-NAME]</p>
<p>in data odierna è stata inserita la seguente circolare nel sito [SITE-NAME]</p>
<p>[POST-EXCERPT]</p>
<p>[POST-CONTENT]</p>
<p><a href='".get_permalink($IDPost)."'>Visualizza la circolare sul sito</a> ".$LinkAllegati,
  		'post_status'   => 'publish',
  		'comment_status'   => 'closed',
  		'ping_status' => 'closed',
  		'post_author' => $DatiPost['post_author'],
  		'post_name' => $DatiPost['post_name'],
  		'post_type' => 'newsletter');
$post_id =wp_insert_post( $my_post );
echo '<div class="wrap">
	  	<i class="fa fa-envelope fa-3x" aria-hidden="true"></i><h2 style="display:inline;margin-left:10px;vertical-align:super;">Crea NewsLetter 
	  	<a href="'.site_url().'/wp-admin/edit.php?post_type=circolari_scuola" class="add-new-h2 tornaindietro">Torna indietro</a></h2>';

	if($post_id>0){
		$recipients=Array();
		$recipients['list'][] = 1;
		$recipients['list'][] = 2;
		add_post_meta ( $post_id, "_easymail_recipients", $recipients );	
		add_post_meta ( $post_id, "_placeholder_easymail_post",  $IDPost);	
		add_post_meta ( $post_id, "_placeholder_post_imgsize", 'thumbnail' );	
		add_post_meta ( $post_id, "_placeholder_newsletter_imgsize", 'thumbnail' );	
		add_post_meta ( $post_id, "_easymail_theme", 'campaignmonitor_elegant.html' );	
		echo "<p style='font-weight: bold;font-size: medium;color:green;'>NewsLetter Creata correttamente</p> 
		<p style='font-weight: bold;font-style: italic;font-size: medium;'>Adesso dovete completare le operazioni di invio seguendo pochi e semplici passi:<ul style='list-style: circle outside;margin-left:20px;'>
			<li>Entrare in modifica nella <a class='function' href='".admin_url()."post.php?post=".$post_id."&action=edit'>circolare appena creata</a> (l'ultima, quella in cima alla lista)</li>
			<li>Selezionate i destinatari</li>
			<li>Memorizzare le modifiche</li>
			<li>Dall'elenco delle NewsLetter, sulla riga corrente cliccare su <em>Richiesto: Crea la lista dei destinatari</em></li>
		</ul>
		</p>";
		add_post_meta ( $IDPost, "_sendNewsLetter",date("d/m/y g:i O"));
		add_post_meta ( $IDPost, "_placeholder_easymail_post",$post_id);
	}else{
		echo "<p  style='font-weight: bold;font-size: medium;color:red;'>NewsLetter Non Creata correttamente, errore riportato:</p>";
				print_r($errore);			
	}
}

function circolari_Parametri(){
	$DestTutti  =  get_option('Circolari_Visibilita_Pubblica');
	$GiorniScadenza  =  get_option('Circolari_GGScadenza');
	$NotificaFirma = get_option('Circolari_NotificaFirma');
	$MittenteNotifica= get_option('Circolari_From_NotificaFirma');
	$OggettoNotifica= get_option('Circolari_Oggetto_NotificaFirma');
	$MessaggioNotifica=get_option('Circolari_Messaggio_NotificaFirma');
	$GestPerm=get_option('Circolari_GestPerm');
	if($GestPerm=="ext")
		$GPE=" checked='checked'";
	else
		$GPP=" checked='checked'";
	if($CircolatiGutenberg){
		$CKGutenberg="checked";
	}else{
		$CKGutenberg="";
	}
	echo'
<div class="wrap">
	  <i class="fa fa-magic fa-3x" aria-hidden="true"></i><h2 style="display:inline;margin-left:10px;vertical-align:super;">Configurazione Circolari</h2>
	  <form name="Circolari_cnf" action="'.get_bloginfo('wpurl').'/wp-admin/index.php" method="post">
	  <input type="hidden" name="circoPar" value="'.wp_create_nonce('ParametriCircolare').'" />
	  <table class="form-table circolari-config">
		<tr valign="top">
			<th scope="row"><label for="pubblica">Gruppo Pubblico Circolari</label></th>
			<td><select name="pubblica" id="pubblica" >';
			$bloggroups =get_terms('gruppiutenti',array('orderby'=> 'name','hide_empty'=> false));
			foreach ($bloggroups as $gruppo) {
		        echo '<option value="'.$gruppo->term_id.'" ';
				//$e.= "Memorizzato ".$DestTutti." Id ".$gruppo->term_id."<br />";
				if($DestTutti==$gruppo->term_id) 
					echo 'selected="selected"';
				echo '>'.$gruppo->name.'</option>';	
			}
echo'</select></td>				
		</tr>
		<tr valign="top">
			<th scope="row"><label for="GGScadenza">N. giorni entro cui firmare di default</label></th>
			<td>
				<input type="text" name="GGScadenza" id="GGScadenza" size="3" maxlength="3" value="'.$GiorniScadenza.'" />
			</td>				
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="NotificaFirma">Notifica firma tramite @mail</label>
			</th>
			<td>		
				<input type="checkbox" name="NotificaFirma" id="NotificaFirma" value="Si" '.($NotificaFirma=="Si"?"checked":"").'/> Attiva
			</td>				
		</tr>					
		<tr valign="top">
			<th scope="row">
				<label for="FromNotificaFirma">Mittente @mail</label>
			</th>
			<td>		
				<input type="text" name="FromNotificaFirma" id="FromNotificaFirma" value="'.$MittenteNotifica.'" size="65"/><br />
					<em>Indicare il mittente nel formato: Nome del mittente &lt;indirizzo@dminio.estensione&gt;</em>
			</td>				
		</tr>					
		<tr valign="top">
			<th scope="row">
				<label for="OggettoNotificaFirma">Oggetto @mail</label>
			</th>
			<td>		
				<input type="text" name="OggettoNotificaFirma" id="OggettoNotificaFirma" value="'.$OggettoNotifica.'" size="65"/>
			</td>				
		</tr>					

		<tr valign="top">
			<th scope="row">
				<label for="$MessaggioNotifica">Messaggio @mail</label>
			</th>
			<td>		
				<textarea name="MessaggioNotifica" id="MessaggioNotifica" cols="150" rows="10">'.$MessaggioNotifica.' </textarea><br />
				{Link_Circolare} aggiunge il link alla circolare firmata<br />
				{Dati_Utente} visualizza una stringa nel formato &quot;None Cognome&quot; dell\'utente che ha firmato la circolare<br />
				{Data} visualizza una stringa nel formato &quot;gg/mm/aaaa - hh:mm&quot; del momento in cui viene firmata la circolare<br />
				{Operazione} Visualizza l\'operazione effettuata : riportando il testo impostato in Tipi di circolari > Risposte
			</td>				
		</tr>					
		<tr valign="top">
			<th scope="row"><label for="GestPerm">Tipologia gestione permessi</label></th>
			<td>
				<input type="radio" name="GestPerm" id="GestPerm" value="int" '.$GPP.'" />Standard
				<input type="radio" name="GestPerm" id="GestPerm" value="ext" '.$GPE.'" />Esteso
				<ul style="list-style: disc;font-style: italic;">
					<li>Standard: vengono mantenuti le capabilities standard dei Posts</li>
					<li>Esteso: vengono creati capabilities
						<p>Capabilities
						<ul style="margin-left:30px;list-style: circle;font-style: italic;">
							<li>delete_circolares</li>
							<li>delete_others_circolares</li>
							<li>delete_private_circolares</li>
							<li>delete_published_circolares</li>
							<li>edit_circolares</li>
							<li>edit_others_circolares</li>
							<li>edit_private_circolares</li>
							<li>edit_published_circolares</li>
							<li>publish_circolares</li>
							<li>read_private_circolares</li>
							<li>send_circ@mail; <strong>permette di gestire le newsletter</strong></li>
							<li>manage_adesioni; <strong>permette la gestione delle adesioni/firme</strong></li>
						</ul>
						<em><strong>Per la gestione delle capabilities si consiglia l\'uso del plugin <a href="https://wordpress.org/plugins/user-role-editor">User Role Editor</a></strong></em> 
						</p>
					</li>
				</ul>
			</td>				
		</tr>	
	</table>
	    <p class="submit">
	        <input type="submit" name="Circolari_submit_button" value="Salva Modifiche" />
	    </p> 
	    </form>
	    </div>';
}
// Nuova Colonna Gestione  
function circolari_NuoveColonne($defaults) {  
	if ($_GET['post_type']=="circolari_scuola"){
		$defaults['stato'] = 'Parametri Circolare'; 
	    if (current_user_can('edit_others_posts')){
			$defaults['gestionecircolari'] = 'Gestione';  
		}
	}
   return $defaults;  
}  
  
// Visualizzazione nuova colonna Gestione  
function circolari_NuoveColonneContenuto($column_name, $post_ID) {  
	global $wpdb;
 	if ($_GET['post_type']=="circolari_scuola"){
		$sign=get_post_meta($post_ID, "_sign",TRUE);
		$tipo=Circolari_find_Tipo($sign);
		if($tipo===FALSE){
			 echo "<span style=\"color:red;\">ERRORE!<br />Tipo di circolare non definito</span>";
			 return;
		}
		if ($column_name == 'gestionecircolari' And current_user_can( 'edit_others_posts')) {  
			$LinkNL="";			
			if (current_user_can( "manage_adesioni" ) Or get_option('Circolari_GestPerm')=="int")
				$GestFA=True;
			else
				$GestFA=False;		
			$Prefisso= $tipo->get_Popup();

			$Linkfirma="";
			$LinkLog="";
			if ($tipo->get_Tipo()=="Firma" )
				$Linkfirma='<a class="function" href="'.admin_url().'edit.php?post_type=circolari_scuola&page=circolari&op=Firme&post_id='.$post_ID.'"><i class="fa fa-pencil" title="Visualizza '.$Prefisso.'"></i></a>';
			elseif($tipo->get_Tipo()!="NoFirma")
				$Linkfirma='<a class="function" href="'.admin_url().'edit.php?post_type=circolari_scuola&page=circolari&op=Adesioni&post_id='.$post_ID.'"><i class="fa fa-pencil-square-o" title="Visualizza '.$Prefisso.'"></i></a>';
	    	if ( defined( 'ALO_EM_INTERVAL_MIN' ) ){
				$DataInvio = get_post_meta( $post_ID, "_sendNewsLetter", true); 
				$IDNL=$wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta Where post_id=$post_ID And meta_key='_placeholder_easymail_post';");
				if(count($IDNL)>0){
					$NL=$wpdb->get_results("SELECT ID FROM $wpdb->posts Where ID=".$IDNL[0]->meta_value.";");
				}
	    		if ($DataInvio And count($IDNL)>0 And count($NL)>0){
					$LinkNL.=' <a class="function" href="'.admin_url().'post.php?post='.$IDNL[0]->meta_value.'&action=edit"><i class="fa fa-envelope" title="Visualizza NewsLetter <br />generata in data '. $DataInvio.'"></i></a>';
				}else
	            	$LinkNL.=' <a class="function" href="'.admin_url().'edit.php?post_type=circolari_scuola&page=circolari&op=email&post_id='.$post_ID.'"><i class="fa fa-inbox" title="Genera NewsLetter"></i></a>';  
			}
			if ($tipo->get_Tipo()!='NoFirma'){
				$LinkLog=' <a class="function" href="'.admin_url().'edit.php?post_type=circolari_scuola&page=circolari&op=Log&post_id='.$post_ID.'"><i class="fa fa-th-list" aria-hidden="true" title="Visualizza Log delle Firme della Circolare"></i></a>';
			}
			echo '<div class="btn-group">'.$Linkfirma.$LinkNL.$LinkLog.'</div>';
	     }
		 if ($column_name == 'stato'){
			$Dati=""; 
			$numero=get_post_meta($post_ID, "_numero",TRUE);
			$anno=get_post_meta($post_ID, "_anno",TRUE);
			$Info= '<i class="fa fa-vcard-o" aria-hidden="true"></i> '.$numero.'_'.$anno." ";		 
			$Dati="Circolare N° ".$numero.'_'.$anno."<br />"; 
			if(get_post_meta($post_ID, "_visibilita", "d")=="d"){
				$Info.= '<i class="fa fa-lock" aria-hidden="true"></i><br />';
				$Dati.="Visibilità:Riservata <br />";
			}else{
				$Info.= '<i class="fa fa-unlock" aria-hidden="true"></i><br />';
				$Dati.="Visibilità:Pubblica <br />";
			}
			$Dati.="Tipo: <strong>".$tipo->get_Tipo()."</strong> <br />";
			$Info.= '<i class="fa fa-hand-pointer-o" aria-hidden="true"></i> '.$tipo->get_Tipo().'<br />';
			if (Is_da_Firmare($post_ID)){
				if(Is_Circolare_Scaduta($post_ID)){
					$Color="red;";
				}else{
					$Color="#14D700;";
				}
				$dest=wp_get_post_terms( $post_ID, 'gruppiutenti', array("fields" => "ids") ); 
				$NU=0;
				$DaVis="";
				$DaVisDati="";
				if (current_user_can( 'edit_others_posts')){
					$IdGruppoTutti=get_option('Circolari_Visibilita_Pubblica');
					$Firmate=Get_Numero_Firme_Per_Circolare($post_ID);
					if(in_array($IdGruppoTutti,$dest))
						$DaVis= $Firmate."/".Get_User_Per_Gruppo($IdGruppoTutti);
					else{
						foreach($dest as $IdGruppo)
							if ($IdGruppoTutti!=$IdGruppo)
								$NU+=Get_User_Per_Gruppo($IdGruppo);
						$DaVis=  $Firmate."/$NU";			
					}
					$DaVis="(".$DaVis.")";
					$DaVisDati=" Firmate ".$Firmate." su ".$NU."<br />";
				}
				$Info.= ' <i class="fa fa-pencil" aria-hidden="true" style="color:'.$Color.'"></i> '.FormatDataItalianoBreve(get_post_meta($post_ID, "_scadenza",TRUE)).$DaVis."<br />";
				$Dati.="Data scadenza firma ".FormatDataItalianoBreve(get_post_meta($post_ID, "_scadenza",TRUE))."<br />".$DaVisDati;
			}
			$DesDest=GetEencoDestinatari($post_ID,TRUE);
			$Info.= '<i class="fa fa-users" aria-hidden="true"></i> '.$DesDest;
			$Dati.="Destinatari Circolari: ".$DesDest;
			echo '<a href="#" title="'.$Dati.'" class="nolink">'.$Info."</a>";
		}
	}
}  

// add links/menus to the admin bar

function circolari_admin_bar_render() {
	global $wp_admin_bar,$DaFirmare;
	if (!isset($DaFirmare))
		$DaFirmare=GetCircolariDaFirmare("N");
	if ($DaFirmare>0)
		$VisNumCircolari=' <span style="background-color:red;">&nbsp;'.$DaFirmare.'&nbsp;</span>';
	else
		$VisNumCircolari="";
	$wp_admin_bar->add_menu( array(
		'id' => 'pluginCircoalri', // link ID, defaults to a sanitized title value
		'title' => 'Circolari '.$VisNumCircolari, // link title
		'href' => site_url().'/wp-admin/edit.php?post_type=circolari_scuola&page=Firma', // name of file
		'meta' => array(  'title' => 'Circolari da Firmare' )));
}

function circolari_updated_messages( $messages ) {
	global $post, $post_ID;
    $messages['circolari'] = array(
	0 => '', 
	1 => sprintf('Circolare aggiornata. <a href="%s">Visualizza Circolare</a>', esc_url( get_permalink($post_ID) ) ),
	2 => 'Circolare aggiornata',
/* translators: %s: date and time of the revision */
	3 => isset($_GET['circolari']) ? sprintf( 'Circolare ripristinata alla versione %s', wp_post_revision_title( (int) $_GET['circolari'], false ) ) : false,
	4 => sprintf( 'Circolare pubblicata. <a href="%s">Visualizza Circolare</a>', esc_url( get_permalink($post_ID) ) ),
	5 => 'Circolare memorizzata',
	6 => sprintf( 'Circolare inviata. <a target="_blank" href="%s">Anteprima Circolare</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	7 => sprintf( 'Circolare schedulata per: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Anteprima circolare</a>',date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
	8 => sprintf( 'Bozza Circolare aggiornata. <a target="_blank" href="%s">Anteprima Circolare</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
);
return $messages;
}
function circolari_salva_dettagli( $post_id ){
//	print_r($_POST);exit;
		if ( filter_input(INPUT_POST,'post_type') == 'circolari_scuola' ) {	
			delete_post_meta( $post_id, '_scadenza' );
			delete_post_meta( $post_id, '_numero' );
			delete_post_meta( $post_id, '_anno' );
			delete_post_meta( $post_id, '_visibilita' );
			wp_set_post_categories( $post_id, array($Circolari) );
			$term_list = wp_get_post_terms($post_id, 'gruppiutenti', array("fields" => "names"));
			if (count($term_list)==0) {
				$DestTutti=get_option('Circolari_Visibilita_Pubblica');
				wp_set_object_terms( $post_id, (int)$DestTutti,"gruppiutenti",FALSE );
			}
			if ($_POST["Sign"]!="NoFirma"){
				if ($_POST["scadenza"])
					update_post_meta( $post_id, '_scadenza', FormatDataDB($_POST["scadenza"]));
				else
					update_post_meta( $post_id, '_scadenza', FormatDataDB($_POST["scadenza"],get_option('Circolari_GGScadenza')));
			}else{
				update_post_meta( $post_id, '_scadenza', "");
			}
			if (isset($_POST["Sign"]))
				update_post_meta( $post_id, '_sign', $_POST["Sign"]);
			else
				update_post_meta( $post_id, '_sign', "NoFirma");
			if (isset($_POST["numero"])){
				  update_post_meta( $post_id, '_numero', (int)$_POST["numero"]);
			}
			if (isset($_POST["anno"])) update_post_meta( $post_id, '_anno', $_POST["anno"]);
			if (isset($_POST["visibilita"])) update_post_meta( $post_id, '_visibilita', $_POST["visibilita"]);

		}
}

function circolari_crea_box(){
  add_meta_box('parametri', 'Parametri Circolari', 'circolari_crea_box_parametri', 'circolari_scuola', 'side', 'high',array(
        '__block_editor_compatible_meta_box' => true,
    ));

/*  add_meta_box('prog', 'Progressivo', 'circolari_crea_box_progressivo', 'circolari', 'advanced', 'high');
  add_meta_box('firma', 'Richiesta Firma', 'circolari_crea_box_firma', 'circolari', 'advanced', 'high');
  add_meta_box('sciopero', 'Circolare comunicazione Sciopero', 'circolari_crea_box_firma_sciopero', 'circolari', 'advanced', 'high');
  add_meta_box('visibilita', 'Visibilit&agrave;', 'circolari_crea_box_visibilita', 'circolari', 'advanced', 'high');*/
}

function circolari_crea_box_parametri( $post ){
echo "<h4>Firmare entro</h4>";	
circolari_crea_box_data_scadenza($post);
echo "<h4>Progessivo</h4>";
circolari_crea_box_progressivo($post);
echo "<h4>Firme</h4>";
circolari_crea_box_firma($post);
//circolari_crea_box_firma_sciopero($post);
echo "<h4>Visibilit&agrave;</h4>";
circolari_crea_box_visibilita($post);
}

function NewNumCircolare($numero){
	$args = array( 'numberposts' => '1','post_type'=> 'circolari_scuola','post_status' => 'publish','meta_key' => '_anno','meta_value' => $numero);
	$ultimo=wp_get_recent_posts($args);
	$ID=$ultimo[0]['ID'];
	$numero=get_post_meta($ID, "_numero");
	return $numero[0]+1;
}
function circolari_crea_box_progressivo( $post ){
$numero=get_post_meta($post->ID, "_numero",TRUE);
$anno=get_post_meta($post->ID, "_anno",TRUE);
if ($anno=="" or !$anno){	
	$anno=date("Y");	
	$canno=strval(date("y"))+1;	
	if (date("n")>8)		
		$anno=$anno."/".$canno;	
	else			
		$anno=($anno-1)."/".date("y");
}
if ($numero=="" or !$numero)
	$numero=NewNumCircolare($anno);
echo '<label>Numero/Anno</label>
	<input type="text" name="numero" value="'.$numero.'" size="5" id="numero_circolare" style="text-align:right"/>_<input type="text" name="anno" value="'.$anno.'" size="5"/>
	<br />' ;
}

function circolari_crea_box_visibilita( $post ){
$visibilita=get_post_meta($post->ID, "_visibilita");
$selp="";
$seld="";
if (count($visibilita)==0)
	$selp='checked="checked"';
else 
	if ($visibilita[0]=="p")
		$selp='checked="checked"';
	else	
		$seld='checked="checked"';
echo 'Pubblica <input type="radio" name="visibilita" value="p" '.$selp.'/><br />
Riservata <input type="radio" name="visibilita" value="d" '.$seld.'/>';
//$term_list = wp_get_post_terms($post->ID, 'gruppiutenti', array("fields" => "names"));
//print_r($term_list);
}
function circolari_crea_box_firma( $post ){
	global $Testi;
$sign=get_post_meta($post->ID, "_sign",TRUE);
echo Circolari_Tipo::get_Tipi($sign);
/*
$firma=get_post_meta($post->ID, "_firma");
if($firma[0]=="Si")
	$firma='checked="checked"';
 echo "<label>E' richiesta la firma</label>
	<input type='checkbox' name='firma' value='Si' $firma />
	<br />" ;
}
function circolari_crea_box_firma_sciopero( $post ){
$sciopero=get_post_meta($post->ID, "_sciopero");
if($sciopero[0]=="Si")
	$sciopero='checked="checked"';
 echo "<label>Circolare sindacale</label>
	<input type='checkbox' name='sciopero' value='Si' $sciopero />
	<br />" ;
*/

}

function circolari_crea_box_data_scadenza( $post ){
$scadenza=FormatDataItalianoBreve(get_post_meta($post->ID, "_scadenza",TRUE));
echo "<label>Data</label> <input type='text' name='scadenza' value='".$scadenza."' size='8' style='text-align:left' id='DataScadenza'/>
	<br />" ;
}

function circolari_VisualizzaFirme($post_id,$Tipo=0){
	global $GestioneScuola,$Testi,$TestiRisposte;
	$numero=get_post_meta($post_id, "_numero");
	$anno=get_post_meta($post_id, "_anno");
	$circolare=get_post($post_id);
	// Inizio interfaccia
	$TipoC=get_post_meta($post_id, "_sign",TRUE);
	echo' 
	<div class="wrap">
		<h2 >Circolare n° '.$numero[0].'_'.$anno[0].'<br /><strong>'.$circolare->post_title.'</strong></h2>';
	$globale=get_post_meta($post_id, '_visibilita_generale');
	$fgs = wp_get_object_terms($post_id, 'gruppiutenti');
	$Elenco="";
	if(!empty($fgs)){
		foreach($fgs as $fg){
			$Elenco.="<em>".$fg->name."</em> - ";
		}
		$Elenco=substr($Elenco,0,strlen($Elenco)-3);
	}
	echo'
		<p style="font-style:italic;font-weight:bold;margin-top:20px;">
			<i class="fa fa-users fa-2x" aria-hidden="true"></i> '.$Elenco.'</p>
	</div>
	';
	$utenti=Get_Users_per_Circolare($post_id);
	if ($Tipo==1)
		$sottrai=3;
	else	
		$sottrai=0;
	$NumUtentiFirme =count($utenti);
	echo '
	<div style="margin-right:10px;">
		<table  id="TabellaCircolari" class="widefat"  cellspacing="0" width="100%">
			<thead>
				<tr>
					<th style="width:'.(20-$sottrai).'%;">User login</th>
					<th style="width:'.(30-$sottrai).'%;" id="ColOrd" sorted="1">Cognome</th>
					<th style="width:'.(15-$sottrai).'%;">Gruppo</th>
					<th style="width:'.(15-$sottrai).'%;">Data Operazione</th>
					<th style="width:'.(15-$sottrai).'%;">Espressione</th>';
	if ($Tipo==1){
			$Testo=Circolari_find_Tipo($TipoC);
			echo '
						<th style="width:12%;">'.$Testo->get_TestoElenco().'</th>';
		}			
	echo '
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th style="width:'.(20-$sottrai).'%;">User login</th>
					<th style="width:'.(30-$sottrai).'%;">Cognome</th>
					<th style="width:'.(15-$sottrai).'%;">Gruppo</th>
					<th style="width:'.(15-$sottrai).'%;">Data Operazione</th>
					<th style="width:'.(15-$sottrai).'%;">Espressione</th>';
	if ($Tipo==1){
			$Testo=Circolari_find_Tipo($TipoC);
			echo '
						<th style="width:12%;">'.$Testo->get_TestoElenco().'</th>';
		}				
	echo '
				</tr>
			</tfoot>
			<tboby>';
	foreach($utenti as $utente){
		$GruppoUtente=get_user_meta($utente->ID, "gruppo", true);
		if(($firma=get_Firma_Circolare($post_id,$utente->ID))===FALSE){
			$Firma="";
		}else{
			$Firma=$firma->datafirma;	
		}
		if($GruppoUtente!==""){
			$gruppiutenti=get_terms('gruppiutenti', array('hide_empty' => true,'include'=>$GruppoUtente));
			$DesGsU="";
			foreach($gruppiutenti as $gruppoutente){
				$DesGsU.=$gruppoutente->name.", ";
			}
			$DesGsU=substr($DesGsU,0,strlen($DesGsU)-2);
		}else{
			$DesGsU="<span style='color:red;'>Nessuno</span>";
		}
		echo '
					<tr>
						<td>'.$utente->user_login.'</td>
						<td>'.$utente->display_name.'</td>
						<td>'.$DesGsU.'</td>
						<td>'.$Firma.'</td>';
		if (isset($firma->adesione)) 
			if(Circolari_is_set_IDRisposte($firma->adesione))
				$desad=$TestiRisposte[(int)$firma->adesione]->get_Risposta();
			else
				$desad="Errore Etichetta non trovata";
		else
			$desad="";
		echo '
					<td>'.$desad.'</td>
					</tr>';
	}
	echo'
				</tbody>
			</table>
	</div>
	';
}	

?>