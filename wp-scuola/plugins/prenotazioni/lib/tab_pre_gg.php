<?php
/**
 * Prenotazioni
 * Generazione tabelle prenotazione
 * @package Prenotazioni
 * @author Scimone Ignazio
 * @copyright 2014-2099
 * @version 1.6.6
 */

function createTablePrenotazioni($data="",$visOreDisp="n"){
	global $Gest_Prenotazioni;
	$Parametri=get_Pre_Parametri();
//	var_dump($data);
	if($data=="")
		$data_p=date("d/m/Y");
	else
		$data_p=$data;
	$spazi = get_posts(array('post_type'=> 'spazi','posts_per_page'   => -1));
	$numSpazi=1;
	foreach ( $spazi as $spazio ){
		$StatoPrenotazioni[$numSpazi]=$Gest_Prenotazioni->getPreGioSpa($data_p,$spazio->ID);
		$numSpazi++;
	}
        if(!is_set_pre($data)){
            $data=pren_DateAdd(date("Y-m-d-H",current_time( 'timestamp', 0 ) ),"g",7-date("N"));
        }else{
            $data=pren_DateAdd(date("Y-m-d-H",current_time( 'timestamp', 0 ) ),"o",$Parametri["PrenEntro"]);
        }
		//	echo "<pre>";var_dump($StatoPrenotazioni[3]);echo "</pre>";
	$numSpazi=count($spazi);
//	echo "Num Spazi:".$numSpazi;
//	echo "<pre>";var_dump($StatoPrenotazioni[3]);echo "</pre>";
	$MyID =get_current_user_id();
	$HTML='
	    <div id="tabPrenotazioniSpazi">
	    <input type="hidden" id="NumMaxOre" value="'.$Parametri['MaxOrePrenotabili'].'" />
	    <input type="hidden" id="secur" value="'.wp_create_nonce( 'secmemopren' ).'" />
		<div id="dialog-form" title="Edit User" style="display:none;">  
			<div style="font-size: 12px;font-weight:bold;color: #ff0000;text-align:center;">Dati della Prenotazione</div>
			<div>
				<table>
					<tr>
						<td style="font-size: 12px;font-weight:bold;text-align:right;">Data:</td>
						<td><span id="dataPre"></span></td>
					</tr>
					<tr>
						<td style="font-size: 12px;font-weight:bold;text-align:right;">Ora Inizio:</td>
						<td><span id="InizioPre"></span></td>
					</tr>
					<tr>
						<td style="font-size: 12px;font-weight:bold;text-align:right;">Spazio:</td>
						<td><span id="SpazioPre"></span></td>
					</tr>
				</table>
			</div>
		          <form>
		            <fieldset>
		            	<legend>Dati della prenotazione:</legend>
		                <label>Numero Ore &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp:</label><select id="NumOrePren"></select><br />
		                <label>Numero Settimane:</label><input type="number" min="1" max="20" id="NumeroSettimane" value="1"></select><br />
		                <label>Motivo Prenotazione</label><textarea rows="4" cols="40" id="notePrenotazione"></textarea>
		            </fieldset>
		        </form>
		</div>
		<div id="dialog-confirm" title="Cancellazione Prenotazione" style="display:none;"></div> 
		<div id="dialog-infonew" title="Prenotazioni Memorizzate" style="display:none;"></div> 
		<div id="dialog-help" title="Informazioni di utilizzo" style="display:none;">
			<ul>
				<li>Per <span style="font-weight:bold;color: #ff0000;">Cancellare</span> una prenotazione bisogna posizionarsi sul giorno della prenotazione attraverso il sitema di scorrimento o selezionando la data dal calendario e cliccare sull\'icona <span class="DatiPrenRev dashicons dashicons-trash"></span></li>
				<li>Per <span style="font-weight:bold;color: #ff0000;">inserire</span> una nuova prenotazione bisogna cliccare sulla cella della prima ora di prenotazione, al rilascio si aprir&agrave; una finestra nella quale bisogna inserire il numero delle ore da prenotare, il motivo della prenotazione e confermare</li>
				<li>Per <span style="font-weight:bold;color: #ff0000;">Visualizzare le Informazioni</span> di una prenotazione bisogna cliccare sull\'icona:<ul><li>
				 <span class="DatiPrenRev dashicons dashicons-info"></span> per le informazioni generali</li><li><span class="DatiPrenRev dashicons dashicons-universal-access-alt"></span> per le informazioni dell\'utente che ha prenotato</li></ul>
				 </li>
				<li>Per <span style="font-weight:bold;color: #ff0000;">Visualizzare Informazioni di uno spazio</span> basta posizionare il mouse sul nome dello spazio presente ulla prima riga della tabella</li>
			</ul>
		</div>	 
		<table class="settimanale" id="selectable" style="width:95%;height:450px;" >
 		    <thead>
	          	<tr>
	                <th style="background-color:#00FFCC;width:5%">Ora </th>';
	          	$i=0;
	          	$dimeColonna=95 / $numSpazi;
	          	//echo $numSpazi;exit;
	          	$IdSpazi=array();
	          	foreach ( $spazi as $spazio ){
	          		$IdSpazi[$i+1]=$spazio->ID;
	          		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($spazio->ID), 'medium' );
	                if($i % 2 ==1)
	                	$colore="#00FFCC";
	                else	
	                	$colore="#33CCFF";
	                $HTML.= '
	                <th style="background-color:'.$colore.';width:'.$dimeColonna.'%" abbr="img('.$thumb['0'].')'.$spazio->post_excerpt .'"  id="Spazio_'.$spazio->ID.'">'.$spazio->post_title.'</th>';
					$i++;				
				}
				$HTML.= '
	          </tr>
 	    </thead>
	    <tbody>';
	    for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
	    	if($visOreDisp=="n" and ($i<$Parametri['OraInizio'] or $i>$Parametri['OraFine']))
	    		continue;		
   			$HTML.= '          
     		<tr>
                <th style="background-color:#00FFCC">'.$i.'</th>';
  			for ($ns=1;$ns<=$numSpazi;$ns++){
//				echo $ns."<br>";
	            $D1=explode("/",$data_p);
	            if($i<10)
	            	$Hore="0".$i;
	            else
	            	$Hore=$i;
	            $D1=$D1[2]."-".$D1[1]."-".$D1[0]."-".$Hore;
//	            echo $D1."<->".$data."<br />";
//var_dump($StatoPrenotazioni);die();
			if(isset($StatoPrenotazioni[$ns][$i]["ID"]) And $D1>$data)
					$Cancella='
							<div style="display:inline;float:left;cursor: pointer;">
							<span id="'.$StatoPrenotazioni[$ns][$i]["ID"].'" class="DelPren DatiPren dashicons dashicons-trash" ></span>';
								
			else
					$Cancella="";
			if (!current_user_can( 'manage_options' ) And $StatoPrenotazioni[$ns][$i]["IDUser"]!=$MyID){
				$Cancella="";	
			}
            	
/*				if (current_user_can( 'manage_options' ) or $StatoPrenotazioni[$ns][$i]["IDUser"]==$MyID)
                            $Info='abbr="'.str_replace('"',"'",$StatoPrenotazioni[$ns][$i]["Motivo"]).'"';
                    else */
                            $Info="";
//							echo $StatoPrenotazioni[$ns][$i]['Impegno']."<br >";
		    	switch ($StatoPrenotazioni[$ns][$i]['Impegno']){
					case 2:
						${'bg'.$ns}='background-color:'.$Parametri['ColPrenotato'].';';
						break;
					case 1:
						${'bg'.$ns}='background-color:'.$Parametri['ColRiservato'].';';
						break;
					case 3:
						${'bg'.$ns}='background-color:'.$Parametri['ColNonDisponibile'].';';
						$selctable=' ui-widget-content';
						break;
					default:
						${'bg'.$ns}='background-color:#FFFFFF;';
						break;				
				}
				//'.${'bg'.$ns}.'	
				//echo strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " +1 day");
//				echo "    ".$StatoPrenotazioni[$ns][$i]['Impegno']."<br>";
				if($StatoPrenotazioni[$ns][$i]['OreCons']==1 and $StatoPrenotazioni[$ns][$i]['Impegno']!=2){
					$appo = explode ('/',$data_p);
					$dataOC=mktime($i,0,0,$appo[1],$appo[0],$appo[2]);				
					if ($dataOC<pren_cvdate($data))
						$classe="style='background-color:".$Parametri['ColNonPrenotabile']."'";
					else	
						if ($StatoPrenotazioni[$ns][$i]['Impegno']==0) 
							$classe="class='adminpreStyle' style='".${'bg'.$ns}."'";
						else
							$classe="style='".${'bg'.$ns}."'";	
//					echo $i.'-0'.$IdSpazi[$ns]." - ".$classe."<br>";					
					$HTML.= '
					<td id="'.$i.'-0'.$IdSpazi[$ns].'" '.$classe.'>
					</td>';
				}
				elseif($StatoPrenotazioni[$ns][$i]['Impegno']==2){
					$HTML.= '
					<td id="'.$i.'-0'.$IdSpazi[$ns].'" class="adminpre" '.$Info.' style="'.${'bg'.$ns}.'">';
					if (current_user_can( 'manage_options' ) or $StatoPrenotazioni[$ns][$i]["IDUser"]==$MyID or $Parametri["VisPubDatiPren"]==1)
						$HTML.= '
							<div style="display:inline;float:left;cursor: pointer;">
							<span class="UserPren DatiPren dashicons dashicons-universal-access-alt" abr="Prenotazione effettuata da: '.$StatoPrenotazioni[$ns][$i]["Motivo"].'"></span>
							</div>
							<div style="display:inline;float:left;cursor: pointer;">
								<span  id="InfoPren'.$StatoPrenotazioni[$ns][$i]["ID"].'" class="InfoPren DatiPren dashicons dashicons-info" abr="Prenotazione effettuata il: '.$StatoPrenotazioni[$ns][$i]["DataPren"].' <br />da: '.$StatoPrenotazioni[$ns][$i]["Motivo"].' <br />Note: '.str_replace('"',"'",$StatoPrenotazioni[$ns][$i]["Note"]).'"></span>
							</div>'.$Cancella;
					$HTML.='
						</div>
					</td>';
				}else{
					//if($StatoPrenotazioni[$ns][$i]['OreCons']==1)
						$HTML.= '<td id="'.$i.'-0'.$IdSpazi[$ns].'" style="'.${'bg'.$ns}.'"></td>';
				}					
			}
			$HTML.= '
			</tr>';
		}
	 $HTML.= '
	    </tbody>
	   </table>
	  </div> 
	  ';
	return $HTML;
}

