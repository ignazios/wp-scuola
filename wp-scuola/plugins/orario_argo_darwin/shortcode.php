<?php
add_shortcode('orarioDarwin', 'OAD_publicShortCode');

function OAD_publicShortCode($Parametri){
	$ret="";
	$Parametri=shortcode_atts(array(
		'orari' => 'docenti;classi;aule;ricevimentoclasse',
	), $Parametri,"orarioDarwin");
	$ret=OAD_visualizzaOrario($Parametri);
	return $ret;
}

function OAD_visualizzaOrario($Parametri){
	OAD_caricaDati();
	ob_start();?>
<div class="container-fluid">
  <div class="row">
    <div class="col col-lg-3">
    	<?php echo OAD_getElencoDocenti("Cognome,Nome","Select",'<span id="OrarioDocente" title="Visualizza orario docente" class="fas fa-calendar-alt fa-2x text-primary align-middle icoGrab"></span>').
		OAD_getElencoClassi("Select",'<span id="OrarioClasse" title="Visualizza orario della Classe" class="fas fa-calendar-alt fa-2x text-primary align-middle icoGrab"></span> 
	<span id="OrarioRicevimentoDocenti" title="Visualizza orario ricevimento docenti della classe" class="fas fa-comments fa-2x align-middle text-danger icoGrab pl-2"></span>').
		OAD_getElencoStrutture("Select",'<span id="OrarioStruttura" title="Visualizza orario struttura" class="fas fa-calendar-alt fa-2x text-primary align-middle icoGrab"></span>');?>
    </div>
    <div class="col col-lg-9">
    	<div class="table-responsive" id="TabellaOrario">

		</div>
    </div>
  </div>
</div>
<?php	
	return ob_get_clean();
}