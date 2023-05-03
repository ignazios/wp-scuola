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
  
  
function Statistiche($Titoli,$NumSchede){
  global $Gest_Prenotazioni,$G_Spaces;
	ob_start();
    $Stat="";
//    var_dump($Titoli);var_dump($NumSchede);
	if(isset($Titoli[1]) And strlen($Titoli[1])>0 and $NumSchede==1){?>
	<h3><?php echo $Titoli[1];?></h3>
<?php
	}
	$ElencoPassate=$Gest_Prenotazioni->get_Prenotazioni("<");
    $ElencoOggi=$Gest_Prenotazioni->get_Prenotazioni("=");
	$ElencoProssime=$Gest_Prenotazioni->get_Prenotazioni(">");
	$NumPassate=count($ElencoPassate);
	$NumOggi=count($ElencoOggi);
	$NumProssime=count($ElencoProssime);
/*echo "<pre>Passate";var_dump($ElencoPassate);
echo "Oggi";var_dump($ElencoOggi);
echo "Domani";var_dump($ElencoProssime);
echo "</pre>";
*/
	if($NumPassate==0 And $NumOggi==0 And $NumProssime==0){?>
		<div class="alert alert-info" role="alert">
	      <header class="header">
			  <h3 class="alert-heading"><?php _e( 'Informazioni:', 'wpscuola' ); ?></h3>
			  
		  </header>
		 <p><?php _e( 'Non ci sono prenotazioni registrate', 'wpscuola' ); ?></p>
		</div>
<?php
		return ob_get_clean();
	}?>
<div id="ListePrenotazioni" class="collapse-div collapse-background-active" role="tablist">
	<div class="collapse-header" id="headingIeri">
    	<button data-toggle="collapse" data-target="#Ieri" aria-expanded="false" aria-controls="Ieri">
      		<?php _e( 'Ultime 5 prenotazione passate', 'wpscuola' );?> <span class="badge <?php echo ($NumPassate==0?"badge-primary":"badge-danger");?>"><?php echo ($NumPassate);?></span>
    	</button>
  	</div>
   	<div id="Ieri" class="collapse" role="tabpanel" aria-labelledby="headingIeri" data-parent="#ListePrenotazioni">
    	<div class="collapse-body">
	 		<table class="table table-striped" >
			  <thead>
			    <tr>
			      <th scope="col">Spazio</th>
			      <th scope="col">Data</th>
			      <th scope="col">Ora Inizio</th>
			      <th scope="col">Ora fine</th>
			    </tr>
			  </thead>
			  <tbody>
<?php         foreach ($ElencoPassate as $Elemento) {?>
                    <tr>
                        <td><?php echo $G_Spaces->get_NomeSpazio($Elemento->IdSpazio);?></td>
                        <td><?php echo DataVisualizza($Elemento->DataPrenotazione);?></td>
                        <td><?php echo $Elemento->OraInizio;?></td>
                        <td><?php echo $Elemento->OraFine;?></td>
                	</tr>
<?php         }?>
			  </tbody>
			</table>    		
    	</div>
  	</div>

	<div class="collapse-header" id="headingOggi">
    	<button data-toggle="collapse" data-target="#Oggi" aria-expanded="false" aria-controls="Oggi">
      		<?php _e( 'Prenotazioni di oggi', 'wpscuola' );?> <span class="badge <?php echo ($NumOggi==0?"badge-primary":"badge-danger");?>"><?php echo ($NumOggi);?></span>
    	</button>
  	</div>
   	<div id="Oggi" class="collapse" role="tabpanel" aria-labelledby="headingOggi" data-parent="#ListePrenotazioni">
    	<div class="collapse-body">
	 		<table class="table table-striped">
			  <thead>
			    <tr>
			      <th scope="col">Spazio</th>
			      <th scope="col">Data</th>
			      <th scope="col">Ora Inizio</th>
			      <th scope="col">Ora fine</th>
			    </tr>
			  </thead>
			  <tbody>
<?php         foreach ($ElencoOggi as $Elemento) {?>
                    <tr>
                        <td><?php echo $G_Spaces->get_NomeSpazio($Elemento->IdSpazio);?></td>
                        <td><?php echo DataVisualizza($Elemento->DataPrenotazione);?></td>
                        <td><?php echo $Elemento->OraInizio;?></td>
                        <td><?php echo $Elemento->OraFine;?></td>
                    </tr>
<?php          }?>
			  </tbody>
			</table>    		
    	</div>
  	</div>

	<div class="collapse-header" id="headingDomani">
    	<button data-toggle="collapse" data-target="#Domani" aria-expanded="false" aria-controls="Domani">
      		<?php _e( 'Prossime 5 Prenotazioni', 'wpscuola' );?> <span class="badge <?php echo ($NumProssime==0?"badge-primary":"badge-danger");?>"><?php echo ($NumProssime);?></span>
    	</button>
  	</div>
   	<div id="Domani" class="collapse" role="tabpanel" aria-labelledby="headingDomani" data-parent="#ListePrenotazioni">
    	<div class="collapse-body">
	 		<table class="table table-striped">
			  <thead>
			    <tr>
			      <th scope="col">Spazio</th>
			      <th scope="col">Data</th>
			      <th scope="col">Ora Inizio</th>
			      <th scope="col">Ora fine</th>
			    </tr>
			  </thead>
			  <tbody>
<?php         foreach ($ElencoProssime as $Elemento) {?>
	                    <tr>
	                        <td><?php echo $G_Spaces->get_NomeSpazio($Elemento->IdSpazio);?></td>
	                        <td><?php echo DataVisualizza($Elemento->DataPrenotazione);?></td>
	                        <td><?php echo $Elemento->OraInizio;?></td>
	                        <td><?php echo $Elemento->OraFine;?></td>
	                    </tr>
<?php         }?>
			  </tbody>
			</table>    		
    	</div>
  	</div>
</div>	
<?php  
	return ob_get_clean();          
}

