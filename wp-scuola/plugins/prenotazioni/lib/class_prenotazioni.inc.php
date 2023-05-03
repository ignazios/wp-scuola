<?php
/**
 * Prenotazioni
 * Classe Prenotazioni
 * @package Prenotazioni
 * @author Scimone Ignazio
 * @copyright 2014-2099
 * @version 1.6.6
 */

class Prenotazioni{

    function __construct(){}
    
    private $Riservato=array("Giorno" => 1,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
                                "Giorno" => 2,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
                                "Giorno" => 3,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
                                "Giorno" => 4,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
                                "Giorno" => 5,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
                                "Giorno" => 6,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
                                "Giorno" => 7,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)
                                );
	private $PrenotazioniGiorno=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);	
	
	function Tabella_Mie_Prenotazioni(){
		global $Gest_Prenotazioni,$G_Spaces;
		$Parametri=get_Pre_Parametri();
		$OraC=$data=pren_DateAdd(date("Y-m-d-H",current_time( 'timestamp', 0 ) ),"o",$Parametri["PrenEntro"]);
		$StatPre="
		<table class=\"TabellaFE\">
	 		<thead>
		    	<tr>
		        	<th style='width:60%;'>Spazio</th>
		        	<th style='width:20%;'>Data</th>
		        	<th style='width:10%;'>Ora Inizio</th>
		        	<th style='width:10%;'>Ora Fine</th>
		        </tr>
		     </thead>
		     <tbody>";
		$Elenco=$Gest_Prenotazioni->get_Prenotazioni("<",-1,"Desc");
		if (count($Elenco)>0){
			foreach ($Elenco as $Elemento) {
				$StatPre.='
			    	<tr>
			        	<td><img src="'.$G_Spaces->get_Foto_By_ID($Elemento->IdSpazio).'" style="width: 75px;height= 75px;margin-right:10px;" class="alignleft"/><h4>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</h4></td>
			        	<td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
			        	<td>'.$Elemento->OraInizio.'</td>
			        	<td>'.$Elemento->OraFine.'</td>
			        </tr>';
			}
			$StatPre.= "
					</tbody>
				</table>";
		}else{
			$StatPre='<p style="margin-left:50px;margin-top:50px;font-style: italic;font-weight: bold;color: #F00000;">Non ci sono prenotazioni presenti in questa cartella</p>';			
		}
			$StatCor="
			<table class=\"TabellaFE\">
		 		<thead>
			    	<tr>
			        	<th style='width:50%;'>Spazio</th>
			        	<th style='width:20%;'>Data</th>
			        	<th style='width:10%;'>Ora Inizio</th>
			        	<th style='width:10%;'>Ora Fine</th>
			        	<th style='width:10%;'>Operazioni</th>
			        </tr>
			     </thead>
			     <tbody>";
		$Elenco=$Gest_Prenotazioni->get_Prenotazioni("=",-1);
		if (count($Elenco)>0){
			foreach ($Elenco as $Elemento) {
				$D1=$Elemento->DataPrenotazione."-".$Elemento->OraInizio;
				$StatCor.= '
			    	<tr>
			        	<td>'.$OraC."-".$D1.'<img src="'.$G_Spaces->get_Foto_By_ID($Elemento->IdSpazio).'" style="width: 75px;height= 75px;margin-right:10px;" class="alignleft"/><h4>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</h4></td>
			        	<td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
			        	<td>'.$Elemento->OraInizio.'</td>
			        	<td>'.$Elemento->OraFine.'</td>';
		        if($OraC<$D1)
		        	$StatCor.= '
		        	<td><img src="'.Prenotazioni_URL.'img/del.png" alt="Icona cancella prenotazione" class="CancMiaPren" id="'.$Elemento->IdPrenotazione.'"/></td>';
		        else
		        	$StatCor.= '<td></td>';
			    $StatCor.= '
			        </tr>';
			}
			$StatCor.= "
				</tbody>
			</table>";
		}else{
			$StatCor='<p style="margin-left:50px;margin-top:50px;font-style: italic;font-weight: bold;color: #F00000;">Non ci sono prenotazioni presenti in questa cartella</p>';			
		}
		$StatFut="
		<table class=\"TabellaFE\">	
	 		<thead>
		    	<tr>
		        	<th style='width:50%;'>Spazio</th>
		        	<th style='width:20%;'>Data</th>
		        	<th style='width:10%;'>Ora Inizio</th>
		        	<th style='width:10%;'>Ora Fine</th>
		        	<th style='width:10%;'>Operazioni</th>
		        </tr>
		     </thead>
		     <tbody>";
		$Elenco=$Gest_Prenotazioni->get_Prenotazioni(">",-1);
		if (count($Elenco)>0){
			foreach ($Elenco as $Elemento) {
				$D1=$Elemento->DataPrenotazione."-".$Elemento->OraInizio;
				$StatFut.= '
			    	<tr>
			        	<td><img src="'.$G_Spaces->get_Foto_By_ID($Elemento->IdSpazio).'" style="width: 75px;height= 75px;margin-right:10px;" class="alignleft"/><h4>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</h4></td>
			        	<td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
			        	<td>'.$Elemento->OraInizio.'</td>
			        	<td>'.$Elemento->OraFine.'</td>';
		        if($OraC<$D1)
		        	$StatFut.= '
		        	<td><img src="'.Prenotazioni_URL.'img/del.png" alt="Icona cancella prenotazione" class="CancMiaPren" id="'.$Elemento->IdPrenotazione.'"/></td>';
		        else
		        	$StatFut.= '<td></td>';
			    $StatFut.= '
			        </tr>';
			}
			$StatFut.= '
				</tbody>
			</table>';
		}else{
			$StatFut='<p style="margin-left:50px;margin-top:50px;font-style: italic;font-weight: bold;color: #F00000;">Non ci sono prenotazioni presenti in questa cartella</p>';			
		}
		echo "
		<div id='dialog-confirm' title='Cancellazione Prenotazione' style='display:none;'></div> 
		<div id='loading'>LOADING!</div>
			<h2>Le mie prenotazioni</h2>
			<div id=\"CartellePrenotazioni\">
				<ul>
					<li><a href=\"#CartellaP1\">Passate</a></li>
					<li><a href=\"#CartellaP2\">Di Oggi</a></li>
					<li><a href=\"#CartellaP3\">Prossime</a></li>
				</ul>
				<div id=\"CartellaP1\">
				       $StatPre
		        </div>
				<div id=\"CartellaP2\">
		              $StatCor
				</div>			
				<div id=\"CartellaP3\">
		              $StatFut
				</div>			
			</div>";
	}	
	function Tabella_Giornaliera_Prenotazioni(){
		$Parametri=get_Pre_Parametri();
		echo '		
		<div style="margin-top: 40px;margin-right: 50px;float: right;width:120px;color: #000;padding:5px;">
			<span style="background-color:'.$Parametri['ColPrenotato'].';">&nbsp;&nbsp;&nbsp;&nbsp;</span> Prenotato<br />
	 		<span style="background-color:'.$Parametri['ColRiservato'].';">&nbsp;&nbsp;&nbsp;&nbsp;</span> Riservato<br />
	 		<span style="background-color:'.$Parametri['ColNonDisponibile'].';">&nbsp;&nbsp;&nbsp;&nbsp;</span> Non disponibile<br />
	 		<span style="background-color:'.$Parametri['ColNonPrenotabile'].';">&nbsp;&nbsp;&nbsp;&nbsp;</span> Prenotazione chiusa<br />
	 	</div>
		<div class="wrap" style="width:99%" >
	  	<h2><span class="dashicons dashicons-calendar-alt"  style="font-size:1.3em;margin-right:0.5em;"></span> Gestione Prenotazioni</h2>
		</div>		
                <div>
 	 		<table>
	 			<tr>
	 				<td><span class="navGiorni dashicons dashicons-controls-back" data-op="<<"></span></td>
	 				<td style="width:70px;text-align: center;"><span id="giornodataCal">'.giornoSettimana(date("d/m/Y"),"l").' </span></td>
	 				<td style="width:70px;text-align: center;"><span id="dataCal">'.date("d/m/Y").'</span>
	 					<input type="hidden" id="dataCalVal" value="" />
	 				</td>
	 				<td><span class="navGiorni dashicons dashicons-controls-forward"></span></td>
	 				<td><input id="preSelDay" type="text" class="calendarioGiorni" style="display:none;"/>
	 				<span class="XX Info dashicons dashicons-calendar"></span>
	 				</td>
	 				<td><span id="HelpPrenotazioni" class="Info dashicons dashicons-info"></span></td>
	 				<td><p style="font-weight: bold;font-size:2em;color:red;">Occupazione giornaliera degli Spazi</p></td>
	 				<td></td>
	 			</tr>
	 		</table>
		</div>
                <div id="loading"><br />LOADING!</div>
		 ';
		 echo createTablePrenotazioni();
	}

	private function GetNumOrePren($Riservato,$giorno,$i,$OraInizio,$OraFine){
//echo $giorno."-".$i."<br />";
		if ($i>=$OraInizio and ($Riservato[$giorno][$i-1]==$Riservato[$giorno][$i]) and ($Riservato[$giorno][$i]!=0))
			return 0;
		if ($Riservato[$giorno][$i]==0)
			return 1;
		$NumCons=1;
		while ($Riservato[$giorno][$i]==$Riservato[$giorno][$i+1] and $i<$OraFine){
			$NumCons++;
			$i++;
		}
//		echo $NumCons." - ";
		return $NumCons;
	}
	
	function get_Prenotazioni($SegnoFiltro="=",$Numero=5,$OrderData="Asc",$OrderOra="Asc"){
		global $wpdb,$table_prefix;
		if ($Numero==-1)
			$Limite="";
		else
			$Limite=" LIMIT 0,".$Numero;
		if (current_user_can( 'manage_options' ))
			$FiltroUtente="";
		else{
			$MyID =get_current_user_id();
			$FiltroUtente=" And IdUtente=\"$MyID\" ";		
		}	
		$Oggi=date('Y-m-d');
		$Sql="SELECT IdSpazio,IdUtente,OraInizio,OraFine,Note,DataPrenotazione,IdPrenotazione FROM $wpdb->table_prenotazioni WHERE DataPrenotazione$SegnoFiltro\"$Oggi\" $FiltroUtente Order By DataPrenotazione $OrderData, OraInizio $OrderOra".$Limite;
		return $wpdb->get_results($Sql);
	}
	
	function getPreGioSpa($data,$IdSpazio){
//		echo "<pre>";var_dump($data);
//		var_dump($IdSpazio);
		global $wpdb,$table_prefix;
		$giornoS=giornoSettimana($data);
		$Riservato=get_post_meta( $IdSpazio, "_riservato",true);
		$Riservato=unserialize($Riservato);
		$Parametri=get_Pre_Parametri();
//		var_dump($giornoS);
//echo "<pre>";var_dump($Riservato);
//		var_dump($Parametri);
//		echo "</pre>";
		if(isset($Parametri['Giorni'][$giornoS-1]) And $Parametri['Giorni'][$giornoS-1]==0){
			for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
				if ($i==$Parametri['OraInizio'])
					$NumO=$Parametri['OraFine']-$Parametri['OraInizio']+1;
				else
					$NumO=0;
				$PrenotazioniGiorno[$i]=array("Impegno"=>1,
											"Motivo"=>"",
											"Note"=>"",
											"OreCons"=>$NumO);
			}
			return $PrenotazioniGiorno;
		}
		for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
			$PrenotazioniGiorno[$i]=array("Impegno"=>$Riservato[$giornoS][$i],
										"Motivo"=>"",
										"Note"=>"",
										"OreCons"=>$this->GetNumOrePren($Riservato,$giornoS,$i,$Parametri['OraInizio'],$Parametri['OraFine']));
		}
