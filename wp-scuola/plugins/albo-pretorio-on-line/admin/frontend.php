<?php
/**
 * Gestione FrontEnd.
 * @link       http://www.eduva.org
 * @since      4.2
 *
 * @package    ALbo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
ob_start();

if(isset($_REQUEST['id']) And !is_numeric($_REQUEST['id'])){
	$_REQUEST['id']=0;
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>ID</span>";
	return;
}
if(isset($_REQUEST['action']) And $_REQUEST['action']!=wp_strip_all_tags($_REQUEST['action'])){
	unset($_REQUEST['action']);
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>Action</span>";
	return;
}
if(isset($_REQUEST['categoria']) And !is_numeric($_REQUEST['categoria'])){
	$_REQUEST['categoria']=0;
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>Categoria</span>";
}
if(isset($_REQUEST['numero']) And $_REQUEST['numero']!="" AND !is_numeric($_REQUEST['numero'])){
	$_REQUEST['numero']="";
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>Numero</span>";
}
if(isset($_REQUEST['anno']) And !is_numeric($_REQUEST['anno'])){
	$_REQUEST['anno']=0;
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>Anno</span>";
}
if(isset($_REQUEST['ente']) And !is_numeric($_REQUEST['ente'])){
	$_REQUEST['ente']="-1";
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>Ente</span>";
}
if(isset($_REQUEST['Pag']) And !is_numeric($_REQUEST['Pag'])){
	$_REQUEST['Pag']=1;
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>Pag</span>";
}
if(isset($_REQUEST['oggetto']) And $_REQUEST['oggetto']!=wp_strip_all_tags($_REQUEST['oggetto'])){
	$_REQUEST['oggetto']="";
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>Oggetto</span>";
}
if(isset($_REQUEST['riferimento']) And $_REQUEST['riferimento']!=wp_strip_all_tags($_REQUEST['riferimento'])){
	$_REQUEST['riferimento']="";
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>Riferimento</span>";
}
if(isset($_REQUEST['DataInizio']) And $_REQUEST['DataInizio']!=wp_strip_all_tags($_REQUEST['DataInizio'])){
	$_REQUEST['DataInizio']="";
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>Da Data</span>";
}
if(isset($_REQUEST['DataFine']) And $_REQUEST['DataFine']!=wp_strip_all_tags($_REQUEST['DataFine'])){
	$_REQUEST['DataFine']="";
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>A Data</span>";
}
if(isset($_REQUEST['filtra']) And $_REQUEST['filtra']!="Filtra"){
	$_REQUEST['filtra']="Filtra";
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>filtra</span>";
}
if(isset($_REQUEST['vf']) And ($_REQUEST['vf']!="s" And $_REQUEST['vf']!="h" And $_REQUEST['vf']!="undefined")){
	$_REQUEST['vf']="undefined";
	echo "<br /><span style='color:red;'>ATTENZIONE:</span> E' stato indicato un VALORE non valido per il parametro <span style='color:red;'>vf</span>";
}
foreach($_REQUEST as $Key => $Val){
	$_REQUEST[$Key]=htmlspecialchars(wp_strip_all_tags($_REQUEST[$Key]));
}

include_once(dirname (__FILE__) .'/frontend_filtro.php');

if(isset($_REQUEST['action'])){
	switch ($_REQUEST['action']){
        case 'printatto':
            if (is_numeric($_REQUEST['id'])) {
                if ($_REQUEST['pdf'] == 'c') {
                    StampaAtto($_REQUEST['id'], 'c');
                } elseif ($_REQUEST['pdf'] == 'a') {
                    StampaAtto($_REQUEST['id'], 'a');
                }
            }else{
				echo "ATTENZIONE:<br />E' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO";
			}
            break;
		case 'visatto':
			if(is_numeric($_REQUEST['id']))
				$ret=VisualizzaAtto($_REQUEST['id']);
			else{
				echo "ATTENZIONE:<br />E' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO";
			}
			break;
		case 'addstatall':
			if(is_numeric($_GET['id']) and is_numeric($_GET['idAtto']))
				ap_insert_log(5,5,(int)$_GET['id'],"Visualizzazione",(int)$_GET['idAtto']);
			break;
		default: 
			if (isset($_REQUEST['filtra'])){
				if(!is_numeric($_REQUEST['categoria']) OR
				   !is_numeric($_REQUEST['numero']) OR
				   !is_numeric($_REQUEST['anno']) OR
				   !is_numeric($_REQUEST['ente'])){
						echo "ATTENZIONE:<br />E' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO";
						break;
				}
			if($_REQUEST['oggetto']!=wp_strip_all_tags($_REQUEST['oggetto'])){
				echo "ATTENZIONE:<br />E' stato indicato un parametro non valido nel campo Oggetto che può rappresentare un ATTACCO INFORMATICO AL SITO";
				break;
			}
			if($_REQUEST['riferimento']!=wp_strip_all_tags($_REQUEST['riferimento'])){
				echo "ATTENZIONE:<br />E' stato indicato un parametro non valido nel campo Riferimento che può rappresentare un ATTACCO INFORMATICO AL SITO";
				break;
			}
	 		$ret=Lista_Atti($Parametri,$_REQUEST['categoria'],(int)$_REQUEST['numero'],(int)$_REQUEST['anno'], htmlentities($_REQUEST['oggetto']),htmlentities($_REQUEST['DataInizio']),htmlentities($_REQUEST['DataFine']), htmlentities($_REQUEST['riferimento']),$_REQUEST['ente']);
			}else if(isset($_REQUEST['annullafiltro'])){
					 unset($_REQUEST['categoria']);
					 unset($_REQUEST['numero']);
					 unset($_REQUEST['anno']);
					 unset($_REQUEST['oggetto']);
					 unset($_REQUEST['riferimento']);
					 unset($_REQUEST['DataInizio']);
					 unset($_REQUEST['DataFine']);
					 unset($_REQUEST['ente']);
					 $ret=Lista_Atti($Parametri);
				}else{
					$ret=Lista_Atti($Parametri);
				}
		}	
	}else{
		if (isset($_REQUEST['filtra'])){
			if((isset($_REQUEST['categoria']) And !is_numeric($_REQUEST['categoria'])) OR
			   (isset($_REQUEST['numero']) And $_REQUEST['numero']!="" AND !is_numeric($_REQUEST['numero'])) OR
			   (isset($_REQUEST['anno']) And !is_numeric($_REQUEST['anno'])) OR
			   (isset($_REQUEST['ente']) And !is_numeric($_REQUEST['ente']))){
					echo "ATTENZIONE:<br />E' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO";
					return;
			}
			if($_REQUEST['oggetto']!=wp_strip_all_tags($_REQUEST['oggetto'])){
				echo "ATTENZIONE:<br />E' stato indicato un parametro non valido nel campo Oggetto che può rappresentare un ATTACCO INFORMATICO AL SITO";
				return;
			}
			if($_REQUEST['riferimento']!=wp_strip_all_tags($_REQUEST['riferimento'])){
				echo "ATTENZIONE:<br />E' stato indicato un parametro non valido nel campo Riferimento che può rappresentare un ATTACCO INFORMATICO AL SITO";
				return;
			}
			$ret=Lista_Atti($Parametri,(int)$_REQUEST['categoria'],(int)$_REQUEST['numero'],(int)$_REQUEST['anno'], htmlentities($_REQUEST['oggetto']),htmlentities($_REQUEST['DataInizio']),htmlentities($_REQUEST['DataFine']), htmlentities($_REQUEST['riferimento']),(int)$_REQUEST['ente']);			
		}else 
			if(isset($_REQUEST['annullafiltro'])){
				 unset($_REQUEST['categoria']);
				 unset($_REQUEST['numero']);
				 unset($_REQUEST['anno']);
				 unset($_REQUEST['oggetto']);
				 unset($_REQUEST['riferimento']);
				 unset($_REQUEST['DataInizio']);
				 unset($_REQUEST['ente']);
				 $ret=Lista_Atti($Parametri);
			}else{
				$ret=Lista_Atti($Parametri);

			}
	}
function VisualizzaAtto($id){
	$risultato=ap_get_atto($id);
	$risultato=$risultato[0];
	$risultatocategoria=ap_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$allegati=ap_get_all_allegati_atto($id);
	ap_insert_log(5,5,$id,"Visualizzazione");
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	if($risultato->DataAnnullamento!='0000-00-00')
		$Annullato='<p style="background-color: '.$coloreAnnullati.';text-align:center;font-size:1.5em;">Atto Annullato dal Responsabile del Procedimento<br /><br />Motivo: <span style="font-size:1;font-style: italic;">'.stripslashes($risultato->MotivoAnnullamento).'</span></p>';
	else
		$Annullato='';
?>
<section  id="DatiAtto">
	<div class="container clearfix mb-3 pb-3">
		<button class="btn btn-primary" onclick="window.location.href='<?php echo $_SERVER['HTTP_REFERER'];?>'"><i class="fas fa-arrow-circle-left"></i> Torna alla Lista</button>
		<h2 class="u-text-h2 pt-3 pl-2">Dati atto</h2>
		<?php echo ($Annullato?"<h3>'.$Annullato.'</h3>":"");?>
	   	<div class="row">
	   		<div class="col-12 col-xl-8">
				<table class="table table-striped table-hove">
				    <tbody id="dati-atto">
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
		echo	"Tel. ".$Soggetto->Telefono."<br />";
	if ($Soggetto->Orario)
		echo 	"Orario ricevimento: ".$Soggetto->Orario.'<br />';
	if ($Soggetto->Note)
		echo	"Note: ".$Soggetto->Note;
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
		<div class="row border-dashed border-primary mb-1">
			<div class="col-1 icona-comunicazione">
<?php
	if(isset($allegato->TipoFile) and $allegato->TipoFile!="" and ap_isExtensioType($allegato->TipoFile)){
		$Estensione=ap_ExtensionType($allegato->TipoFile);
		echo '<img src="'.$TipidiFiles[$Estensione]['Icona'].'" alt="'.$TipidiFiles[$Estensione]['Descrizione'].'" height="30" width="30"/>';
	}else{
		echo '<img src="'.$TipidiFiles[strtolower($Estensione)]['Icona'].'" alt="'.$TipidiFiles[strtolower($Estensione)]['Descrizione'].'" height="30" width="30"allegato/>';
	}?>
			</div>
			<div class="col-11">  				
				<?php echo (isset($allegato->TitoloAllegato)?"<span class=\"font-weight-semibold Titolo\">".strip_tags($allegato->TitoloAllegato)."</span><br />":""); ?>
 <?php	if (is_file($allegato->Allegato))
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
}

function Lista_Atti($Parametri,$Categoria=0,$Numero=0,$Anno=0,$Oggetto='',$Dadata=0,$Adata=0,$Riferimento='',$Ente=-1){
	ob_start();
	switch ($Parametri['stato']){
			case 0:
				$TitoloAtti="Tutti gli Atti";
				break;
			case 1:
				$TitoloAtti="Atti in corso di Validit&agrave;";
				break;
			case 2:
				$TitoloAtti="Atti Scaduti";
				break;
			case 3:
				$TitoloAtti="Atti da Pubblicare";
				break;
	}
	if (isset($Parametri['per_page'])){
		$N_A_pp=$Parametri['per_page'];	
	}else{
		$N_A_pp=10;
	}
	if (isset($Parametri['cat']) and $Parametri['cat']!=0){
		$DesCategorie="";
		$Categoria="";
		$Categorie=explode(",",$Parametri['cat']);
		foreach($Categorie as $Cate){
			$DesCat=ap_get_categoria($Cate);
			$DesCategorie.=$DesCat[0]->Nome.",";
			$Categoria.=$Cate.",";
		}
		$DesCategorie= substr($DesCategorie,0, strlen($DesCategorie)-1);
		$TitoloAtti.=" Categorie ".$DesCategorie;
		$Categoria=substr($Categoria,0, strlen($Categoria)-1);
		$cat=1;
	}else{
		$Categorie=$Categoria;
		$cat=0;
	}
	if (!isset($_REQUEST['Pag'])){
		$Da=0;
		$A=$N_A_pp;
	}else{
		$Da=($_REQUEST['Pag']-1)*$N_A_pp;
		$A=$N_A_pp;
	}
	if (!isset($_REQUEST['ente'])){
         $Ente = '-1';
	}else{
        $Ente = $_REQUEST['ente'];
	}
	$TotAtti=ap_get_all_atti($Parametri['stato'],$Numero,$Anno,$Categorie,$Oggetto,$Dadata,$Adata,'',0,0,true,false,$Riferimento,$Ente);
	$lista=ap_get_all_atti($Parametri['stato'],$Numero,$Anno,$Categorie,$Oggetto,$Dadata,$Adata,'Anno DESC,Numero DESC',$Da,$A,false,false,$Riferimento,$Ente); 
	$titEnte=get_option('opt_AP_LivelloTitoloEnte');
	if ($titEnte=='')
		$titEnte="h2";
	$titPagina=get_option('opt_AP_LivelloTitoloPagina');
	if ($titPagina=='')
		$titPagina="h3";
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	$colorePari=get_option('opt_AP_ColorePari');
	$coloreDispari=get_option('opt_AP_ColoreDispari');?>
<section  id="FiltroAtti">
	<div class="container shadow clearfix mb-3 pb-3">
		<h2 class="u-text-h2 pt-3 pl-2">Filtri</h2>
	   	<div class="row">
	  	 	<div class="col-12 col-lg-6">
	  	 		<div id="FiltriParametri" class="collapse-div collapse-background-active" role="tablist">
					<div class="collapse-header" id="headingA1">
				    	<button data-toggle="collapse" data-target="#Parametri" aria-expanded="false" aria-controls="Parametri" class="ButtonUF">Parametri</button>
				  	</div>
					<div id="Parametri" class="collapse" role="tabpanel" aria-labelledby="headingA1" data-parent="#FiltriParametri">
						<div class="collapse-body border border-primary rounded-bottom">
 
							<?php echo get_FiltriParametri();?>
					    </div>
					</div>
				</div>
			</div>
	  	 	<div class="col-12 col-lg-6">
	  	 		<div id="FiltriParametri" class="collapse-div collapse-background-active" role="tablist">
					<div class="collapse-header" id="headingA1">
				    	<button data-toggle="collapse" data-target="#Categorie" aria-expanded="false" aria-controls="Categorie" class="ButtonUF">Categorie</button>    	
				  	</div>
					<div id="Categorie" class="collapse" role="tabpanel" aria-labelledby="headingA1" data-parent="#FiltriParametri">
						<div class="collapse-body border border-primary rounded-bottom">
							<?php echo get_FiltriCategorie();?>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php				  
echo ' <div class="Visalbo">
<a name="dati"></a> ';
if (get_option('opt_AP_VisualizzaEnte')=='Si')
		echo '<'.$titEnte.' ><span  class="titoloEnte">'.stripslashes(get_option('opt_AP_Ente')).'</span></'.$titEnte.'>';
echo '<'.$titPagina.'>'.$TitoloAtti.'</'.$titPagina.'>';
$Nav="";
if ($TotAtti>$N_A_pp){
	    $Para='';
	    foreach ($_REQUEST as $k => $v){
			if ($k!="Pag" and $k!="vf")
				if ($Para=='')
					$Para.=$k.'='.$v;
				else
					$Para.='&amp;'.$k.'='.$v;
		}
		if ($Para=='')
			$Para="?Pag=";
		else
			$Para="?".$Para."&amp;Pag=";
		$Npag=(int)($TotAtti/$N_A_pp);
		if ($TotAtti%$N_A_pp>0){
			$Npag++;
		}
		$Nav.= '<div> 
    		<strong>N. Atti '.$TotAtti.'</strong>
	     		<ul class="page-numbers" id="PagineAlboOnLine">';
     	if (isset($_REQUEST['Pag']) And $_REQUEST['Pag']>1 ){
 			$Pagcur=$_REQUEST['Pag'];
			$PagPre=$Pagcur-1; 
				$Nav.= '<li><a href="'.$Para.'1" class="prev page-numbers" title="Vai alla prima pagina"><i class="it-arrow-left-triangle"></i></a></li>
					  <li><a href="'.$Para.$PagPre.'" class="page-numbers" title="Vai alla pagina precedente"><i class="it-chevron-left"></i></a></li> ';
		}else{
			$Pagcur=1;
		}
		if($Pagcur<3){
			$MInf=1;
			$MSup=($Npag<5?$Npag:5);
		}else{
			$MInf=$Pagcur-2;
			$MSup=($Pagcur+2>$Npag?$Npag:$Pagcur+2);		
		}
		for($i=$MInf;$i<$MSup+1;$i++){
			if($i==$Pagcur){
				$Nav.= "<li><span aria-current=\"page\" class=\"page-numbers current\" title=\"Sei gi&agrave; nella prima pagina\">".$i."</span></li>";
			}else{
				$Nav.= "<li><a class=\"page-numbers\" href=\"".$Para.$i."\">".$i."</a>";
			}
		}
   		if (isset($_REQUEST['Pag']) And $_REQUEST['Pag']<$Npag ){
   			$PagSuc=($Pagcur==$Npag?$Npag:$Pagcur+1);
 			$Nav.= '<li><a href="'.$Para.$PagSuc.'" class="next page-numbers" title="Vai alla pagina successiva"><i class="it-chevron-right"></i></a></li>
				  <li><a href="'.$Para.$Npag.'" class="next page-numbers" title="Vai all\'ultima pagina"><i class="it-arrow-right-triangle"></i></a>';
		}
		$Nav.= '</ul>
		</div>';
	}	
echo $Nav;
$FEColsOption=get_option('opt_AP_ColonneFE',array(
									"Data"=>0,
									"Ente"=>0,
									"Riferimento"=>0,
									"Oggetto"=>0,
									"Validita"=>0,
									"Categoria"=>0,
									"Note"=>0,
									"RespProc"=>0,
									"DataOblio"=>0));
if(!is_array($FEColsOption)){
	$FEColsOption=shortcode_atts(array(
				"Data"=>0,
				"Ente"=>0,
				"Riferimento"=>0,
				"Oggetto"=>0,
				"Validita"=>0,
				"Categoria"=>0,
				"Note"=>0,
				"RespProc"=>0,
				"DataOblio"=>0), json_decode($FEColsOption,TRUE),"");
}	
echo '	<div class="tabalbo">                               
		<table class="table table-striped table-hover" summary="atti validi per riferimento, oggetto e categoria"> 
		<thead>
	    	<tr>
	        	<th scope="col">Prog.</th>';
foreach($FEColsOption as $Opzione => $Valore){
		if($Valore==1){
			echo '			<th scope="col">'.$Opzione.'</th>';
		}
}
echo '	</tr>
	    </thead>
	    <tbody>';
	    $CeAnnullato=false;
	if ($lista){
	 	$pari=true;
		if (strpos(get_permalink(),"?")>0)
			$sep="&amp;";
		else
			$sep="?";
		foreach($lista as $riga){
			$Link='<a href="'.get_permalink().$sep.'action=visatto&amp;id='.$riga->IdAtto.'"  style="text-decoration: underline;">';
			$categoria=ap_get_categoria($riga->IdCategoria);
			$cat=$categoria[0]->Nome;
			$NumeroAtto=ap_get_num_anno($riga->IdAtto);
	//		Bonifica_Url();
			$ParCella='';
			if($riga->DataAnnullamento!='0000-00-00'){
				$ParCella='style="background-color: '.$coloreAnnullati.';" title="Atto Annullato. Motivo Annullamento: '.$riga->MotivoAnnullamento.'"';
				$CeAnnullato=true;
			}
			echo '<tr >
			        <td '.$ParCella.'>'.$Link.$NumeroAtto.'/'.$riga->Anno .'</a> 
					</td>';
			if ($FEColsOption['Data']==1)
				echo '
					<td '.$ParCella.'>
						'.ap_VisualizzaData($riga->Data) .'
					</td>';
			if ($FEColsOption['Ente']==1)
				echo '
					<td '.$ParCella.'>
						'.stripslashes(ap_get_ente($riga->Ente)->Nome) .'
					</td>';
			if ($FEColsOption['Riferimento']==1)
				echo '
					<td '.$ParCella.'>
						'.stripslashes($riga->Riferimento) .'
					</td>';
			if ($FEColsOption['Oggetto']==1)
				echo '			
					<td '.$ParCella.'>
						'.stripslashes($riga->Oggetto) .'
					</td>';
			if ($FEColsOption['Validita']==1)
				echo '								
					<td '.$ParCella.'>
						'.ap_VisualizzaData($riga->DataInizio) .'<br />'.ap_VisualizzaData($riga->DataFine) .'  
					</td>';
			if ($FEColsOption['Categoria']==1)
				echo '								
					<td '.$ParCella.'>
						'.$cat .'
					</td>';
			if ($FEColsOption['Note']==1)
				echo '
					<td '.$ParCella.'>
						'.stripslashes($riga->Informazioni) .'
					</td>';
			if ($FEColsOption['RespProc']==1){
				$responsabileprocedura=ap_get_responsabile($riga->RespProc);
				if(count($responsabileprocedura)>0){
					$respproc=$responsabileprocedura[0]->Cognome." ".$responsabileprocedura[0]->Nome;
					echo '
					<td '.$ParCella.'>
						'.$respproc .'
					</td>';				
				}
			}

			if ($FEColsOption['DataOblio']==1)
				echo '
					<td '.$ParCella.'>
						'.ap_VisualizzaData($riga->DataOblio) .'
					</td>';
		echo '	
				</tr>'; 
			}
	} else {
			echo '<tr>
					<td colspan="6">Nessun Atto Codificato</td>
				  </tr>';
	}
	echo '
     </tbody>
    </table>';
echo '</div>';
	if ($CeAnnullato) 
		echo '<p>Le righe evidenziate con questo sfondo <span style="background-color: '.$coloreAnnullati.';">&nbsp;&nbsp;&nbsp;</span> indicano Atti Annullati</p>';
echo '</div><!-- /wrap -->	';
echo $Nav;
return ob_get_clean();
}
?>