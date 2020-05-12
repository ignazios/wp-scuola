<?php
/**
 * Prenotazioni
 * Codice di gestione della componente Pubblica
 * @package Prenotazioni
 * @author Scimone Ignazio
 * @copyright 2014-2099
 * @version 1.6.6
 **/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
  global $Gest_Prenotazioni,$G_Spaces;

if (!is_user_logged_in()){
    
	echo $G_Spaces->get_ListaSpaziDiv();
}else{
	if (isset($_POST['navigazioneGiorni']) and $_POST['navigazioneGiorni']=="Prenota"){
//		var_dump($_POST);die();
		$ris=$Gest_Prenotazioni->newPrenotazione($_POST['DataPrenotazione'],$_POST['OraInizioPrenotazione'],$_POST['NumOrePren'],$_POST['SpazioP'],$_POST['NumSet'],$_POST['notePrenotazione'],$_POST['_wpnonce']);
		echo '<div id="message" style="border: thin inset;background-color: #FFFACD;">
			<p>Risultato prenotazione:<br />'.$ris.'</p></div>
      		<meta http-equiv="refresh" content="5;url='.get_permalink().'"/>';	
	}else{
                $Nuovo=FALSE;
                $Statistiche=FALSE;
                $Spazi=FALSE;
                $NumSchede=0;
                if(isset($Para['schede'])){
                    $SetSC=explode(",",strtolower($Para['schede']));                 
                }
                else {
                    $SetSC=array("nuovo","statistiche","spazi");
                }
                if(isset($Para['titoli'])){
                    $Titoli=explode(",",$Para['titoli']);                 
                }else {
                    $Titoli=array("Nuovo","Statistiche","Spazi");
                }
                if(in_array("nuovo", $SetSC)){
                    $Nuovo=TRUE;
                    $NumSchede++;
                }
                if(in_array("statistiche", $SetSC)){
                    $Statistiche=TRUE;
                    $NumSchede++;
                }
                if(in_array("spazi", $SetSC)){
                    $Spazi=TRUE;
                    $NumSchede++;
                }
                $Parametri=get_Pre_Parametri();
                if($Statistiche){
                    if(isset($Titoli[1]) And strlen($Titoli[1])>0 and $NumSchede==1)
                        $Stat="<h3>".$Titoli[1]."</h3>";
                    else
                        $Stat="";
                    $Stat.="
                    <strong>Ultime 5 prenotazione passate</strong>
                    <table class=\"TabellaFE\">
                            <thead>
                            <tr>
                                    <th>Spazio</th>
                                    <th>Data</th>
                                    <th>Ora Inizio</th>
                                    <th>Ora Fine</th>
                            </tr>
                         </thead>
                         <tbody>";
                    $Elenco=$Gest_Prenotazioni->get_Prenotazioni("<");
                    foreach ($Elenco as $Elemento) {
                            $Stat.='
                            <tr>
                                    <td>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</td>
                                    <td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
                                    <td>'.$Elemento->OraInizio.'</td>
                                    <td>'.$Elemento->OraFine.'</td>
                            </tr>';
                    }
                    $Stat.= "
                                    </tbody>
                            </table>
                    <strong>Prenotazioni di oggi</strong>
                    <table class=\"TabellaFE\">
                            <thead>
                            <tr>
                                    <th>Spazio</th>
                                    <th>Data</th>
                                    <th>Ora Inizio</th>
                                    <th>Ora Fine</th>
                            </tr>
                         </thead>
                         <tbody>";
                    $Elenco=$Gest_Prenotazioni->get_Prenotazioni("=");
                    foreach ($Elenco as $Elemento) {
                            $Stat.= '
                            <tr>
                                    <td>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</td>
                                    <td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
                                    <td>'.$Elemento->OraInizio.'</td>
                                    <td>'.$Elemento->OraFine.'</td>
                            </tr>';
                    }
                    $Stat.= "
                            </tbody>
                    </table>
                    <strong>Prossime 5 Prenotazioni</strong>
                    <table class=\"TabellaFE\">	
                            <thead>
                            <tr>
                                    <th>Spazio</th>
                                    <th>Data</th>
                                    <th>Ora Inizio</th>
                                    <th>Ora Fine</th>
                            </tr>
                         </thead>
                         <tbody>";
                    $Elenco=$Gest_Prenotazioni->get_Prenotazioni(">");
                    foreach ($Elenco as $Elemento) {
                            $Stat.= '
                            <tr>
                                    <td>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</td>
                                    <td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
                                    <td>'.$Elemento->OraInizio.'</td>
                                    <td>'.$Elemento->OraFine.'</td>
                            </tr>';
                    }
                    $Stat.= "
                            </tbody>
                    </table>";                    
                }
                if($Nuovo){
                    if(isset($Titoli[0]) And strlen($Titoli[0])>0 and $NumSchede==1)
                        $TitFinPren="<h3 style=\"margin-top:5px;\">".$Titoli[0]."</h3>";
                    else
                        $TitFinPren="<h3 style=\"margin-top:5px;\">Dati della prenotazione:</h3>";
                    $PathImg=Prenotazioni_URL."/img/Info.png";
                    echo "";
                    $Spazio=$G_Spaces->get_ListaSpazi("SpazioP","SpazioP","");
                    $FinPren='	    
                    <div id="AreaDatiPrenotazioniSpazi">
                            <img id="SHInfo" src="'.$PathImg.'" title="Clicca per visualizzare la leggenda" style="float:right;border: none;box-shadow: none;margin-top:5px;"/>
                            <div id="info" style="display:none;font-size:0.8em;margin:5px;padding:5px;box-webkit-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
-moz-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);">
                                <em><strong>Per realizzare una prenotazione bisogna completare i dati dall\'alto verso il basso, da sinistra verso destra nel seguente ordine:
                                <ul>
                                    <li>Selezionare lo spazio</li>
                                    <li>Selezionare la Data prenotazione</li>
                                    <li>Nella tabella Ora Inizio cliccare sul pulsante\'Imposta ora inizio\' corrispondenete all\'ora in cui deve iniziare la prenotazione</li>
                                    <li>Selezionare il N° Ore della prenotazione</li>
                                    <li>Se la prenotazione deve essere ripetuta più volte in settimane diverse, bisogna in dicare il N° settimane</li>
                                    <li>Indicare opzionalmente il Motivo Prenotazione</li>
                                    <li>Cliccare su Prenota</li>
                                </ul>
                            </div>
                        <form name="Memo_Prenotazioni"  action="'.$_SERVER["REQUEST_URI"].'" method="post">
                        <fieldset id="CampiPrenotazioniSpazi" style="border:none;">
                           '.$TitFinPren.'
                            <div style="float:left;margin-left:5px;">
                                    <img src="'.$G_Spaces->get_Foto().'" id="imgSpazio" style="border:none;"/>
                            </div>
                            <div style="float:left;margin-left:5px;">
                                <div style="float:left;">
                                    <p>
                                            <label>Spazio:</label> '.$Spazio.'
                                    </p>	
                                    <p>
                                            <label>Data prenotazione:</label>
                                            <input type="text" id="DataPrenotazione" name="DataPrenotazione" style="width: 100px;" value="'.get_pre_Oggi().'">
                                    </p>
                                </div>
                                <div id="loading" style="float:left;margin-left:15px;margin-top:15px;">LOADING!</div>
                            </div>
                            <div style="clear:both;"></div>
                            <div style="float:left;">
                                <label>Ora Inizio: <span id="VisOraInizio"></span></label>
                                <div id="InizioPre">
                                        '.createTablePrenotazioniSpazio($G_Spaces->get_FirstID()).'
                                </div>
                            </div>
                            <div style="float:left;margin-left:20px;">
                                <p>
                                    <p>
                                        <label>N&deg; ore:</label> 
                                        <select id="NumOrePren" name="NumOrePren" style="width:70%;">
                                                <option value="0">----</option>		
                                        </select>
                                    </p>
                                    <p>
                                        <label> N&deg; settimane:</label> 
                                        <select id="NumSet" name="NumSet">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>		
                                        </select>
                                    </p>
                                </p>
                                <p>
                                    <label>Motivo Prenotazione:</label><br />
                                    <textarea rows="8"  cols="32" id="notePrenotazione" style="width:85%;" name="notePrenotazione"></textarea>
                                </p>
                                <p>					
                                    <input type="hidden" id="OraInizioPrenotazione" value="" name="OraInizioPrenotazione"/>
                                    <input type="hidden" id="UrlAjax" value="'.home_url().'/wp-admin/admin-ajax.php" name="UrlAjax"/>
                                    <input type="hidden" id="ColPrenotato" value="'.$Parametri['ColPrenotato'].'" />
                                    <input type="hidden" id="OraInizio" value="'.$Parametri['OraInizio'].'" />
                                    <input type="hidden" id="OraFine" value="'.$Parametri['OraFine'].'" />
                                    <input type="hidden" id="NumMaxOre" value="'.$Parametri['MaxOrePrenotabili'].'" />
                                    <input type="hidden" id="MinOrePrima" value="'.$Parametri['PrenEntro'].'" />
                                    <input type="hidden" id="_wpnonce" value="'.wp_create_nonce( 'secmemopren' ).'" name="_wpnonce" />
                                    <input type="submit" class="navigazioneGiorni" value="Prenota" name="navigazioneGiorni" />
                                </p>
                            </div>				
                        </fieldset>
                    </form>
                    </div>';                    
                }
 		echo "<div id=\"CartellePrenotazioni\">";
                if($NumSchede>1){
                    echo '<ul>';
                    if($Nuovo)          echo "<li><a href=\"#CartellaP1\">".((isset($Titoli[0]) And strlen($Titoli[0]))>0?$Titoli[0]:"Nuova")."</a></li>";
                    if($Statistiche)    echo "<li><a href=\"#CartellaP2\">".((isset($Titoli[1]) And strlen($Titoli[1]))>0?$Titoli[1]:"Statistiche")."</a></li>";
                    if($Spazi)          echo "<li><a href=\"#CartellaP3\">".((isset($Titoli[2]) And strlen($Titoli[2]))>0?$Titoli[2]:"Catalogo Spazi")."</a></li>";   
                    echo "</ul>";
                }
                if($Nuovo){
                    echo "<div id=\"CartellaP1\">
                    $FinPren
		    </div>";
                }
		if($Statistiche){
                    echo "<div id=\"CartellaP2\">
		            $Stat
                    </div>";
                }		     
		if($Spazi){
                    echo "<div id=\"CartellaP3\">";
                    if(strlen($Titoli[2])>0 and $NumSchede==1)
                        echo "<h3>".$Titoli[2]."</h3>";
		    echo $G_Spaces->get_ListaSpaziDiv()."
                    </div>";
                }			
                echo "</div>";			
	}

}
?>