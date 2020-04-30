<?php
/**
 * Gestione FrontEnd.
 * @link       http://www.eduva.org
 * @since      4.3
 *
 * @package    Albo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

function Visualizza_Atto($Parametri){
	ob_start();
	if(isset($_GET["titolo"])){
		$Titolo=$_GET["titolo"];
	}else{
		if (isset($Parametri['titolo'])){
			$Titolo=$Parametri['titolo'];	
		}
	}
	if (isset($Parametri['numero']) And is_numeric($Parametri['numero'])){
		$Numero=$Parametri['numero'];	
	}else{
		if(isset($_GET["numero"]) And is_numeric($_GET["numero"])){
			$Numero=$_GET["numero"];
		}else{
			echo "Parametro Numero Atto non impostato";
			return ob_get_clean();		
		}
	}
	if (isset($Parametri['anno']) And is_numeric($Parametri['anno'])){
		$Anno=$Parametri['anno'];	
	}else{
		if(isset($_GET["anno"]) And is_numeric($_GET["anno"])){
			$Anno=$_GET["anno"];
		}else{
			echo "Parametro Anno Atto non impostato";
			return ob_get_clean();
		}
	}
	$risultato=ap_get_all_atti(0,$Numero,$Anno);
	if(count($risultato)==0){
		echo "Nessun atto trovato con questi parametri";
		return ob_get_clean();
	}
	$risultato=$risultato[0];
	$id=$risultato->IdAtto;
	$risultatocategoria=ap_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$allegati=ap_get_all_allegati_atto($id);
	ap_insert_log(5,5,$id,"Visualizzazione");
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	if($risultato->DataAnnullamento!='0000-00-00')
		$Annullato='<p style="background-color: '.$coloreAnnullati.';text-align:center;font-size:1.5em;">Atto Annullato dal Responsabile del Procedimento<br /><br />Motivo: <span style="font-size:1;font-style: italic;">'.stripslashes($risultato->MotivoAnnullamento).'</span></p>';
	else
		$Annullato='';
	$Stato="Scaduto";
	if ($risultato->DataFine>date("Y-m-d"))
		$Stato="In corso di Validità";
?>
<section  id="DatiAtto">
	<div class="container clearfix mb-3 pb-3">
		<h2 class="u-text-h2 pt-3 pl-2"><?php echo $Titolo;?></h2>
		<?php echo ($Annullato?"<h3>'.$Annullato.'</h3>":"");?>
	   	<div class="row">
	   		<div class="col-12 col-xl-8">
				<table class="table table-striped table-hove">
				    <tbody id="dati-atto">
				    <tr>
				    	<th class="w-25 text-right">Stato Atto</th>
				    	<td class="align-middle"><?php echo $Stato;?></td>
				    </tr>
					<tr>
						<th class="w-25 text-right">Ente titolare dell'Atto</th>
						<td class="align-middle"><?php echo stripslashes(ap_get_ente($risultato->Ente)->Nome);?></td>
					</tr>
					<tr>
						<th class="w-25 text-right">Numero Albo</th>
						<td class="align-middle"><?php echo $risultato->Numero."/".$risultato->Anno;?></td>
					</tr>
					<tr>
						<th class="w-25 text-right">Codice di Riferimento</th>
						<td class="align-middle"><?php echo stripslashes($risultato->Riferimento);?></td>
					</tr>
					<tr>
						<th class="w-25 text-right">Oggetto</th>
						<td class="align-middle"><?php echo stripslashes($risultato->Oggetto);?></td>
					</tr>
					<tr>
						<th class="w-25 text-right">Data inizio Pubblicazione</th>
						<td class="align-middle"><?php echo ap_VisualizzaData($risultato->DataInizio);?></td>
					</tr>
					<tr>
						<th class="w-25 text-right">Data fine Pubblicazione</th>
						<td class="align-middle"><?php echo ap_VisualizzaData($risultato->DataFine)?></td>
					</tr>
					<tr>
						<th class="w-25 text-right">Data oblio</th>
						<td class="align-middle"><?php echo ap_VisualizzaData($risultato->DataOblio);?></td>
					</tr>
					<tr>
						<th class="w-25 text-right">Note</th>
						<td class="align-middle"><?php echo stripslashes($risultato->Informazioni);?></td>
					</tr>
					<tr>
						<th class="w-25 text-right">Categoria</th>
						<td class="align-middle"><?php echo stripslashes($risultatocategoria->Nome)?></td>
					</tr>
<?php
$MetaDati=ap_get_meta_atto($id);
if($MetaDati!==FALSE){
	$Meta="";
	foreach($MetaDati as $Metadato){
		$Meta.="{".$Metadato->Meta."=".$Metadato->Value."} - ";
	}
	$Meta=substr($Meta,0,-3);?>
					<h2 class="u-text-h2 pt-3 pl-2"><?php echo $Titolo;?></h2>
					<tr>
						<th>Meta Dati</th>
						<td style="vertical-align: middle;"><?php echo $Meta;?></td>
					</tr>
<?php }?>
		 	    </tbody>
			</table>
		</div>
		<div class="col-12 col-xl-4">
<?php 
$Soggetti=unserialize($risultato->Soggetti);
$Soggetti=ap_get_alcuni_soggetti_ruolo(implode(",",$Soggetti));
$Ruolo="";
if($Soggetti){
			echo "<h3 class=\"u-text-h2 pt-3 pl-2\">Soggetti</h3>";
}
foreach($Soggetti as $Soggetto){
	if(ap_get_Funzione_Responsabile($Soggetto->Funzione,"Display")=="No"){
		continue;
	}
	if($Soggetto->Funzione!=$Ruolo){
			$Ruolo=$Soggetto->Funzione;?>
			<div class="callout mycallout">
  				<div class="callout-title"><?php echo ap_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione"); ?></div>
 				<div>
					<?php echo $Soggetto->Cognome." ".$Soggetto->Nome;?><br />
<?php	} 
	if ($Soggetto->Email)
		echo'		<a href="mailto:'.$Soggetto->Email.'">'.$Soggetto->Email.'</a><br />';
	if ($Soggetto->Telefono)
		echo	$Soggetto->Telefono."<br />";
	if ($Soggetto->Orario)
		echo 	$Soggetto->Orario.'<br />';
	if ($Soggetto->Note)
		echo	$Soggetto->Note;
?>
				</div>
			</div>
<?php }?>
		</div>
	</div>
	   	<div class="row">
	   		<div class="col">
			<h3 class="u-text-h2 pt-3 pb-2">Allegati</h3>
<?php
if (strpos(get_permalink(),"?")>0)
	$sep="&amp;";
else
	$sep="?";
$TipidiFiles=ap_get_tipidifiles();
foreach ($allegati as $allegato) {
	$Estensione=ap_ExtensionType($allegato->Allegato);?>
			<div class="callout mycallout">
  				<div class="callout-title"><?php echo strip_tags($allegato->TitoloAllegato); ?></div>
 				<div>
<?php
	if(isset($allegato->TipoFile) and $allegato->TipoFile!="" and ap_isExtensioType($allegato->TipoFile)){
		$Estensione=ap_ExtensionType($allegato->TipoFile);
		echo '<img src="'.$TipidiFiles[$Estensione]['Icona'].'" alt="'.$TipidiFiles[$Estensione]['Descrizione'].'" height="30" width="30"/>';
	}else{
		echo '<img src="'.$TipidiFiles[strtolower($Estensione)]['Icona'].'" alt="'.$TipidiFiles[strtolower($Estensione)]['Descrizione'].'" height="30" width="30"allegato/>';
	}
	if (is_file($allegato->Allegato))
		echo '        <a href="'.ap_DaPath_a_URL($allegato->Allegato).'" class="addstatdw" rel="'.get_permalink().$sep.'action=addstatall&amp;id='.$allegato->IdAllegato.'&amp;idAtto='.$id.'" target="_blank">'. basename( $allegato->Allegato).'</a> ('.ap_Formato_Dimensione_File(filesize($allegato->Allegato)).')<br />'.htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']).' <a href="'.get_permalink().$sep.'action=dwnalle&amp;id='.$allegato->IdAllegato.'&amp;idAtto='.$id.'" >Scarica allegato</a>';		
			else
				echo basename( $allegato->Allegato)." File non trovato, il file &egrave; stato cancellato o spostato!";?>
			</div>
		</div>
<?php	}?>
		</div>
		</div>
	</div>
</section>
<?php
return ob_get_clean();
}
?>