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
$wpdb->table_circolari_firme = $table_prefix . "firme_circolari";

define("wps_Circolari_URL",get_template_directory_uri()."/plugins/gestione-circolari");
define("wps_Circolari_DIR",dirname (__FILE__));
define("wps_Circolari_DIR_Servizio",WP_CONTENT_DIR."/Circolari");
include_once(wps_Circolari_DIR."/admin/gruppi.php");
include_once(wps_Circolari_DIR."/admin/firme.php");
include_once(wps_Circolari_DIR."/functions.inc.php");
include_once(wps_Circolari_DIR."/GestioneCircolari_CustomPostType.php");
include_once(wps_Circolari_DIR."/admin/testi.php");
if((get_option('wps_Circolari_ModuloCircolari')!= 'Si'||!get_option('wps_Circolari_ModuloCircolari')) And get_theme_mod("scuola_circolari_attiva")){
	wps_circolari_activate();
	update_option('wps_Circolari_ModuloCircolari', 'Si');
}
if(get_option('wps_Circolari_ModuloCircolari')== 'Si' And !get_theme_mod("scuola_circolari_attiva")){
	wps_circolari_uninstall();
	update_option('wps_Circolari_ModuloCircolari', 'No');
}
$msg="";
$wps_TestiRisposte="";
$wps_Testi="";
require_once(ABSPATH . 'wp-includes/pluggable.php'); 
if(isset($_REQUEST["op"])){
	if (isset($_REQUEST['circoFir'])) 
		if (wp_verify_nonce($_REQUEST['circoFir'],'FirmaCircolare')){
			switch ($_REQUEST["op"]){
				case "Firma":
					global $msg;
					$msg=wps_FirmaCircolare((int)$_REQUEST["pid"],4);
					break;
				case "Adesione":
					global $msg;
					$msg=wps_FirmaCircolare((int)$_REQUEST["pid"],$_REQUEST["scelta"]);
					break;	
			}			
		}else
			wp_die("Errore di sicurezza");

	if (isset($_REQUEST['circoRmFir'])){
		if (wp_verify_nonce($_REQUEST['circoRmFir'],'RmFirmaCircolare') And $_REQUEST["op"]=="RemoveFirma"){
			global $msg;
			$msg=wps_RimuoviFirmaCircolare($_REQUEST["pid"]);
			wp_redirect( $_SERVER['HTTP_REFERER'] );
		}	
	}
}
if (isset($_GET['update']) And $_GET['update'] == 'true')
	$stato="<div id='setting-error-settings_updated' class='updated settings-error'> 
			<p><strong>".__( 'Impostazioni salvate', 'wpscuola' ).".</strong></p></div>";