function NuovaPrenotazione($Titoli,$NumSchede){
	global $Gest_Prenotazioni,$G_Spaces;
	$Parametri=array("OraInizio" =>7,
	"OraFine"            => 20,
	"Giorni"             => array(1,1,1,1,1,0,0),
	"ColNonPrenotabile"  =>"#EBEBEB",
	"ColNonDisponibile"  =>"#b6b5b5",
	"ColRiservato"       =>"#FF0000",
	"ColPrenotato"       =>"#0000FF",
	"MaxOrePrenotabili"  => 6,
	"PrenEntro"          => 12,
	"PrenSetPre"         => 0,
	"VisPubDatiPren"	 => 0);
	$P  =  get_option('opt_PrenotazioniParametri');
	if($P!==false)
		$Parametri=unserialize($P);
	ob_start();
    $Stat="";
//    var_dump($Titoli);var_dump($NumSchede);
	if(isset($Titoli[0]) And strlen($Titoli[0])>0 and $NumSchede==1){?>
	<h3><?php echo $Titoli[1];?></h3>
<?php
	} else{?>
		<h3><?php _e('Dati della prenotazione', 'wpscuola');?>:</h3>
<?php }
    $PathImg=Prenotazioni_URL."/img/Info.png";
    $Spazio=$G_Spaces->get_ListaSpazi("SpazioP","SpazioP","");?>	    
	<div id="loading" style="float:left;margin-left:15px;margin-top:15px;">LOADING!</div>
		<form name="Memo_Prenotazioni" action="<?php echo $_SERVER["REQUEST_URI"];?>" method="post">
			<fieldset id="CampiPrenotazioniSpazi" >
			<div class="container-fluid mt-5">
				<div class="row">
					<div class="col-sm col-sm-6 col-md-6 col-lg-4">
						<div class="bootstrap-select-wrapper">
							<label><?php _e('Spazio', 'wpscuola');?>:</label> <?php echo $Spazio;?>
						</div>	
							<img src="<?php echo $G_Spaces->get_Foto();?>" id="imgSpazio" />
					</div>
					<div class="col-sm col-sm-6 col-md-6 col-lg-4">
						<div class="it-datepicker-wrapper">
							<div class="form-group">
								<input class="form-control w-50" id="DataPrenotazione" name="DataPrenotazione" type="date" placeholder="data in formato gg/mm/aaaa" value="<?php echo date("Y-m-d");?>">
								<label for="DataPrenotazione"><?php _e('Data prenotazione', 'wpscuola');?>:</label>
							</div>
						</div>
						<label><?php _e('Ora Inizio', 'wpscuola');?>: <span id="VisOraInizio"></span></label>
						<div id="InizioPre">
								<?php echo createTablePrenotazioniSpazio($G_Spaces->get_FirstID());?>
						</div>
					</div>
					<div class="col-sm col-sm-6 col-md-6 col-lg-4">
						<div class="form-group" style="margin-bottom: 1rem;">
							<label for="NumOrePren" class="active" style="width: auto;display: contents;"><?php _e('N&deg; ore', 'wpscuola');?>: </label>
							<select id="NumOrePren" name="NumOrePren" style="display:inline;">
								<option value="0">----</option>		
							</select>
						</div>
						<div class="form-group">
							<label for="NumSet" class="active" style="width: auto;display: contents;"><?php _e('N&deg; settimane', 'wpscuola');?>: </label>
							<select id="NumSet" name="NumSet"  style="display:inline;">
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
						</div>
						<div class="form-group">
							<textarea name="motivoprenotazione" id="motivoprenotazione" rows="5" style="box-shadow: 0 0 0 1px rgba(0, 0, 0, .2);"></textarea>
							<label for="motivoprenotazione"><?php _e('Descrivere il motivo della prenotazione', 'wpscuola');?></label>
						</div>
					</div>	
				</div>
				<div class="row">
					<div class="mx-auto text-center mt-3" style="width: 200px;">
						<input type="hidden" id="OraInizioPrenotazione" value="" name="OraInizioPrenotazione"/>
						<input type="hidden" id="NumMaxOre" value="<?php echo $Parametri["MaxOrePrenotabili"];?>" name="NumMaxOre"/>
						<input type="hidden" id="_wpnonce" value="<?php echo wp_create_nonce( 'secmemopren' );?>" name="_wpnonce" />
						<button type="submit" class="btn btn-primary" value="Prenota" name="navigazioneGiorni">Prenota</button>
					</div>
				</div>
			</div>			
			</fieldset>
		</form>
<?php
	return ob_get_clean();  
}