//		echo "<pre>";var_dump($PrenotazioniGiorno);echo "</pre>";
/*		echo $IdSpazio." <br />";
		print_r($Riservato[6]);
		echo " <br />";
		print_r($PrenotazioniGiorno);
		echo " <br />";*/
		$pezziData=explode("/",$data);
		$newData=$pezziData[2]."-".$pezziData[1]."-".$pezziData[0];
		$Sql="SELECT IdPrenotazione,IdSpazio,IdUtente,OraInizio,OraFine,Note,Data FROM $wpdb->table_prenotazioni WHERE DataPrenotazione=\"$newData\" and IdSpazio=$IdSpazio Order By OraInizio";
		$Prenotazioni=$wpdb->get_results($Sql);
		foreach($Prenotazioni as $Prenotazione){
//			print_r($Prenotazione);
			$user_info = get_userdata($Prenotazione->IdUtente);
			$numOre=1;
			for($ora=$Prenotazione->OraInizio;$ora<$Prenotazione->OraFine;$ora++){
				if($Prenotazione->OraFine-$Prenotazione->OraInizio>1 and $numOre==1)
					$numOre=$Prenotazione->OraFine-$Prenotazione->OraInizio;
				else
					$numOre=0;
				if($Prenotazione->OraFine-$Prenotazione->OraInizio==1)
					$numOre=1;
				$PrenotazioniGiorno[$ora]=array("ID"=>$Prenotazione->IdPrenotazione,
												"Impegno"=>"2",
											    "Motivo"=>$user_info->display_name,
											    "IDUser"=>$user_info->ID,
										  		"Note"=>$Prenotazione->Note,
										  		"OreCons"=>$numOre,
										  		"DataPren"=>date("d/m/Y H:i"));
				
			}
		}
