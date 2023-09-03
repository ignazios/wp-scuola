<?php
/**
 * Gestione Circolari - Funzioni libreia generale
 * 
 * @package Gestione Circolari
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @ver 2.7.3
 */
 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

function wps_MakeDir($Dir="Circolari",$Base="wp-content"){
	$MkDir=WP_CONTENT_DIR;
	$Cartelle=explode("/",$Dir);
	foreach($Cartelle as $Cartella){
		$MkDir.="/".$Cartella;
		if (!is_dir ( $MkDir)){
			if (!mkdir($MkDir, 0755)){
				return false;
			}
		}
	}
	return true;
}
function wps_FormatDataItalianoBreve($Data,$AnnoBreve=FALSE){
	$d=explode("-",$Data);
	if ($AnnoBreve And strlen($d[0])==4){
		$Anno=substr($d[0],2,2);
	}else{
		$Anno=$d[0];
	}
	if (count($d)==3)
		return $d[2]."/".$d[1]."/".$Anno;
	else
		return "";
}

function wps_NormalData($Data){
	$Pdata=explode("-",$Data);
	$Anno=$Pdata[0];
	$Giorno=$Pdata[2];
	$Mese=$Pdata[1];
	$NDA=strlen($Pdata[0]);
	if ($NDA<4){
		$AnnoCorrente=date("Y");
		$Anno=substr($AnnoCorrente,0,4-$NDA).$Pdata[0];
	}
	$NDG=strlen($Pdata[2]);
	if ($NDG==1){
		$Giorno="0".$Pdata[2];
	}
	if ($NDG==0){
		$Giorno=date("d");
	}
	$NDM=strlen($Pdata[1]);
	if ($NDM==1){
		$Mese="0".$Pdata[1];
	}
	if ($NDM==0){
		$Mese=date("d");
	}	
	return $Anno."-".$Mese."-".$Giorno;
}
function wps_FormatDataDB($Data,$incGG=0,$incMM=0,$incAA=0){
	$d=explode("/",$Data);
	if (strlen($d[1])<2)
		$Mese="0".$d[1];
	else
		$Mese=$d[1];
	if (strlen($d[0])<2)
		$Giorno="0".$d[0];
	else
		$Giorno=$d[0];
	$Data=$d[2]."-".$Mese."-".$Giorno;
	if ($incAA>0)
		$Data=$d[2]+$incAA."-".$d[1]."-".$d[0];
	if ($incGG>0)
		$Data=date('Y-m-d', strtotime($Data. ' + '.$incGG.' days'));
	if ($incMM>0)
		$Data=date('Y-m-d', strtotime($Data. ' + '.$incMM.' months'));
	return $Data;
}

function wps_GetNumeroCircolare($PostID){
	$numero=get_post_meta($PostID, "_numero");
	$numero=(isset($numero[0])?$numero[0]:"ND");
	$anno=get_post_meta($PostID, "_anno");
	$anno=(isset($anno[0])?$anno[0]:"ND");
	$NumeroCircolare=$numero.'_'.$anno ;
return $NumeroCircolare;
}
function wps_GetEencoDestinatari($PostID,$Bold=FALSE,$Link=FALSE){
	$fgs = wp_get_object_terms($PostID, 'gruppiutenti');
	$Elenco="";
	if(!empty($fgs)){
		foreach($fgs as $fg){
			if($Link){
				$Elenco.='<a href="'.esc_url(home_url('/')).'destinatari/'.$fg->slug.'" >'.($Bold?"<strong>":"").$fg->name.($Bold?"</strong>":"").'</a> - ';
			}else{
				$Elenco.=($Bold?"<strong>":"").$fg->name.($Bold?"</strong>":"")." - ";
			}
		}
		$Elenco=substr($Elenco,0,strlen($Elenco)-3);
	}
	return $Elenco;
}
function wps_Is_da_Firmare($IDCircolare){
	$sign=get_post_meta( $IDCircolare, "_sign",true);
	if ($sign!="NoFirma" )
		return TRUE;
	else
		return FALSE;
}
function wps_Is_Circolare_Da_Firmare($IDCircolare,$Tutte=False){
	global $wpdb;
	$ora=date("Y-m-d");
	$current_user =wp_get_current_user();
	//$destinatari=Get_Users_per_Circolare($IDCircolare,"ID");
	$DaFirmare=get_post_meta( $IDCircolare, "_sign",true);
	if (!$Tutte){
		$Scadenza=get_post_meta( $IDCircolare, "_scadenza",true);
		if(!$Scadenza)
			$Scadenza=date("Y-m-d");
	}
	else
		$Scadenza=$ora;
//	if (in_array($current_user->ID,$destinatari) and (($DaFirmare=="Si" or $PresaVisione=="Si") and $Scadenza>=$ora))
	if (wps_Is_Circolare_per_User($IDCircolare) And ($DaFirmare!="NoFirma" And $Scadenza>=$ora))
		return TRUE;
	else
		return FALSE;
}