function wps_circolari_post_type_archive( $query ) { 
	if (get_post_type()=='newsletter' ) {
	     $query->set('post_type', array( 'post', 'circolari_scuola' ) );
		return $query; 
	}
	if(current_user_can('administrator')) return $query;
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

function wps_circolari_posts_where($where, $query) {
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
	$CondPrivati= " OR ( ".$wpdb->term_relationships.".term_taxonomy_id IN ".$GruppoUtente." And ".$wpdb->postmeta.".meta_value ='d' )";
	$where=substr($where,strpos($where, "AND",3));
	$where=$where." ".$CondPrivati;
	return $where;
}

function wps_circolari_scuola_rewrite_rules(){

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

add_filter( 'post_updated_messages', 		'wps_circolari_updated_messages');
add_filter( 'manage_posts_columns', 		'wps_circolari_NuoveColonne');  
add_filter( 'post_row_actions',				'wps_circolari_remove_quick_edit',10,2);
add_filter( 'wp_get_attachment_url', 		'wps_circolari_getFileUrl', 10, 2);
add_filter( 'the_content', 					'wps_circolari_FiltroVisualizzaCircolare');
add_filter( 'get_the_excerpt', 				'wps_circolari_FiltroVisualizzaRiassuntoCircolare' );
add_filter( 'posts_where', 					'wps_circolari_posts_where', 10, 2);

add_action( 'init', 						'wps_circolari_scuola_rewrite_rules' );
add_action( 'save_post', 					'wps_circolari_salva_dettagli');
add_action( 'add_meta_boxes',				'wps_circolari_crea_box');
add_action( 'manage_posts_custom_column', 	'wps_circolari_NuoveColonneContenuto', 10, 2); 
add_action( 'admin_menu', 					'wps_circolari_add_menu' ); 
add_action( 'init', 						'wps_circolari_Inizializzazione');
// Disattivate le notifiche per rallentamento BackEnd
add_action( 'wp_before_admin_bar_render', 	'wps_circolari_admin_bar_render' );
add_action( 'admin_menu', 					'wps_circolari_add_menu_bubble' );
add_action( 'wp_head', 						'wps_circolari_Testata' );
add_action( 'admin_enqueue_scripts',  		'wps_circolari_Admin_Enqueue_Scripts' );
add_action( 'wp_enqueue_scripts', 			'wps_circolari_FrontEnd_Enqueue_Scripts' );
add_action( 'do_feed', 						'wps_circolari_disable_feed', 1);
add_action( 'do_feed_rdf', 					'wps_circolari_disable_feed', 1);
add_action( 'do_feed_rss', 					'wps_circolari_disable_feed', 1);
add_action( 'do_feed_rss2', 				'wps_circolari_disable_feed', 1);
add_action( 'do_feed_atom', 				'wps_circolari_disable_feed', 1);
add_action( 'pre_get_posts', 				'wps_circolari_post_type_archive' );  
add_action( 'save_post', 					'set_post_default_group', 10,3 );

function wps_circolari_Inizializzazione(){
	global $wps_TestiRisposte,$wps_Testi;
/**
*  Creazione del custom post type Circolari
*/    
	wps_crea_custom_circolari();
/**
* Impostazione dei tipi di circolari e dei testi delle risposte 
*/
	if(FALSE!==($wps_TestiRisposte=get_option('Circolari_TestiRisposte'))){
    	$wps_TestiRisposte= unserialize($wps_TestiRisposte);
    }else{
    	$wps_TestiRisposte=wps_circolari_CreaTestiRisposta();
    }
   	if(FALSE!==($wps_Testi=get_option('Circolari_Tipo'))){
    	$wps_Testi=unserialize($wps_Testi);
    }else{
    	$wps_Testi=wps_circolari_CreaTesti();
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

function wps_circolari_getFileUrl($url, $id){
    if (get_post_type($id)=="circolari_scuola") {
		$visibilita=get_post_meta($id, "_visibilita");
		if (count($visibilita)==0)
			$selp="Pb";
		else 
			if ($visibilita[0]=="p")
				$selp="Pb";
			else	
				$seld="Pr";
		if ((!is_user_logged_in() Or !wps_Is_Circolare_per_User($id)) And $seld=="Pr")	
			$url = get_home_url().'?circolarifiletype=attachment&circolarigetfile='.$url;		
		}
        return $url;
    }
//removes quick edit from custom post type list
function wps_circolari_remove_quick_edit( $actions ) {
	global $post;
    if( $post->post_type == 'circolari_scuola' ) {
 		unset($actions['inline hide-if-no-js']);
	}
    return $actions;
}

function wps_circolari_disable_feed() {
	if ( get_post_type()=='circolari_scuola') {
        wp_die( __( 'Non ci sono feed disponibili per le Circolari, per visualizzarle naviga la', 'wpscuola' ).'  <a href="'. get_bloginfo('url') .'">'.__( 'Home', 'wpscuola' ).'</a> '.__( 'del sito', 'wpscuola' ).'!');
    }
}

function wps_circolari_FrontEnd_Enqueue_Scripts() {
//	wp_enqueue_style( 'circolari_styles-fonts', wps_Circolari_URL.'/font/css/font-awesome.min.css',  array(), null, 'all' );
//	wp_enqueue_style( 'circolari_styles', wps_Circolari_URL.'/css/style.css',  array(), null, 'all' );
	}
	
function wps_circolari_Admin_Enqueue_Scripts($hook) {
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'jquery-ui-datepicker', '', array('jquery'),false,true);
		wp_enqueue_script( 'Circolari-admin', wps_Circolari_URL.'/js/Circolari.js', array(), null);
		if($hook == 'edit.php' And (isset($_GET['post_type']) And $_GET['post_type']=="circolari_scuola")) {
//			wp_enqueue_script( 'jquery-ui-tooltip', '', array('jquery'),false,true);
		}
// DataTable JS
		wp_enqueue_script( 'Circolari-DataTable', wps_Circolari_URL.'/js/jquery.dataTables.min.js');
		wp_enqueue_script( 'Circolari-DataTable-Tools_button', wps_Circolari_URL.'/js/dataTables.buttons.min.js');
		wp_enqueue_script( 'Circolari-DataTable-Tools_jszip', wps_Circolari_URL.'/js/jszip.min.js');
		wp_enqueue_script( 'Circolari-DataTable-Tools_button_html5', wps_Circolari_URL.'/js/buttons.html5.min.js');
		wp_enqueue_script( 'Circolari-DataTable-Tools_button_print', wps_Circolari_URL.'/js/buttons.print.min.js');
		wp_enqueue_script( 'Circolari-DataTable-Tools_pdfmake', wps_Circolari_URL.'/js/pdfmake.min.js');
		wp_enqueue_script( 'Circolari-DataTable-Tools_vfs', wps_Circolari_URL.'/js/vfs_fonts.js');
//DataTable CSS		
		wp_enqueue_style( 'Circolari-DataTable-theme', wps_Circolari_URL.'/css/jquery.dataTables.min.css' );
		wp_enqueue_style( 'Circolari-DataTable-theme-buttons', wps_Circolari_URL.'/css/buttons.dataTables.min.css' );
//Stile Circolari
		wp_enqueue_style( 'Circolari-style', wps_Circolari_URL.'/css/style.css' );
}

function wps_circolari_FiltroVisualizzaCircolare( $content ){
	$PostID= get_the_ID();
	/*
	 * Se l'articolo non appartiene al CustomPostType circolari_scuola rimando il contenuto
	 */
		if (get_post_type( $PostID) !="circolari_scuola")
			return $content;

		if(FALSE!==($wps_TestiRisposte=get_option('Circolari_TestiRisposte'))){
			$wps_TestiRisposte= unserialize($wps_TestiRisposte);
		}else{
			return $content;
		}
		if(FALSE!==($wps_Testi=get_option('Circolari_Tipo'))){
			$wps_Testi=unserialize($wps_Testi);
		}else{
			return $content;
		}
	$sign=get_post_meta($PostID, "_sign",TRUE);
	if(!isset($sign) or $sign=="") return $content;
		
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
		if ((!is_user_logged_in() Or !wps_Is_Circolare_per_User($PostID)) And $seld=="Pr")	
			return '
		<div class="alert alert-success" role="alert">
	  		<h4 class="alert-heading">'.__( 'Avviso di sicurezza', 'wpscuola' ).'!</h4>
	  		<p>'.__( 'Circolare riservata a specifici gruppi di utenti registrati', 'wpscuola' ).'.</p>
	  		<hr>
	  		<p class="mb-0">'.__( 'Loggati per accedere alla circolare', 'wpscuola' ).'.</p>
		</div>';
	/*
	 * Se la circolare è per l'utente ed è da firmare visualizzo la gestione della firma 
	 * 
	 * Altrimenti rimando il messaggio di risorsa riservata
	 */
		if (!wps_Is_Circolare_Da_Firmare($PostID) or !wps_Is_Circolare_per_User($PostID))
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
	 background: #0095cd;' onclick='javascript:history.back()'>".__( 'Torna alla Firma', 'wpscuola' )."</button>".$content;
		else{ 
			$Campo_Firma="";
			if (wps_Is_Circolare_per_User($PostID)){	
				if($sign=="NoFirma"){
					return $content;
				}else{
					$BaseUrl=admin_url()."edit.php";
					if (wps_Is_Circolare_Firmata($PostID)){
						return  $content;
					}else{
						if ($sign!="Firma"){
							$Campo_Firma='<form action=""  method="get" style="display:inline;">
								<div>
									<div class="row">
										<input type="hidden" name="op" value="Adesione" />
										<input type="hidden" name="pid" value="'.$PostID.'" />
										<input type="hidden" name="circoFir" value="'.wp_create_nonce('FirmaCircolare').'" />';
						$TipoC=wps_Circolari_find_Tipo($sign);
						$Primo=true;
						foreach($TipoC->get_Risposte() as $Risposta){
							$Campo_Firma.='    <div class="form-check form-check-inline">
	      <input name="scelta" type="radio" id="radio'.$Risposta.'" class="s'.$Risposta.'-'.$PostID.'" value="'.$Risposta.'" '.($Primo?"checked":"").'>
	      <label for="radio'.$Risposta.'">'.wps_Circolari_find_Risposta($Risposta)->get_Risposta().'</label>
	    </div>';
	    				$Primo=false;
						}
						$Campo_Firma.='
										<button class="btn btn-primary btn-icon inviaadesione" type="submit" name="inviaadesione" id="'.$PostID.'" value="Firma" rel="'.get_the_title($PostID).'"><span class="fas fa-signature pr-2"></span> <span>'.__( 'Esprimi scelta', 'wpscuola' ).'</span>
									</div>
								</div>
							</form>';
						}else
							$Campo_Firma='<button class="btn btn-primary btn-icon" onclick="window.location.href=\''.get_permalink($PostID).'?op=Firma&pid='.$PostID.'&circoFir='.wp_create_nonce('FirmaCircolare').'\'"><span class="fas fa-signature pr-2"></span> <span>'.__( 'Firma la Circolare', 'wpscuola' ).'</span>';					
					}
				}
			}
		return $content." <br />".$Campo_Firma;
		}	
}

function wps_circolari_FiltroVisualizzaRiassuntoCircolare( $excerpt ){

	$PostID= get_the_ID();
/*
 * Se l'articolo richiede la password rimando tutto il contenuto con la richiesta della password
 */
	if (post_password_required( $PostID ))
		return __( 'Contenuto protetto da Password', 'wpscuola' );
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
	$seld="Pb";
	if (count($visibilita)==0)
		$selp="Pb";
	else 
		if ($visibilita[0]=="p")
			$selp="Pb";
		else	
			$seld="Pr";
	if ((!is_user_logged_in() Or !wps_Is_Circolare_per_User($PostID)) And $seld=="Pr")	
		return __( 'Contenuto riservato a specifici gruppi di utenti registrati', 'wpscuola' );
	else
		return $excerpt;
//	return $excerpt;
}
function wps_circolari_activate() {
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	require_once(ABSPATH . 'wp-admin/includes/plugin.php'); 
	$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->table_circolari_firme." (
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
		add_option('Circolari_From_NotificaFirma', __( 'Servizio di Notifica firma delle circolari <indirizzo_email@dominio.it>', 'wpscuola' ));
	}
	if(get_option('Circolari_Oggetto_NotificaFirma')== ''||!get_option('Circolari_Oggetto_NotificaFirma')){
		add_option('Circolari_Oggetto_NotificaFirma', __( 'Notifica firma circolare', 'wpscuola' ));
	}
	if(get_option('Circolari_Messaggio_NotificaFirma')== ''||!get_option('Circolari_Messaggio_NotificaFirma')){
		add_option('Circolari_Messaggio_NotificaFirma', sprintf(__( 'Caro  %s

con la presente email ti confermiamo di aver registrato, in data %s,  la tua scelta di %s circolare %s.

Grazie per la collaborazione', 'wpscuola' ),"{Dati_Utente}","{Data}","{Operazione}","{Link_Circolare}").'    ');
	}
	if(get_option('Circolari_TestiRisposte')== ''||!get_option('Circolari_TestiRisposte') ){			
		wps_circolari_CreaTestiRisposta();
	}
	if(get_option('Circolari_Tipo')== ''||!get_option('Circolari_Tipo')){
		wps_circolari_CreaTesti();
	}
}
function wps_circolari_CreaTestiRisposta(){
$wps_TestiRisposte=array(new Circolari_Risposta(0,__( "Non Firmata", 'wpscuola' ),__( "Non Firmare la", 'wpscuola' )),
				 new Circolari_Risposta(1,__( "Si", 'wpscuola' ),__( "Aderire alla", 'wpscuola' )),
                 new Circolari_Risposta(2,__( "No", 'wpscuola' ),__( "Non Aderire alla", 'wpscuola' )),
                 new Circolari_Risposta(3,__( "Presa Visione", 'wpscuola' ),__( "Prendere Visione in merito alla", 'wpscuola' )),
                 new Circolari_Risposta(4,__( "Firma", 'wpscuola' ),__( "Firmare la", 'wpscuola' )));
add_option('Circolari_TestiRisposte', serialize($wps_TestiRisposte));
return $wps_TestiRisposte;
}
function wps_circolari_CreaTesti(){
$wps_Testi=array(new Circolari_Tipo(__( "NoFirma", 'wpscuola' ),__( "Informativa", 'wpscuola' ),"",__( "La circolare non richiede conferma", 'wpscuola' ),__( "Firma non prevista", 'wpscuola' ),"",array()),
		new Circolari_Tipo(__( "Sciopero", 'wpscuola' ),__( "Adesioni allo sciopero", 'wpscuola' ),__( "Sciopero", 'wpscuola' ),__( "Adesione", 'wpscuola' ),__( "La circolare si riferisce ad uno sciopero.<br />Bisogna indicare Si/No/Presa Visione", 'wpscuola' ),__( "Adesione allo sciopero", 'wpscuola' ),array(1,2,3)),
        new Circolari_Tipo(__( "Firma", 'wpscuola' ),__( "Firme", 'wpscuola' ),__( "Circolare ordinaria", 'wpscuola' ),__( "Da Firmare", 'wpscuola' ),__( "&Egrave; richiesta la firma alla circolare ordinaria", 'wpscuola' ),__( "Firma la circolare ordinaria", 'wpscuola' ),array(4)),
        new Circolari_Tipo(__( "Assemblea", 'wpscuola' ),__( "Partecipazioni all'assemblea", 'wpscuola' ),__( "Assembea Sindacale", 'wpscuola' ),__( "Partecipazione", 'wpscuola' ),__( "La circolare si riferisce ad una assemblea sindacale.<br />Bisogna indicare Si/No", 'wpscuola' ),__( "Partecipazione all'assemblea", 'wpscuola' ),array(1,2)));
add_option('Circolari_Tipo', serialize($wps_Testi));
return $wps_Testi;
}
function wps_circolari_create_Testi_Risposte(){
	global $wps_TestiRisposte,$wps_Testi;
	if(isset($_REQUEST['MemoRisposta']) And $_REQUEST['MemoRisposta'] == 'Crea Nuova Risposta'){
		$nuova=new Circolari_Risposta(wps_Circolari_Get_New_Numero_Risposta(),$_REQUEST['risposta'],$_REQUEST['testomail']);
		$wps_TestiRisposte[]=$nuova;
		update_option('Circolari_TestiRisposte', serialize($wps_TestiRisposte));			
	}
	if(isset($_REQUEST['MemoTesto']) And $_REQUEST['MemoTesto'] == 'Creazione Tipo Circolare'){
		$num=0;
		$suff="";
		while (wps_Circolari_is_set_Tipo($_REQUEST['tipo'].$suff)){
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
		$wps_Testi[]=$nuova;
		update_option('Circolari_Tipo', serialize($wps_Testi));			
	}
}
function wps_circolari_update_Testi_Risposte(){
	global $wps_TestiRisposte,$wps_Testi;
	if(filter_input(INPUT_GET,'MemoTesto') == 'Memorizza Modifiche'){		
			foreach($wps_Testi as $Testo)
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
		update_option('Circolari_Tipo', serialize($wps_Testi));			
	}
	if(filter_input(INPUT_GET,'MemoRisposta') == 'Memorizza Modifiche'){
			foreach($wps_TestiRisposte as $Risposta)
				if ($Risposta->get_IDRisposta()==(int)$_REQUEST['id']){
					$Risposta->set_Risposta($_REQUEST['risposta']);
					$Risposta->set_RispostaMail($_REQUEST['rispostamail']);
				}		
		update_option('Circolari_TestiRisposte', serialize($wps_TestiRisposte));			
	}
}
function wps_circolari_add_menu_bubble() {
  	global $menu,$DaFirmare;
	$DaFirmare=wps_GetCircolariDaFirmare("N");
 	if ($DaFirmare==0) return;
	foreach($menu as $i=>$m){
	  	if ($m[0]=="Circolari"){
			$menu[$i][0] .= "<span class='update-plugins count-1'><span class='update-count'>$DaFirmare</span></span>";
			return;
		}
 	}
}

function wps_circolari_add_menu(){
   add_submenu_page( 'edit.php?post_type=circolari_scuola', 'Parametri',  __( 'Parametri', 'wpscuola' ), 'edit_others_posts', 'circolari', 'wps_circolari_MenuPagine');
   $pageFirma=add_submenu_page( 'edit.php?post_type=circolari_scuola', 'Firma',  __( 'Firma', 'wpscuola' ), 'read', 'Firma', 'wps_circolari_GestioneFirme');
   add_action( 'admin_head-'. $pageFirma, 'wps_circolari_Testata' );
   $pageArchivio=add_submenu_page( 'edit.php?post_type=circolari_scuola', 'Archivio Circolari', __( 'Archivio Circolari', 'wpscuola' ), 'read', 'Archivio', 'wps_circolari_VisualizzaArchivio');
   add_action( 'admin_head-'. $pageArchivio, 'wps_circolari_Testata' );
   $gestionetesti=add_submenu_page( 'edit.php?post_type=circolari_scuola', 'Testi',  __( 'Tipi di circolari', 'wpscuola' ), 'edit_published_posts', 'Testi', 'wps_circolari_MenuTesti');
   add_action( 'admin_head-'. $gestionetesti, 'wps_circolari_Testata' );  
   $utility=add_submenu_page( 'edit.php?post_type=circolari_scuola', 'Utility',  __( 'Utility', 'wpscuola' ), 'edit_others_posts', 'Utility', 'wps_circolari_Utility');
   add_action( 'admin_head-'. $utility, 'wps_circolari_Testata' );
}
function wps_circolari_MenuTesti(){
	global $wps_TestiRisposte,$wps_Testi;	
	if (isset($_REQUEST['opT'])){
		switch ($_REQUEST['opT']){
			case "Edit":
				wps_circolari_GestioneTesti();
				break;
			case "Risp":
				wps_circolari_GestioneRisposteTesti();
				break;
			case "NewTipo":
				wps_circolari_NewTesto();
				break;
			case "Canc":
				if (($Num=wps_Circolari_IsUsed_TipoCircolare($_REQUEST['id']))==0 And !in_array($_REQUEST['id'],array("NoFirma","Sciopero","Firma","Assemblea"))){
					unset($wps_Testi[wps_($_REQUEST['id'])]);
					update_option('Circolari_Tipo', serialize($wps_Testi));
				echo'<div class="updated">
	<p></p><em><strong>'.__( 'Cancellazione avvenuta con successo!', 'wpscuola' ).'</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="5;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
				}else{
				echo'<div class="updated">
	<p></p><em><strong>'.sprintf(__( 'Cancellazione non effettuata:<br />Ci sono %s Circolari che utilizzano questo Tipo<br />Bisogna prima disassociare le circolari e poi si può cancellare il Tipo', 'wpscuola' ),$Num).'</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';				
				}
				break;
			case "MemorizzaNew":
				if (isset($_REQUEST["circoTesti"])) {
					if (wp_verify_nonce($_REQUEST["circoTesti"],'GestioneTestiCircolari')){
						wps_circolari_create_Testi_Risposte();
						echo'<div class="updated">
	<p></p><em><strong>'.__( 'Creazione avvenuta con successo!', 'wpscuola' ).'</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
					}
				}
				break;			
			case "Memorizza":
				if (isset($_REQUEST["circoTesti"])) {
					if (wp_verify_nonce($_REQUEST["circoTesti"],'GestioneTestiCircolari')){
						wps_circolari_update_Testi_Risposte();
						echo'<div class="updated">
	<p></p><em><strong>'.__( 'Memorizzazione avvenuta con successo!', 'wpscuola' ).'</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
					}
				}
				break;
			default:
				echo'<div class="updated">
	<p></p><em><strong>'.__( 'Operazione non eseguita!', 'wpscuola' ).'</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
				break;
		}
		return;
	}
	if (isset($_REQUEST['opR'])){
		switch ($_REQUEST['opR']){
			case "Edit":
				wps_circolari_GestioneRisposte();
				break;
			case "Canc":
				if (($Num=wps_Circolari_IsUsed_Risposta($_REQUEST['id']))==0){
					unset($wps_TestiRisposte[wps_Circolari_find_Index_Risposta($_REQUEST['id'])]);
					update_option('Circolari_TestiRisposte', serialize($wps_TestiRisposte));
				echo'<div class="updated">
	<p></p><em><strong>'.__( 'Cancellazione avvenuta con successo!', 'wpscuola' ).'</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="5;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
				}else{
				echo'<div class="updated">
	<p></p><em><strong>'.sprintf(__( 'Cancellazione non effettuata:<br />Ci sono %s di Tipi di Circolare che utilizzano questa Risposta<br />Bisogna prima disassociare la risposta dalle circolari e poi si può cancellare la risposta', 'wpscuola' ),$Num).'</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';				
				}		
				break;
			case "NewRisp":
				wps_circolari_NewRisposta();
				break;
			case "MemorizzaNew":
				if (isset($_REQUEST["circoTesti"])) {
					if (wp_verify_nonce($_REQUEST["circoTesti"],'GestioneTestiCircolari')){
						wps_circolari_create_Testi_Risposte();
						echo'<div class="updated">
	<p></p><em><strong>'.__( 'Creazione avvenuta con successo!', 'wpscuola' ).'</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
					}
				}
				break;				
			case "Memorizza":
				if (isset($_REQUEST["circoTesti"])) {
					if (wp_verify_nonce($_REQUEST["circoTesti"],'GestioneTestiCircolari')){
						wps_circolari_update_Testi_Risposte();
						echo'<div class="updated">
	<p></p><em><strong>'.__( 'Memorizzazione avvenuta con successo!', 'wpscuola' ).'</em></strong><p> </p>
</div>
	<meta http-equiv="refresh" content="3;url=edit.php?post_type=circolari_scuola&page=Testi"/>';
					}
				}
				break;
		}
		return;
	}
	wps_circolari_Testi();
}
function wps_circolari_GestioneRisposteTesti(){
	global $wps_TestiRisposte,$wps_Testi;
	foreach($wps_Testi as $Testo){
		if($Testo->get_Tipo()==$_GET['id'])
			break;
	}	
	$UrlB=get_site_url()."/wp-admin/edit.php";
echo '<div class="wrap">
	<span class="fas fa-pencil-alt fa-3x" aria-hidden="true"></span>
		<h2 style="display:inline;margin-left:10px;vertical-align:super;">'.__( 'Gestione Risposte', 'wpscuola' ).'</h2>
	</div> 
		<br />
		<table class="widefat">
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Tipo', 'wpscuola' ).'</th>
				<td>'.$Testo->get_Tipo().'</td>
			</tr>
			<tr class="intestariga">
				<th class="intestariga">'.__( 'Descrizione Tipo', 'wpscuola' ).'</th>
				<td>'.$Testo->get_DescrizioneTipo().'</td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Prefisso risposta', 'wpscuola' ).'</th>
				<td>'.$Testo->get_Prefisso().'</td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Descrizione', 'wpscuola' ).'</th>
				<td>'.$Testo->get_Descrizione().'</td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Testo Elenco', 'wpscuola' ).'</th>
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
		<p style="font-weight: bold;font-size: 1.3em;text-align: center;margin-bottom: 15px;">'.__( 'Risposte assegnate a questo tipo di corcolare', 'wpscuola' ).'</p>';	
		foreach($wps_TestiRisposte as $TRisposte){
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
<a href=\''.$UrlB.'?post_type=circolari_scuola&page=Testi'.'\'>'.__( 'Annulla Modifiche', 'wpscuola' ).'</a>
		</p>
		</form>';
							
}
function wps_circolari_GestioneTesti(){
	global $wps_TestiRisposte,$wps_Testi;
	foreach($wps_Testi as $Testo){
		if($Testo->get_Tipo()==$_GET['id'])
			break;
	}	
	if($_GET['opT']=="Edit")
		$Operazione=__( 'Modifica', 'wpscuola' );
	else 
		$Operazione=__( 'Nuovo', 'wpscuola' );
	$UrlB=get_site_url()."/wp-admin/edit.php";
echo '<div class="wrap">
	<span class="fas fa-pencil-alt fa-3x" aria-hidden="true"></span><h2 style="display:inline;margin-left:10px;vertical-align:super;">'.$Operazione.' '.__( 'Testo Circolare', 'wpscuola' ).'</h2>
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
				<th width="20%" class="intestariga">'.__( 'Tipo', 'wpscuola' ).'</th>
				<td>'.$Testo->get_Tipo().'</td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Popup', 'wpscuola' ).'</th>
				<td><input type="text" name="popup" value="'.$Testo->get_Popup().'"" size="100" id="popup"/><br />
				<span style="font-style: italic;font-weight: bold;">'.__( "Testo che viene riportato nel Popup dell'elenco delle circolari", 'wpscuola' ).'</span></td>
			</tr>
			<tr class="intestariga">
				<th class="intestariga">'.__( 'Descrizione Tipo', 'wpscuola' ).'</th>
				<td><input type="text" name="des_tipo" value="'.$Testo->get_DescrizioneTipo().'" size="100" id="descrizioneTipo"/><br />
				<span style="font-style: italic;font-weight: bold;">'.__( "Testo che viene riportato nella seconda parte del messaggio di richiesta di adesione da parte dell'utente", 'wpscuola' ).'</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Prefisso risposta', 'wpscuola' ).'</th>
				<td><input type="text" name="pref_risposta" value="'.$Testo->get_Prefisso().'" size="100" id="descrizioneTipo"/><br />
				<span style="font-style: italic;font-weight: bold;">'.__( "Testo che viene riportato nella prima parte del messaggio di richiesta di adesione da parte dell'utente", 'wpscuola' ).'</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Descrizione', 'wpscuola' ).'</th>
				<td><input type="text" name="descrizione" value="'.$Testo->get_Descrizione().'" size="100" id="descrizione"/><br /><span style="font-style: italic;font-weight: bold;">'.__( "Testo che viene riportato nel Box di Creazione/Modifica delle circolari in cui si selezione il tipo di circolare", 'wpscuola' ).'</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Testo Elenco', 'wpscuola' ).'</th>
				<td><input type="text" name="testo_elenco" value="'.$Testo->get_TestoElenco().'" size="100" id="testoelenco"/><br /><span style="font-style: italic;font-weight: bold;">'.__( "Testo che viene riportato nell'intestazione della colonna che riporta la scelta dell'utente nel report delle Firme/Adesioni", 'wpscuola' ).'</span></td>
			</tr>
		</table>
		<p style="text-align:center;">
<input type="submit" value="Memorizza Modifiche" name="MemoTesto"/>
<a href=\''.$UrlB.'?post_type=circolari_scuola&page=Testi'.'\'>'.__( 'Annulla Modifiche', 'wpscuola' ).'</a>
		</p>
		</form>';
							
}
function wps_circolari_GestioneRisposte(){
	global $wps_TestiRisposte;
	foreach($wps_TestiRisposte as $Risposta){
		if($Risposta->get_IDRisposta()==(int)$_GET['id'])
			break;
	}	
	if($_GET['opR']=="Edit")
		$Operazione="Modifica";
	else 
		$Operazione="Nuovo";
	$UrlB=get_site_url()."/wp-admin/edit.php";
echo '<div class="wrap">
	<span class="fas fa-pencil-alt fa-3x" aria-hidden="true"></span><h2 style="display:inline;margin-left:10px;vertical-align:super;">'.$Operazione.' '.__( 'Risposta Circolare', 'wpscuola' ).'</h2>
	</div> 
		<form action="'.$UrlB.'" name="MRisposte" metod="post">
		<input type="hidden" name="opR" value="Memorizza"/>
		<input type="hidden" name="post_type" value="circolari_scuola"/>
		<input type="hidden" name="page" value="Testi"/>
		<input type="hidden" name="id" value="'.$Risposta->get_IDRisposta().'"/>
		<input type="hidden" name="circoTesti" value="'.wp_create_nonce('GestioneTestiCircolari').'"/>
		<p style="text-align:left;font-size:1em;font-style: italic;margin-top:30px;">
		<table class="widefat">
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Risposta', 'wpscuola' ).'</th>
				<td><input type="text" name="risposta" value="'.$Risposta->get_Risposta().'" size="100" id="risposta"/></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Testo email', 'wpscuola' ).'</th>
				<td><input type="text" name="rispostamail" value="'.$Risposta->get_RispostaMail().'" size="100" id="rispostamail"/></td>
			</tr>
		</table>
		<p style="text-align:center;">
<input type="submit" value="Memorizza Modifiche" name="MemoRisposta"/>&nbsp;
<a href=\''.$UrlB.'?post_type=circolari_scuola&page=Testi'.'\'>'.__( 'Annulla Modifiche', 'wpscuola' ).'</a>
		</p>
		</form>';
							
}
function wps_circolari_NewTesto(){
	$UrlB=get_site_url()."/wp-admin/edit.php";
echo '<div class="wrap">
	<span class="fas fa-pencil-alt fa-3x" aria-hidden="true"></span><h2 style="display:inline;margin-left:10px;vertical-align:super;">'.__( 'Nuovo Tipo Circolare', 'wpscuola' ).'</h2>
	</div> 
		<form action="'.$UrlB.'" name="MTesti" metod="post">
		<input type="hidden" name="opT" value="MemorizzaNew"/>
		<input type="hidden" name="post_type" value="circolari_scuola"/>
		<input type="hidden" name="page" value="Testi"/>
		<input type="hidden" name="circoTesti" value="'.wp_create_nonce('GestioneTestiCircolari').'"/>
		<p style="text-align:left;font-size:1em;font-style: italic;margin-top:30px;">
		<table class="widefat">
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Tipo', 'wpscuola' ).'</th>
				<td><input type="text" name="tipo" size="10" id="Tipo"/><br />
				<span style="font-style: italic;font-weight: bold;">'.__( 'Codice Univoco che identificherà; il tipo di circolare', 'wpscuola' ).'</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Popup', 'wpscuola' ).'</th>
				<td><input type="text" name="popup" size="10" id="Popup"/><br />
				<span style="font-style: italic;font-weight: bold;">'.__( "Testo del Popup dell'elenco delle circolari", 'wpscuola' ).'</span></td>
			</tr>
			<tr class="intestariga">
				<th class="intestariga">'.__( 'Descrizione Tipo', 'wpscuola' ).'</th>
				<td><input type="text" name="des_tipo" size="100" id="descrizioneTipo"/><br />
				<span style="font-style: italic;font-weight: bold;">'.__( "Testo che viene riportato nella seconda parte del messaggio di richiesta di adesione da parte dell'utente", 'wpscuola' ).'</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Prefisso risposta', 'wpscuola' ).'</th>
				<td><input type="text" name="pref_risposta" size="100" id="descrizioneTipo"/><br />
				<span style="font-style: italic;font-weight: bold;">'.__( "Testo che viene riportato nella prima parte del messaggio di richiesta di adesione da parte dell'utente", 'wpscuola' ).'</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Descrizione', 'wpscuola' ).'</th>
				<td><input type="text" name="descrizione" size="100" id="descrizione"/><br /><span style="font-style: italic;font-weight: bold;">'.__( 'Testo che viene riportato nel Box di Creazione/Modifica delle circolari in cui si selezione il tipo di circolare', 'wpscuola' ).'</span></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Testo Elenco', 'wpscuola' ).'</th>
				<td><input type="text" name="testo_elenco" size="100" id="testoelenco"/><br /><span style="font-style: italic;font-weight: bold;">'.__( "Testo che viene riportato nell'intestazione della colonna che riporta la scelta dell'utente nel report delle Firme/Adesioni", 'wpscuola' ).'</span></td>
			</tr>
		</table>
		<p style="text-align:center;">
<input type="submit" value="Creazione Tipo Circolare" name="MemoTesto"/>
<a href=\''.$UrlB.'?post_type=circolari_scuola&page=Testi'.'\'>'.__( 'Annulla Creazione', 'wpscuola' ).'</a>
		</p>
		</form>';							
}
function wps_circolari_NewRisposta(){
	global $wps_TestiRisposte;
	$UrlB=get_site_url()."/wp-admin/edit.php";
echo '<div class="wrap">
	<span class="fas fa-pencil-alt fa-3x" aria-hidden="true"></span><h2 style="display:inline;margin-left:10px;vertical-align:super;">'.__( 'Nuova Risposta Circolare', 'wpscuola' ).'</h2>
	</div> 
		<form action="'.$UrlB.'" name="MRisposte" metod="post">
		<input type="hidden" name="opR" value="MemorizzaNew"/>
		<input type="hidden" name="post_type" value="circolari_scuola"/>
		<input type="hidden" name="page" value="Testi"/>
		<input type="hidden" name="id" value="'.wps_Circolari_Get_New_Numero_Risposta().'"/>
		<input type="hidden" name="circoTesti" value="'.wp_create_nonce('GestioneTestiCircolari').'"/>
		<p style="text-align:left;font-size:1em;font-style: italic;margin-top:30px;">
		<table class="widefat">
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Risposta', 'wpscuola' ).'</th>
				<td><input type="text" name="risposta" size="100" id="risposta"/></td>
			</tr>
			<tr class="intestariga">
				<th width="20%" class="intestariga">'.__( 'Testo email', 'wpscuola' ).'</th>
				<td><input type="text" name="testomail" size="100" id="testomail"/></td>
			</tr>
		</table>
		<p style="text-align:center;">
<input type="submit" value="Crea Nuova Risposta" name="MemoRisposta"/>&nbsp;
<a href=\''.$UrlB.'?post_type=circolari_scuola&page=Testi'.'\'>'.__( 'Annulla Creazione', 'wpscuola' ).'</a>
		</p>
		</form>';
							
}

function wps_circolari_Testi(){
	global $wps_TestiRisposte,$wps_Testi;
	$UrlB=get_site_url()."/wp-admin/edit.php?post_type=circolari_scuola&page=Testi";
echo '<div class="wrap">
	<span class="fas fa-pencil-alt fa-3x" aria-hidden="true"></span><h2 style="display:inline;margin-left:10px;vertical-align:super;">'.__( 'Tipi di Circolari', 'wpscuola' ).'</h2>
	</div> 
		<p></p>
		<div class="widefat">
<div style="width:100px;margin:0 auto;font-size:1.5em;font-weight: bold;">
			'.__( 'Tipi', 'wpscuola' ).' <a href="'.$UrlB.'&opT=NewTipo"><span class="fa fa-plus-square" aria-hidden="true"></span></a>
			</div>
			<p style="text-align:left;font-size:1em;font-style: italic;">
			<table class="widefat">
				<tr class="border_bottom">
					<th>'.__( 'Tipo', 'wpscuola' ).'</th>
					<th>'.__( 'Popup', 'wpscuola' ).'</th>
					<th>'.__( 'Descrizione Tipo', 'wpscuola' ).'</th>
					<th>'.__( 'Prefisso risposta', 'wpscuola' ).'</th>
					<th>'.__( 'Descrizione', 'wpscuola' ).'</th>
					<th>'.__( 'Testo Elenco', 'wpscuola' ).'</th>
					<th>'.__( 'Operazioni', 'wpscuola' ).'</th>
				</tr>';
	foreach($wps_Testi as $Testo){
		$RisposteModifica="<a href='".$UrlB."&opT=Edit&id=".$Testo->get_Tipo()."' title='".__( 'Modifica Testi Tipo Circolare', 'wpscuola' )."'><span class=\"fas fa-pencil-alt\" aria-hidden=\"true\"></span></a> ";
		$RisposteCancella="<a href='".$UrlB."&opT=Canc&id=".$Testo->get_Tipo()."' title='".__( 'Cancella Tipo Circolare', 'wpscuola' )."'><span class=\"fas fa-trash-alt\" aria-eraser=\"true\" style=\"color:red;\"></span></a> ";
		$Risposte="<a href='".$UrlB."&opT=Risp&id=".$Testo->get_Tipo()."' title='".__( 'Modifica le risposte', 'wpscuola' )."'><span class=\"fas fa-edit\" aria-eraser=\"true\" style=\"color:green;\"></span></a> ";
		if($Testo->get_Tipo()=="NoFirma"){
			$RisposteCancella=$Risposte="";
		}
		if(in_array($Testo->get_Tipo(),array('NoFirma','Sciopero', 'Firma', 'Assemblea'))){
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
			Risposte <a href="'.$UrlB.'&amp;opR=NewRisp"><span class="fa fa-plus-square" aria-hidden="true"></span></a>
			</div>
			<p style="text-align:left;font-size:1em;font-style: italic;">
			<table class="widefat">
				<tr class="border_bottom">
					<th>ID</th>
					<th>'.__( 'Testo', 'wpscuola' ).'</th>
					<th>'.__( 'Testo Mail', 'wpscuola' ).'</th>
					<th>'.__( 'Operazioni', 'wpscuola' ).'</th>
				</tr>';
	foreach($wps_TestiRisposte as $TestoRisposta){
		$RisposteModifica="
		<a href='".$UrlB."&opR=Edit&id=".$TestoRisposta->get_IDRisposta()."'><span class=\"fas fa-pencil-alt\" aria-hidden=\"true\"></span></a>
		";
		if ($TestoRisposta->get_IDRisposta()>4)
			$RisposteCancella="
		<a href='".$UrlB."&opR=Canc&id=".$TestoRisposta->get_IDRisposta()."'><span class=\"fas fa-trash-alt\" aria-eraser=\"true\" style=\"color:red;\"></span></a>
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

function get_IDGruppoParent($Gruppi,$ParentID){
	foreach($Gruppi as $Gruppo){
		if($Gruppo["group_id"]==$ParentID) return $Gruppo["IDGruppo"];
	}
	return 0;
}

function wps_split_GruppiUtente($Gruppi){
	$Utenti=array();
	foreach($Gruppi as $Gruppo){
		foreach($Gruppo["utenti"] as $Utente)
			$Utenti[$Utente][]=$Gruppo["IDGruppo"];
	}
	return $Utenti;
}
function wps_split_GroupsGruppo($Gruppi){
	$TabGruppi=array();
	foreach($Gruppi as $Gruppo){
		$TabGruppi[$Gruppo["group_id"]]=$Gruppo["IDGruppo"];
	}
	return $TabGruppi;
	
}

function wps_circolari_Utility($Stato=""){
echo '<div class="wrap">
		<span class="fa fa-cogs fa-3x" aria-hidden="true"></span> <h2 style="display:inline;margin-left:10px;vertical-align:super;">'.__( 'Utility Circolari', 'wpscuola' ).'</h2>';
$lista="";
$azione= filter_input(INPUT_GET, "action");
if($azione){
	switch ($azione){
		
		
		case "vertipo":
			$MeLink=$_SERVER['REQUEST_URI'];
			echo "<p style='text-align:center;font-size:1.5em;font-weight: bold;'>".__( 'Stato Operazioni', 'wpscuola' ).":</p>";
				$Posts = get_posts('post_type=circolari_scuola&numberposts=-1');
				foreach($Posts as $post){
					$sign=get_post_meta($post->ID, "_sign",TRUE);
					if(is_null(Circolari_Tipo::get_TipoCircolare($sign))){
						$permalink="/wp-admin/post.php?post=".$post->ID."&action=edit";
						$lista.="			<li><a href='$permalink'  target='_blank'>$post->ID $post->post_title</a>";
						$lista.=' &nbsp;<span style="color:red;font-weight: bold;">ERRORE: tipo circolare errato</span>';
						$lista.= "</li>";					
					}
				}
				if (!empty($lista)){
					echo "<a href='$MeLink'>".__( 'Aggiorna Lista', 'wpscuola' )."</a> <h4>".__( 'Circolari con Tipo Circolare ERRATO', 'wpscuola' )."</h4>
			<ul>
					$lista
			</ul>";
			}else{
				echo "<h4 style='color:green;font-weight: bold;'>".__( 'Nessun errore sul Tipo di Circolare riscontrato', 'wpscuola' )."</h4>";
			}
				
			break;		
		
		
		
		
		
		
		
		
		
		case "versca":			
			echo "<p style='text-align:center;font-size:1.5em;font-weight: bold;'>'.__( 'Stato Operazioni', 'wpscuola' ).':</p>";
				$GGscadenza=get_option("Circolari_GGScadenza");
				$Posts = get_posts('post_type=circolari_scuola&numberposts=-1');
				foreach($Posts as $post){
					$Adesione=get_post_meta($post->ID, "_sciopero",TRUE);
					$firma=get_post_meta($post->ID, "_sign",TRUE);
					if (($firma!="NoFirma") and empty($scadenza)){
						$lista.="			<li>$post->ID $post->post_title $post->post_date_gmt";
						if (isset($_GET['opt']) && $_GET['opt']=="aggsca"){
							$Scadenza=date('Y-m-d', strtotime(substr($post->post_date_gmt,0,10). " + $GGscadenza days"));
							if (update_post_meta($post->ID,"_scadenza",$Scadenza ))
								$lista.= ' <img src="'.wps_Circolari_URL.'/img/verificato.png" alt="Icona Permessi" style="display:inline;margin-left:5px;"/>';
						}else
						$lista.=" ".__( 'la data verrà aggiornata a', 'wpscuola' ).":".date('Y-m-d', strtotime(substr($post->post_date_gmt,0,10). " + $GGscadenza days"));
					$lista.= "</li>";					
					}
				}
				if (!empty($lista)){
					echo "<h4>".__( 'Circolari da firmare senza data di scadenza', 'wpscuola' )."</h4>
			<ul>
					$lista
			</ul>";
			if (isset($_GET['opt']) && $_GET['opt']=="aggsca")
				_e( 'Aggiornamento effettuato', 'wpscuola' );
			else
				echo '<p style="text-align:left;font-size:1em;font-style: italic;">'.sprintf(__( 'Aggiorna la Data entro cui firmare = aggiunngendo %s giorni alla data di pubblicazione', 'wpscuola' ),$GGscadenza).' <spam style="text-align:center;font-size:1.5em;font-weight: bold;"> <a href="edit.php?post_type=circolari_scuola&page=Utility&action=versca&opt=aggsca">'.__( 'Aggiorna', 'wpscuola' ).'</a></spam>
	</p>
	</div>';			
			}else
				echo "<h4>".__( 'Tutte le circolari da firmare hanno la data di scadenza', 'wpscuola' )."</h4>";
			break;
		case "verforsca":
				echo "<p style='text-align:center;font-size:1.5em;font-weight: bold;'>".__( 'Stato Operazioni', 'wpscuola' ).":</p>";
				$Posts = get_posts('post_type=circolari_scuola&numberposts=-1');
				foreach($Posts as $post){
					$scadenza=get_post_meta($post->ID, "_scadenza",TRUE);
					$Data=explode("-",$scadenza);
					if (!empty($scadenza) And (strlen($Data[0])<4 or strlen($Data[1])<2 or strlen($Data[2])<2)){
						$lista.="			<li>$post->ID $post->post_title ";
						if (isset($_GET['opt']) && $_GET['opt']=="aggsca"){
							if (update_post_meta($post->ID,"_scadenza",wps_NormalData($scadenza)))
								$lista.= ' <img src="'.wps_Circolari_URL.'/img/verificato.png" alt="Icona Permessi" style="display:inline;margin-left:5px;"/>';
						}else
						$lista.="<spam style='font-weight: bold;'>".$scadenza ."</spam> ".__( 'la data verrà aggiornata a', 'wpscuola' ).":<spam style='font-weight: bold;'>".wps_NormalData($scadenza)."</spam>";
					$lista.= "</li>";					
					}
				}
				if (!empty($lista)){
					echo "<h4>".__( 'Circolari da firmare con formato data di scadenza errata', 'wpscuola' )."</h4>
			<ul>
					$lista
			</ul>";
			if (isset($_GET['opt']) && $_GET['opt']=="aggsca")
				_e("Aggiornamento effettuato", 'wpscuola' );
			else
				echo '<p style="text-align:left;font-size:1em;font-style: italic;">'.__( 'Aggiorna il formato della Data entro cui firmare', 'wpscuola' ).' <spam style="text-align:center;font-size:1.5em;font-weight: bold;"> <a href="edit.php?post_type=circolari_scuola&page=Utility&action=verforsca&opt=aggsca">'.__( 'Aggiorna', 'wpscuola' ).'</a></spam>
	</p>
	</div>';			
			}else
				echo "<h4>".__( 'Tutte le circolari da firmare hanno la data di scadenza in formato corretto', 'wpscuola' )."</h4>";
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
			$Circolari = get_posts("post_type=circolari_scuola&orderby=date&numberposts=-1&posts_per_page=-1&post_status=any");
			foreach($Circolari as $post) {
				$firma=get_post_meta($post->ID,"_firma");
				$sciopero=get_post_meta($post->ID,"_sciopero");
				$sign=get_post_meta($post->ID,"_sign");
				$NC1=get_post_meta($post->ID,"_numero");
				$NC2=get_post_meta($post->ID,"_anno");
				if (isset($sign[0]))
					echo "<strong><em>".sprintf(__( 'Circolare %s N. %s_$s già Aggiornato', 'wpscuola' ),$post->ID,$NC1[0],$NC2[0])."</strong></em> ";
				else{
					echo sprintf(__( 'Circolare %s N. %s_$s Aggiornata a:', 'wpscuola' ),$post->ID,$NC1[0],$NC2[0]);
					if ($sciopero[0]=="Si"){
						update_post_meta($post->ID,"_sign","Sciopero" );
						_e("Sciopero", 'wpscuola' );
					}else
						if($firma[0]=="Si"){
							update_post_meta($post->ID,"_sign","Firma" );
							_e("Firma", 'wpscuola' );				
						}
						else{
							update_post_meta($post->ID,"_sign","NoFirma" );
							_e("NoFirma", 'wpscuola' );
						}
					$Calcellata="";
					if(delete_post_meta($post->ID, "_firma")==TRUE)
						$Calcellata= " Firma ";
					if(delete_post_meta($post->ID, "_sciopero")==TRUE)
						$Calcellata= " ".__("Sciopero", 'wpscuola' );
					if($Calcellata!="")
						echo "<strong> ".__("Cancellata per questa circolare l'impostazione", 'wpscuola' )." ".$Calcellata."</strong>"; 
				}
				echo "<br />";
			}
			echo "<div class='update-nag'><em><strong>".__("Operazione conclusa con successo!", 'wpscuola' )."</em></strong></div>";
			echo '<meta http-equiv="refresh" content="5;url=edit.php?post_type=circolari_scuola&page=Utility"/>';
			break;
		case "importa":
			if(class_exists( "Groups_Group" ) And count(get_terms('gruppiutenti', array('hide_empty' => false)))==0){
				$Obj_Gruppi=new Groups_Group(array(1));
				$Gruppi=array();
				$Groups=$Obj_Gruppi->get_groups();
				echo "<h3>Gruppi</h3>";
				foreach($Groups as $Group){
					if ($Group->group_id>1){
						$group = new Groups_Group( $Group->group_id );
						$users = $group->users;
						$UtentiGruppo=array();
						if(!is_null($users) And count($users)>0){
							foreach($users as $user){
								$UtentiGruppo[]=$user->ID;
							}
						}
						$NewGruppo=wp_insert_term($Group->name,"gruppiutenti",array("description"=>$Group->description));
						if ( ! is_wp_error( $NewGruppo ) ){
							$Gruppo=array( "group_id"    => $Group->group_id,
										   "parent_id"   => $Group->parent_id,
										   "name"		 => $Group->name,
										   "description" => $Group->description,
										   "utenti"		 => $UtentiGruppo,
										   "IDGruppo"	 => isset( $NewGruppo['term_id'] ) ? $NewGruppo['term_id'] :0,
										   "IDParent"	 => 0);
						}else{
							$Gruppo=array( "group_id"    => $Group->group_id,
										   "parent_id"   => $Group->parent_id,
										   "name"		 => $Group->name,
										   "description" => $Group->description,
										   "utenti"		 => $UtentiGruppo,
										   "IDGruppo"	 => -1,
										   "IDParent"	 => 0);							
						}
						$Gruppi[]=$Gruppo;
					}
				}
				
				foreach($Gruppi as $Key=>$Gruppo){
					if(!is_null($Gruppo["parent_id"]) And $Gruppo["parent_id"]>0){
						$IDGruppoParent=get_IDGruppoParent($Gruppi,$Gruppo["parent_id"]);
						$AggGruppo=wp_update_term($Gruppo["IDGruppo"],"gruppiutenti",array("parent"=>$IDGruppoParent));
						if ( is_wp_error( $AggGruppo ) ){
						    echo $AggGruppo->get_error_message();
						}else{
							$Gruppi[$Key]["IDParent"]=$IDGruppoParent;
						}
					}
				}
				update_option("ImportazioneGroups",$Gruppi);
				$UtentiGruppi=wps_split_GruppiUtente($Gruppi);
				update_option("ImportazioneGroupsUtentiGruppi",$UtentiGruppi);
				$GroupsGruppi=wps_split_GroupsGruppo($Gruppi);
				update_option("ImportazioneTabellaGroupsGruppi",$GroupsGruppi);
//				echo "<pre>";var_dump($Gruppi);echo "</pre>";
				foreach($UtentiGruppi as $Key=>$UtenteGruppo){
//					echo "Utente ".$Key." - gruppo ";
//					echo "<pre>";var_dump($UtenteGruppo);echo "</pre> <br />";
					update_user_meta( $Key, 'gruppo', $UtenteGruppo);
				}
//				echo get_option('Circolari_Visibilita_Pubblica')."<pre>";var_dump($GroupsGruppi);echo "</pre>".$GroupsGruppi[get_option('Circolari_Visibilita_Pubblica')] ;
				update_option('Circolari_Visibilita_Pubblica',$GroupsGruppi[get_option('Circolari_Visibilita_Pubblica')] );
//				var_dump(get_option('Circolari_Visibilita_Pubblica'));
				$GP = get_term( get_option('Circolari_Visibilita_Pubblica'), "gruppiutenti" );
				//echo "<pre>";var_dump($GP);echo "</pre>";
				echo "<p style=\"color:green;font-size:1.5em;font-weight: bold;\">".sprintf(__("Importazione Gruppi ed assegnazione degli utenti eseguita con successo. Impostato il gruppo Pubblico a:%s", 'wpscuola' ),$GP->name)."</p>";
			}else{
				echo "<p style=\"color:red;font-size:1.5em;font-weight: bold;\">".__("Impossibile importare i dati da Circolari Groups, il plugin Groups non mi risulta installato o l'importazione è già avvenuta", 'wpscuola' )."</p>";
			}
			break;
		case "importacircolarigroups":
			$Circolari = get_posts( "post_type=circolari&numberposts=-1" );
			if(count($Circolari)==0){
				echo "<p style=\"color:red;font-size:1.5em;font-weight: bold;\">".__("Non ci sono Circolari da importare da Circolari Groups", 'wpscuola' )."</p>";
				break;
			}
			if(count(get_terms('gruppiutenti', array('hide_empty' => false)))>0){
				$GruppiImportati=get_option("ImportazioneTabellaGroupsGruppi");
//				echo "<pre>";var_dump($GruppiImportati);echo "</pre>";
				foreach($Circolari as $Circolare){
					$Destinatari=get_post_meta($Circolare->ID,"_destinatari");
					$Destinatari=unserialize($Destinatari[0]);
//					echo "<pre>".$Circolare->ID." ";var_dump($Destinatari);echo "</pre>";
					$GDest=array();
					foreach($Destinatari as $Destinatario){
						$GDest[]=$GruppiImportati[$Destinatario];
					}
					if(count($GDest)==1 and $GDest[0]==NULL){
						$DestTutti=get_option('Circolari_Visibilita_Pubblica');
						$GDest[]=(int)$DestTutti;
					}
//					echo "<pre>".$Circolare->ID." ";var_dump($GDest);echo "</pre>";
					wp_set_post_terms($Circolare->ID,$GDest,"gruppiutenti");
					if (get_post_meta( $Circolare->ID, 'groups-read', true )) update_post_meta( $Circolare->ID, '_visibilita', 'd');
					set_post_type($Circolare->ID,"circolari_scuola");
				}
//				echo "<pre>";var_dump($UtentiGruppi);echo "</pre>";
				echo "<p style=\"color:green;font-size:1.5em;font-weight: bold;\">".__("Importazione Circolari eseguita con successo", 'wpscuola' )."</p>";
			}else{
				echo "<p style=\"color:red;font-size:1.5em;font-weight: bold;\">".__("Impossibile importare le Circolari da Circolari Groups, devi prima importare i gruppi", 'wpscuola' )."</p>";
			}
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
			
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">'.__("Verifica Tipo circolare", 'wpscuola' ).'</p>
				<ul>
					<li> '.__("Questa procedura esegue una verifica sulle circoalri per identificare le circolari che hanno un 'Tipo Circoalre' errato o non definito", 'wpscuola' ).'</spam><br /><spam style="font-size:1em;font-style: italic;margin-left:10px;font-weight: bold;">
		'.__("Verifica Tipo Circolare", 'wpscuola' ).' <spam style="text-align:center;font-size:1.5em;font-weight: bold;"> <a href="edit.php?post_type=circolari_scuola&page=Utility&action=vertipo">'.__("Verifica", 'wpscuola' ).'</a></spam>
					</li>
				</ul>			
			
			
			
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">'.__("Verifica procedura", 'wpscuola' ).'</p>
				<ul>
					<li> '.__("Questa procedura esegue un test generale della procedura e riporta eventuali anomalie nei dati e nelle impostazioni.", 'wpscuola' ).'</spam><br /><spam style="font-size:1em;font-style: italic;margin-left:10px;font-weight: bold;">
		'.__("Verifica Presenza data scadenza firma", 'wpscuola' ).' <spam style="text-align:center;font-size:1.5em;font-weight: bold;"> <a href="edit.php?post_type=circolari_scuola&page=Utility&action=versca">'.__("Verifica", 'wpscuola' ).'</a></spam>
					</li>
					<li style="text-align:left;font-size:1em;">'.__("Questa procedura verifica il formato delle date di scadenza.", 'wpscuola' ).'<br /><spam style="font-size:1em;font-style: italic;margin-left:10px;font-weight: bold;">
		'.__("Verifica Formato data scadenza firma", 'wpscuola' ).' <spam style="text-align:center;font-size:1.5em;font-weight: bold;"> <a href="edit.php?post_type=circolari_scuola&page=Utility&action=verforsca">'.__("Verifica", 'wpscuola' ).'</a></spam>
					</li>
				</ul>
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">'.__("Importa Gruppi da Groups", 'wpscuola' ).'</p>
					<ul>
						<li style="text-align:left;font-size:1em;">
						<p>'.__("Questa procedura deve essere eseguita prima di disattivare Groups.", 'wpscuola' ).'</p>
						'.__("Le operazioni che verranno eseguite sono:", 'wpscuola' ).'
						<ol>
							<li>'.__("Importazione dei Gruppi", 'wpscuola' ).'</li>
							<li>'.__("Aggiornamento dei gruppi degli utenti", 'wpscuola' ).'</li>
							<li>'.__("Impostazione Gruppo Pubblico", 'wpscuola' ).'</li>
						</ol>
						</li>
						</p>
					</ul>
					<spam style="text-align:center;font-size:1.5em;font-weight: bold;">
						<a href="edit.php?post_type=circolari_scuola&page=Utility&action=importa">'.__("Importa Gruppi", 'wpscuola' ).'</a>
					</spam>	
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">'.__("Importa Circolari da Circolari Groups", 'wpscuola' ).'</p>
					<ul>
						<li style="text-align:left;font-size:1em;">
						<p>'.__("Questa procedura deve essere eseguita dopo la disattivazione di Circolari Groups.", 'wpscuola' ).'</p>
						'.__("Le operazioni che verranno eseguite sono:", 'wpscuola' ).'
						<ol>
							<li>'.__("Importazione delle Circolari", 'wpscuola' ).'Modifica del tipo post delle circolari</li>
							<li>'.__("Aggiornamento dei gruppi destinatari delle circolari", 'wpscuola' ).'</li>
							<li>'.__("Aggiornamento delle Circolari Private", 'wpscuola' ).'</li>
							<li>'.__("Cancellazione delle Circolari codificate con Circolari Groups", 'wpscuola' ).'</li>
						</ol>
						</li>
						</p>
					</ul>
					<spam style="text-align:center;font-size:1.5em;font-weight: bold;">
						<a href="edit.php?post_type=circolari_scuola&page=Utility&action=importacircolarigroups">'.__("Importa Circolari", 'wpscuola' ).'</a>
					</spam>';
}
function wps_circolari_Testata() {
	global $wps_TestiRisposte,$post;
		
	if( isset($post->post_type) And $post->post_type != 'circolari_scuola' ) {
		return;
	}
	$sele="";
	if($wps_TestiRisposte){
		foreach($wps_TestiRisposte as $Risp){
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
			var answer = confirm("Circolare "+$(this).attr('rel') +"\n<?php _e('Confermi la scelta', 'wpscuola' );?>: " + s +"?")
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

function wps_circolari_MenuPagine(){
	switch (filter_input(INPUT_GET,"op")){
		case "Firme":
			wps_circolari_VisualizzaFirme((int)$_REQUEST["post_id"]);
			break;
		case "Adesioni":
			wps_circolari_VisualizzaFirme((int)$_REQUEST["post_id"],1);
			break;
		case "email":
			wps_circolari_SpostainNewsletter((int)$_REQUEST["post_id"]);
			break;
		case "Log":
			wps_circolari_VisualizzaLog((int)$_REQUEST["post_id"]);
			break;
		default:
			wps_circolari_Parametri();	
	}
}
function wps_circolari_uninstall() {
	global $wpdb;
// Eliminazione Tabelle data Base
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_circolari_firme);
	$Circolari = get_posts( "post_type=circolari_scuola&numberposts=-1" );
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

function wps_circolari_VisualizzaLog($IDPost){
	global $wps_TestiRisposte;
	wps_MakeDir();
	$numero=get_post_meta($IDPost, "_numero");
	$anno=get_post_meta($IDPost, "_anno");
	$circolare=get_post($IDPost);
	$Dati=wps_Get_Log_Circolare($IDPost);
?>	
	<div class="wrap">
		<span class="fa fa-th-list fa-3x" aria-hidden="true"></span>
		<h2 style="display:inline;margin-left:10px;vertical-align:super;"><?php _e("Log Firme Circolare n°", 'wpscuola' );?> <?php echo $numero[0].'_'.$anno[0];?></h2>
		<h3><?php echo $circolare->post_title;?></h3>
			<table  id="TabellaCircolari" class="widefat"  cellspacing="0" width="99%">
			<thead>
				<th><?php _e("Utente", 'wpscuola' );?></th>
				<th><?php _e("Data Ora", 'wpscuola' );?></th>
				<th><?php _e("Operazione", 'wpscuola' );?></th>
				<th><?php _e("Espressione", 'wpscuola' );?></th>
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

function wps_circolari_SpostainNewsletter($IDPost){
$DatiPost=get_post( $IDPost,  ARRAY_A);
		$args = array(
			'post_type' => 'attachment',
			'numberposts' => null,
			'post_status' => null,
			'post_parent' => $IDPost); 
		$attachments = get_posts($args);
		$LinkAllegati="";
		if ($attachments) {
			$LinkAllegati.="<p>".__("Allegati", 'wpscuola' )."
			<ul>";
			foreach ($attachments as $attachment) {
				$LinkAllegati.="		<li><a href='$attachment->guid'>$attachment->post_title</a></li>";
			}
			$LinkAllegati.="</p>
			</ul>";	
		}
$my_post = array(
  		'post_title'    => $DatiPost['post_title'],
  		'post_content'  => "<p>".sprintf(__("Ciao [USER-NAME]</p>
%sin data odierna è stata inserita la seguente circolare nel sito [SITE-NAME]%s
%s[POST-EXCERPT]%s
%s[POST-CONTENT]%s", 'wpscuola' ),"<p>","</p>","<p>","</p>","<p>","</p>")."
<p><a href='".get_permalink($IDPost)."'>".__("Visualizza la circolare sul sito", 'wpscuola' )."</a> ".$LinkAllegati,
  		'post_status'   => 'publish',
  		'comment_status'   => 'closed',
  		'ping_status' => 'closed',
  		'post_author' => $DatiPost['post_author'],
  		'post_name' => $DatiPost['post_name'],
  		'post_type' => 'newsletter');
$post_id =wp_insert_post( $my_post );
echo '<div class="wrap">
	  	<span class="fa fa-envelope fa-3x" aria-hidden="true"></span><h2 style="display:inline;margin-left:10px;vertical-align:super;">'.__("Crea NewsLetter", 'wpscuola' ).'
	  	<a href="'.site_url().'/wp-admin/edit.php?post_type=circolari_scuola" class="add-new-h2 tornaindietro">'.__("Torna indietro", 'wpscuola' ).'</a></h2>';

	if($post_id>0){
		$recipients=Array();
		$recipients['list'][] = 1;
		$recipients['list'][] = 2;
		add_post_meta ( $post_id, "_easymail_recipients", $recipients );	
		add_post_meta ( $post_id, "_placeholder_easymail_post",  $IDPost);	
		add_post_meta ( $post_id, "_placeholder_post_imgsize", 'thumbnail' );	
		add_post_meta ( $post_id, "_placeholder_newsletter_imgsize", 'thumbnail' );	
		add_post_meta ( $post_id, "_easymail_theme", 'campaignmonitor_elegant.html' );	
		echo "<p style='font-weight: bold;font-size: medium;color:green;'>".__("NewsLetter Creata correttamente", 'wpscuola' )."</p> 
		<p style='font-weight: bold;font-style: italic;font-size: medium;'>".__("Adesso dovete completare le operazioni di invio seguendo pochi e semplici passi", 'wpscuola' ).":<ul style='list-style: circle outside;margin-left:20px;'>
			<li>".__("Entrare in modifica nella", 'wpscuola' )." <a class='function' href='".admin_url()."post.php?post=".$post_id."&action=edit'>".__("circolare appena creata", 'wpscuola' )."</a> ".__("(l'ultima, quella in cima alla lista)", 'wpscuola' )."</li>
			<li>".__("Selezionate i destinatari", 'wpscuola' )."</li>
			<li>".__("Memorizzare le modifiche", 'wpscuola' )."</li>
			<li>".__("Dall'elenco delle NewsLetter, sulla riga corrente cliccare su <em>Richiesto: Crea la lista dei destinatari", 'wpscuola' )."</em></li>
		</ul>
		</p>";
		add_post_meta ( $IDPost, "_sendNewsLetter",date("d/m/y g:i O"));
		add_post_meta ( $IDPost, "_placeholder_easymail_post",$post_id);
	}else{
		echo "<p  style='font-weight: bold;font-size: medium;color:red;'>".__("NewsLetter Non Creata correttamente, errore riportato", 'wpscuola' ).":</p>";
				print_r($errore);			
	}
}

function wps_circolari_Parametri(){
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
	echo'
<div class="wrap">
	  <span class="fa fa-magic fa-3x" aria-hidden="true"></span><h2 style="display:inline;margin-left:10px;vertical-align:super;">'.__("Configurazione Circolari", 'wpscuola' ).'</h2>
	  <form name="Circolari_cnf" action="'.get_bloginfo('wpurl').'/wp-admin/index.php" method="post">
	  <input type="hidden" name="circoPar" value="'.wp_create_nonce('ParametriCircolare').'" />
	  <table class="form-table circolari-config">
		<tr valign="top">
			<th scope="row"><label for="pubblica">'.__("Gruppo Pubblico Circolari", 'wpscuola' ).'</label></th>
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
			<th scope="row"><label for="GGScadenza">'.__("N. giorni entro cui firmare di default", 'wpscuola' ).'</label></th>
			<td>
				<input type="text" name="GGScadenza" id="GGScadenza" size="3" maxlength="3" value="'.$GiorniScadenza.'" />
			</td>				
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="NotificaFirma">'.__("Notifica firma tramite @mail", 'wpscuola' ).'</label>
			</th>
			<td>		
				<input type="checkbox" name="NotificaFirma" id="NotificaFirma" value="Si" '.($NotificaFirma=="Si"?"checked":"").'/> Attiva
			</td>				
		</tr>					
		<tr valign="top">
			<th scope="row">
				<label for="FromNotificaFirma">'.__("Mittente @mail", 'wpscuola' ).'</label>
			</th>
			<td>		
				<input type="text" name="FromNotificaFirma" id="FromNotificaFirma" value="'.$MittenteNotifica.'" size="65"/><br />
					<em>'.__("Indicare il mittente nel formato: Nome del mittente <indirizzo@dminio.estensione>", 'wpscuola' ).'</em>
			</td>				
		</tr>					
		<tr valign="top">
			<th scope="row">
				<label for="OggettoNotificaFirma">'.__("Oggetto @mail", 'wpscuola' ).'</label>
			</th>
			<td>		
				<input type="text" name="OggettoNotificaFirma" id="OggettoNotificaFirma" value="'.$OggettoNotifica.'" size="65"/>
			</td>				
		</tr>					

		<tr valign="top">
			<th scope="row">
				<label for="$MessaggioNotifica">'.__("Messaggio @mail", 'wpscuola' ).'</label>
			</th>
			<td>		
				<textarea name="MessaggioNotifica" id="MessaggioNotifica" cols="150" rows="10">'.$MessaggioNotifica.' </textarea><br />
				'.__("{Link_Circolare} aggiunge il link alla circolare firmata<br />
				{Dati_Utente} visualizza una stringa nel formato <strong>None Cognome</strong> dell'utente che ha firmato la circolare<br />
				{Data} visualizza una stringa nel formato <strong>gg/mm/aaaa - hh:mm</strong> del momento in cui viene firmata la circolare<br />
				{Operazione} Visualizza l'operazione effettuata : riportando il testo impostato in Tipi di circolari > Risposte", 'wpscuola' ).'
				
			</td>				
		</tr>					
		<tr valign="top">
			<th scope="row"><label for="GestPerm">'.__("Tipologia gestione permessi", 'wpscuola' ).'</label></th>
			<td>
				<input type="radio" name="GestPerm" id="GestPerm" value="int" '.$GPP.'" />'.__("Standard", 'wpscuola' ).'
				<input type="radio" name="GestPerm" id="GestPerm" value="ext" '.$GPE.'" />'.__("Esteso", 'wpscuola' ).'
				<ul style="list-style: disc;font-style: italic;">
					<li>'.__("Standard: vengono mantenuti le capabilities standard dei Posts", 'wpscuola' ).'</li>
					<li>'.__("Esteso: vengono creati capabilities", 'wpscuola' ).'
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
							<li>send_circ@mail; <strong>'.__("permette di gestire le newsletter", 'wpscuola' ).'</strong></li>
							<li>manage_adesioni; <strong>'.__("permette la gestione delle adesioni/firme", 'wpscuola' ).'</strong></li>
						</ul>
						<em><strong>'.__("Per la gestione delle capabilities si consiglia l'uso del plugin", 'wpscuola' ).' <a href="https://wordpress.org/plugins/user-role-editor">User Role Editor</a></strong></em> 
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
function wps_circolari_NuoveColonne($defaults) {  
	if (isset($_GET['post_type']) And $_GET['post_type']=="circolari_scuola"){
		$defaults['stato'] = 'Parametri Circolare'; 
	    if (current_user_can('edit_others_posts')){
			$defaults['gestionecircolari'] = 'Gestione';  
		}
	}
   return $defaults;  
}  
  
// Visualizzazione nuova colonna Gestione  
function wps_circolari_NuoveColonneContenuto($column_name, $post_ID) {  
	global $wpdb;
 	if (isset($_GET['post_type']) And $_GET['post_type']=="circolari_scuola"){
		$sign=get_post_meta($post_ID, "_sign",TRUE);
		$tipo=wps_Circolari_find_Tipo($sign);
		if($tipo===FALSE){
			 echo "<span style=\"color:red;\">".__("ERRORE!<br />Tipo di circolare non definito", 'wpscuola' )."</span>";
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
				$Linkfirma='<a class="function" href="'.admin_url().'edit.php?post_type=circolari_scuola&page=circolari&op=Firme&post_id='.$post_ID.'"><span class="fas fa-pencil-alt tooltip" title="Visualizza '.$Prefisso.'"></span></a>';
			elseif($tipo->get_Tipo()!="NoFirma")
				$Linkfirma='<a class="function" href="'.admin_url().'edit.php?post_type=circolari_scuola&page=circolari&op=Adesioni&post_id='.$post_ID.'"><span class="fas fa-pencil-alt tooltip" title="Visualizza '.$Prefisso.'"></span></a>';
	    	if ( defined( 'ALO_EM_INTERVAL_MIN' ) ){
				$DataInvio = get_post_meta( $post_ID, "_sendNewsLetter", true); 
				$IDNL=$wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta Where post_id=$post_ID And meta_key='_placeholder_easymail_post';");
				if(count($IDNL)>0){
					$NL=$wpdb->get_results("SELECT ID FROM $wpdb->posts Where ID=".$IDNL[0]->meta_value.";");
				}
	    		if ($DataInvio And count($IDNL)>0 And count($NL)>0){
					$LinkNL.=' <a class="function" href="'.admin_url().'post.php?post='.$IDNL[0]->meta_value.'&action=edit"><span class="fa fa-envelope tooltip" title="Visualizza NewsLetter <br />generata in data '. $DataInvio.'"></span></a>';
				}else
	            	$LinkNL.=' <a class="function" href="'.admin_url().'edit.php?post_type=circolari_scuola&page=circolari&op=email&post_id='.$post_ID.'"><span class="fa fa-inbox tooltip" title="Genera NewsLetter"></span></a>';  
			}
			if ($tipo->get_Tipo()!='NoFirma'){
				$LinkLog=' <a class="function" href="'.admin_url().'edit.php?post_type=circolari_scuola&page=circolari&op=Log&post_id='.$post_ID.'"><span class="fa fa-th-list tooltip" aria-hidden="true" title="Visualizza Log delle Firme della Circolare"></span></a>';
			}
			echo '<div class="btn-group">'.$Linkfirma.$LinkNL.$LinkLog.'</div>';
	     }
		 if ($column_name == 'stato'){
			$Dati=""; 
			$numero=get_post_meta($post_ID, "_numero",TRUE);
			$anno=get_post_meta($post_ID, "_anno",TRUE);
			$Info= '<span class="fa fa-vcard-o" aria-hidden="true"></span> '.$numero.'_'.$anno." ";		 
			$Dati="Circolare N° ".$numero.'_'.$anno."\r\n"; 
			if(get_post_meta($post_ID, "_visibilita", "d")=="d"){
				$Info.= '<span class="fa fa-lock" aria-hidden="true"></span>'."\r\n";
				$Dati.=__("Visibilità:Riservata", 'wpscuola' )."\r\n";
			}else{
				$Info.= '<span class="fa fa-unlock" aria-hidden="true"></span><br />';
				$Dati.=__("Visibilità:Pubblica", 'wpscuola' )."\r\n";
			}
			$Dati.=__("Tipo", 'wpscuola' ).": ".$tipo->get_Tipo()."\r\n";
			$Info.= '<span class="fa fa-hand-pointer-o" aria-hidden="true"></span> '.$tipo->get_Tipo().'<br />';
			if (wps_Is_da_Firmare($post_ID)){
				if(wps_Is_Circolare_Scaduta($post_ID)){
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
					$Firmate=wps_Get_Numero_Firme_Per_Circolare($post_ID);
					if(in_array($IdGruppoTutti,$dest))
						$DaVis= $Firmate."/".wps_Get_User_Per_Gruppo($IdGruppoTutti);
					else{
						foreach($dest as $IdGruppo)
							if ($IdGruppoTutti!=$IdGruppo)
								$NU+=wps_Get_User_Per_Gruppo($IdGruppo);
						$DaVis=  $Firmate."/$NU";			
					}
					$DaVis="(".$DaVis.")";
					$DaVisDati="Firmate ".$Firmate." su ".$NU."\r\n";
				}
				$Info.= ' <span class="fas fa-pencil-alt" aria-hidden="true" style="color:'.$Color.'"></span> '.wps_FormatDataItalianoBreve(get_post_meta($post_ID, "_scadenza",TRUE)).$DaVis."<br />";
				$Dati.=__("Data scadenza firma", 'wpscuola' )." ".wps_FormatDataItalianoBreve(get_post_meta($post_ID, "_scadenza",TRUE))."\r\n".$DaVisDati;
			}
			$DesDest=wps_GetEencoDestinatari($post_ID,FALSE);
			$Info.= '<span class="fa fa-users" aria-hidden="true"></span> '.$DesDest;
			$Dati.=__("Destinatari Circolari", 'wpscuola' ).": ".$DesDest;
			echo '<a href="#" title="'.$Dati.'" class="nolink">'.$Info."</a>";
		}
	}
}  

// add links/menus to the admin bar

function wps_circolari_admin_bar_render() {
	global $wp_admin_bar,$DaFirmare;
	if (!isset($DaFirmare))
		$DaFirmare=wps_GetCircolariDaFirmare("N");
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

function wps_circolari_updated_messages( $messages ) {
	global $post, $post_ID;
    $messages['circolari'] = array(
	0 => '', 
	1 => sprintf(__('Circolare aggiornata. <a href="%s">Visualizza Circolare</a>', 'wpscuola' ), esc_url( get_permalink($post_ID) ) ),
	2 => 'Circolare aggiornata',
/* translators: %s: date and time of the revision */
	3 => isset($_GET['circolari']) ? sprintf( __('Circolare ripristinata alla versione %s', 'wpscuola' ), wp_post_revision_title( (int) $_GET['circolari'], false ) ) : false,
	4 => sprintf( __('Circolare pubblicata. <a href="%s">Visualizza Circolare</a>', 'wpscuola' ), esc_url( get_permalink($post_ID) ) ),
	5 => 'Circolare memorizzata',
	6 => sprintf( __('Circolare inviata. <a target="_blank" href="%s">Anteprima Circolare</a>', 'wpscuola' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	7 => sprintf( __('Circolare schedulata per: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Anteprima circolare</a>', 'wpscuola' ),date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
	8 => sprintf( __('Bozza Circolare aggiornata. <a target="_blank" href="%s">Anteprima Circolare</a>', 'wpscuola' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
);
return $messages;
}

function set_post_default_group($post_id, $post, $update){
	if ( 'circolari_scuola' !== $post->post_type ) {
        return;
    }
	$term_list = wp_get_post_terms($post_id, 'gruppiutenti', array("fields" => "names"));
	if (count($term_list)==0) {
		$DestTutti=get_option('Circolari_Visibilita_Pubblica');
		wp_set_object_terms( $post_id, (int)$DestTutti,"gruppiutenti",FALSE );
	}
}
function wps_circolari_salva_dettagli( $post_id ){
//	print_r($_POST);exit;
		if ( filter_input(INPUT_POST,'post_type') == 'circolari_scuola' ) {	
			delete_post_meta( $post_id, '_scadenza' );
			delete_post_meta( $post_id, '_numero' );
			delete_post_meta( $post_id, '_anno' );
			delete_post_meta( $post_id, '_visibilita' );
			//wp_set_post_categories( $post_id, array($Circolari) );
			if ($_POST["Sign"]!="NoFirma"){
				if ($_POST["scadenza"])
					update_post_meta( $post_id, '_scadenza', $_POST["scadenza"]);
				else
					update_post_meta( $post_id, '_scadenza', wps_FormatDataDB(date("d/m/Y"),get_option('Circolari_GGScadenza')));
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

function wps_circolari_crea_box(){
  add_meta_box('parametri', __('Parametri Circolari', 'wpscuola' ), 'wps_circolari_crea_box_parametri', 'circolari_scuola', 'side', 'high',array(
        '__block_editor_compatible_meta_box' => true,
    ));
}

function wps_circolari_crea_box_parametri( $post ){
	echo "<h4>".__("Firmare entro", 'wpscuola' )."</h4>";	
	wps_circolari_crea_box_data_scadenza($post);
	echo "<h4>".__("Progessivo", 'wpscuola' )."</h4>";
	wps_circolari_crea_box_progressivo($post);
	echo "<h4>".__("Firme", 'wpscuola' )."</h4>";
	wps_circolari_crea_box_firma($post);
	//circolari_crea_box_firma_sciopero($post);
	echo "<h4>".__("Visibilità", 'wpscuola' )."</h4>";
	wps_circolari_crea_box_visibilita($post);
}

function wps_circolari_NewNumCircolare($numero){
	$args = array( 'numberposts' => '1','post_type'=> 'circolari_scuola','post_status' => 'publish','meta_key' => '_anno','meta_value' => $numero);
	$ultimo=wp_get_recent_posts($args);
	if(count($ultimo)==0){
		return 1;
	}else{
		$ID=$ultimo[0]['ID'];
		$numero=get_post_meta($ID, "_numero");
		return $numero[0]+1;
	}
}
function wps_circolari_crea_box_progressivo( $post ){
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
	$numero=wps_circolari_NewNumCircolare($anno);
echo '<label>'.__("Numero/Anno", 'wpscuola' ).'</label>
	<input type="text" name="numero" value="'.$numero.'" size="5" id="numero_circolare" style="text-align:right"/>_<input type="text" name="anno" value="'.$anno.'" size="5"/>
	<br />' ;
}

function wps_circolari_crea_box_visibilita( $post ){
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
echo __("Pubblica", 'wpscuola' ).' <input type="radio" name="visibilita" value="p" '.$selp.'/><br />
'.__("Riservata", 'wpscuola' ).' <input type="radio" name="visibilita" value="d" '.$seld.'/>';
}
function wps_circolari_crea_box_firma( $post ){
	global $wps_Testi;
	$sign=get_post_meta($post->ID, "_sign",TRUE);
	echo Circolari_Tipo::get_Tipi($sign);
}

function wps_circolari_crea_box_data_scadenza( $post ){
	$scadenza=get_post_meta($post->ID, "_scadenza",TRUE);
	echo "<label>".__("Data", 'wpscuola' )."</label> <input type='date' name='scadenza' value='".$scadenza."' style='text-align:left'/>
	<br />" ;
}

function wps_circolari_VisualizzaFirme($post_id,$Tipo=0){
	global $GestioneScuola,$wps_Testi,$wps_TestiRisposte;
	$numero=get_post_meta($post_id, "_numero");
	$anno=get_post_meta($post_id, "_anno");
	$circolare=get_post($post_id);
	// Inizio interfaccia
	$TipoC=get_post_meta($post_id, "_sign",TRUE);
	echo' 
	<div class="wrap">
		<h2 >'.__("Circolare n°", 'wpscuola' ).' '.$numero[0].'_'.$anno[0].'<br /><strong>'.$circolare->post_title.'</strong></h2>';
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
			<span class="fa fa-users fa-2x" aria-hidden="true"></span> '.$Elenco.'</p>
	</div>
	';
	$utenti=wps_Get_Users_per_Circolare($post_id);
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
					<th style="width:'.(20-$sottrai).'%;">'.__("User login", 'wpscuola' ).'</th>
					<th style="width:'.(30-$sottrai).'%;" id="ColOrd" sorted="1">'.__("Cognome", 'wpscuola' ).'</th>
					<th style="width:'.(15-$sottrai).'%;">'.__("Gruppo", 'wpscuola' ).'</th>
					<th style="width:'.(15-$sottrai).'%;">'.__("Data Operazione", 'wpscuola' ).'</th>';
	if ($Tipo==1){
			$Testo=wps_Circolari_find_Tipo($TipoC);
			echo '
						<th style="width:12%;">'.$Testo->get_TestoElenco().'</th>';
		}else
		echo '
					<th style="width:'.(15-$sottrai).'%;">'.__("Espressione", 'wpscuola' ).'</th>';			
	echo '
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th style="width:'.(20-$sottrai).'%;">'.__("User login", 'wpscuola' ).'</th>
					<th style="width:'.(30-$sottrai).'%;">'.__("Cognome", 'wpscuola' ).'</th>
					<th style="width:'.(15-$sottrai).'%;">'.__("Gruppo", 'wpscuola' ).'</th>
					<th style="width:'.(15-$sottrai).'%;">'.__("Data Operazione", 'wpscuola' ).'</th>';
	if ($Tipo==1){
			$Testo=wps_Circolari_find_Tipo($TipoC);
			echo '
						<th style="width:12%;">'.$Testo->get_TestoElenco().'</th>';
		}else
		echo '<th style="width:'.(15-$sottrai).'%;">'.__("Espressione", 'wpscuola' ).'</th>';
	echo '
				</tr>
			</tfoot>
			<tboby>';
	foreach($utenti as $utente){
		$GruppoUtente=get_user_meta($utente->ID, "gruppo", true);
		if(($firma=wps_get_Firma_Circolare($post_id,$utente->ID))===FALSE){
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
			$DesGsU="<span style='color:red;'>".__("Nessuno", 'wpscuola' )."</span>";
		}
		echo '
					<tr>
						<td>'.$utente->user_login.'</td>
						<td>'.$utente->display_name.'</td>
						<td>'.$DesGsU.'</td>
						<td>'.$Firma.'</td>';
		if (isset($firma->adesione)) 
			if(wps_Circolari_is_set_IDRisposte($firma->adesione))
				$desad=$wps_TestiRisposte[(int)$firma->adesione]->get_Risposta();
			else
				$desad=__("Errore Etichetta non trovata", 'wpscuola' );
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