function createTablePrenotazioniSpazio($IDSpazio=0,$data=""){
	global $Gest_Prenotazioni;
	$Parametri=get_Pre_Parametri();
	if($data=="")
		$data_p=date("d/m/Y");
	else
		$data_p=cvtDate($data);
        if(!is_set_pre($data)){
            $data=pren_DateAdd(date("Y-m-d-H",current_time( 'timestamp', 0 ) ),"g",7-date("N"));
        }else{
            $data=pren_DateAdd(date("Y-m-d-H",current_time( 'timestamp', 0 ) ),"o",$Parametri["PrenEntro"]);
        }
	$StatoPrenotazioni=$Gest_Prenotazioni->getPreGioSpa($data_p,$IDSpazio);
	$HTML= '
 		<input type="hidden" id="OldSel" value="" />
		<table class="table table-striped" id="selectable">
 		    <thead>
	          	<tr>
	                <th class="bg-primary" style="width:25%;border-right: 2px solid #d6dce3;">Ora</th>';
	                	$colore="#33CCFF";
	                $HTML.= '
	                <th class="bg-primary" style="width:70%"">Occupazione</th>
 	           </tr>
 	    </thead>
	    <tbody>';
	    for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
/*	    	if($i<$Parametri['OraInizio'] or $i>$Parametri['OraFine'])
	    		continue;		*/
//	    		var_dump($StatoPrenotazioni);
			switch ($StatoPrenotazioni[$i]['Impegno']){
				case "2":
					$colore=$Parametri['ColPrenotato'];
					break;
				case "1":
					$colore=$Parametri['ColRiservato'];
					break;
				case "3":
					$colore=$Parametri['ColNonDisponibile'];
					break;
				case "0":
					$colore="#FFFFFF";
			}
			$HTML.= '          
     		<tr>
                <th  class="bg-primary">'.$i.'</th>';
				$DeletePren="";
                if ($StatoPrenotazioni[$i]['OreCons']>0){
					$appo = explode ('/',$data_p);
					$dataOC=mktime($i,0,0,$appo[1],$appo[0],$appo[2]);		
//                       echo $dataOC." ".pren_cvdate($data)."<br />";		
					if ($dataOC<pren_cvdate($data)){
						$classe="";
						$colore=$Parametri['ColNonPrenotabile'];
					}else	
						$classe="class='adminpreStyle'";
					if($StatoPrenotazioni[$i]['Impegno']=="2"){
						if ($Gest_Prenotazioni->isMyPrenotazione($StatoPrenotazioni[$i]["ID"])){
							$DeletePren='<span  id="InfoPren'.$StatoPrenotazioni[$i]["ID"].'" class="DelPren DatiPren dashicons dashicons-trash" abr="Prenotazione effettuata il: '.$StatoPrenotazioni[$i]["DataPren"].' <br />Note: '.str_replace('"',"'",$StatoPrenotazioni[$i]["Note"]).'"></span>';
						}
					}
					if($StatoPrenotazioni[$i]['Impegno']=="0" And $classe!=""){
//                       	echo "Ci passo ". $classe." - ".$colore." ".$data." ".$data_p."<br />";
							$HTML.= '
							<td id="'.$i.'" '.$classe.' style="background-color:'.$colore.';">
							<button id="adminpreStyle'.$i.'" type="button" class="bottoneprenotazione">Imposta Ora Inizio</button>
							</td>';
					}
					elseif($StatoPrenotazioni[$i]['OreCons']==1){
							$HTML.= '
							<td id="'.$i.'" class="adminpre" style="background-color:'.$colore.'">'.$DeletePren.'</td>';
					}else{
						$HTML.= '
						<td id="'.$i.'" class="adminpre" rowspan="'.$StatoPrenotazioni[$i]['OreCons'].'" style="background-color:'.$colore.'">'.$DeletePren.'</td>';
					}
			}elseif ($StatoPrenotazioni[$i]['OreCons']==0 And $i==$Parametri['OraInizio']){
					$HTML.= '
					<td id="'.$i.'" class="adminpre" rowspan="'.$StatoPrenotazioni[$i]['OreCons'].'" style="background-color:'.$colore.'">'.$DeletePren.'</td>';
				}	
                $HTML.= '
                </tr>';
		}
	 $HTML.= '
	    </tbody>
	   </table>
	  ';
	return $HTML;
}
function getStartAndEndDate($week, $year,$FormatDa,$FormatA){
  $date_string = $year . 'W' . sprintf('%02d', $week);
//  echo $date_string ."  ".date($FormatDa, strtotime($date_string));
  $return[0] = ($FormatDa=="TimeStamp")?strtotime($date_string):date($FormatDa, strtotime($date_string));
  $return[1] = ($FormatA=="TimeStamp")?strtotime($date_string . '7'):date($FormatA, strtotime($date_string . '7'));
//  var_dump($return);
  return $return;
}
function createTablePrenotazioniSpazioSettimana($IDSpazio=0,$settimana=0,$anno=0){
// echo "Ci PAsso".$IDSpazio." ".$settimana." ".$anno;
 	global $Gest_Prenotazioni;
        $Prenotazioni=new Prenotazioni();
        $Spazio= new Spazi();
        $settimana=($settimana==0?date("W"):$settimana);
        $anno=($anno==0?date("Y"):$anno);
 	$riserv=get_post_meta($IDSpazio, "_riservato");
	$RL=unserialize($riserv[0]);
//        $DaData=date("d/m",mktime(0,0,0,1,1+$settimana*7-(6),$anno));
//        $AData=date("d/m Y",mktime(0,0,0,1,1+$settimana*7,$anno));
//echo $settimana." ".$anno;
		$IntervalloDate=getStartAndEndDate($settimana,$anno,"d/m","d/m Y");
		$DaData=$IntervalloDate[0];
		$AData=$IntervalloDate[1];
//echo $DaData." ".$AData;
        $Elenco="<select name=\"settimane\" id=\"SelSettimana\" class=\"SelSettimana\">"; 
        $SetAnnoSuc=($settimana>41?9-(52-$settimana):0);
        $SetAnnoPre=0;
        if($settimana<10){
            $SetAnnoPre=52-$settimana;
            $AnnoPrec=$anno-1;
            for ($Set=$SetAnnoPre;$Set<=52;$Set++){
//                $DD=date("d/m",mktime(0,0,0,1,1+$Set*7-(6),$AnnoPrec));
//                $AD=date("d/m Y",mktime(0,0,0,1,1+$Set*7,$AnnoPrec));
				$IntervalloDate=getStartAndEndDate($Set,$AnnoPrec,"d/m","d/m Y");
				$DD=$IntervalloDate[0];
				$AD=$IntervalloDate[1];
                $Elenco.="<option value=\"$Set\" ".($settimana==$Set?'selected':'').">$Set $DD-$AD";
            }            
        }
        $Inizio=1;$Fine=52;
        if($SetAnnoPre>0){
            $Inizio=1;$Fine=$SetAnnoPre-1;
        }
        if($SetAnnoSuc>0){
            $Inizio=$SetAnnoSuc+1;$Fine=52;//$Fine=53-$SetAnnoSuc;
        }        
//        echo $Inizio." ".$Fine." ".$SetAnnoPre." ".$SetAnnoSuc;
        for ($Set=$Inizio;$Set<=$Fine;$Set++){
//             $DD=date("d/m",mktime(0,0,0,1,1+$Set*7-(6),$anno));
//             $AD=date("d/m Y",mktime(0,0,0,1,1+$Set*7,$anno));
			$IntervalloDate=getStartAndEndDate($Set,$anno,"d/m","d/m Y");
			$DD=$IntervalloDate[0];
			$AD=$IntervalloDate[1];
            $Elenco.="<option value=\"$Set\" ".($settimana==$Set?'selected':'').">$Set $DD-$AD";
         }
       if($SetAnnoSuc>0){
            $AnnoSuc=$anno+1;
            for ($Set=1;$Set<=$SetAnnoSuc;$Set++){
//                $DD=date("d/m",mktime(0,0,0,1,1+$Set*7-(6),$AnnoSuc));
//                $AD=date("d/m Y",mktime(0,0,0,1,1+$Set*7,$AnnoSuc));
				$IntervalloDate=getStartAndEndDate($Set,$AnnoSuc,"d/m","d/m Y");
				$DD=$IntervalloDate[0];
				$AD=$IntervalloDate[1];
                $Elenco.="<option value=\"$Set\" ".($settimana==$Set?'selected':'').">$Set $DD-$AD";
            }            
        }
         
         $Elenco.="</select>";
// Recupero i parametri della gestione        
//        $PO=$Prenotazioni->getPreGioSpa("03/04/2017",$IDSpazio);
//        echo "<pre>";print_r($PO);echo"</pre>";
	$IntervalloDate=getStartAndEndDate($settimana,$anno,"TimeStamp","TimeStamp");
	$Giorni=array();
	for($i=0;$i<=6;$i++){
		$Giorni[]=$IntervalloDate[0];
		$IntervalloDate[0]+=86400;
	}
/*	$DaData=$IntervalloDate[0];
	$AData=$IntervalloDate[1];
*/	$Parametri=get_Pre_Parametri();
/*echo "<pre>";
var_dump($Parametri);
var_dump($RL);
echo "</pre>";
*/for($giorno=1;$giorno<=7;$giorno++){
            for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
                    if(!isset($Parametri['Giorni'][$giorno-1]))
                            $Riservato[$giorno][$i]=3;
                    else
                            if (!isset($RL[$giorno][$i]) or $RL[$giorno][$i]==0)
                                    $Riservato[$giorno][$i]=4;
                            else
                                    $Riservato[$giorno][$i]=1;	
            } 
//            $Data=date("d/m/Y",mktime(0,0,0,1,1+$settimana*7-(7-$giorno),$anno));
			$Data=date("d/m/Y",$Giorni[$giorno-1]);
            $PO=$Prenotazioni->getPreGioSpa($Data,$IDSpazio);
            for($ora=$Parametri['OraInizio'];$ora<=$Parametri['OraFine'];$ora++){
                if($PO[$ora]['Impegno']==2){
                    $Riservato[$giorno][$ora]="<strong>".ucwords($PO[$ora]['Motivo'])."</strong><br />".$PO[$ora]['Note'];
                }
            }
        }	
	echo '