function wps_Is_Circolare_Scaduta($IDCircolare){
	global $wpdb;
	$ora=date("Y-m-d");
	$current_user =wp_get_current_user();
//	$destinatari=Get_Users_per_Circolare($IDCircolare,"ID");
	$DaFirmare=get_post_meta( $IDCircolare, "_sign",true);
	$Scadenza=get_post_meta( $IDCircolare, "_scadenza",true);		
	if (!$Scadenza){		
		return FALSE;	
	}
	if (wps_Is_Circolare_per_User($IDCircolare) and ($DaFirmare!="NoFirma" And  strtotime($Scadenza)<strtotime($ora)))
		return TRUE;
	else
		return FALSE;
}

function wps_Get_scadenzaCircolare($ID,$TipoRet="Data",$Giorni=False){
	$Scadenza=get_post_meta( $ID, "_scadenza",true);
	if (!$Scadenza){
		$Scadenza=date("Y-m-d");		
		if ($Giorni){
			return -1;		
		}
	}
	if ($Giorni){
//		echo "in giorni ";
		$seconds_diff = strtotime($Scadenza) - strtotime(date("Y-m-d"));
		$GGDiff=intval(floor($seconds_diff/3600/24));
		return $GGDiff;
	}
	if ($TipoRet=="Data"){
		$Pezzi=explode("-",$Scadenza);
		return $Pezzi[2]."/".$Pezzi[1]."/".$Pezzi[0];
	}else
		return $Scadenza;
}
function wps_Is_Circolare_Firmata($IDCircolare){
	global $wpdb;
	$current_user =wp_get_current_user();
	if (!wps_Is_Circolare_Da_Firmare($IDCircolare,TRUE))
		return FALSE;
	$ris=$wpdb->get_results("SELECT * FROM $wpdb->table_circolari_firme WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;");
/*	echo "SELECT * FROM $wpdb->table_circolari_firme WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;";
	print_r($ris);
echo count($ris);*/
	if (count($ris)==1)
		return TRUE;
	else
		return FALSE;	
}
function wps_Get_Data_Firma($IDCircolare){
	global $wpdb;
	$current_user =wp_get_current_user();
	if (!wps_Is_Circolare_Da_Firmare($IDCircolare,TRUE))
		return "";
	$ris=$wpdb->get_results("SELECT datafirma FROM $wpdb->table_circolari_firme WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;");
/*	echo "SELECT * FROM $wpdb->table_circolari_firme WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;";
	
echo count($ris);*/
	if (count($ris)==1)
		return substr($ris[0]->datafirma,0,10);
	else
		return "";	
}

function wps_Get_DataOra_Firma($IDCircolare){
	global $wpdb;
	$current_user =wp_get_current_user();
	$ris=$wpdb->get_results("SELECT datafirma FROM $wpdb->table_circolari_firme WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;");
//	echo "SELECT * FROM $wpdb->table_circolari_firme WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;";
//  echo count($ris);
	if (count($ris)==1)
		return wps_FormatDataItalianoBreve(substr($ris[0]->datafirma,0,10))." ".substr($ris[0]->datafirma,11,595);
	else
		return "";	
}

function wps_get_Circolare_Adesione($IDCircolare){
	global $wpdb;
	$current_user =wp_get_current_user();
	$ris=$wpdb->get_results("SELECT * FROM $wpdb->table_circolari_firme WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;");
	if (!empty($ris))
		return $ris[0]->adesione;
	else
		return 0;	
}

