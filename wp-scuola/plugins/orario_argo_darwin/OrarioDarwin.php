<?php
$target_dir = get_template_directory()."/plugins/orario_argo_darwin/orari/";
$target_url = get_template_directory_uri()."/plugins/orario_argo_darwin/";

add_action( 'admin_menu', 								'OAD_admin_menu');
add_action( 'admin_enqueue_scripts',					'OAD_admin_Enqueue_Scripts');
add_action( 'wp_head',									'OAD_public_Enqueue_Scripts');
add_action( 'wp_ajax_GetOrarioDocente',					'OAD_GetOrarioDocente_Public' );
add_action( 'wp_ajax_GetOrarioClasse',					'OAD_GetOrarioClasse_Public' );
add_action( 'wp_ajax_GetOrarioRicevimentoClasse',		'OAD_GetOrarioRicevimentoClasse_Public' );
add_action( 'wp_ajax_GetOrarioStruttura',				'OAD_GetOrarioStruttura_Public' );
add_action( 'wp_ajax_nopriv_GetOrarioDocente',			'OAD_GetOrarioDocente_Public' );
add_action( 'wp_ajax_nopriv_GetOrarioClasse',			'OAD_GetOrarioClasse_Public' );
add_action( 'wp_ajax_nopriv_GetOrarioRicevimentoClasse','OAD_GetOrarioRicevimentoClasse_Public' );
add_action( 'wp_ajax_nopriv_GetOrarioStruttura',		'OAD_GetOrarioStruttura_Public' );

include_once ( dirname (__FILE__) . '/shortcode.php' );

function OAD_GetOrarioDocente_Public(){
	check_ajax_referer('WPScuolaSecret','security');
	$IDDocente= filter_input(INPUT_POST,'IDDocente');
	OAD_caricaDati();
	OAD_daArrayATabella(OAD_getOrarioDocente($IDDocente),"","table table-sm table-bordered table-striped thead-dark");
	wp_die();
}
function OAD_GetOrarioClasse_Public(){
	check_ajax_referer('WPScuolaSecret','security');
	$IDClasse= filter_input(INPUT_POST,'IDClasse');
	OAD_caricaDati();
	OAD_daArrayATabella(OAD_getOrarioClasse($IDClasse),"","table table-sm table-bordered table-striped thead-dark");
	wp_die();
}
function OAD_GetOrarioRicevimentoClasse_Public(){
	check_ajax_referer('WPScuolaSecret','security');
	$IDClasse= filter_input(INPUT_POST,'IDClasse');
	OAD_caricaDati();
	OAD_daArrayATabella(OAD_getOrarioRicevimentoClasse($IDClasse),"","table table-sm table-bordered table-striped thead-dark");
	wp_die();
}
function OAD_GetOrarioStruttura_Public(){
	check_ajax_referer('WPScuolaSecret','security');
	$IDStruttura= filter_input(INPUT_POST,'IDStruttura');
	OAD_caricaDati();
	OAD_daArrayATabella(OAD_getOrarioStruttura($IDStruttura),"","table table-sm table-bordered table-striped thead-dark");
	wp_die();
}
function OAD_admin_menu(){
  	add_menu_page('Orario Darwin', __('Orario Argo Darwin','wpscuola'), 'manage_options', 'Orario_Darwin','OAD_impostazioni',get_template_directory_uri() . "/plugins/orario_argo_darwin/img/logoArgo.png");
}

function OAD_admin_Enqueue_Scripts( $hook_suffix ) {
	global $target_url;
	if($hook_suffix!="toplevel_page_Orario_Darwin") return;
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('Orario_Darwin-admin',$target_url.'/js/orarioAdmin.js');
    wp_enqueue_style( 'Orario_Darwin-jquery.ui.theme', $target_url.'/css/jquery-ui-orarioDarwin.css');
    wp_enqueue_style( 'Orario_Darwin-adminCSS', $target_url.'/css/admin.css');  
}
function OAD_public_Enqueue_Scripts( $hook_suffix ) {
	global $target_url;
    wp_enqueue_script('Orario_Darwin-public',$target_url.'/js/orarioPublic.js');
}
function OAD_caricaDati(){
	global $target_dir,$arrOrario;
		
	$CurrentOrario=get_option('wps_OrarioArgoDarwin',"");
	if($CurrentOrario!=""){
		libxml_use_internal_errors(TRUE);
 		$objXmlOrario = simplexml_load_file($CurrentOrario);
 		if ($objXmlOrario === FALSE) {
		    _e("Si sono verificati i seguenti errori nell'Importazione dell'Orario","wpscuola");
		    foreach(libxml_get_errors() as $error) {
		        echo $error->message;
		    }
    		exit;
		}
		$objJsonOrario = json_encode($objXmlOrario);
		$arrOrario = json_decode($objJsonOrario, TRUE);
//		echo "<pre>";print_r($arrOrario);echo "</pre>";
	}
}