<div align="center" id="TabellaSettimanale">
    <h2>Occupazione '.$DaData."-".$AData.' Spazio: <strong>'.$Spazio->get_NomeSpazio($IDSpazio).'</strong></h2>
    <input type="hidden" id="Spazio" value="'.$IDSpazio.'"/>
    <input type="hidden" id="Anno" value="'.$anno.'"/>
    <div class="noPrint">
    Settimana: '.$Elenco.' 
    <a href="'.admin_url().'edit.php?post_type=spazi&page=Prenotazioni&op=rsettimanale&PreviewPrint=0&event_id='.$IDSpazio.'&settimana='.$settimana.'&anno='.$anno.'" target="_blank">Stampa</a>
        </div>
    <div id="loading"><br />LOADING!</div>
    <table class="settimanale report">
    <thead>
          <tr>
                <th style="background-color:#00FFCC">Ora</th>
                <th style="background-color:#33CCFF">Lun '.date("d/m",$Giorni[0]).'</th>
                <th style="background-color:#00FFCC">Mar '.date("d/m",$Giorni[1]).'</th>
                <th style="background-color:#33CCFF">Mer '.date("d/m",$Giorni[2]).'</th>
                <th style="background-color:#00FFCC">Gio '.date("d/m",$Giorni[3]).'</th>
                <th style="background-color:#33CCFF">Ven '.date("d/m",$Giorni[4]).'</th>
                <th style="background-color:#00FFCC">Sab '.date("d/m",$Giorni[5]).'</th>
                <th style="background-color:#00FFCC">Dom '.date("d/m",$Giorni[6]).'</th>
          </tr>
     </thead>
     <tbody>';