//		print_r($PrenotazioniGiorno);
		return $PrenotazioniGiorno;
	}
	function delPrenotazione($IdPrenotazione){
		global $wpdb;
	 	$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_prenotazioni WHERE IdPrenotazione=%d",$IdPrenotazione));
 	 	return $wpdb->num_rows;
	}
	function IsPossibilePrenotare($IDSpazio,$Data,$DaOre,$nOre){
		global $wpdb;
		if(strpos($Data,"/")!==FALSE){
			$Data=explode("/",$Data);
			$Data=$Data[2]."-".$Data[1]."-".$Data[0];
		}
		$Sql="SELECT OraInizio,OraFine FROM $wpdb->table_prenotazioni WHERE DataPrenotazione='$Data' And IdSpazio=$IDSpazio Order By OraInizio";
/*		echo $Sql."<br />";
		echo $IDSpazio."  ".$Data." ".$DaOre."  ".$nOre."<br />";die();*/
		$re=$wpdb->get_results($Sql);
		$orep=array();
		foreach($re as $prenotazione){
			for ($i=$prenotazione->OraInizio;$i<$prenotazione->OraFine;$i++)
				$orep[]=$i;
		}
		for($i=$DaOre;$i<$DaOre+$nOre;$i++)
			if(in_array($i,$orep))
				return false;
//		print_r($re);
//		echo "<br />";
//		print_r($orep);
		return true;
	}