function OAD_impostazioni(){
	global $target_dir,$arrOrario;
	$CurrentOrario=get_option('wps_OrarioArgoDarwin',"");
	if(isset($_POST["submit"])) {
		$NomeFile= basename($_FILES["fileOrario"]["name"]);
		$target_file=$target_dir.substr($NomeFile,0,strrpos($NomeFile,".")).date("Ymd_Gis").substr($NomeFile,strrpos($NomeFile,"."),strlen($NomeFile)-strrpos($NomeFile,"."));
		$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	  	if($FileType != "xml") {
	  		echo '<div class="notice notice-error"><p>'.
		  	__("Tipo di file ERRATO, si deve caricare un file in formato XML","wpscuola").'</p></div>
		      <meta http-equiv="refresh" content="2;url=admin.php?page=Orario_Darwin"/>';
		      return;
		}else{
			if (move_uploaded_file($_FILES["fileOrario"]["tmp_name"], $target_file)) {
    		echo '<div class="notice notice-success"><p>'.
		  	__("Il file dell'Orario è stato caricato correttamente","wpscuola").'</p></div>
		      <meta http-equiv="refresh" content="2;url=admin.php?page=Orario_Darwin"/>
		      ';
		      update_option('wps_OrarioArgoDarwin',$target_file);
		      return;
  			} else {
    			echo '<div class="notice notice-error"><p>'.
		  			__("Errore nel caricamento del file:","wpscuola").' '.$target_file.'</p></div>
		      <meta http-equiv="refresh" content="2;url=admin.php?page=Orario_Darwin"/>';
		      return;
  			}
		}
	}
	OAD_caricaDati();
	?>
<div class="welcome-panel" >
	<div class="welcome-panel-content">
		<h2><img src="<?php echo get_template_directory_uri();?>/plugins/orario_argo_darwin/img/logoDarwin.png" style="float:left;padding-right: 2em;"/><?php _e( 'Pannello di amministrazione del Modulo di gestione dell\'Orario Scolastico <br />importato dal programma Argo Darwin','wpscuola' ); ?></h2>
	    <div class="" style="margin-top: 2em;">
	    	<div style="padding-bottom: 2em;">
	    		<h3><?php _e("File XML dell'Orario","wpscuola");?></h3>
	    		<div style="padding: 1em;font-size: 1.2em;">
	    			<strong>File corrente: </strong><em><span style="color:red;"><?php echo $CurrentOrario;?></span></em>
				</div>
				<form method="post" enctype="multipart/form-data">
				  <strong><?php _e("Selezione il file XML che contiene l'Orario","wpscuola");?></strong>
				  <input type="file" name="fileOrario" id="fileOrario">
				  <input type="submit" value="<?php _e("Carica il file","wpscuola");?>" name="submit">
				</form>	
			</div>
			<div style="padding-bottom: 2em;">
			<?php OAD_CreaTabelleDati();?>
			</div>
		</div>
	</div>
</div>
<?php
}
function OAD_materiaInOrario($IDmateria){
	global	$arrOrario;
	foreach($arrOrario["ATTIVITA"]["CORSO"] as $Corso){
		if($Corso["MATERIA"]==$IDmateria) return TRUE;
	}
	return FALSE;
}
function OAD_materieDocente($IDDocente){
	global	$arrOrario;
	$Materie=array();
	foreach($arrOrario["ATTIVITA"]["CORSO"] as $Corso){
		if(is_array($Corso["DOCENTE"])){
			foreach($Corso["DOCENTE"] as $Docente){
				if($Docente==$IDDocente){
					$Materie[]=$arrOrario["MATERIE"]["MATERIA"][$Corso["MATERIA"]];
				}
			}
		}else{
			if($Corso["DOCENTE"]==$IDDocente){
				$Materie[]=$arrOrario["MATERIE"]["MATERIA"][$Corso["MATERIA"]];
			}
		}
	}
	return implode(", ",array_unique($Materie));
}
function OAD_getOrarioDocente($IdDocente){
	echo "<p><strong>Orario del docente: ".OAD_getDocenteNome($IdDocente)."</strong></p>";
	global	$arrOrario;
	$Orario[1]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[2]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[3]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[4]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[5]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[6]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[7]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[8]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[9]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[10]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[11]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[12]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	if(!is_null($arrOrario)){
		foreach($arrOrario["ATTIVITA"]["CORSO"] as $Corso){
//		echo "<pre>";print_r($Corso);echo "</pre>";
			if(is_array($Corso["DOCENTE"])){
				foreach($Corso["DOCENTE"] as $Docente){
					if($Docente==$IdDocente){
	//					echo "<pre>";print_r($Docente);echo "</pre>";
						$Strutture="Struttura ND";
						if(is_array($Corso["STRUTTURA"])){
							$DesStrut=array();
							foreach($Corso["STRUTTURA"] as $Struttura)
								$DesStrut[]=OAD_getStruttura($Struttura);
								$Strutture=implode(", ",$DesStrut);
						}else{
							$Strutture=OAD_getStruttura($Corso["STRUTTURA"]);
						}
						foreach($Corso["LEZIONI"] as $Lezione){
	//						echo "<pre>";print_r($Lezione);echo "</pre>";
							if(isset($Lezione["DURATA"])){
	//							echo "<pre>";print_r($Lezione);echo "</pre>";
								for($i=0;$i<$Lezione["DURATA"];$i++)
									$Orario[$Lezione["ORA"]+$i][$Lezione["GIORNO"]]="<strong>".OAD_getClasse($Corso["CLASSE"])."</strong> (".$Strutture.") "."<br /><em>".OAD_getMateria($Corso["MATERIA"])."</em>";
							}
						}
					}
				}
			}else{
				if($Corso["DOCENTE"]==$IdDocente){
						$Strutture="Struttura ND";
						if(is_array($Corso["STRUTTURA"])){
							$DesStrut=array();
							foreach($Corso["STRUTTURA"] as $Struttura)
								$DesStrut[]=OAD_getStruttura($Struttura);
								$Strutture=implode(", ",$DesStrut);
						}else{
							$Strutture=OAD_getStruttura($Corso["STRUTTURA"]);
						}
					foreach($Corso["LEZIONI"] as $Lezione){
	//					echo "<pre>";print_r($Lezione);echo "</pre>";
						if(isset($Lezione["DURATA"])){
		//				echo "<pre>";print_r($Lezione);echo "</pre>";
							for($i=0;$i<$Lezione["DURATA"];$i++)
								$Orario[$Lezione["ORA"]+$i][$Lezione["GIORNO"]]="<strong>".OAD_getClasse($Corso["CLASSE"])."</strong><br />(".$Strutture.") "."<br /><em>".OAD_getMateria($Corso["MATERIA"])."</em>";
						}
					}
				}		
			}
		}
	}
	if(($Ricevimento=OAD_getDocenteRicevimento($IdDocente))!==FALSE){
		$Orario[$Ricevimento["ORA"]][$Ricevimento["GIORNO"]]="<strong>Ricevimento</strong>";
	}
	if(($Disposizioni=OAD_getDocenteDisposizione($IdDocente))!==FALSE){
//		echo "<pre>";print_r($Disposizione);echo "</pre>";
		foreach($Disposizioni as $Disposizione){
			$Orario[$Disposizione["ORA"]][$Disposizione["GIORNO"]]="<strong>Disposizione</strong>";
		}
	}
	return $Orario;
}
function OAD_getOrarioClasse($IdClasse){
	echo "<p><strong>Orario della classe: ".OAD_getClasse($IdClasse)."</strong></p>";
	global	$arrOrario;
	$Orario[1]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[2]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[3]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[4]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[5]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[6]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[7]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[8]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[9]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[10]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[11]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[12]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	if(!is_null($arrOrario)){
		foreach($arrOrario["ATTIVITA"]["CORSO"] as $Corso){
	//		echo "<pre>";print_r($Corso);echo "</pre>";
			if($Corso["CLASSE"]==$IdClasse){
	//					echo "<pre>";print_r($Corso);echo "</pre>";
				$Strutture="Struttura ND";
				if(is_array($Corso["STRUTTURA"])){
					$DesStrut=array();
					foreach($Corso["STRUTTURA"] as $Struttura)
						$DesStrut[]=OAD_getStruttura($Struttura);
						$Strutture=implode(", ",$DesStrut);
				}else{
					$Strutture=OAD_getStruttura($Corso["STRUTTURA"]);
				}
				$Docenti="Docente ND";
				if(is_array($Corso["DOCENTE"])){
					$DesDocent=array();
					foreach($Corso["DOCENTE"] as $Docente)
						$DesDocent[]=OAD_getDocenteDispNome($Docente);
						$Docenti=implode(", ",$DesDocent);
				}else{
					$Docenti=OAD_getDocenteDispNome($Corso["DOCENTE"]);
				}
				foreach($Corso["LEZIONI"] as $Lezione){
	//			if($Corso["MATERIA"]==48){
	//				echo "<pre>";print_r($Lezione);echo "</pre>".$Docenti;}
					if(isset($Lezione[0])){
						foreach($Lezione as $LezioneSingola){
							for($i=0;$i<$LezioneSingola["DURATA"];$i++)
								$Orario[$LezioneSingola["ORA"]+$i][$LezioneSingola["GIORNO"]]="<strong>".OAD_getMateria($Corso["MATERIA"])."</strong><br /><em>".$Docenti."</em><br />(".$Strutture.")";					
						}
					}else{
						for($i=0;$i<$Lezione["DURATA"];$i++)
							$Orario[$Lezione["ORA"]+$i][$Lezione["GIORNO"]]="<strong>".OAD_getMateria($Corso["MATERIA"])."</strong><br /><em>".$Docenti."</em><br />(".$Strutture.")";
					}
				}
			}
		}
	}
	return $Orario;
}
function OAD_getOrarioRicevimentoClasse($IdClasse){
	echo "<p><strong>Orario ricevimento docenti della classe: ".OAD_getClasse($IdClasse)."</strong></p>";
	global	$arrOrario;
	$Orario[1]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[2]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[3]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[4]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[5]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[6]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[7]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[8]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[9]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[10]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[11]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[12]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Docenti=array();
	if(!is_null($arrOrario)){
		foreach($arrOrario["ATTIVITA"]["CORSO"] as $Corso){
	//		echo "<pre>";print_r($Corso);echo "</pre>";
			if($Corso["CLASSE"]==$IdClasse){
	//					echo "<pre>";print_r($Corso);echo "</pre>";
				if(is_array($Corso["DOCENTE"])){
					foreach($Corso["DOCENTE"] as $Docente)
						$Docenti[]=$Docente;
				}else{
					$Docenti[]=$Corso["DOCENTE"];
				}
			}
		}
		foreach($Docenti as $Docente){
			if(($Ricevimento=OAD_getDocenteRicevimento($Docente))!==FALSE){
				$Orario[$Ricevimento["ORA"]][$Ricevimento["GIORNO"]]=OAD_getDocenteDispNome($Docente);
			}
		}
	}
	return $Orario;
}
function OAD_getOrarioStruttura($IdStruttura){
	echo "<p><strong>Orario della strutura: ".OAD_getStruttura($IdStruttura)."</strong></p>";
	global	$arrOrario;
	$Orario[1]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[2]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[3]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[4]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[5]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[6]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[7]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[8]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[9]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[10]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[11]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	$Orario[12]=array("LUN"=>"","MAR"=>"","MER"=>"","GIO"=>"","VEN"=>"","SAB"=>"");
	if(!is_null($arrOrario)){
		foreach($arrOrario["ATTIVITA"]["CORSO"] as $Corso){
	//		echo "<pre>";print_r($Corso);echo "</pre>";
			if($Corso["STRUTTURA"]==$IdStruttura){
	//					echo "<pre>";print_r($Corso);echo "</pre>";
				$Classe="Classe ND";
				if(is_array($Corso["CLASSE"])){
					$DesClasse=array();
					foreach($Corso["CLASSE"] as $Classe)
						$DesClasse[]=OAD_getClasse($Classe);
						$Classe=implode(", ",$DesClasse);
				}else{
					$Classe=OAD_getClasse($Corso["CLASSE"]);
				}
				$Docenti="Docente ND";
				if(is_array($Corso["DOCENTE"])){
					$DesDocent=array();
					foreach($Corso["DOCENTE"] as $Docente)
						$DesDocent[]=OAD_getDocenteDispNome($Docente);
						$Docenti=implode(", ",$DesDocent);
				}else{
					$Docenti=OAD_getDocenteDispNome($Corso["DOCENTE"]);
				}
				foreach($Corso["LEZIONI"] as $Lezione){
	//			if($Corso["MATERIA"]==48){
	//				echo "<pre>";print_r($Lezione);echo "</pre>".$Docenti;}
					if(isset($Lezione[0])){
						foreach($Lezione as $LezioneSingola){
							for($i=0;$i<$LezioneSingola["DURATA"];$i++)
								$Orario[$LezioneSingola["ORA"]+$i][$LezioneSingola["GIORNO"]]="<strong>".$Classe."<br />".OAD_getMateria($Corso["MATERIA"])."</strong><br /><em>".$Docenti."</em>";
						}
					}else{
						for($i=0;$i<$Lezione["DURATA"];$i++)
							$Orario[$Lezione["ORA"]+$i][$Lezione["GIORNO"]]="<strong>".$Classe."<br />".OAD_getMateria($Corso["MATERIA"])."</strong><br /><em>".$Docenti."</em>";
					}
				}
			}
		}
	}
	return $Orario;
}
function OAD_daArrayATabella($Arr,$IDTable="",$ClassTable="table table-bordered table-striped",$ClassThead="",$ClassTbody=""){
	end($Arr);
	$NumRighe=0;
	do {
		$riga=false;
		foreach(current($Arr) as $LezioniOra){
			if($LezioniOra!="")
				$riga=true;
		}
		if($riga)
			break;
		else
			$NumRighe++;
	}while(prev($Arr) !== false);	
	$NumRighe=count($Arr)-$NumRighe;
//	var_dump($Arr);
	?>
	<table <?php echo ($IDTable!=""?'id="'.$IDTable.'"':'');?> <?php echo ($ClassTable!=""?'class="'.$ClassTable.'"':'');?>>
		<thead <?php echo ($ClassThead!=""?'class="'.$ClassThead.'"':'');?>>
			<tr>
				<th scope="col" width="2%">Ora</th>
				<th scope="col" width="14%">Lunedì</th>
				<th scope="col" width="14%">Martedì</th>
				<th scope="col" width="14%">Mercoledì</th>
				<th scope="col" width="14%">Giovedì</th>
				<th scope="col" width="14%">Venerdì</th>
				<th scope="col" width="14%">Sabato</th>
			</tr>
		</thead>
		<tbody <?php echo ($ClassTbody!=""?'class="'.$ClassTbody.'"':'');?>>
<?php	for($i=1;$i<=$NumRighe;$i++){?>
			<tr>
				<th scope="row"><?php echo $i;?></th>
<?php		foreach($Arr[$i] as $LezioniOra){?>
				<td><?php echo $LezioniOra;?></td>
<?php		}?>
			</tr>
<?php	}?>			
		</tbody>
	</table>
<?php
}

