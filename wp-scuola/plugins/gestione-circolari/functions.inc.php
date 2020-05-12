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

function MakeDir($Dir="Circolari",$Base="wp-content"){
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
function FormatDataItalianoBreve($Data,$AnnoBreve=FALSE){
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

function NormalData($Data){
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
function FormatDataDB($Data,$incGG=0,$incMM=0,$incAA=0){
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

function circ_MeseLettere($mm){
	$mesi = array('', __("Gennaio", 'wpscuola' ), __("Febbraio", 'wpscuola' ), __("Marzo", 'wpscuola' ), __("Aprile", 'wpscuola' ), __("Maggio", 'wpscuola' ), __("Giugno", 'wpscuola' ), __("Luglio", 'wpscuola' ),  __("Agosto", 'wpscuola' ), __("Settembre", 'wpscuola' ), __("Ottobre", 'wpscuola' ), __("Novembre", 'wpscuola' ),__("Dicembre", 'wpscuola' ));
	return $mesi[$mm];	
}

function circ_GiornoLettere($gg){
	$giorni = array(__("Domenica", 'wpscuola' ),__("Lunedì", 'wpscuola' ),__("Martedì", 'wpscuola' ), __("Mercoledì", 'wpscuola' ), __("Giovedì", 'wpscuola' ), __("Venerdì", 'wpscuola' ),__("Sabato", 'wpscuola' ));
	return $giorni[$gg];
}

function FormatDataItaliano($Data){
	list($anno,$mese,$giorno) = explode('-',substr($Data,0,10)); 
	return $giorno.' '.substr(circ_MeseLettere(intval($mese)),0,3).' '.$anno;
}
function GetNumeroCircolare($PostID){
	$numero=get_post_meta($PostID, "_numero");
	$numero=(isset($numero[0])?$numero[0]:"ND");
	$anno=get_post_meta($PostID, "_anno");
	$anno=(isset($anno[0])?$anno[0]:"ND");
	$NumeroCircolare=$numero.'_'.$anno ;
return $NumeroCircolare;
}
function GetEencoDestinatari($PostID,$Bold=FALSE){
	$fgs = wp_get_object_terms($PostID, 'gruppiutenti');
	$Elenco="";
	if(!empty($fgs)){
		foreach($fgs as $fg){
			$Elenco.=($Bold?"<strong>":"").$fg->name.($Bold?"</strong>":"")." - ";
		}
		$Elenco=substr($Elenco,0,strlen($Elenco)-3);
	}
	return $Elenco;
}
function Is_da_Firmare($IDCircolare){
	$sign=get_post_meta( $IDCircolare, "_sign",true);
	if ($sign!="NoFirma" )
		return TRUE;
	else
		return FALSE;
}
function Is_Circolare_Da_Firmare($IDCircolare,$Tutte=False){
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
	if (Is_Circolare_per_User($IDCircolare) And ($DaFirmare!="NoFirma" And $Scadenza>=$ora))
		return TRUE;
	else
		return FALSE;
}

function Is_Circolare_Scaduta($IDCircolare){
	global $wpdb;
	$ora=date("Y-m-d");
	$current_user =wp_get_current_user();
//	$destinatari=Get_Users_per_Circolare($IDCircolare,"ID");
	$DaFirmare=get_post_meta( $IDCircolare, "_sign",true);
	$Scadenza=get_post_meta( $IDCircolare, "_scadenza",true);		
	if (!$Scadenza){		
		return FALSE;	
	}
	if (Is_Circolare_per_User($IDCircolare) and ($DaFirmare!="NoFirma" And  strtotime($Scadenza)<strtotime($ora)))
		return TRUE;
	else
		return FALSE;
}

function Get_scadenzaCircolare($ID,$TipoRet="Data",$Giorni=False){
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
function Is_Circolare_Firmata($IDCircolare){
	global $wpdb;
	$current_user =wp_get_current_user();
	if (!Is_Circolare_Da_Firmare($IDCircolare,TRUE))
		return FALSE;
	$ris=$wpdb->get_results("SELECT * FROM $wpdb->table_firme_circolari WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;");
/*	echo "SELECT * FROM $wpdb->table_firme_circolari WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;";
	print_r($ris);
echo count($ris);*/
	if (count($ris)==1)
		return TRUE;
	else
		return FALSE;	
}
function Get_Data_Firma($IDCircolare){
	global $wpdb;
	$current_user =wp_get_current_user();
	if (!Is_Circolare_Da_Firmare($IDCircolare,TRUE))
		return "";
	$ris=$wpdb->get_results("SELECT datafirma FROM $wpdb->table_firme_circolari WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;");
/*	echo "SELECT * FROM $wpdb->table_firme_circolari WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;";
	
echo count($ris);*/
	if (count($ris)==1)
		return substr($ris[0]->datafirma,0,10);
	else
		return "";	
}

function Get_DataOra_Firma($IDCircolare){
	global $wpdb;
	$current_user =wp_get_current_user();
	$ris=$wpdb->get_results("SELECT datafirma FROM $wpdb->table_firme_circolari WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;");
//	echo "SELECT * FROM $wpdb->table_firme_circolari WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;";
//  echo count($ris);
	if (count($ris)==1)
		return FormatDataItaliano(substr($ris[0]->datafirma,0,10))." ".substr($ris[0]->datafirma,11,595);
	else
		return "";	
}

function get_Circolare_Adesione($IDCircolare){
	global $wpdb;
	$current_user =wp_get_current_user();
	$ris=$wpdb->get_results("SELECT * FROM $wpdb->table_firme_circolari WHERE post_ID=$IDCircolare AND user_ID=$current_user->ID;");
	if (!empty($ris))
		return $ris[0]->adesione;
	else
		return 0;	
}

function get_Firma_Circolare($IDCircolare,$IDUser=-1){
	global $wpdb;
	if ($IDUser==-1){
		$current_user =wp_get_current_user();
		$IDUser=$current_user->ID;
	}
	$ris=$wpdb->get_results("SELECT datafirma,ip,adesione FROM $wpdb->table_firme_circolari WHERE post_ID=$IDCircolare AND user_ID=$IDUser;");
	if (!empty($ris))
		return $ris[0];
	else
		return FALSE;	
}

function GetAnniCircolari(){
	global $wpdb;

	$Sql="SELECT YEAR(post_date) AS `Anno`
		FROM $wpdb->posts 
		WHERE $wpdb->posts.post_status = 'publish' 
			AND $wpdb->posts.post_type = 'circolari_scuola'
		GROUP BY YEAR(post_date)
		ORDER BY post_date DESC"; 
	return $wpdb->get_results($Sql); 
}

function GetArchivioCircolari($Anno="*"){
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

function GetCircolariFirmate($Tipo="N"){
	global $wpdb,$table_prefix;
	$tabella_firme = $table_prefix . "firme_circolari";
	$current_user =wp_get_current_user();
	$IDUser=$current_user->ID;
	$Sql="SELECT $wpdb->posts.ID,$wpdb->posts.post_title, $tabella_firme.*
		  FROM $wpdb->posts inner join $tabella_firme on
		   ($wpdb->posts.ID = $tabella_firme.post_ID)
		  WHERE 
		        $wpdb->posts.post_type   ='circolari_scuola' 
	        and $wpdb->posts.post_status ='publish'
	        and $tabella_firme.user_ID=$IDUser";
	$ris= $wpdb->get_results($Sql);
	if (empty($ris)){
		if ($Tipo=="N")
			return 0;
		else
			return array();		
	}else{
		if ($Tipo=="N")
			return count($ris);
		else	
			return $ris;		
	}
}

function GetCircolariDaFirmare($Tipo="N"){
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
//    echo $Sql;
	$ris= $wpdb->get_results($Sql);
	if ($Tipo=="N")
		$Circolari=0;
	else
		$Circolari=array();
	foreach($ris as $riga){
		if (Is_Circolare_per_User($riga->ID)){
			if ($Tipo=="N"){
				$Circolari++;
			}
			else
				$Circolari[]=$riga;
		}
	}
	return $Circolari;		
}

function GetCircolariNonFirmate($Tipo="N"){
	global $wpdb,$table_prefix;
	$tabella_firme = $table_prefix . "firme_circolari";
	$current_user =wp_get_current_user();
	$IDUser=$current_user->ID;
	$Oggi=date('Y-m-d');
	$Sql="SELECT $wpdb->posts.ID,$wpdb->posts.post_title
		  FROM $wpdb->posts inner join $wpdb->postmeta on
		   ($wpdb->posts.ID = $wpdb->postmeta.post_id)
		  WHERE ($wpdb->posts.post_type   ='circolari_scuola' and $wpdb->posts.post_status ='publish')
		    and (($wpdb->postmeta.meta_key = '_sign' and $wpdb->postmeta.meta_value <> 'NoFirma')  
            and ($wpdb->posts.ID IN (Select $wpdb->postmeta.post_ID from $wpdb->postmeta Where $wpdb->postmeta.meta_key = '_scadenza' and $wpdb->postmeta.meta_value <'$Oggi'))
	        and ($wpdb->posts.ID NOT IN (Select $tabella_firme.post_ID from $tabella_firme Where $tabella_firme.user_ID=$IDUser))
            GROUP BY ID";
//    echo $Sql;
	$ris= $wpdb->get_results($Sql);
	if ($Tipo=="N")
		$Circolari=0;
	else
		$Circolari=array();
	foreach($ris as $riga){
		if (Is_Circolare_per_User($riga->ID)){
			if ($Tipo=="N"){
				$Circolari++;
			}
			else
				$Circolari[]=$riga;
		}
	}
	return $Circolari;		
}


function Get_Users_per_Circolare($IDCircolare,$Cosa="*"){
$DestTutti=get_option('Circolari_Visibilita_Pubblica');
$dest=wp_get_post_terms( $IDCircolare, 'gruppiutenti', array("fields" => "ids") ); 
$ListaUtenti=get_users();
if (in_array($DestTutti,$dest))
	$Tutti="S";
else
	$Tutti="N";
$UtentiCircolare=array();
foreach($ListaUtenti as $utente){
	if (Is_Circolare_per_User($IDCircolare,$utente->ID) or $Tutti=="S")
		if ($Cosa=="*")
			$UtentiCircolare[]=$utente;
		else
			$UtentiCircolare[]=$utente->ID;
}
return $UtentiCircolare;
}

function get_Circolari_Gruppi(){
	global $wpdb,$table_prefix;
	$Gruppi=array();
	if (get_option('Circolari_UsaGroups')=="si"){
		$Sql="Select group_id, name From ".$table_prefix."groups_group Where group_id>1";
		$Records=$wpdb->get_results($Sql,ARRAY_A);
		foreach( $Records as $Record)
			$Gruppi[]=array("Id"=>$Record["group_id"],
						  "Nome"=>$Record["name"]);
	}else{
		$Records =get_terms('gruppiutenti',array('orderby'=> 'name','hide_empty'=> false));
		foreach( $Records as $Record)
			$Gruppi[]=array("Id"=>$Record->term_id,
					      "Nome"=>$Record->name);
	}
	return $Gruppi;
}

function Is_Circolare_Pubblica($IDCircolare){
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

function Is_Circolare_per_User($IDCircolare,$IDUser=-1){
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

function ScriviLog($PostID,$UserID,$Operazione="Sign",$Espressione="Firma"){
	$data = date("d-m-y"); 
	$ora = date("G:i:s");
	$ip = $_SERVER['REMOTE_ADDR'];
	$testo = $data." ".$ora."|".$ip."|".$PostID."|".$UserID."|".$Operazione."|".$Espressione."\n";
	$var=@fopen(Circolari_Dir_Servizio."/Firme.log","a");
	fwrite($var,$testo);
	fclose($var);
}
function array_msort($array, $cols)
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

function Get_Log_Circolare($PostID){
	$log=array();
	$file=@fopen(Circolari_Dir_Servizio."/Firme.log","r");
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
		$file=@fopen(Circolari_Dir_Servizio."/Firme.log","w");
	}
	$LOG=array_msort($log, array('Utente'=>SORT_ASC, 'Data'=>SORT_DESC));
	fclose($file);
	return $LOG;
}

function RimuoviFirmaCircolare($IDCircolare){
	global $wpdb;
	if(FALSE!==($TestiRisposte=get_option('Circolari_TestiRisposte'))){
    	$TestiRisposte= unserialize($TestiRisposte);
    }else{
		$TestiRisposte=array();
	}
	$current_user =wp_get_current_user();
	$MsgFirmaLog="Errore";
	$firma=get_Firma_Circolare($IDCircolare);
	if ( false === $wpdb->delete(
			$wpdb->table_firme_circolari,array(
				'post_ID' => $IDCircolare,
				'user_ID' => $current_user->ID),array("%d","%d"))){
// echo "Sql==".$wpdb->last_query ."    Ultimo errore==".$wpdb->last_error;
		$err=$wpdb->last_error;
        return "La Firma alla Circolare Num. ".GetNumeroCircolare($IDCircolare)." non &egrave; stata Rimossa (msg: ".$err.")";
	}else{
		$MittenteNotifica= get_option('Circolari_From_NotificaFirma');
		$OggettoNotifica= get_option('Circolari_Oggetto_NotificaFirma');
		$MessaggioNotifica=get_option('Circolari_Messaggio_NotificaFirma');
		$MsgFirma=$TestiRisposte[$firma->adesione]->get_RispostaMail();
		$MsgFirmaLog=$TestiRisposte[$firma->adesione]->get_Risposta();
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
		ScriviLog($IDCircolare,$current_user->ID,"UnSign",$MsgFirmaLog);
		return sprintf(__("La Firma alla Circolare Num. %s è correttamente rimossa", 'wpscuola'),GetNumeroCircolare($IDCircolare))."<br />".$StatoEmail;
	}	
}
function FirmaCircolare($IDCircolare,$Pv=-1){
	global $wpdb;
	MakeDir();
	if(FALSE!==($TestiRisposte=get_option('Circolari_TestiRisposte'))){
    	$TestiRisposte= unserialize($TestiRisposte);
    }else{
		$TestiRisposte=array();
	}
	$current_user =wp_get_current_user();
	if ( false === $wpdb->insert(
		$wpdb->table_firme_circolari ,array(
				'post_ID' => $IDCircolare,
				'user_ID' => $current_user->ID,
				'ip' => $_SERVER['REMOTE_ADDR'],
				'adesione' => $Pv))){
// echo "Sql==".$wpdb->last_query ."    Ultimo errore==".$wpdb->last_error;
		$err=$wpdb->last_error;
        return "La Circolare Num. ".GetNumeroCircolare($IDCircolare)." &egrave; gi&agrave; stata Firmata (msg: ".$err.")";
	}else{
		$MittenteNotifica= get_option('Circolari_From_NotificaFirma');
		$OggettoNotifica= get_option('Circolari_Oggetto_NotificaFirma');
		$MessaggioNotifica=get_option('Circolari_Messaggio_NotificaFirma');
		$MsgFirma=$TestiRisposte[$Pv]->get_RispostaMail();
		$MsgFirmaLog=$TestiRisposte[$Pv]->get_Risposta();
		$StatoEmail="";
		if(get_option('Circolari_NotificaFirma')=="Si"){
			$DatiUtente=$current_user->display_name;
			$Data=Get_DataOra_Firma($IDCircolare);
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
		ScriviLog($IDCircolare,$current_user->ID,"Sign",$MsgFirmaLog);
		return sprintf(__("Circolare Num. %s Firmata correttamente", 'wpscuola'),GetNumeroCircolare($IDCircolare))."<br />".$StatoEmail;
	}
}
function Get_User_Per_Gruppo($IdGruppo){
	global $wpdb;
	if ($IdGruppo==get_option('Circolari_Visibilita_Pubblica'))
		return 	$wpdb->get_var("Select count(*) FROM $wpdb->users");
	else
		return $wpdb->get_var($wpdb->prepare(
					"Select count(*) FROM $wpdb->usermeta WHERE meta_key='gruppo' AND meta_value like %s",
					"%".$IdGruppo."%"));
}

function Get_Numero_Firme_Per_Circolare($IDCircolare){
	global $wpdb;
	return $wpdb->get_var($wpdb->prepare(
			"Select count(*) FROM $wpdb->table_firme_circolari WHERE post_ID=%d",
			$IDCircolare));
}
function Circolari_ElenchiAnniMesi($urlCircolari){

global $wpdb,$table_prefix;
if (strpos($urlCircolari,"?")>0){
	$Sep="&amp;";
}else{
	$Sep="?";
}
$Ritorno="<ul>
";
//echo $tipo."  ".$Categoria."  ".$Anno;
$mesi = array('', __("Gennaio", 'wpscuola' ), __("Febbraio", 'wpscuola' ), __("Marzo", 'wpscuola' ), __("Aprile", 'wpscuola' ), __("Maggio", 'wpscuola' ), __("Giugno", 'wpscuola' ), __("Luglio", 'wpscuola' ),  __("Agosto", 'wpscuola' ), __("Settembre", 'wpscuola' ), __("Ottobre", 'wpscuola' ), __("Novembre", 'wpscuola' ),__("Dicembre", 'wpscuola' ));

	$Sql='SELECT year('.$table_prefix.'posts.post_date) as anno  
		FROM '.$table_prefix.'posts JOIN '.$table_prefix.'term_relationships ON '.$table_prefix.'posts.ID = '.$table_prefix.'term_relationships.object_id
                                    JOIN '.$table_prefix.'term_taxonomy ON '.$table_prefix.'term_taxonomy.term_taxonomy_id = '.$table_prefix.'term_relationships.term_taxonomy_id
		WHERE post_type IN ("post","circolari_scuola") and post_status="publish" 
		group by year('.$table_prefix.'posts.post_date)
		order by year('.$table_prefix.'posts.post_date) DESC;';


	$Anni=$wpdb->get_results($Sql,ARRAY_A );

		foreach( $Anni as $Anno){
			$SqlMese='
SELECT month('.$table_prefix.'posts.post_date) as mese  
FROM '.$table_prefix.'posts JOIN '.$table_prefix.'term_relationships ON '.$table_prefix.'posts.ID = '.$table_prefix.'term_relationships.object_id
                            JOIN '.$table_prefix.'term_taxonomy ON '.$table_prefix.'term_taxonomy.term_taxonomy_id = '.$table_prefix.'term_relationships.term_taxonomy_id
WHERE post_type IN ("post","circolari_scuola") and post_status="publish" 
	and year('.$table_prefix.'posts.post_date)='.$Anno["anno"].' 
group by month('.$table_prefix.'posts.post_date)
order by month('.$table_prefix.'posts.post_date) DESC;';
$Ritorno.= "Anno ".$Anno["anno"].' <select name="archive-anni'.$Anno["anno"].'" onChange=\'document.location.href=this.options[this.selectedIndex].value;\'>
        <option value="">'.__("Seleziona Mese\Anno", 'wpscuola' ).'</option>
        <option value="'.$urlCircolari.$Sep.'Anno='.$Anno["anno"].'">'.$Anno["anno"].'</option>';
			$Mesi=$wpdb->get_results($SqlMese,ARRAY_A );
			foreach( $Mesi as $Mese){
				$Ritorno.='<option value="'.$urlCircolari.$Sep.'Anno='.$Anno["anno"].'&amp;Mese='.$Mese['mese'].'">'.$mesi[$Mese['mese']].'</option>';
			}
		$Ritorno.='</select><br />';
		}
//echo $Sql;	
//$Ritorno.="</ul>";
return $Ritorno;

}

function Circolari_ElencoAnniMesi($urlCircolari){

global $wpdb,$table_prefix;
if (strpos($urlCircolari,"?")>0){
	$Sep="&amp;";
}else{
	$Sep="?";
}
$Ritorno="<label for=\"archivio-anni-mesi\">".__("Archivio", 'wpscuola' )."</label>: <select id=\"archivio-anni-mesi\" name=\"archivio-anni-mesi\" onChange=\"document.location.href=this.options[this.selectedIndex].value;\">";
//echo $tipo."  ".$Categoria."  ".$Anno;
$mesi = array('', __("Gennaio", 'wpscuola' ), __("Febbraio", 'wpscuola' ), __("Marzo", 'wpscuola' ), __("Aprile", 'wpscuola' ), __("Maggio", 'wpscuola' ), __("Giugno", 'wpscuola' ), __("Luglio", 'wpscuola' ),  __("Agosto", 'wpscuola' ), __("Settembre", 'wpscuola' ), __("Ottobre", 'wpscuola' ), __("Novembre", 'wpscuola' ),__("Dicembre", 'wpscuola' ));

	$Sql='SELECT year('.$table_prefix.'posts.post_date) as anno  
		FROM '.$table_prefix.'posts JOIN '.$table_prefix.'term_relationships ON '.$table_prefix.'posts.ID = '.$table_prefix.'term_relationships.object_id
                                    JOIN '.$table_prefix.'term_taxonomy ON '.$table_prefix.'term_taxonomy.term_taxonomy_id = '.$table_prefix.'term_relationships.term_taxonomy_id
		WHERE post_type IN ("post","circolari_scuola") and post_status="publish" 
		group by year('.$table_prefix.'posts.post_date)
		order by year('.$table_prefix.'posts.post_date) DESC;';


	$Anni=$wpdb->get_results($Sql,ARRAY_A );

		foreach( $Anni as $Anno){
			$SqlMese='
SELECT month('.$table_prefix.'posts.post_date) as mese  
FROM '.$table_prefix.'posts JOIN '.$table_prefix.'term_relationships ON '.$table_prefix.'posts.ID = '.$table_prefix.'term_relationships.object_id
                            JOIN '.$table_prefix.'term_taxonomy ON '.$table_prefix.'term_taxonomy.term_taxonomy_id = '.$table_prefix.'term_relationships.term_taxonomy_id
WHERE post_type IN ("post","circolari_scuola") and post_status="publish" 
	and year('.$table_prefix.'posts.post_date)='.$Anno["anno"].' 
group by month('.$table_prefix.'posts.post_date)
order by month('.$table_prefix.'posts.post_date) DESC;';
$Ritorno.= '<option value="'.$urlCircolari.$Sep.'Anno='.$Anno["anno"].'">'.$Anno["anno"].'</option>';
			$Mesi=$wpdb->get_results($SqlMese,ARRAY_A );
			foreach( $Mesi as $Mese){
				$Ritorno.='<option value="'.$urlCircolari.$Sep.'Anno='.$Anno["anno"].'&amp;Mese='.$Mese['mese'].'"> &nbsp;&nbsp;&nbsp;'.$mesi[$Mese['mese']]." ".$Anno["anno"].'</option>';
			}
		}
$Ritorno.='</select><br />';
//echo $Sql;	
//$Ritorno.="</ul>";
return $Ritorno;
}
// Funzione che restituisce il numero totale di risposte codificate
function Circolari_Get_Numero_Risposte(){
	if(FALSE!==($TestiRisposte=get_option('Circolari_TestiRisposte'))){
    	$TestiRisposte= unserialize($TestiRisposte);
    	return count($TestiRisposte);
    }
	else	
		return 0; 
}
// Funzione che restituisce True se la risposta con Codice = $ID è codificata, serve per il calcolo del codice univoco in fase creazione di una nuova risposta
function Circolari_is_set_IDRisposte($ID){
	global $TestiRisposte;
	foreach($TestiRisposte as $Risposta){
		if($Risposta->get_IDRisposta()==$ID)
			return TRUE;
	}
	return FALSE;
}
// Funzione che restituisce True se il tipo di circolare  con Codice = $ID è codificato, serve in fase creazione di un nuovo tipo di circolare per verificare se il codice è già presente
function Circolari_is_set_Tipo($ID){
	global $Testi;
	foreach($Testi as $Tipo){
		if($Tipo->get_Tipo()==$ID)
			return TRUE;
	}
	return FALSE;
}
// Funzione che restituisce il codice univoco di una nuova risposta 
function Circolari_Get_New_Numero_Risposta(){
	global $TestiRisposte;
    $num=4;
    while (Circolari_is_set_IDRisposte($num))
    	$num++;
	return $num; 
}
function Circolari_find_Tipo($IDTipo){
	global $Testi;
	if($Testi){
		foreach($Testi as $Tipo){
			if($Tipo->get_Tipo()==$IDTipo)
				return $Tipo;
		}
	}
	return FALSE;
}

function Circolari_find_Index_Tipo($IDTipo){
	global $Testi;
	for($i=1;$i<=count($Testi);$i++){
		$Testo=$Testi[$i];
		if($Testo->get_Tipo()==$IDTipo)
			return $i;
	}
	return FALSE;
}
function Circolari_find_Index_Risposta($IDRisposta){
	global $TestiRisposte;
	for($i=1;$i<=count($TestiRisposte);$i++){
		$Risposta=$TestiRisposte[$i];
		if($Risposta->get_IDRisposta()==$IDRisposta)
			return $i;
	}
	return FALSE;
}
function Circolari_IS_Update_Firme(){
	global $wpdb,$table_prefix;
	$Sql="SELECT $wpdb->posts.ID
		  FROM $wpdb->posts inner join $wpdb->postmeta on
		   ($wpdb->posts.ID = $wpdb->postmeta.post_id)
		  WHERE ($wpdb->posts.post_type   ='circolari_scuola')
		    and ($wpdb->postmeta.meta_key = '_firma' or $wpdb->postmeta.meta_key = '_sciopero') ";
	$CircoalriOldFirma= count($wpdb->get_results($Sql));
	$Sql="SELECT $wpdb->posts.ID
		  FROM $wpdb->posts left join $wpdb->postmeta on
		   ($wpdb->posts.ID = $wpdb->postmeta.post_id)
		  WHERE ($wpdb->posts.post_type   ='circolari_scuola')
		    and ($wpdb->postmeta.meta_key = '_firma' or $wpdb->postmeta.meta_key = '_sciopero' or $wpdb->postmeta.meta_key = '_sign') group by $wpdb->posts.ID";
	$CircoalriOldNoFirma= count($wpdb->get_results($Sql));
	$PostCircolari=wp_count_posts("circolari_scuola");
	$CountCircolari=$PostCircolari->publish+$PostCircolari->future+$PostCircolari->draft+$PostCircolari->pending+$PostCircolari->private+$PostCircolari->trash;
	if($CircoalriOldFirma>0 or ($CountCircolari-$CircoalriOldNoFirma)>0)
		return True;
	else
		return False;
}
function Circolari_find_Risposta($IDRisposta){
	global $TestiRisposte;
	foreach($TestiRisposte as $Risposta){
		if($Risposta->get_IDRisposta()==$IDRisposta)
			return $Risposta;
	}
	return FALSE;
}

function Circolari_IsUsed_TipoCircolare($Tipo){
	global $wpdb;
	$Sql="SELECT count(*) FROM $wpdb->postmeta WHERE meta_key='_sign' And meta_value= %s";
	$NumCircolari= $wpdb->get_var( $wpdb->prepare( $Sql,$Tipo));
//	echo $wpdb->last_query;
	return $NumCircolari;
}

function Circolari_IsUsed_Risposta($IDRisp){
	global $Testi;
	$NumRiposte=0;
	for($i=1;$i<count($Testi);$i++){
		$Testo=$Testi[$i];
		//print_r($Testo);
		$Risposte=$Testo->get_Risposte();
		//print_r($Risposte);echo $IDRisp;exit;
		if(in_array($IDRisp,$Risposte,TRUE))
			$NumRiposte++;
	}	
	return $NumRiposte;
}