function wps_get_Firma_Circolare($IDCircolare,$IDUser=-1){
	global $wpdb;
	if ($IDUser==-1){
		$current_user =wp_get_current_user();
		$IDUser=$current_user->ID;
	}
	$ris=$wpdb->get_results("SELECT datafirma,ip,adesione FROM $wpdb->table_circolari_firme WHERE post_ID=$IDCircolare AND user_ID=$IDUser;");
	if (!empty($ris))
		return $ris[0];
	else
		return FALSE;	
}

function wps_GetArchivioCircolari($Anno="*"){
	global $wpdb;
	if (is_integer($Anno)){
		$F_Anno=" And YEAR(post_date)=".$Anno;
	}else{
		$F_Anno="";
	}
	$Sql="SELECT *
		FROM $wpdb->posts 
		WHERE $wpdb->posts.post_status = 'publish' 
			AND $wpdb->posts.post_type = 'circolari_scuola'
		".$F_Anno."
		ORDER BY post_date DESC"; 
	return $wpdb->get_results($Sql); 
}

function wps_GetCircolariDaFirmare($Tipo="N"){
	global $wpdb,$table_prefix;
	$tabella_firme = $table_prefix . "firme_circolari";
	$current_user =wp_get_current_user();
	$IDUser=$current_user->ID;
	$Oggi=date('Y-m-d');
	$Sql="SELECT $wpdb->posts.ID,$wpdb->posts.post_title
		  FROM $wpdb->posts inner join $wpdb->postmeta on
		   ($wpdb->posts.ID = $wpdb->postmeta.post_id)
		  WHERE ($wpdb->posts.post_type   ='circolari_scuola' and $wpdb->posts.post_status ='publish')
		    and ($wpdb->postmeta.meta_key = '_sign' and $wpdb->postmeta.meta_value !='NoFirma')  
            and ($wpdb->posts.ID IN (Select $wpdb->postmeta.post_ID from $wpdb->postmeta Where $wpdb->postmeta.meta_key = '_scadenza' and $wpdb->postmeta.meta_value >='$Oggi'))
	        and ($wpdb->posts.ID NOT IN (Select $tabella_firme.post_ID from $tabella_firme Where $tabella_firme.user_ID=$IDUser))
            GROUP BY ID";
//echo $Sql;wp_die();
	$ris= $wpdb->get_results($Sql);
	if ($Tipo=="N")
		$Circolari=0;
	else
		$Circolari=array();
	foreach($ris as $riga){
		if (wps_Is_Circolare_per_User($riga->ID)){
			if ($Tipo=="N"){
				$Circolari++;
			}
			else
				$Circolari[]=$riga;
		}
	}
	return $Circolari;		
}

function wps_Get_Users_per_Circolare($IDCircolare,$Cosa="*"){
$DestTutti=get_option('Circolari_Visibilita_Pubblica');
$dest=wp_get_post_terms( $IDCircolare, 'gruppiutenti', array("fields" => "ids") ); 
$ListaUtenti=get_users();
if (in_array($DestTutti,$dest))
	$Tutti="S";
else
	$Tutti="N";
$UtentiCircolare=array();
foreach($ListaUtenti as $utente){
	if (wps_Is_Circolare_per_User($IDCircolare,$utente->ID) or $Tutti=="S")
		if ($Cosa=="*")
			$UtentiCircolare[]=$utente;
		else
			$UtentiCircolare[]=$utente->ID;
}
return $UtentiCircolare;
}

function wps_Is_Circolare_Pubblica($IDCircolare){
	$visibilita=get_post_meta($IDCircolare, "_visibilita");
//	echo $IDCircolare." - ".$visibilita[0]." <br />";
	if (!$visibilita)
		return True;
	else 
		if ($visibilita[0]=="p")
			return True;
		else	
			return False;	
}

