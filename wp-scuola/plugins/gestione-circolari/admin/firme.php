<?php 
/**
 * Gestione Circolari - Funzioni Gestione Firme
 * 
 * @package Gestione Circolari
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @ver 2.7.3
 */
function wps_circolari_VisualizzaArchivio()
{
global $msg,$wps_TestiRisposte,$wps_Testi;
	$current_user =wp_get_current_user();
	$DataCreazioneUtente=substr(get_userdata($current_user->ID)->user_registered,0,10);
	echo'
		<div class="wrap">
			<span class="fa fa-archive fa-3x" aria-hidden="true"></span> <h2 style="display:inline;margin-left:10px;vertical-align:super;">Archivio Circolari</h2>
		</div>';
	if($msg!="") 
		echo '<div id="message" class="updated"><p>'.$msg.'</p></div>';
	$Posts=wps_GetArchivioCircolari();
	echo '
	<div>
		<table id="TabellaCircolari" class="widefat"  cellspacing="0" width="99%">
			<thead>
				<tr>
					<th style="width:30px;">N°</th>
					<th >Titolo</th>
					<th style="width:60px;"  id="ColOrd" sorted="2">Del</th>
					<th style="width:100px;">Tipo</th>
					<th style="width:130px;">Scadenza</th>
					<th style="width:60px;">Firma</th>
					<th style="width:70px;" >Data Firma</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th style="width:30px;">N°</th>
					<th >Titolo</th>
					<th style="width:60px;">Del</th>
					<th style="width:100px;">Tipo</th>
					<th style="width:130px;">Scadenza</th>
					<th style="width:60px;">Firma</th>
					<th style="width:70px;">Data Firma</th>
				</tr>
			</tfoot>
			<tboby>';
	foreach($Posts as $post){
//	print_r($post);
		if (!(wps_Is_Circolare_Pubblica($post->ID) Or wps_Is_Circolare_per_User($post->ID)))
			continue;
		$sign=get_post_meta($post->ID, "_sign",TRUE);
		$RimuoviFirma="";
		$Campo_Firma="";
		$GGDiff="";
		$BGC="";
		$Scadenza=wps_Get_scadenzaCircolare($post->ID,"DataDB");
//		echo $Scadenza." - ".$sign." - ".wps_Is_Circolare_Firmata($post->ID)." - <br />";
		if($Scadenza>date("Y-m-d") and $sign!="NoFirma" And wps_Is_Circolare_Firmata($post->ID)){
			$Titolo="Rimuovi ".($sign=="Firma"?"Firma":"Espressione");
			$LinkRmFirma=admin_url()."edit.php?post_type=circolari_scuola&page=Archivio&op=RemoveFirma&pid=".$post->ID."&circoRmFir=".wp_create_nonce('RmFirmaCircolare');
			$RimuoviFirma='<a href="'.$LinkRmFirma.'"<span class="fa fa-times" aria-hidden="true" title="'.$Titolo.'" style="color:red;"></span></a>';		
		}
		if ($Scadenza>=$DataCreazioneUtente){
			if($sign!="NoFirma"){			
				if ($Scadenza=="9999-12-31")
					$GGDiff=9999;
				else{
					$seconds_diff = strtotime($Scadenza) - strtotime(date("Y-m-d"));
					$GGDiff=floor($seconds_diff/3600/24);				
				}
				if ($GGDiff>0){
					$GGDiff="tra ".$GGDiff." gg";
					$BGC="color: #14D700;";
				}else{
					$GGDiff="da ".(abs($GGDiff)>100?"+100":abs($GGDiff))." gg";
					$BGC="color: red;";
				}	
				if($sign=="Firma"){
					if (wps_Is_Circolare_Firmata($post->ID)){
						 $Campo_Firma="Firmata";
					}else{
						$Campo_Firma="Non Firmata";
					}
				}else{
					$Campo_Firma=(is_null($wps_TestiRisposte[wps_get_Circolare_Adesione($post->ID)])?"Errore rosposta":$wps_TestiRisposte[wps_get_Circolare_Adesione($post->ID)]->get_Risposta());
				}
//				if(is_null($wps_TestiRisposte[wps_get_Circolare_Adesione($post->ID)]))
//					echo $post->ID;
				$Campo_Firma=(is_null($wps_TestiRisposte[wps_get_Circolare_Adesione($post->ID)])?"Errore rosposta":$wps_TestiRisposte[wps_get_Circolare_Adesione($post->ID)]->get_Risposta());
				}
        }
//		setup_postdata($post);
//		$dati_firma=wps_get_Firma_Circolare($post->ID);
		if(is_null(Circolari_Tipo::get_TipoCircolare($sign)))
			$TC='<span style="color:red;font-weight: bold;">ERRORE: tipo circolare errato</span>';
		else
			$TC=Circolari_Tipo::get_TipoCircolare($sign)->get_DescrizioneTipo();
		echo "
				<tr>
					<td> ".wps_GetNumeroCircolare($post->ID)."</td>
					<td>
						<a href='".get_permalink( $post->ID )."'>
						$post->post_title
						</a>
					</td> 
					</td> 
					<td>".wps_FormatDataItalianoBreve(substr($post->post_date,0,10),TRUE)."</td>
					<td>".$TC."</td>
					<td><spam style='$BGC'>".wps_FormatDataItalianoBreve(wps_Get_scadenzaCircolare( $post->ID,"" ),TRUE)." $GGDiff</spam></td>
					<td>$RimuoviFirma $Campo_Firma</td>
					<td>".wps_FormatDataItalianoBreve(wps_Get_Data_Firma($post->ID),TRUE)."</td>
				</tr>";
	}
	echo '
				</tbody>
			</table>
		</div>';	
}
function wps_circolari_GestioneFirme()
{
global $msg;
echo'
		<div class="wrap">
			<span class="fa fa-pencil fa-3x" aria-hidden="true"></span> <h2 style="display:inline;margin-left:10px;vertical-align:super;">Circolari da firmare</h2>
		</div>';
if($msg!="") 
	echo '<div id="message" class="updated"><p>'.$msg.'</p></div>';
		wps_VisualizzaTabellaCircolari();		
}
function wps_VisualizzaTabellaCircolari(){
	global $wps_TestiRisposte,$wps_Testi;
	$Posts=wps_GetCircolariDaFirmare("D");
	
	echo '
	<div>
		<table id="TabellaCircolari" class="widefat"  cellspacing="0" width="99%">
			<thead>
				<tr>
					<th style="width:5%;">N°</th>
					<th style="width:60%;">Titolo</th>
					<th style="width:15%;" id="ColOrd" sorted="2">Scadenza</th>
					<th style="width:20%;">Firma</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th style="width:5%;">N°</th>
					<th style="width:60%;">Titolo</th>
					<th style="width:15%;">Scadenza</th>
					<th style="width:20%;">Firma</th>
				</tr>
			</tfoot>
			<tboby>';
//var_dump($Posts);
	$BaseUrl=admin_url()."edit.php";
	foreach($Posts as $post){
		$sign=get_post_meta($post->ID, "_sign",TRUE);
		$Scadenza=wps_Get_scadenzaCircolare($post->ID,"DataDB");
		if ($Scadenza=="9999-12-31")
			$GGDiff=9999;
		else{
			$seconds_diff = strtotime($Scadenza) - strtotime(date("Y-m-d"));
			$GGDiff=floor($seconds_diff/3600/24);
		}
		switch ($GGDiff){
			case ($GGDiff <3):
				$BGC="color: Red;";
				break;
			case ($GGDiff >2 And $GGDiff <7):
				$BGC="color: #FFA500;";
				break;
			case ($GGDiff >6  And $GGDiff <15):
				$BGC="color: #71E600;";
				break;
			default:
				$BGC="color: Blue;";
				break;	
		}
		$TipoCircolare= wps_Circolari_find_Tipo($sign);
		if($sign=="Firma"){
				$Campo_Firma='<a href="'.$BaseUrl.'?post_type=circolari_scuola&page=Firma&op=Firma&pid='.$post->ID.'&circoFir='.wp_create_nonce('FirmaCircolare').'">Firma Circolare</a>';
		}elseif ($sign!="NoFirma"){	
				$Campo_Firma=$TipoCircolare->get_DescrizioneTipo().'<br />';
				$Campo_Firma.='<form action="'.$BaseUrl.'"  method="get" style="display:inline;">
					<input type="hidden" name="post_type" value="circolari_scuola" />
					<input type="hidden" name="page" value="Firma" />
					<input type="hidden" name="op" value="Adesione" />
					<input type="hidden" name="pid" value="'.$post->ID.'" />
					<input type="hidden" name="circoFir" value="'.wp_create_nonce('FirmaCircolare').'" />';
				$Risposte=$TipoCircolare->get_Risposte();
				foreach($Risposte as $Risposta){
					$Risp=wps_Circolari_find_Risposta($Risposta);
					$Campo_Firma.='<input type="radio" name="scelta" class="s'.$Risposta.'-'.$post->ID.'" value="'.$Risposta.'"/>'.$Risp->get_Risposta().' '; 
				}
				$Campo_Firma.= ' <input type="hidden" name="to" id="to" value="'.$TipoCircolare->get_TestoElenco().'" />
					<input type="submit" name="inviaadesione" class="button inviaadesione" id="'.$post->ID.'" value="Firma" rel="'.$post->post_title.'"/>
					</form>';
			}				
			echo "
				<tr>
					<td> ".wps_GetNumeroCircolare($post->ID)."</td>
					<td>
					<a href='".get_permalink( $post->ID )."'>
					$post->post_title
					</a>
					</td>
					<td><spam style='$BGC'>$Scadenza ($GGDiff gg)</spam></td>
					<td>$Campo_Firma</td>
				</tr>";
	}	
	echo '
				</tbody>
			</table>
		</div>';
}