function OAD_getMateria($IdMateria){
	global	$arrOrario;
	if(isset($arrOrario["MATERIE"]["MATERIA"][$IdMateria]))
		return $arrOrario["MATERIE"]["MATERIA"][$IdMateria];
	else
		return "Materia ND";
}
function OAD_getElencoStrutture($Visualizzazione="Array",$Pulsanti=""){
	global	$arrOrario;
	$Strutture=array();
	foreach($arrOrario["STRUTTURE"]["STRUTTURA"] as $id => $Struttura){
		$Strutture[$id]=array("Struttura"	=>$Struttura);
//		}
	}
//	var_dump($Docenti);
	ksort($Strutture);	
	switch($Visualizzazione){
		case "Select":
			$Html='<div class="bootstrap-select-wrapper pt-5">
  <label class="labelNoFormat">Strutture</label>
  <select id="ElencoStrutture" title="Seleziona una Struttura" data-live-search="true" data-live-search-placeholder="Cerca struttura" class="w-75">
  	<option value="" title="Seleziona una Struttura" data-content="Annulla selezione<span class="reset-label"></span>"></option>';
  	foreach($Strutture as $id => $Struttura){
 // 		var_dump($Classe);
  		$Html.= '<option value="'.$id.'">'.Ucwords(strtolower($Struttura["Struttura"])).'</option>';
	}
	$Html.= '  </select>'.$Pulsanti.'
</div>';
			return $Html;
			break;
		default: return $Strutture;
	}
}
function OAD_getStruttura($IdStruttura){
	global	$arrOrario;
	if(isset($arrOrario["STRUTTURE"]["STRUTTURA"][$IdStruttura])){
		return $arrOrario["STRUTTURE"]["STRUTTURA"][$IdStruttura];
	}else{
		return "Struttura ND";
	}
}
function OAD_crea_tabella_materie(){
	global	$arrOrario;
	$Materie=array();
	if(! is_null($arrOrario)){
		foreach($arrOrario["MATERIE"]["MATERIA"] as $id => $Materia){
			if(OAD_materiaInOrario($id)){
				$Materie[$Materia]=$id;
			}
		}
		ksort($Materie);
	}
	?>
	<table>
		<thead>
			<tr>
				<th style="width: 4em;position: sticky; top: 0;text-align: left;background: white;">ID</th>
				<th style="position: sticky; top: 0;text-align: left;background: white;">Materia</th>
			</tr>
		</thead>
		<tbody>
<?php	foreach($Materie as $Mate => $id){?>
			<tr>
				<td style="border: 1px solid #ddd;"><?php echo $id;?></td>
				<td style="border: 1px solid #ddd;"><?php echo $Mate;?></td>
			</tr>
<?php	}?>
		</tbody>
	</table>
<?php
}
function DaArrayAStringa($Array,$Format,$Sep){
	$Stringa="";
	foreach($Array as $K=>$Value){
		if(is_array($Value)){
			foreach($Value as $K=>$Valore){
				if($K=="ORA")
					$Stringa.=" (".$Valore.")";
				else
					$Stringa.=$Valore;
			}
			$Stringa.=$Sep;
		}
		else
			if($K=="ORA")
					$Stringa.=" (".$Value.")";
				else
					$Stringa.=$Value;
	}
	return $Stringa;
}
function OAD_getDocenteRicevimento($IdDocente){
	global	$arrOrario;
	if(isset($arrOrario["DOCENTI"]["DOCENTE"][$IdDocente]["RICEVIMENTO"])){
		return $arrOrario["DOCENTI"]["DOCENTE"][$IdDocente]["RICEVIMENTO"];
	}
	return FALSE;
}
function OAD_getDocenteDisposizione($IdDocente){
	global	$arrOrario;
	if(isset($arrOrario["DOCENTI"]["DOCENTE"][$IdDocente]["DISPOSIZIONE"])){
		return $arrOrario["DOCENTI"]["DOCENTE"][$IdDocente]["DISPOSIZIONE"];
	}
	return FALSE;
}
function OAD_getDocenteNome($IdDocente){
	global	$arrOrario;
	if(isset($arrOrario["DOCENTI"]["DOCENTE"][$IdDocente])){
		return Ucwords(strtolower($arrOrario["DOCENTI"]["DOCENTE"][$IdDocente]["COGNOME"]))." ".Ucwords(strtolower($arrOrario["DOCENTI"]["DOCENTE"][$IdDocente]["NOME"]));
	}
	return FALSE;
}
function OAD_getDocenteDispNome($IdDocente){
	global	$arrOrario;
	if(isset($arrOrario["DOCENTI"]["DOCENTE"][$IdDocente])){
		return Ucwords(strtolower($arrOrario["DOCENTI"]["DOCENTE"][$IdDocente]["ID"]));
	}
	return FALSE;
}

