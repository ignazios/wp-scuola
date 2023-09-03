<?php
/*
Plugin Name:Prenotazioni
Plugin URI: http://www.eduva.org
Description: Plugin utilizzato per delle risorse Aule, Sale conferenza, Laboratori, etc...
Version:1.6.6
Author: Scimone Ignazio
Author URI: http://www.eduva.org
License: GPL2
    Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : info@sisviluppo.info)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
	
include_once(dirname (__FILE__) .'/functions.inc.php');				/* Various functions used throughout */
//include_once(dirname (__FILE__) .'/AlboPretorio.widget.inc');
define("Prenotazioni_URL",get_template_directory_uri().'/plugins/prenotazioni/');
define("Prenotazioni_DIR",dirname (__FILE__));
include_once ( dirname (__FILE__) . '/lib/class_spazi.inc.php' );
include_once ( dirname (__FILE__) . '/lib/class_prenotazioni.inc.php' );
include_once ( dirname (__FILE__) . "/lib/tab_pre_gg.php");
if (!class_exists('Plugin_Prenotazioni')) {
class Plugin_Prenotazioni {
	
	var $minium_WP   = '5';
	var $options     = '';
	
	function __construct() {
		global $G_Spaces;
		if ( ! function_exists( 'get_plugins' ) )
	 		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	    $plugins = get_plugins( "/".plugin_basename( dirname( __FILE__ ) ) );
            $plugin_nome = basename( ( __FILE__ ) );
		// Inizializzazioni
		
		$this->define_tables();
		$this->plugin_name = plugin_basename(dirname( __FILE__ ) );
		// Hook per attivazione/disattivazione plugin
		$G_Spaces=new Spazi();
//		register_activation_hook( __FILE__, array(&$this, 'activate'));
//		register_deactivation_hook(__FILE__, array(&$this, 'deactivate') );	
//		register_uninstall_hook( __FILE__, array(&$this, 'uninstall') );
		// Hook di inizializzazione che registra il punto di avvio del plugin
		add_action( 'admin_enqueue_scripts',    	array(&$this,'enqueue_scripts') );
		add_action( 'init',                      	array(&$this, 'update_Prenotazioni_settings'));
		add_action( 'wp_enqueue_scripts',       	array(&$this, 'head_Front_End'));
		add_action( 'admin_menu',               	array(&$this, 'add_menu') ); 
		add_action( 'wp_ajax_prenSpazi',        	array(&$this,'getPrenotazioniSpazi'));
		add_action( 'wp_ajax_FEprenSpazi',      	array(&$this,'getPrenotazioniSpazi'));
		add_action( 'wp_ajax_StampPrenSpazi',   	array(&$this,'staPrenotazioniSpazi'));
//		add_action( 'wp_ajax_nopriv_VisPrenSpazi',  array(&$this,'VisPrenotazioniSpazi'));
		add_action( 'wp_ajax_VisPrenSpazi',     	array(&$this,'VisPrenotazioniSpazi'));
		add_action( 'wp_ajax_delPren',          	array(&$this,'deletePrenotazioniSpazi'));
		add_action( 'wp_ajax_newPren',          	array(&$this,'nuovaPrenotazioneSpazi'));
        add_filter( 'manage_posts_columns',  		array(&$this,'SpaziNuoveColonne'),10,1);
        add_action( 'manage_posts_custom_column',	array(&$this,'SpaziNuoveColonneContenuto'), 10, 2); 
		add_shortcode('Prenotazioni',           	array(&$this, 'FrontEndPrenotazioni'));
		add_shortcode('OccupazioneSpazio',      	array(&$this, 'FEOccupazioneSpazio'));
		
		if((get_option('wps_ModuloPrenotazioni')!= 'Si'||!get_option('wps_ModuloPrenotazioni')) And get_theme_mod("scuola_prenotazioni_attiva")){
			$this->activate();
			update_option('wps_ModuloPrenotazioni', 'Si');
		}
		if(get_option('wps_ModuloPrenotazioni')== 'Si' And !get_theme_mod("scuola_prenotazioni_attiva")){
			$this->uninstall();
			update_option('wps_ModuloPrenotazioni', 'No');
		}
	}
	function FrontEndPrenotazioni($Para){
	    ob_start();
           $Para=shortcode_atts(array('schede'=> 'nuovo,statistiche,spazi',
                                      'titoli'=> 'Dati Nuova Prenotazione,Statische mie Prenotazioni,Catalogo Spazi'),$Para,"Prenotazioni");
           if (is_file(get_stylesheet_directory() . '/plugins/prenotazioni/lib/frontend.php')){
		   		include_once ( get_stylesheet_directory() . '/plugins/prenotazioni/lib/frontend.php' );		
		   }else{
	           include_once ( dirname (__FILE__) . '/lib/frontend.php' );	   	
		   }
		return ob_get_clean();   
	}
	
	function getPrenotazioniSpazi(){
        check_ajax_referer('WPScuolaSecret','security');
        $Data=filter_input(INPUT_POST,"data");
        $Spazio=filter_input(INPUT_POST,"spazio");
        $Sorgente=filter_input(INPUT_POST,"sorg");
//       echo $Data." - ".$Spazio." - ".$Sorgente; die();
		if($Sorgente=="FE")
			echo createTablePrenotazioniSpazio($Spazio,$Data);
		else
			echo createTablePrenotazioni($Data);
		die();
	}
	function deletePrenotazioniSpazi(){
        check_ajax_referer('WPScuolaSecret','security');
		global $Gest_Prenotazioni;
        $Appuntamento=filter_input(INPUT_POST,"id");
		$ris=$Gest_Prenotazioni->delPrenotazione($Appuntamento);
		echo "Ho cancellato l' appuntamenti";
		die();
	}
	function nuovaPrenotazioneSpazi(){
		global $Gest_Prenotazioni;
        check_ajax_referer('WPScuolaSecret','security');
		$ris=$Gest_Prenotazioni->newPrenotazione($_POST['data'],$_POST['OraI'],$_POST['Ore'],$_POST['IdS'],$_POST['NSet'],$_POST['Note']);
		echo "<p id='TestoRisMemo'>Risultato prenotazione:<br />".$ris."</p>";
		die();
	}
	function enqueue_scripts( $hook_suffix ) {
        if($hook_suffix=="widgets.php") return;
            wp_enqueue_script('jquery');
            wp_enqueue_script( 'jquery-ui-core',		false, array('jquery'), false, false);
            wp_enqueue_script( 'jquery-ui-datepicker', 	false, array('jquery'), false, false);
            wp_enqueue_script( 'jquery-ui-widget', 		false, array('jquery'), false, false);
            wp_enqueue_script( 'jquery-ui-tabs', 		false, array('jquery'), false, false);
            wp_enqueue_script( 'jquery-ui-dialog', 		false, array('jquery'), false, false);
            wp_enqueue_script( 'jquery-ui-slider', 		false, array('jquery'), false, false);
            wp_enqueue_script( 'wp-color-picker');         
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'wp-jquery-ui-dialog' );
            wp_enqueue_style( 'jquery.ui.theme', Prenotazioni_URL. 'css/jquery-ui-custom.css');
            wp_register_style($this->plugin_name,  Prenotazioni_URL. 'css/style.css');
            wp_enqueue_style( $this->plugin_name);
           if($hook_suffix == 'toplevel_page_Prenotazioni' And isset($_GET["PreviewPrint"])) {
                wp_register_style("Prenotazioni_AnteprimaStampa", Prenotazioni_URL.  'css/printPre.css');
                wp_enqueue_style( "Prenotazioni_AnteprimaStampa");
                wp_enqueue_script( 'Prenotazioni_AnteprimaStampa', Prenotazioni_URL. 'js/printPre.js');
            }
            wp_enqueue_script( 'Prenotazioni-admin-fields', Prenotazioni_URL. 'js/Prenotazioni.js');?>
<script type='text/javascript'>var prenajaxsec = "<?php echo wp_create_nonce('WPScuolaSecret');?>";</script>
<?php
	}
	function head_Front_End() {
		global $post;
		if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'Prenotazioni') ) {
      		wp_enqueue_script('Prenotazioni-FrontEnd', Prenotazioni_URL. 'js/Prenotazioni_FrontEnd.js');
      		wp_enqueue_style( 'prenotazioni-style', Prenotazioni_URL . "css/style.css");
      		wp_enqueue_script('Prenotazioni-FrontEnd_tooltip', Prenotazioni_URL. 'js/Prenotazioni_tooltip.js',array(),null,true);
            wp_enqueue_style( 'jquery.ui.theme', Prenotazioni_URL. 'css/jquery-ui-custom.css');
            wp_enqueue_script( 'jquery-ui-dialog', 		false, array('jquery'), false, false);?>
<script type='text/javascript'>var prenajaxsec = "<?php echo wp_create_nonce('WPScuolaSecret');?>";</script>
<?php
		}
	}
	function add_menu(){
  		add_menu_page('Panoramica', 'Prenotazioni', 'read', 'Prenotazioni',array( $this,'show_menu'),Prenotazioni_URL."img/logo.png",24);
  		$parametri_page=add_submenu_page( 'Prenotazioni', 'Parametri', 'Parametri', 'manage_options', 'config', array( $this,'show_menu'));
		$prenotazioni_page=add_submenu_page( 'Prenotazioni', 'Gestione Prenotazioni', 'Gestione Prenotazioni', 'read', 'gest_prenotazioni', array( $this,'show_menu'));
		$prenotazioni_my_page=add_submenu_page( 'Prenotazioni', 'Prenotazioni', 'Mie Prenotazioni', 'read', 'myprenotazioni', array( $this,'show_menu'));
		$prenotazioni_about=add_submenu_page( 'Prenotazioni', 'Prenotazioni', 'Bacheca', 'edit_posts', 'bacheca', array( $this,'show_menu'));
}
	function staPrenotazioniSpazi(){
            $Spazio=filter_input(INPUT_POST, "IdSpazio");
            $Settimana=filter_input(INPUT_POST, "Settimana");
            $Anno=filter_input(INPUT_POST, "Anno");
            createTablePrenotazioniSpazioSettimana($Spazio,$Settimana,$Anno);
            die();
        }
	function VisPrenotazioniSpazi(){
            check_ajax_referer('publicsecretOccupazioneSettimanale','secur');
            $Spazio=filter_input(INPUT_POST, "IdSpazio");
            $Settimana=filter_input(INPUT_POST, "Settimana");
            $Anno=filter_input(INPUT_POST, "Anno");
            $Para=filter_input(INPUT_POST, "para");
            $Para= unserialize($Para);
            createTablePrenotazioniSpazioSettimanaFE($Para,$Spazio,$Settimana,$Anno);
             die();
        }
	function FEOccupazioneSpazio($Para){
            $Para=shortcode_atts(array('titolo' 			=> 'Piano occupazione Spazio',
                                       'didascalia' 		=> 'si',
                                       'etichetta_spazio' 	=> 'Spazio',
                                       'coloreoccupato' 	=> 'def',
                                       'visibilita' 		=> 'Tutti'),$Para,"OccupazioneSpazio");
            $IDSpazio= get_the_ID();
            ob_start();
            echo createTablePrenotazioniSpazioSettimanaFE($Para,$IDSpazio,date("W"),date("Y"));
            return ob_get_clean();
        }       
        function show_menu() {
		global $Gest_Prenotazioni;
		switch ($_REQUEST['page']){
			case "myprenotazioni" :
				$Gest_Prenotazioni->Tabella_Mie_Prenotazioni();		
				break;
			case "gest_prenotazioni" :
				$Gest_Prenotazioni->Tabella_Giornaliera_Prenotazioni();		
				break;
			case "config" :
				$this->Prenotazioni_config();
				break;
			case "bacheca" :
				$this->Bacheca();
				break;
			case "Prenotazioni" :
		        if(isset($_REQUEST["op"]) And $_REQUEST["op"]=="rsettimanale"){
		        	if(current_user_can( 'edit_posts')){
			            $IdEvento= (isset($_GET['event_id'])?$_GET['event_id']:0);
			            $NSettimana=(isset($_GET['settimana'])?$_GET['settimana']:0);
			            $Anno=(isset($_GET['anno'])?$_GET['anno']:0);
			            createTablePrenotazioniSpazioSettimana($IdEvento,$NSettimana,$Anno);					}else{
						wp_die("Non sei autorizzato ad accedere a questa pagina","Accesso risorsa");
					}
		        }else{				
			        $this->Bacheca("Base");
				}				
				break;
		}
	}
        public function Bacheca($Cosa=""){
 ?> 
<div id="fb-root"></div>
    <div class="wrap">
            <h2>Bacheca</h2>
            <div id="welcome-panel" class="welcome-panel">
                <div style="float:left;">
                    <img src="<?php echo Prenotazioni_URL."img/icona.png";?>" />
                </div>
                <h3>Applicativo per la gestione delle prenotazioni di spazi</h3>
                <p><em>By Ignazio Scimone</em></p>
            </div>
<?php
	if($Cosa=="Base"){
		echo "
	</div>
</div>";
		return;	
	}
?>
            <div id="welcome-panel" class="welcome-panel">
                <h3>Informazioni</h3>
                <div  style="float:right;">
                    <img src="<?php echo Prenotazioni_URL."img/Prenotazioni.png";?>" />
                </div>                        
                <p>Questo plugin permette la gestione delle prenotazioni di spazi come aule, laboratori, sale conferenza etc..<br />
Le prenotazioni possono essere realizzate solo dagli utenti registrati al sito mentre i visitatori del sito possono solo vedere il catalogo degli spazi messi a disposizione dalla struttura ed opzionalmente le prenotazioni dei singoli spazi.<br />
Il plugin utilizza Jquery massivamente sia nel BackEnd che nella FrontEnd del sito.</p>
                <h3>ShortCode</h3>
                <ul style="list-style-type: disc;margin-left:15px;">
                    <li><strong>[Prenotazioni schede="nuovo,statistiche,spazi" titoli="Dati Nuova Prenotazione,Statische mie Prenotazioni,Catalogo Spazi"]</strong>
                        <p>Questo shortcode inserito in una pagina, permette la visualizzazione nel FrontEnd del sito la visualizzazione dell'area Prenotazioni.<br />
                           Per gli utenti loggati viene visualizzata una finestra che può essere suddivisa di tre schede:
                        <ul style="list-style-type: square;margin-left:15px;">
                            <li>Nella scheda <strong>Nuovo</strong> si possono effettuare le prenotazioni</li>
                            <li>Nella scheda <strong>Statistiche</strong> vengono visualizzate le <em>Ultime 5 prenotazione passate</em> - le <em>Prenotazioni di oggi</em> - le <em>Prossime 5 Prenotazioni</em></li>
                            <li>Nella schda <strong>Catalogo Spazi</strong> viene visualizzato il catalogo degli spazi della gestione</li>
                        </ul> 
                        lo shortcode ha  due parametri:
                        <ul style="list-style-type: square;margin-left:15px;">
                            <li><strong>schede</strong> valore di default <em>nuovo,statistiche,spazi</em> [Testi separati da virgola] definisce le scede che devono essere visualizzate, indicarne 1,2 oppure tutte e 3 nel caso si indica una sola scheda, viene visualizzata senza la linguette ed il titolo viene visualizzata come titolo della finestra</li>
                            <li><strong>titoli</strong> valore di default <em>Dati Nuova Prenotazione,Statische mie Prenotazioni,Catalogo Spazi</em> [Testi separati da virgola] obbligatoriamente tre elementi in corrispondenza della scheda se non presente la scheda indicare solo la virgola</li>
                        </ul> 
                        </p>
                        <p>
                            es. [Prenotazioni schede="nuovo,spazi" titoli="Nuova prenotazione,,Catalogo Spazi Disponibili"]<br />
                            questa configurazione di shortcode permette la visualizzazione della finestra informativa sulle prenotazioni dello spazio come riportato nellimmagine di esempio sulla destra.
                        </p>
                    </li>
                    <li>
                        <strong>[OccupazioneSpazio titolo="Piano occupazione Spazio" didascalia ="si" etichetta_spazio ="Spazio" coloreoccupato ="def" visibilita ="Tutti"]</strong>
                        <div  style="float:left;padding-right:20px;">
                        <img src="<?php echo Prenotazioni_URL."img/OccupazioneSpazio.png";?>" />
                        </div>
                        <p><strong><strong><span style="color:red;">Questo shortcode inserito in uno spazio</span></strong>, permette la visualizzazione nel FrontEnd della tabella delle prenotazioni dello specifico spazio.<br />
                           I parametri sono
                        <ul style="list-style-type: square;margin-left:15px;">
                            <li><strong>titolo</strong> valore di default <em>Piano occupazione Spazio</em> [Testo Libero] si può definire un testo personalizzato per ogni spazio </li>
                            <li><strong>didascalia</strong> valore di default <em>si</em> [si/no] indica se visualizzare o meno la didascalia con il colore che indica l'occupazione dello spazio nello specifico giorno/ora</li>
                            <li><strong>etichetta_spazio</strong> valore di default <em>Spazio</em> [Testo libero] testo che viane visualizzato prima del nome dello spazio</li>
                            <li><strong>coloreoccupato</strong> valore di default <em>defo</em> [def/colore in base alle specifiche CSS] def indica il colore definito nelle impostazioni, altrimenti può essere inserito un colore con il nome mnemonico es. red o il valore esadecimale es. #1f4f7a</li>
                            <li><strong>visibilità</strong> valore di default <em>Tutti</em> [Tutti/Visitatori/Utenti] se viene specificato Tutti, la finestra con le informazioni verrà visualizzata a tutti a prescindere se sono loggati o meno<br />Visitatori, la finestra viene visualizzata solo ai visitatori e non agli utenti loggato<br />Utenti, la finestra viene visualizzata solo agli utenti loggati.</li>
                        </ul> 
                        <br />
                        <p>
                            es. [OccupazioneSpazio titolo="Piano settimanale occupazione" etichetta_spazio="Laboratorio" coloreoccupato="red" didascalia="si"]<br />
                            questa configurazione di shortcode permette la visualizzazione della finestra informativa sulle prenotazioni dello spazio come riportato nellimmagine di esempio sulla sinistra.
                        </p>
                        <p>Selezionando una settimana dalla combobox verrà aggiornata la tabella senza il ricaricamento della pagina attraverso chiamata ajax.</p>
                        </p>
                    </li>
                </ul>
            </div>
            <div id="welcome-panel" class="welcome-panel">
                <h3>Contatti</h3>
                <div style="float:left;margin-top:20px;">
                    <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2FPrenotazioni-756571287730947%2F&tabs=timeline&width=340&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId=315161832278306" width="340" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
                </div>
                <ul style="list-style-type: square;margin-left:400px;">
                    <li>email:<a href="mailto:ignazio.scimone@gmail.com">ignazio.scimone@gmail.com</a></li>
                    <li>Sito Web:<a href="http://www.eduva.org" target="_blank">http://www.eduva.org</a></li>
                </ul>
            </div>
            </div>
<?php
        }

	function Prenotazioni_config(){
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
            $Comunicazioni=array("OggettoAdmin" =>"Prenotazione registrata",
            					 "MsgAdmin"     =>"Ti volevo informare che è stata registrata una prenotazione:

Dettagli prenotazione

Nome  : #_PRENUTENTE
Email : #_PRENMAIL
Spazio: #_PRENSPAZIO
Data  : #_PRENDATA  
Ore   : #_PRENORAE

Note
#_PRENNOTE",
            					 "OggettoUte" =>"Prenotazione registrata",
            					 "MsgUte"     =>"Ti confermiamo la tua prenotazione:

Dettagli prenotazione

Spazio: #_PRENSPAZIO
Data  : #_PRENDATA  
Ore   : #_PRENORAE

Note
#_PRENNOTE");
            $M  =  get_option('opt_PrenotazioniMail');
            if($M!==false)
                  $Comunicazioni=unserialize($M);            
            for($i=0;$i<7;$i++)
                   if(isset($Parametri['Giorni'][$i]) And $Parametri['Giorni'][$i]==1)
                          ${"GD_".$i."_SEL"}=" checked ";
                   else
                   		  ${"GD_".$i."_SEL"}="";
            //var_dump($Parametri);
        echo '
            <div class="wrap">
                  <h2><span class="dashicons dashicons-admin-tools" style="font-size:1.3em;margin-right:0.5em;"></span> Parametri Prenotazioni</h2>
            <form name="Prenotazioni_Parametri" action="'.get_bloginfo('wpurl').'/wp-admin/index.php" method="post">
			  <div id="config-tabs-container" style="margin-top:20px;">
				<ul>
					<li><a href="#Conf-tab-1">Impostazioni Generali</a></li>
					<li><a href="#Conf-tab-2">Email</a></li>
				</ul>	 
				<div id="Conf-tab-1">
		        <table class="widefat tabepar">
                  <tr valign="top">
                          <th scope="row">Fascia oraria disponibilità risorse</th>
                          <td>
                                  <input type="hidden" id="OI" name="OraInizio" value="'.$Parametri['OraInizio'].'">
                                  <input type="hidden" id="OF" name="OraFine" value="'.$Parametri['OraFine'].'">
                                  <input type="text" id="dispo-valore-range" style="width:60px;background-color: inherit;border: none;">
                          <div id="dispo-range" style="width:200px;"></div></td>
                  </tr>
                  <tr valign="top">
                          <th scope="row">Giorni disponibili per la prenotazione</th>
                          <td>
                                  <input type="checkbox" name="GD_l" value="1" '.$GD_0_SEL.' id="GD_1"/>Lun
                                  <input type="checkbox" name="GD_m" value="1" '.$GD_1_SEL.' id="GD_2"/>Mar
                                  <input type="checkbox" name="GD_e" value="1" '.$GD_2_SEL.'  id="GD_3"/>Mer
                                  <input type="checkbox" name="GD_g" value="1" '.$GD_3_SEL.'  id="GD_4"/>Gio
                                  <input type="checkbox" name="GD_v" value="1" '.$GD_4_SEL.'  id="GD_5"/>Ven
                                  <input type="checkbox" name="GD_s" value="1" '.$GD_5_SEL.'  id="GD_6"/>Sab
                                  <input type="checkbox" name="GD_d" value="1" '.$GD_6_SEL.'  id="GD_7"/>Dom
                          </td>
                  </tr>	
                  <tr valign="top">
                          <th scope="row">Colore Spazio non disponibile</th>
                          <td> 
                                  <input type="text" id="ColNonDisponibile" name="ColNonDisponibile" size="5" value="'.$Parametri["ColNonDisponibile"].'"/>
                          </td>
                  </tr>
                  <tr valign="top">
                          <th scope="row">Colore Ore Riservate</th>
                          <td> 
                                  <input type="text" id="coloreRiservato" name="coloreRiservato" size="5" value="'.$Parametri["ColRiservato"].'"/>
                          </td>
                  </tr>
                  <tr valign="top">
                          <th scope="row">Colore Ore Prenotate</th>
                          <td> 
                                  <input type="text" id="colorePrenotato" name="colorePrenotato" size="5" value="'.$Parametri["ColPrenotato"].'"/>
                          </td>
                  </tr>		
                  <tr valign="top">
                          <th scope="row">Colore Ore non Prenotabili</th>
                          <td> 
                                  <input type="text" id="colorenonprenotabile" name="colorenonprenotabile" size="5" value="'.$Parametri["ColNonPrenotabile"].'"/>
                          </td>
                  </tr>		
                  <tr valign="top">
                          <th scope="row">Numero Massimo di ore prenotabili</th>
                          <td> 
                                  <input type="text" id="max-ore-valore" name="maxOre" style="width:60px;background-color: inherit;border: none;" value="'.$Parametri["MaxOrePrenotabili"].'" />
                                  <div id="max-ore-range" style="width: 100px;"></div>
                          </td>
                  </tr>	
                  <tr valign="top">
                          <th scope="row"><label for="entro">Numero ore entro cui bisogna fare le prenotazioni</label></th>
                          <td> 
                                  <input type="text" id="entro" name="entro" style="width:60px;background-color: inherit;border: none;" value="'.$Parametri["PrenEntro"].'" />
                          </td>
                  </tr>			
                  <tr valign="top">
                          <th scope="row"><label for="setpre">La Prenotazione può essere effettuata dal Lunedi al Venerdi della settimana precedente</label></th>
                          <td> 
                                  <input type="checkbox" id="setpre" name="setpre"  value="1" '.($Parametri["PrenSetPre"]==1?"checked":"").' />
                          </td>
                  </tr>		            
                  <tr valign="top">
                          <th scope="row"><label for="setpre">I Dati delle prenotazioni nel BackEnd devono essere visibili a tutti</label></th>
                          <td> 
                                  <input type="checkbox" id="vispubbatipren" name="vispubbatipren"  value="1" '.($Parametri["VisPubDatiPren"]==1?"checked":"").' />
                          </td>
                  </tr>		  
              </table>
   		</div>
        <div id="Conf-tab-2">
	        <table class="widefat">
	            <tr class="em-header">
		            <td colspan="2">
						<h3>Email di prenotazione Amministratore</h3>
						<em>Questa email viene inviata all\'amministratore del sito quando la prenotazione di uno spazio viene memorizzata.</em>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width:20%;">Oggetto email Amministratore</th>
				    <td>
						<input name="oggetto_em_amm" type="text" id="oggetto_em_amm" value="'.$Comunicazioni['OggettoAdmin'].'" size="45" />			
				    		    	<br />
									<em>È possibile disattivare questa email lasciando vuoto il soggetto.</em>
					</td>
				</tr>
				<tr valign="top">
				<th scope="row">Testo email di prenotazione Amministratore </th>
					<td>
						<textarea name="corpo_em_amm" id="corpo_em_amm" rows="10" style="width:100%;">'.$Comunicazioni['MsgAdmin'].'</textarea>			
					</td>
				</tr>
	            <tr class="em-header">
		            <td colspan="2">
						<h3>Email di prenotazione Utente</h3>
						<em>Questa email viene inviata all\'utente quando effettua la prenotazione di uno spazio.</em>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width:20%;">Oggetto email Utente</th>
				    <td>
						<input name="oggetto_em_ute" type="text" id="oggetto_em_ute" value="'.$Comunicazioni['OggettoUte'].'" size="45" />			
				    	<br />
						<em>È possibile disattivare questa email lasciando vuoto il soggetto.</em>
					</td>
				</tr>
				<tr valign="top">
				<th scope="row">Testo email di prenotazione Amministratore </th>
					<td>
						<textarea name="corpo_em_ute" id="corpo_em_ute" rows="10" style="width:100%;">'.$Comunicazioni['MsgUte'].'</textarea>			
					</td>
				</tr>
				<tr valign="top">
				<th scope="row"><h3>Segnaposto da utilizzare nel corpo delle mail</h3></th>
					<td>
						<ul>
							<li>#_PRENUTENTE - Dati Utente: ID Cognome Nome</li>
							<li>#_PRENMAIL   - Email dell\'utente che ha prenotato</li>
							<li>#_PRENSPAZIO - Nome dello spazio prenotato</li>
							<li>#_PRENDATA   - Data/Date (per prenotazioni su più settimane) della prenotazione</li>
							<li>#_PRENORAE   - Ore prenotate Dalle 00.00 alle 00.00</li>
							<li>#_PRENNOTE   - Note inserite nella prenotazione</li>
						</ul>	
					</td>
				</tr>
			</table>
   		</div>      
            </form>
              <p class="submit">
                  <input type="hidden" id="origine" name="origine" value="Salva_Opzioni_Prenotazioni">
                  <input type="submit" name="Prenotazioni_submit_button" value="Salva Modifiche" />
              </p>     	  
            </div>';
	}
	function define_tables() {		
		global $wpdb,$table_prefix;
		$wpdb->table_prenotazioni = $table_prefix . "prenotazioni_spazi";
	}
	static function activate() {
		global $wpdb;
		create_Pre_Tabelle($wpdb->table_prenotazioni); 
		if(!get_option('opt_PrenotazioniParametri')|| get_option('opt_PrenotazioniParametri')== '' ){
	        $Parametri=array("OraInizio" =>7,
	                        "OraFine"            => 20,
	                        "Giorni"             => array(0,0,0,0,0,0,0),
	                        "ColNonPrenotabile"  =>"#EBEBEB",
	                        "ColNonDisponibile"  =>"#b6b5b5",
	                        "ColRiservato"       =>"#FF0000",
	                        "ColPrenotato"       =>"#0000FF",
	                        "MaxOrePrenotabili"  => 6,
	                        "PrenEntro"          => 12,
	                        "PrenSetPre"         => 0,
	                        "VisPubDatiPren"	 =>0);
			$P=serialize($Parametri);
			add_option('opt_PrenotazioniParametri',$P);
		}
		if(!get_option('opt_PrenotazioniMail')|| get_option('opt_PrenotazioniMail')== '' ){
	        $Comunicazioni=array("OggettoAdmin" =>"",
	        					 "MsgAdmin"     =>"",
	        					 "OggettoUte" =>"",
	        					 "MsgUte"     =>"");		
			$M=serialize($Comunicazioni);
			update_option('opt_PrenotazioniMail',$M);
		}
	}  	 
	static function deactivate() {
	}
	static function uninstall() {
	}
	function update_Prenotazioni_settings(){
	    if(isset($_POST['origine']) And $_POST['origine'] == 'Salva_Opzioni_Prenotazioni'){	
			$Comunicazioni["OggettoAdmin"]=$_POST['oggetto_em_amm'];
			$Comunicazioni["MsgAdmin"]=$_POST['corpo_em_amm'];
			$Comunicazioni["OggettoUte"]=$_POST['oggetto_em_ute'];
			$Comunicazioni["MsgUte"]=$_POST['corpo_em_ute'];
		    $Parametri['OraInizio']=$_POST['OraInizio'];
		    $Parametri['OraFine']=$_POST['OraFine'];
		    $Parametri['ColNonPrenotabile']=$_POST['colorenonprenotabile'];
		    $Parametri['ColNonDisponibile']=$_POST['ColNonDisponibile'];
		    $Parametri['ColRiservato']=$_POST['coloreRiservato'];
		    $Parametri['ColPrenotato']=$_POST['colorePrenotato'];
		    $Parametri['MaxOrePrenotabili']=$_POST['maxOre'];
		    $Parametri['PrenEntro']=$_POST['entro'];
            $Parametri['PrenSetPre']=isset($_POST['setpre'])?1:0;
            $Parametri["VisPubDatiPren"]=isset($_POST['vispubbatipren'])?1:0;
			if (isset($_POST['GD_l']))
				$Parametri['Giorni'][0]=1;
			if (isset($_POST['GD_m']))
				$Parametri['Giorni'][1]=1;
			if (isset($_POST['GD_e']))
				$Parametri['Giorni'][2]=1;
			if (isset($_POST['GD_g']))
				$Parametri['Giorni'][3]=1;
			if (isset($_POST['GD_v']))
				$Parametri['Giorni'][4]=1;
			if (isset($_POST['GD_s']))
				$Parametri['Giorni'][5]=1;
			if (isset($_POST['GD_d']))
				$Parametri['Giorni'][6]=1;
			$P=serialize($Parametri);
			$M=serialize($Comunicazioni);
			update_option('opt_PrenotazioniParametri',$P);
			update_option('opt_PrenotazioniMail',$M);
			header('Location: '.get_bloginfo('wpurl').'/wp-admin/admin.php?page=config&update=true'); 
 		}
	}
    // Nuova Colonna Report  
        function SpaziNuoveColonne($defaults) {  
            if (isset($_GET['post_type']) And $_GET['post_type']=="spazi"){
                if (current_user_can('edit_posts')){
                            $defaults['report'] = 'Report';  
                    }
            }
           return $defaults;  
        }  
  
    // Visualizzazione nuova colonna Report  
    function SpaziNuoveColonneContenuto($column_name, $post_ID) {  
        if (isset($_GET['post_type']) And $_GET['post_type']=="spazi"){
            if ($column_name == 'report' And current_user_can( 'edit_posts')) {
                echo '<a href="'.admin_url().'edit.php?post_type=spazi&page=Prenotazioni&op=rsettimanale&event_id='.$post_ID.'">Settimanale</a>';
            }  
        }
    }  
}
	global $App_Prenotazioni,$Gest_Prenotazioni,$G_Spaces;
	$App_Prenotazioni = new Plugin_Prenotazioni();
	$Gest_Prenotazioni = new Prenotazioni();
}
?>