function wps_Is_Circolare_per_User($IDCircolare,$IDUser=-1){
	if ($IDUser==-1){
		$current_user =wp_get_current_user();
		$IDUser=$current_user->ID;
	}
	if($IDUser==0){
		return FALSE;
	}
	$Vis=FALSE;
	$DestTutti=get_option('Circolari_Visibilita_Pubblica');
	if($DestTutti===FALSE)
		$DestTutti=-1;
	$destinatari=wp_get_post_terms( $IDCircolare, 'gruppiutenti', array("fields" => "ids") ); 
	$dest=array();
	foreach($destinatari as $ele)
		$dest[]=$ele;
	if (in_array($DestTutti,$dest))
		return TRUE;
	else{
		$fgs = wp_get_object_terms($IDCircolare, 'gruppiutenti');
		$GruppiSel=array();
		if(!empty($fgs)){
			foreach($fgs as $fg)
				$GruppiSel[]=$fg->term_id;
		}
		$GruppoUtente=get_user_meta($IDUser, "gruppo", true);
		if(!$GruppoUtente){
			return FALSE;
		}
		if( is_array( $GruppoUtente )){
			foreach ($GruppoUtente as $Gruppo){
				if (in_array($Gruppo,$GruppiSel)){
					return TRUE;
				}
			}
		}
		if (in_array($GruppoUtente,$GruppiSel)){
			return TRUE;
		}
	}
	return FALSE;
}

function wps_ScriviLog($PostID,$UserID,$Operazione="Sign",$Espressione="Firma"){
	$data = date("d-m-y"); 
	$ora = date("G:i:s");
	$ip = $_SERVER['REMOTE_ADDR'];
	$testo = $data." ".$ora."|".$ip."|".$PostID."|".$UserID."|".$Operazione."|".$Espressione."\n";
	$var=@fopen(wps_Circolari_DIR_Servizio."/Firme.log","a");
	fwrite($var,$testo);
	fclose($var);
}
function wps_array_msort($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;

}

function wps_Get_Log_Circolare($PostID){
	$log=array();
	$file=@fopen(wps_Circolari_DIR_Servizio."/Firme.log","r");
	if ($file) {
		while (($riga = fgets($file)) !== false) {
			$buffer=explode("|",$riga);
			if((int)$buffer[2]==$PostID){
				$user_info = get_userdata($buffer[3]);
				$log[]=array("Utente"=>$user_info->first_name." ".$user_info->last_name,
					"Data"=>$buffer[0],
					"Operazione"=>$buffer[4],
					"Espressione"=>$buffer[5],
					);
			}
		}
	}else{
		$file=@fopen(wps_Circolari_DIR_Servizio."/Firme.log","w");
	}
	$LOG=wps_array_msort($log, array('Utente'=>SORT_ASC, 'Data'=>SORT_DESC));
	fclose($file);
	return $LOG;
}