/**
*  Metodo che invia la mail dopo aver effettuato una prenotazione
* @param undefined $DestMail email del destinatario (Amministratore o utente)
* @param undefined $User     Dati dell'utente che ha prenotato che verranno utilizzati per rimpiazzare #_PRENUTENTE
* @param undefined $UserMail Email dell'utente che ha prenotato che verrà utilizzata per rimpiazzare #_PRENMAIL
* @param undefined $Spazio   Nome dello spazio prenotato che verrà utilizzato per rimpiazzare #_PRENSPAZIO
* @param undefined $Data     Data/Date nel caso di prenotazione ripetuta in più settimane. Il parametro verrà utilizzato per rinpiazzare #_PRENDATA
* @param undefined $Ora		 Ore della prenotazione che verrà utilizzata per rimpiazzare #_PRENORAE
* @param undefined $Tipo     Destinatario della mail (Amministratore/Utente)
* 
* @return TRUE se la mail è stata inviata altrimenti FALSE
*/
	function sendMail($DestMail,$User,$UserMail,$Spazio,$Data,$Ora,$Note,$Tipo){
	    $M  =  get_option('opt_PrenotazioniMail');
	    if($M!==false){
			$Comunicazioni=unserialize($M); 
		}else{
			return FALSE;
		}
		if($Tipo=='Amministratore' ){
			if($Comunicazioni['OggettoAdmin']==""){
				return FALSE;
			}
			$headers[] = 'From: '.get_option("blogname").' <'.$DestMail.'>';
			$Oggetto=$Comunicazioni['OggettoAdmin'];
			$Corpo=$Comunicazioni['MsgAdmin'];
		}else{
			if($Comunicazioni['OggettoAdmin']==""){
				return FALSE;
			}
			$headers[] = 'From: '.get_option("blogname").' <'.$UserMail.'>';
			$Oggetto=$Comunicazioni['OggettoUte'];
			$Corpo=$Comunicazioni['MsgUte'];			
		}
		$Corpo=str_replace("#_PRENUTENTE",$User,$Corpo);
		$Corpo=str_replace("#_PRENMAIL",$UserMail,$Corpo);
		$Corpo=str_replace("#_PRENSPAZIO",$Spazio,$Corpo);
		$Corpo=str_replace("#_PRENDATA",$Data,$Corpo);
		$Corpo=str_replace("#_PRENORAE",$Ora,$Corpo);
		$Corpo=str_replace("#_PRENNOTE",$Note,$Corpo);
		if(wp_mail( $DestMail,$Oggetto,$Corpo,$headers)){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	function newPrenotazione($Data,$orai,$ore,$IdSpazio,$Nset=1,$note=""){
		global $wpdb;
		if(strpos($Data,"/")!==FALSE){
			$dataS=explode("/",$Data);
			$dataS=$dataS[2]."-".$dataS[1]."-".$dataS[0];
		}else{
			$dataS=$Data;
		}
    	$PrenCre="";
    	$UserID=get_current_user_id();
    	$user_info = get_userdata($UserID);
     	$MsgDate=array();
 //   	echo "ci passo data ".$data." orai ".$orai." n_ore ".$ore." spazio".$IdSpazio." num set".$Nset." note".$note;
		for($i=0;$i<$Nset;$i++){
			$MsgDate[]=$Data;
			if($this->IsPossibilePrenotare($IdSpazio,$Data,$orai,$ore))
			 	if ( false === $wpdb->insert($wpdb->table_prenotazioni,
			 				array('IPAddress' => $_SERVER['REMOTE_ADDR'],
			                      'IdUtente' => $UserID,
			                      'IdSpazio' => $IdSpazio,
			                      'DataPrenotazione' => $dataS,
								  'OraInizio' => $orai,
								  'OraFine' => $orai+$ore,
								  'Note' => $note)))
		 			$PrenCre.="Prenotazione del ".$Data." non è stata creata<br />";
		 		else
		 			$PrenCre.="Prenotazione del ".$Data." è stata creata<br />";
		 	else
		 		$PrenCre.="Prenotazione del ".$Data." non è stata creata perch&egrave; gi&agrave; occupata<br />";
			$dataS=explode("-",$dataS);
			$dataS = date('Y-m-d', strtotime("+1 week",mktime(0, 0, 0, $dataS[1], $dataS[2], $dataS[0])));
	 	}
	 	$MsgDate=implode(" - ",$MsgDate);
        $Utente="(".$UserID.") ".$user_info->last_name." ".$user_info->first_name;
		$OrePren=" Dalle: ".$orai." Alle: ".($orai+$ore);
		$this->sendMail(get_option("admin_email"),$Utente,$user_info->user_email,get_the_title($IdSpazio),$MsgDate,$OrePren,$note,"Amministratore");
		$this->sendMail($user_info->user_email,$Utente,get_option("admin_email"),get_the_title($IdSpazio),$MsgDate,$OrePren,$note,"Utente");
 	 	return $PrenCre;
	}
	function isMyPrenotazione($IDPrenotazione){
		global $wpdb;
		$Sql="Select $wpdb->table_prenotazioni.IdUtente From $wpdb->table_prenotazioni Where $wpdb->table_prenotazioni.IdPrenotazione=%d;";
		$Prenotazioni=$wpdb->get_results($wpdb->prepare($Sql,$IDPrenotazione));
		if(isset($Prenotazioni)){
			if($Prenotazioni[0]->IdUtente==get_current_user_id()){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}
}
?>