if (!is_user_logged_in()){
	echo $G_Spaces->get_ListaSpaziDiv();
}else{
	echo '<div id="dialog-confirm" title="Cancellazione Prenotazione" style="display:none;"></div>';
	if (isset($_POST['navigazioneGiorni']) and $_POST['navigazioneGiorni']=="Prenota"){
//		var_dump($_POST);die();
		if ( !(isset($_POST['_wpnonce']) And wp_verify_nonce( $_POST['_wpnonce'], 'secmemopren' )) ) {
			die( 'Errore di Sicurezza' ); 
		}
		$DataPrenotazione=filter_input(INPUT_POST,"DataPrenotazione");
		$DataPrenotazione=cvtDate($DataPrenotazione);
		$OraInizioPrenotazione=filter_input(INPUT_POST,"OraInizioPrenotazione");
		$NumOrePren=filter_input(INPUT_POST,"NumOrePren");
		$SpazioP=filter_input(INPUT_POST,"SpazioP");
		$NumSet=filter_input(INPUT_POST,"NumSet");
		$Motivoprenotazione=filter_input(INPUT_POST,"motivoprenotazione");
		$ris=$Gest_Prenotazioni->newPrenotazione($DataPrenotazione,$OraInizioPrenotazione,$NumOrePren,$SpazioP,$NumSet,$Motivoprenotazione);
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
?>
<div>
<?php	    if($NumSchede>1){?>
	<ul class="nav nav-tabs" role="tablist" id="CartellePrenotazioni">
<?php   	if($Nuovo){?>
		<li class="nav-item">
			<a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#CartellaP1" data-bs-toggle="tab" role="tab" aria-controls="CartellaP1" aria-selected="true">
				<?php echo ((isset($Titoli[0]) And strlen($Titoli[0]))>0?$Titoli[0]:"Nuova");?>
			</a>
		</li>
<?php		}
	        if($Statistiche){?>
	    <li class="nav-item">
			<a class="nav-link" id="tab2-tab" data-toggle="tab" href="#CartellaP2" data-bs-toggle="tab" role="tab" aria-controls="CartellaP2" aria-selected="false">
				<?php echo ((isset($Titoli[1]) And strlen($Titoli[1]))>0?$Titoli[1]:"Statistiche");?>
			</a>
		</li>
<?php		}            
			if($Spazi){?>
	    <li class="nav-item">
			<a class="nav-link" id="tab3-tab" data-toggle="tab" href="#CartellaP3" data-bs-toggle="tab" role="tab" aria-controls="CartellaP3" aria-selected="false">
				<?php echo ((isset($Titoli[2]) And strlen($Titoli[2]))>0?$Titoli[2]:"Catalogo Spazi");?>
			</a>
		</li>
<?php		}?>
	</ul>
<?php   }?>
	<div class="tab-content" id="myTabContent">
<?php   if($Nuovo){?>
		<div class="tab-pane p-4 fade show active" id="CartellaP1" role="tabpanel" aria-labelledby="CartellaP1-tab">
        	<?php echo NuovaPrenotazione($Titoli,$NumSchede);//$FinPren;?>
		</div>
<?php   }
		if($Statistiche){?>
		<div class="tab-pane p-4 fade" id="CartellaP2" role="tabpanel" aria-labelledby="CartellaP2-tab">
        	<?php echo Statistiche($Titoli,$NumSchede);?>
		</div>
<?php   }     
		if($Spazi){?>
		<div class="tab-pane p-4 fade" id="CartellaP3" role="tabpanel" aria-labelledby="CartellaP3-tab">
<?php      	if(strlen($Titoli[2])>0 and $NumSchede==1){?>
        		<h3><?php echo $Titoli[2];?></h3>
<?php 		}
			echo $G_Spaces->get_ListaSpaziDiv();?>
		</div>
	</div>
</div>			
<?php	}
	}
}
?>