//     echo "<pre>";print_r($Riservato);echo "</pre>";
     for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
     	echo '          <tr>
                <th style="background-color:#00FFCC">'.$i.'</th>';
     	for($g=1;$g<=7;$g++){
            ${'bg'.$g}='style="background-color:#FFFFFF"';
            $Displ="";
            switch ($Riservato[$g][$i]){
                            case 1:
                                    ${'bg'.$g}='style="background-color:'.$Parametri['ColRiservato'].'"';
                                    $Displ="";
                                    break;
                            case 3:
                                    ${'bg'.$g}='style="background-color:'.$Parametri['ColNonDisponibile'].'"';
                                     $Displ="";
                                    break;
                            case 4:
                                      $Displ="";
                                    break;
                            default:
                                    ${'bg'.$g}='style="background-color:#FFFFFF"';
                                    $Displ=$Riservato[$g][$i];
                                    break;				
                    }
                    echo '
            <td class="preset" '.${'bg'.$g}.'>'.stripcslashes($Displ).'</td>';
            }
         echo '
          </tr>';	
	 }
	echo '
        </tbody>
   </table> 
</div>';
}

function createTablePrenotazioniSpazioSettimanaFE($Para,$IDSpazio=0,$settimana=0,$anno=0){
//    echo "Ci PAsso".$IDSpazio." ".$settimana." ".$anno;
    

	if (isset($Para['visibilita']) And (
			($Para['visibilita']=="Visitatori" And is_user_logged_in()) OR 
			($Para['visibilita']=="Utenti" And !is_user_logged_in()))) {
	    return;
	}
	$IntervalloDate=getStartAndEndDate($settimana,$anno,"TimeStamp","TimeStamp");
	$Giorni=array();
	for($i=0;$i<=6;$i++){
		$Giorni[]=$IntervalloDate[0];
		$IntervalloDate[0]+=86400;
	}

	global $Gest_Prenotazioni;
        $Prenotazioni=new Prenotazioni();
        $Spazio= new Spazi();
        $settimana=($settimana==0?date("W"):$settimana);
        $SetElenco=date("W");
        $anno=($anno==0?date("Y"):$anno);
 	$riserv=get_post_meta($IDSpazio, "_riservato");
	$RL=unserialize($riserv[0]);
//        $DaData=date("d/m",mktime(0,0,0,1,1+$settimana*7-(6),$anno));
//        $AData=date("d/m Y",mktime(0,0,0,1,1+$settimana*7,$anno));
		$IntervalloDate=getStartAndEndDate($settimana,$anno,"d/m","d/m Y");
		$DaData=$IntervalloDate[0];
		$AData=$IntervalloDate[1];
//echo $settimana." - ".$anno;
        $Elenco="<select name=\"settimane\" id=\"SelSettimana\" class=\"SelSettimana\">"; 
        for ($Set=0;$Set<=7;$Set++){
            if (($Set+$SetElenco)>52){
                $SetElenco=1;
            }
//             $DD=date("d/m",mktime(0,0,0,1,1+($Set+$SetElenco)*7-(6),$anno));
//             $AD=date("d/m Y",mktime(0,0,0,1,1+($Set+$SetElenco)*7,$anno));
			$IntervalloDate=getStartAndEndDate($Set+$SetElenco,$anno,"d/m","d/m Y");
			$DD=$IntervalloDate[0];
			$AD=$IntervalloDate[1];
             $NumSett=$Set+$SetElenco;
             $Elenco.="<option value=\"$NumSett;$anno\" ".($settimana==$NumSett?'selected':'').">($NumSett) $DD-$AD";
         }
         $Elenco.="</select>";

         $Parametri=get_Pre_Parametri();
//        var_dump($Parametri);var_dump($RL);
 	for($giorno=1;$giorno<=7;$giorno++){
            for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
                    if($i<$Parametri['OraInizio'] or $i>$Parametri['OraFine'] or $Parametri['Giorni'][$giorno-1]==0)
                            $Riservato[$giorno][$i]=3;
                    else
                            if (!isset($RL[$giorno][$i]) or $RL[$giorno][$i]==0)
                                    $Riservato[$giorno][$i]=4;
                            else
                                    $Riservato[$giorno][$i]=1;	
            } 
//            $Data=date("d/m/Y",mktime(0,0,0,1,1+$settimana*7-(7-$giorno),$anno));
			$Data=date("d/m/Y",$Giorni[$giorno-1]);
            $PO=$Prenotazioni->getPreGioSpa($Data,$IDSpazio);
//            var_dump($PO);
            for($ora=$Parametri['OraInizio'];$ora<=$Parametri['OraFine'];$ora++){
                if($PO[$ora]["Impegno"]==2){
                    $Riservato[$giorno][$ora]="<strong>".ucwords($PO[$ora]["Motivo"])."</strong><br />".$PO[$ora]["Note"];
                }
            }
        }	