function wps_RimuoviFirmaCircolare($IDCircolare){
	global $wpdb;
	if(FALSE!==($wps_TestiRisposte=get_option('Circolari_TestiRisposte'))){
    	$wps_TestiRisposte= unserialize($wps_TestiRisposte);
    }else{
		$wps_TestiRisposte=array();
	}
	$current_user =wp_get_current_user();
	$MsgFirmaLog="Errore";
	$firma=wps_get_Firma_Circolare($IDCircolare);
	if ( false === $wpdb->delete(
			$wpdb->table_circolari_firme,array(
				'post_ID' => $IDCircolare,
				'user_ID' => $current_user->ID),array("%d","%d"))){
// echo "Sql==".$wpdb->last_query ."    Ultimo errore==".$wpdb->last_error;
		$err=$wpdb->last_error;
        return "La Firma alla Circolare Num. ".wps_GetNumeroCircolare($IDCircolare)." non &egrave; stata Rimossa (msg: ".$err.")";
	}else{
		$MittenteNotifica= get_option('Circolari_From_NotificaFirma');
		$OggettoNotifica= get_option('Circolari_Oggetto_NotificaFirma');
		$MessaggioNotifica=get_option('Circolari_Messaggio_NotificaFirma');
		$MsgFirma=$wps_TestiRisposte[$firma->adesione]->get_RispostaMail();
		$MsgFirmaLog=$wps_TestiRisposte[$firma->adesione]->get_Risposta();
		$StatoEmail=__("Email non inviata", 'wpscuola');
		if(get_option('Circolari_NotificaFirma')=="Si"){
			$DatiUtente=$current_user->display_name;
			$Data=date("d/m/Y");
//			echo "Utente ".$DatiUtente." Data ".$Data." ".get_permalink($IDCircolare);die();
			$MessaggioNotifica= str_replace("{Dati_Utente}", $DatiUtente, $MessaggioNotifica);
			$MessaggioNotifica= str_replace("{Data}", $Data, $MessaggioNotifica);
			$MessaggioNotifica= str_replace("{Link_Circolare}", get_permalink($IDCircolare), $MessaggioNotifica);
			$MessaggioNotifica= str_replace("{Operazione}", "rimuovere ".$MsgFirma, $MessaggioNotifica); 
			if (wp_mail($current_user->user_email,$OggettoNotifica,$MessaggioNotifica,"From: ".$MittenteNotifica)){
				$StatoEmail=__("Email inviata correttamente", 'wpscuola');
			}else{
				$StatoEmail=__("ERRORE Email non inviata", 'wpscuola');
			}
		}
		wps_ScriviLog($IDCircolare,$current_user->ID,"UnSign",$MsgFirmaLog);
		return sprintf(__("La Firma alla Circolare Num. %s è correttamente rimossa", 'wpscuola'),wps_GetNumeroCircolare($IDCircolare))."<br />".$StatoEmail;
	}	
}
function wps_FirmaCircolare($IDCircolare,$Pv=-1){
	global $wpdb;
	wps_MakeDir();
	if(FALSE!==($wps_TestiRisposte=get_option('Circolari_TestiRisposte'))){
    	$wps_TestiRisposte= unserialize($wps_TestiRisposte);
    }else{
		$wps_TestiRisposte=array();
	}
	$current_user =wp_get_current_user();
	$wpdb->hide_errors();
	if ( false === $wpdb->insert(
		$wpdb->table_circolari_firme ,array(
				'post_ID' => $IDCircolare,
				'user_ID' => $current_user->ID,
				'ip' => $_SERVER['REMOTE_ADDR'],
				'adesione' => $Pv))){
 //echo "Sql==".$wpdb->last_query ."    Ultimo errore==".$wpdb->last_error;
		$err=$wpdb->last_error;
		$wpdb->show_errors();
        return "La Circolare Num. ".wps_GetNumeroCircolare($IDCircolare)." &egrave; gi&agrave; stata Firmata (msg: ".$err.")";
	}else{
		$MittenteNotifica= get_option('Circolari_From_NotificaFirma');
		$OggettoNotifica= get_option('Circolari_Oggetto_NotificaFirma');
		$MessaggioNotifica=get_option('Circolari_Messaggio_NotificaFirma');
		$MsgFirma=$wps_TestiRisposte[$Pv]->get_RispostaMail();
		$MsgFirmaLog=$wps_TestiRisposte[$Pv]->get_Risposta();
		$StatoEmail="";
		if(get_option('Circolari_NotificaFirma')=="Si"){
			$DatiUtente=$current_user->display_name;
			$Data=wps_Get_DataOra_Firma($IDCircolare);
//			echo "Utente ".$DatiUtente." Data ".$Data." ".get_permalink($IDCircolare);die();
			$MessaggioNotifica= str_replace("{Dati_Utente}", $DatiUtente, $MessaggioNotifica);
			$MessaggioNotifica= str_replace("{Data}", $Data, $MessaggioNotifica);
			$MessaggioNotifica= str_replace("{Link_Circolare}", get_permalink($IDCircolare), $MessaggioNotifica);
			$MessaggioNotifica= str_replace("{Operazione}", $MsgFirma, $MessaggioNotifica); 
			if (wp_mail($current_user->user_email,$OggettoNotifica,$MessaggioNotifica,"From: ".$MittenteNotifica)){
				$StatoEmail=__("Email inviata correttamente", 'wpscuola');
			}else{
				$StatoEmail=__("ERRORE Email non inviata", 'wpscuola');
			}
		}
		wps_ScriviLog($IDCircolare,$current_user->ID,"Sign",$MsgFirmaLog);
		$wpdb->show_errors();
		return sprintf(__("Circolare Num. %s Firmata correttamente", 'wpscuola'),wps_GetNumeroCircolare($IDCircolare))."<br />".$StatoEmail;
	}
}
function wps_Get_User_Per_Gruppo($IdGruppo){
	global $wpdb;
	if ($IdGruppo==get_option('Circolari_Visibilita_Pubblica'))
		return 	$wpdb->get_var("Select count(*) FROM $wpdb->users");
	else
		return $wpdb->get_var($wpdb->prepare(
					"Select count(*) FROM $wpdb->usermeta WHERE meta_key='gruppo' AND meta_value like %s",
					"%".$IdGruppo."%"));
}