function OAD_getElencoDocenti($Visualizza="",$Visualizzazione="Array",$Pulsanti=""){
	global	$arrOrario;
	$Visualizza=explode(",",$Visualizza);
	$Docenti=array();
	if(!is_null($arrOrario))	{
		foreach($arrOrario["DOCENTI"]["DOCENTE"] as $id => $Docente){
			if(isset($Docente["COGNOME"]) And !is_array($Docente["NOME"]))
				$Cognome=$Docente["COGNOME"]." ";
			else
				$Cognome="";
			if(isset($Docente["NOME"]) And !is_array($Docente["NOME"])) 
				$Nome=$Docente["NOME"];
			else
				$Nome="";	
			$CognomeNome=$Cognome.$Nome;
			if($CognomeNome=="") 
				$CognomeNome=$Docente["ID"];
			$Riga=array("Id"=>$id,"CognomeNome"=>$CognomeNome);
			if(in_array("Ricevimento",$Visualizza)){
				if(isset($Docente["RICEVIMENTO"]))
					$Ricevimento=$Docente["RICEVIMENTO"];
				else
					$Ricevimento=array("GIORNO" => "ND","ORA"=>"ND");
				$Riga["Ricevimento"]=$Ricevimento;
			}
			if(in_array("Disposizione",$Visualizza)){	
				if(isset($Docente["DISPOSIZIONE"]))
					$Disposizione=DaArrayAStringa($Docente["DISPOSIZIONE"],"%s (%s)",", ");
				else
					$Disposizione="Nessuna";
				$Riga["Disposizione"]=$Disposizione;
			}
			$Riga=array("Id"=>$id,"CognomeNome"=>$CognomeNome);
			$Docenti[$Docente["ID"]]=$Riga;		
	//		}
		}
	//	var_dump($Docenti);
		ksort($Docenti);			
	}
	switch($Visualizzazione){
		case "Select":
			$Html='<div class="bootstrap-select-wrapper pt-5">
  <label class="labelNoFormat">Docenti</label>
  <select id="ElencoDocenti" title="Seleziona un Docente" data-live-search="true" data-live-search-placeholder="Cerca docente" class="w-75">
  	<option value="" title="Seleziona un Docente" data-content="Annulla selezione<span class="reset-label"></span>"></option>';
  	foreach($Docenti as $id => $Docente){
  		$Html.= '<option value="'.$Docente["Id"].'">'.Ucwords(strtolower($Docente["CognomeNome"])).'</option>';
	}
	$Html.= '  </select>'.$Pulsanti.'
</div>';
			return $Html;
			break;
		default: return $Docenti;
	}
}
function OAD_crea_tabella_docenti(){
	global	$arrOrario;
	$Docenti=array();
	if(!is_null($arrOrario)){
		foreach($arrOrario["DOCENTI"]["DOCENTE"] as $id => $Docente){
	//		if(OAD_materiaInOrario($id)){
	//	echo "<pre>";var_dump($Docente);echo "</pre>";
			if(isset($Docente["COGNOME"]) And !is_array($Docente["NOME"]))
				$Cognome=$Docente["COGNOME"]." ";
			else
				$Cognome="";
			if(isset($Docente["NOME"]) And !is_array($Docente["NOME"])) 
				$Nome=$Docente["NOME"];
			else
				$Nome="";	
			$CognomeNome=$Cognome.$Nome;
			if($CognomeNome=="") 
				$CognomeNome=$Docente["ID"];
			if(isset($Docente["RICEVIMENTO"]))
				$Ricevimento=$Docente["RICEVIMENTO"];
			else
				$Ricevimento=array("GIORNO" => "ND","ORA"=>"ND");
			if(isset($Docente["DISPOSIZIONE"]))
				$Disposizione=DaArrayAStringa($Docente["DISPOSIZIONE"],"%s (%s)",", ");
			else
				$Disposizione="Nessuna";
			$Docenti[$Docente["ID"]]=array("Id"=>$id,"CognomeNome"=>$CognomeNome,"Ricevimento"=>$Ricevimento,"Disposizione"=>$Disposizione);		
	//		}
		}
	//	var_dump($Docenti);
		ksort($Docenti);		
	}
	?>
	<table>
		<thead>
			<tr>
				<th style="width: 4em;position: sticky; top: 0;text-align: left;background: white;">ID</th>
				<th style="width: 30em;padding-right:2em;position: sticky; top: 0;text-align: left;background: white;">Docente</th>
				<th style="width: 8em;position: sticky; top: 0;text-align: left;background: white;">Giorno Ric.</th>
				<th style="width: 8em;position: sticky; top: 0;text-align: left;background: white;">Ora Ric.</th>
				<th style="position: sticky; top: 0;text-align: left;background: white;">Disposizione</th>
				<th style="position: sticky; top: 0;text-align: left;background: white;">Materie</th>
			</tr>
		</thead>
		<tbody>
<?php	foreach($Docenti as $Docente => $datiDocente){?>
			<tr>
				<td style="border: 1px solid #ddd;"><?php echo $datiDocente["Id"];?></td>
				<td style="border: 1px solid #ddd;"><?php echo $datiDocente["CognomeNome"];?></td>
				<td style="border: 1px solid #ddd;"><?php echo $datiDocente["Ricevimento"]["GIORNO"];?></td>
				<td style="border: 1px solid #ddd;"><?php echo $datiDocente["Ricevimento"]["ORA"];?></td>
				<td style="border: 1px solid #ddd;"><?php echo $datiDocente["Disposizione"];?></td>
				<td style="border: 1px solid #ddd;"><?php echo OAD_materieDocente($datiDocente["Id"]);?></td>
			</tr>
<?php	}?>
		</tbody>
	</table>
<?php
}
function OAD_crea_tabella_sedi(){
	global	$arrOrario;
	?>
	<table>
		<thead>
			<tr>
				<th style="width: 4em;position: sticky; top: 0;text-align: left;background: white;">ID</th>
				<th style="position: sticky; top: 0;text-align: left;background: white;">Sede</th>
			</tr>
		</thead>
		<tbody>
<?php	if(!is_null($arrOrario)){
			foreach($arrOrario["SEDI"]["SEDE"] as $id =>$Sede){?>
			<tr>
				<td style="border: 1px solid #ddd;"><?php echo $id;?></td>
				<td style="border: 1px solid #ddd;"><?php echo $Sede;?></td>
			</tr>
<?php		}
		}?>
		</tbody>
	</table>
<?php
}
function OAD_crea_tabella_indirizzi(){
	global	$arrOrario;
	$Indirizzi=array();
	if(!is_null($arrOrario)){
		foreach($arrOrario["SPECS"]["SPEC"] as $id => $Indirizzo){
			$Indirizzi[$Indirizzo]=$id;
		}
		ksort($Indirizzi);
	}
	?>
	<table>
		<thead>
			<tr>
				<th style="width: 4em;position: sticky; top: 0;text-align: left;background: white;">ID</th>
				<th style="position: sticky; top: 0;text-align: left;background: white;">Indirizzo</th>
			</tr>
		</thead>
		<tbody>
<?php	foreach($Indirizzi as $Indirizzo => $id){?>
			<tr>
				<td style="border: 1px solid #ddd;"><?php echo $id;?></td>
				<td style="border: 1px solid #ddd;"><?php echo $Indirizzo;?></td>
			</tr>
<?php	}?>
		</tbody>
	</table>
<?php
}
function OAD_getElencoClassi($Visualizzazione="Array",$Pulsanti=""){
	global	$arrOrario;
	$Classi=array();
	if(!is_null($arrOrario)){
		foreach($arrOrario["CLASSI"]["CLASSE"] as $id => $Classe){
		$Classi[$Classe["ID"]]=array("Id"		=>$id,
								     "Classe"	=>$id,
								     "Sede"		=>$arrOrario["SEDI"]["SEDE"][$Classe["@attributes"]["ID-SEDE"]],
								     "Indirizzo"=>$arrOrario["SPECS"]["SPEC"][$Classe["@attributes"]["ID-SPEC"]]);
//		}
	}
//	var_dump($Docenti);
		ksort($Classi);
	}
	switch($Visualizzazione){
		case "Select":
			$Html='<div class="bootstrap-select-wrapper pt-5">
  <label class="labelNoFormat">Classi</label>
  <select id="ElencoClassi" title="Seleziona una Classe" data-live-search="true" data-live-search-placeholder="Cerca classe" class="w-75">
  	<option value="" title="Seleziona una Classe" data-content="Annulla selezione<span class="reset-label"></span>"></option>';
  	foreach($Classi as $id => $Classe){
 // 		var_dump($Classe);
  		$Html.= '<option value="'.$Classe["Id"].'">'.$id." (".Ucwords(strtolower($Classe["Indirizzo"])).')</option>';
	}
	$Html.= '  </select>'.$Pulsanti.'
</div>';
			return $Html;
			break;
		default: return $Classi;
	}
}
function OAD_getClasse($IdClasse){
	global	$arrOrario;
	if(isset($arrOrario["CLASSI"]["CLASSE"][$IdClasse]))
		return $arrOrario["CLASSI"]["CLASSE"][$IdClasse]["ID"];
	else
		return "Classe non definita";
}
function OAD_crea_tabella_classi(){
	global	$arrOrario;
	$Classi=array();
	if(!is_null($arrOrario)){
		foreach($arrOrario["CLASSI"]["CLASSE"] as $id => $Classe){
			$Classi[$Classe["ID"]]=array("Sede"=>$arrOrario["SEDI"]["SEDE"][$Classe["@attributes"]["ID-SEDE"]],"Indirizzo"=>$arrOrario["SPECS"]["SPEC"][$Classe["@attributes"]["ID-SPEC"]]);
		}
		ksort($Classi);
	}
	?>
	<table>
		<thead>
			<tr>
				<th style="width: 4em;position: sticky; top: 0;text-align: left;background: white;">Classe</th>
				<th style="position: sticky; top: 0;text-align: left;background: white;">Sede</th>
				<th style="position: sticky; top: 0;text-align: left;background: white;">Indirizzo</th>
			</tr>
		</thead>
		<tbody>
<?php	foreach($Classi as $IDClasse => $Classe){?>
			<tr>
				<td style="border: 1px solid #ddd;"><?php echo $IDClasse;?></td>
				<td style="border: 1px solid #ddd;"><?php echo $Classe["Sede"];?></td>
				<td style="border: 1px solid #ddd;"><?php echo $Classe["Indirizzo"];?></td>
			</tr>
<?php	}?>
		</tbody>
	</table>
<?php
}
function OAD_crea_tabella_strutture(){
	global	$arrOrario;
	$Strutture=array();
	if(!is_null($arrOrario)){
		foreach($arrOrario["STRUTTURE"]["STRUTTURA"] as $id => $Struttura){
			$Strutture[$Struttura]=$id;
		}
		ksort($Strutture);
	}
	?>
	<table>
		<thead>
			<tr>
				<th style="width: 4em;position: sticky; top: 0;text-align: left;background: white;">ID</th>
				<th style="position: sticky; top: 0;text-align: left;background: white;">Struttura</th>
			</tr>
		</thead>
		<tbody>
<?php	foreach($Strutture as $Struttura => $ID){?>
			<tr>
				<td style="border: 1px solid #ddd;"><?php echo $ID;?></td>
				<td style="border: 1px solid #ddd;"><?php echo $Struttura;?></td>
			</tr>
<?php	}?>
		</tbody>
	</table>
<?php
}
function OAD_CreaTabelleDati(){
	global $arrOrario;?>
	<div class="wrap">
		<div class="HeadPage">
			<h2 class="wp-heading-inline"><span class="dashicons dashicons-admin-settings" style="font-size:1em;"></span> <?php _e('Dati Orario','wpscuola');?></h2>
		</div>
		<div id="orario-tabelle-dati" style="margin-top:20px;">
			<ul>
				<li><a href="#Materie"><?php _e('Materie','wpscuola');?></a></li>
				<li><a href="#Docenti"><?php _e('Docenti','wpscuola');?></a></li>
				<li><a href="#Sedi"><?php _e('Sedi','wpscuola');?></a></li>
				<li><a href="#Indirizzi"><?php _e('Indirizzi','wpscuola');?></a></li>
				<li><a href="#Classi"><?php _e('Classi','wpscuola');?></a></li>
				<li><a href="#Strutture"><?php _e('Strutture','wpscuola');?></a></li>
				<li><a href="#Attivita"><?php _e('Attivita','wpscuola');?></a></li>
			</ul>
			<div id="Materie" style="height: 20em;overflow: scroll;">
				<?php OAD_crea_tabella_materie();?>
			</div>
			<div id="Docenti" style="height: 20em;overflow: scroll;">
				<?php OAD_crea_tabella_docenti();?>
			</div>
			<div id="Sedi" style="height: 20em;overflow: scroll;">
				<?php OAD_crea_tabella_Sedi();?>
			</div>
			<div id="Indirizzi" style="height: 30em;overflow: scroll;">
				<?php OAD_crea_tabella_indirizzi();?>
			</div>
			<div id="Classi" style="height: 20em;overflow: scroll;">
				<?php OAD_crea_tabella_classi();?>
			</div>
			<div id="Strutture" style="height: 20em;overflow: scroll;">
				<?php OAD_crea_tabella_strutture();?>
			</div>
			<div id="Attivita" style="height: 20em;overflow: scroll;">
				<?php OAD_daArrayATabella(OAD_getOrarioDocente(109));?>
				<?php OAD_daArrayATabella(OAD_getOrarioClasse(60));?>
				<?php OAD_daArrayATabella(OAD_getOrarioRicevimentoClasse(60));?>
				<?php OAD_daArrayATabella(OAD_getOrarioStruttura(90));?>
			</div>
		</div>
	</div>
<?php 
}
?>