//        print_r($Riservato);
        $ColoreOccupato=($Para['coloreoccupato']=='def'?$Parametri['ColNonDisponibile']:$Para['coloreoccupato']);
	echo '
<div align="center" id="TabellaSettimanale">
    <h2 style="margin-left:40px;">'.$Para['titolo'].'</h2>';
   echo ($Para['didascalia']=="si"?'<span style="background-color:'.$ColoreOccupato.';">&nbsp;&nbsp;&nbsp;&nbsp;</span> Non disponibile':"");
   echo '<h3>Occupazione '.$DaData."-".$AData.' <br />'.$Para['etichetta_spazio'].': <strong>'.$Spazio->get_NomeSpazio($IDSpazio).'</strong></h3>
   <script type="text/javascript">
        var ajaxurl = "'.admin_url( 'admin-ajax.php' ).'";
        var ajaxsec = "'.wp_create_nonce('publicsecretOccupazioneSettimanale').'";
        var para    = \''.serialize($Para).'\';
    </script>    
    <input type="hidden" id="Spazio" value="'.$IDSpazio.'"/>
    <p>    Settimana: '.$Elenco.'</p>
	<div id="loading"><br />LOADING!</div>
    <table class="settimanale">
    <thead>
          <tr>
                <th style="background-color:#00FFCC">Ora</th>
                <th style="background-color:#33CCFF;text-align:center;">Lun '.date("d/m",$Giorni[0]).'</th>
                <th style="background-color:#00FFCC;text-align:center;">Mar '.date("d/m",$Giorni[1]).'</th>
                <th style="background-color:#33CCFF;text-align:center;">Mer '.date("d/m",$Giorni[2]).'</th>
                <th style="background-color:#00FFCC;text-align:center;">Gio '.date("d/m",$Giorni[3]).'</th>
                <th style="background-color:#33CCFF;text-align:center;">Ven '.date("d/m",$Giorni[4]).'</th>
                <th style="background-color:#00FFCC;text-align:center;">Sab '.date("d/m",$Giorni[5]).'</th>
                <th style="background-color:#00FFCC;text-align:center;">Dom '.date("d/m",$Giorni[6]).'</th>
          </tr>
     </thead>
     <tbody>';
//     echo "<pre>";print_r($Riservato);echo "</pre>";
     for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
     	echo '          <tr>
                <th style="background-color:#00FFCC;text-align:center;">'.$i.'</th>';
     	for($g=1;$g<=7;$g++){
            ${'bg'.$g}='style="background-color:#FFFFFF"';
            $Displ="";
            if($Riservato[$g][$i]==4){
                $Displ="";
            }else{
                ${'bg'.$g}='style="background-color:'.$ColoreOccupato.'"';
                $Displ="";
            }
            echo '
            <td class="preset" '.${'bg'.$g}.'>'.stripcslashes($Displ).'</td>';
            }
         echo '
          </tr>';	
	 }
	echo '
        </tbody>
   </table> 
</div>';
}
?>