function wps_Get_Numero_Firme_Per_Circolare($IDCircolare){
	global $wpdb;
	return $wpdb->get_var($wpdb->prepare(
			"Select count(*) FROM $wpdb->table_circolari_firme WHERE post_ID=%d",
			$IDCircolare));
}

// Funzione che restituisce True se la risposta con Codice = $ID è codificata, serve per il calcolo del codice univoco in fase creazione di una nuova risposta
function wps_Circolari_is_set_IDRisposte($ID){
	global $wps_TestiRisposte;
	foreach($wps_TestiRisposte as $Risposta){
		if($Risposta->get_IDRisposta()==$ID)
			return TRUE;
	}
	return FALSE;
}
// Funzione che restituisce True se il tipo di circolare  con Codice = $ID è codificato, serve in fase creazione di un nuovo tipo di circolare per verificare se il codice è già presente
function wps_Circolari_is_set_Tipo($ID){
	global $wps_Testi;
	foreach($wps_Testi as $Tipo){
		if($Tipo->get_Tipo()==$ID)
			return TRUE;
	}
	return FALSE;
}
// Funzione che restituisce il codice univoco di una nuova risposta 
function wps_Circolari_Get_New_Numero_Risposta(){
	global $wps_TestiRisposte;
    $num=4;
    while (wps_Circolari_is_set_IDRisposte($num))
    	$num++;
	return $num; 
}
function wps_Circolari_find_Tipo($IDTipo){
	global $wps_Testi;
	if($wps_Testi){
		foreach($wps_Testi as $Tipo){
			if($Tipo->get_Tipo()==$IDTipo)
				return $Tipo;
		}
	}
	return FALSE;
}

function wps_Circolari_find_Index_Tipo($IDTipo){
	global $wps_Testi;
	for($i=1;$i<=count($wps_Testi);$i++){
		$Testo=$wps_Testi[$i];
		if($Testo->get_Tipo()==$IDTipo)
			return $i;
	}
	return FALSE;
}
function wps_Circolari_find_Index_Risposta($IDRisposta){
	global $wps_TestiRisposte;
	for($i=1;$i<=count($wps_TestiRisposte);$i++){
		$Risposta=$wps_TestiRisposte[$i];
		if($Risposta->get_IDRisposta()==$IDRisposta)
			return $i;
	}
	return FALSE;
}

function wps_Circolari_find_Risposta($IDRisposta){
	global $wps_TestiRisposte;
	foreach($wps_TestiRisposte as $Risposta){
		if($Risposta->get_IDRisposta()==$IDRisposta)
			return $Risposta;
	}
	return FALSE;
}

function wps_Circolari_IsUsed_TipoCircolare($Tipo){
	global $wpdb;
	$Sql="SELECT count(*) FROM $wpdb->postmeta WHERE meta_key='_sign' And meta_value= %s";
	$NumCircolari= $wpdb->get_var( $wpdb->prepare( $Sql,$Tipo));
//	echo $wpdb->last_query;
	return $NumCircolari;
}

function wps_Circolari_IsUsed_Risposta($IDRisp){
	global $wps_Testi;
	$NumRiposte=0;
	for($i=1;$i<count($wps_Testi);$i++){
		$Testo=$wps_Testi[$i];
		//print_r($Testo);
		$Risposte=$Testo->get_Risposte();
		//print_r($Risposte);echo $IDRisp;exit;
		if(in_array($IDRisp,$Risposte,TRUE))
			$NumRiposte++;
	}	
	return $NumRiposte;
}