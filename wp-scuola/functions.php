<?php
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			function.php
 * Description:       	File principale in cui vengono definite le impostazioni principale del tema
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
// Inclusione libreria personalizzazione dei menu
include_once(get_template_directory().'/inc/class-wp-bootstrap-navwalker.php');
// Inclusione libreria personalizzazione link
require get_template_directory() . '/plugins/link/custom_link.php';
// Inclusione libreria per la personalizzazione delle impostazioni del tema
require get_template_directory() . '/inc/customizer.php';
// Inclusione libreria per la personalizzazione dell'elenco delle categorie
// Inclusione libreria per la personalizzazione dell'elenco delle categorie
require get_template_directory() . '/inc/my_class-walker-category.php';

/* UPDATER THEME VERSION */
require 'inc/theme-update-checker.php';
$update_checker = new ThemeUpdateChecker(
    'wp-scuola',
    'https://raw.githubusercontent.com/ignazios/wp-scuola/master/wp-scuola.json'
);

add_filter( 'image_size_names_choose', 		'scuola_image_sizes');
add_filter( 'wp_title', 					'scuola_filter_wp_title');
add_filter( 'get_comments_number', 			'scuola_comments_number');
add_filter( 'language_attributes', 			'add_opengraph_doctype');
add_filter( 'excerpt_more', 				'new_excerpt_more');
add_filter( 'excerpt_length', 				'custom_excerpt_length', 10 );
add_filter( 'pre_get_posts',				'scuola_SearchFilter');
add_filter( 'the_password_form', 			'scuola_password_form' );
add_filter( 'manage_posts_columns', 		'scuola_posts_column_views' );
add_filter( 'render_block', 				'personaliza_file_render', 10, 3);
add_filter( 'wp_get_attachment_image_attributes', 'scuola_attributi_img',10,2);
add_filter( 'sanitize_file_name', 			'scuola_ripulisci_filenames', 10, 1 );
add_filter( 'upload_mimes', 				'my_myme_types', 1, 1 );
/**
* Disattiva il gestore a Blocchi dei Widget 
*/
add_filter( 'use_widgets_block_editor', '__return_false' );
/**
* Riattiva la gestione dei link standard di Wordpress 
* I link vengono utilizzati in home page nel widget GalleraLinks
*/
add_filter( 'pre_option_link_manager_enabled', '__return_true' );
add_filter( 'widget_text', 'do_shortcode' );

add_action( 'wp_enqueue_scripts', 			'enqueue_scuola_public' );
add_action( 'comment_form_before', 			'scuola_enqueue_comment_reply_script');
add_action( 'init',							'scuola_inizialize');
add_action( 'widgets_init', 				'scuola_register_Widget' );
add_action( 'customize_register', 			'scuola_customize_register');
add_action( 'wp_head', 						'scuola_opengraph');
add_action( 'wp_head', 						'scuola_customize_head' );
add_action( 'enqueue_block_editor_assets', 	'gutenberg_styles' );
//add_action( 'wp_footer', 					'scuola_customize_footer');
add_action( 'admin_enqueue_scripts', 		'enqueue_scuola_admin' );
add_action( 'after_setup_theme', 			'scuola_setup');
add_action( 'manage_posts_custom_column', 	'scuola_posts_custom_column_views' );
add_action( 'login_head', 					'scuola_custom_login_logo');
add_action( 'admin_init', 					'mytheme_add_editor_styles' );
add_action( 'template_redirect', 			'Gestione_DwnLink');
add_action( 'wp_ajax_ResetCounter',			'scuola_reset_counter' );
add_action( 'add_attachment', 				'scuola_ripulisci_titolofile' );
add_action( 'after_setup_theme', 			'scuola_tema_imposta_valori_default' );
add_action( 'pre_get_posts', 				'scuola_custom_post_archive_changes' );

add_shortcode('articoli', 					'GetArticoliCategoria');
add_shortcode('gfolderdrive', 				'VisualizzaCartellaDrive');
add_shortcode('canccookies', 				'CancellaCookies');
add_shortcode('viscookies', 				'VisualizzaCookies');
add_shortcode('feedrss', 					'VisualizzaFeedRSS');
add_filter( 'spid_filter_sp_attributeconsumingservice', function( $v ) {
    return ["familyName","name","fiscalNumber", "email"];
}, 10, 1 );
function crunchify_embed_defaults($embed_size){
	$embed_size['width'] = 10240;
	$embed_size['height'] = 500;
	return $embed_size;
}
add_filter('embed_defaults', 'crunchify_embed_defaults');


function my_myme_types( $mime_types ) {
  $mime_types['zip'] = 'application/zip';     // Adding .zip extension
    
  return $mime_types;
}

/**
* Funzione che permette di ignorare gli articoli in evidenza nell'archivio.
* Con questa funzione gli articoli sticky_posts vengono riportati nell'elenco dei posts nella posizione naturale
**/
function scuola_custom_post_archive_changes( $query ) {
    if ( !is_post_type_archive() && $query->is_main_query() ) {
        $query->set( 'ignore_sticky_posts' , 1 );

    }
}
/**
*Funzione che imposta i valori di default del tema
*/
function scuola_tema_imposta_valori_default(){
// impostazione dei valori di default in caso di assenza
	$Impostazioni=get_theme_mods();
	if(!isset($Impostazioni["scuola_MTdata_attiva"]))
		set_theme_mod( 'scuola_MTdata_attiva', true);
	if(!isset($Impostazioni["scuola_MTdataLink_attiva"]))
		set_theme_mod( 'scuola_MTdataLink_attiva', true);
	if(!isset($Impostazioni["scuola_MTautore_attiva"]))
		set_theme_mod( 'scuola_MTautore_attiva', true);
	if(!isset($Impostazioni["scuola_docconteggio_attiva"]))
		set_theme_mod( 'scuola_docconteggio_attiva', true);
}
/**
 * Filter current filename to replace or remove problematic characters.
 * @param string $filename Current filename.
 */
function scuola_ripulisci_filenames( $filename ) {
	$original_chars = array('/×/','/№/','/“/','/”/','/«/','/»/','/„/','/@/','/%/','/‘/','/’/','/`/','/´/','/^/','/[\s\+]/','/\.(?=.*\.)/',);
	$sanitized_chars = array('x','','','','','','','','','','','','','','-','-',);
	// Sostituzione dei caratteri accentati con caratteri senza accento.
	$filename = remove_accents( $filename );
	// Sostituzione dei caratteri speciali.
	$filename = preg_replace( $original_chars, $sanitized_chars, $filename );
	// Sostituzione dei caratteri diversi da lettere e numeri con _.
	$filename = preg_replace( '/[^a-zA-Z0-9_\._]/', '_', $filename );
	// Elimina eventuali caratteri multipli _
	$filename =preg_replace('/_+/', '_', $filename); 
	// Elimina eventuali _.
	$filename =preg_replace('/_\./', '.', $filename); 
	// Conversione di tutti i caratteri in minuscolo.
	$filename = ucfirst($filename);
	return $filename;
}
function scuola_ripulisci_titolofile( $post_ID ) {
	$titolo = get_post( $post_ID )->post_title;
	// Rimpiazzo il - ed il _ con uno spazio
	$titolo = str_replace("-"," ",  $titolo );
	$titolo = str_replace("_"," ",  $titolo );
	// Imposto il primo carattere in Maiuscolo e tutti gli altri in minuscolo
	$titolo = ucfirst($titolo);
	if ( !wp_attachment_is_image( $post_ID ) ) {
		$allegato_meta = array(
			'ID'		=> $post_ID,
			'post_title'	=> $titolo);
		
	}else{ 
		$allegato_meta = array(
			'ID'		=> $post_ID,
			'post_title'	=> $titolo,
			'post_excerpt'	=> $titolo,
			'post_content'	=> $titolo);
	}
	wp_update_post( $allegato_meta );
}
function scuola_reset_counter(){
	global $wpdb;
 	$Result=$wpdb->update($wpdb->postmeta,
		 array('meta_value' => 0 ),
		 array('meta_key' => "post_views_count"),
		 array('%d'),
		 array('%s'));
	if($Result===FALSE)
		echo "Si è verificato un errore";
	else
		echo "Sono stati azzerati ".$Result." Oggetti";
	die();
}
/****************************************************** 
* Shortcode che visualizza link dei feed RSS per le Categorie e/o dei Tags
* [feedrss]
*******************************************************/
function VisualizzaFeedRSS($Parametri){
	$ret="";
	$Parametri=shortcode_atts(array(
		'sorgente' 	=> "Categorie",
		'vuote' 	=> "si",
		'id'		=> 0,
	), $Parametri,"feedrss");
	$Parametri=sanitize_type($Parametri,array("%s","%s","%d"));
	$args=array('orderby'	=>'name',
				'fields'	=>'all');
	if(strtolower($Parametri['vuote'])=="si"){
		$args['hide_empty']=false;
	}
	if($Parametri['id']!=0){
		$args['include']=$Parametri['id'];
	}
	$Categorie=get_categories($args);
	$Tags=get_tags($args);
	ob_start();?>
		<div class="link-list-wrapper">
  <ul class="link-list">
<?php if ($Parametri['sorgente']=="Categorie" Or strtolower($Parametri['sorgente'])=="all"):?>
    <li><a class="list-item large medium right-icon" href="#collapseOne" data-toggle="collapse" aria-expanded="false" aria-controls="collapseOne">
      <span class="fas fa-rss"></span> <span>Categorie</span>
      <svg class="icon icon-primary right"><use xlink:href="<?php echo get_bloginfo('template_url');?>/static/svg/sprite.svg#it-expand"></use></svg></a>
      <ul class="link-sublist collapse" id="collapseOne">
<?php foreach( $Categorie as $category ) {?>
        <li><?php echo $category->name;?> (<?php echo $category->count;?>) <span><a class="list-item d-inline" href="<?php echo esc_url( get_category_link( $category->term_id ) );?>feed"><?php echo esc_url( get_category_link( $category->term_id ) );?>feed</span></a>
        </li>
<?php } ?>
      </ul>
    </li>
<?php endif;
	if ($Parametri['sorgente']=="Tag" Or strtolower($Parametri['sorgente'])=="all"):?>
    <li><a class="list-item large medium right-icon" href="#collapseTwo" data-toggle="collapse" aria-expanded="false" aria-controls="collapseTwo">
      <span class="fas fa-rss"></span> <span>Tags</span>
      <svg class="icon icon-primary right"><use xlink:href="<?php echo get_bloginfo('template_url');?>/static/svg/sprite.svg#it-expand"></use></svg></a>
      <ul class="link-sublist collapse" id="collapseTwo">
 <?php foreach( $Tags as $tag ) {?>
        <li><?php echo $tag->name;?> (<?php echo $tag->count;?>) <span><a class="list-item d-inline" href="<?php echo esc_url( get_tag_link( $tag->term_id ) );?>feed"><?php echo esc_url( get_tag_link( $tag->term_id ) );?>feed</span></a>
        </li>
<?php } ?>
      </ul>
    </li>
<?php endif;?>
  </ul>
</div>
<?php
	return ob_get_clean();	
}
/****************************************************** 
* Shortcode che visualizza tutti i cookies registrati dal sito
* [viscookies]
*******************************************************/
function VisualizzaCookies($Parametri){
	$cookies=filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
	$UrlSprite=get_option('opt_AP_UrlSprite');
	ob_start();?>
<div id="ListaCookies" class="collapse-div" role="tablist">
<?php
	foreach($cookies as $Key=>$Dati){?>
	<div class="collapse-header" id="headingA1">
	    <button data-toggle="collapse" class="coloreTesto" data-target="#<?php echo $Key;?>" aria-expanded="false" aria-controls="<?php echo $Key;?>">
	      <?php echo $Key;?>
	    </button>
	  </div>
	  <div id="<?php echo $Key;?>" class="collapse" role="tabpanel" aria-labelledby="headingA1" data-parent="#ListaCookies">
	    <div class="collapse-body">
	      <?php echo $Dati;?>
	    </div>
	  </div>
<?php	}?>
</div>
<?php
	return ob_get_clean();
}
/****************************************************** 
* Shortcode che visualizza un pulsante o link che permette di cancellare tutti i cookies
* [canccookies vis="bottone/link"
               testo= Testo da visualizzare, di default Cancella i cookies del nostro sito]
*******************************************************/
function CancellaCookies($Parametri) {
	$ret="";
	$Parametri=shortcode_atts(array(
		'vis' 	=> "bottone",
		'testo' => "Cancella i cookies del nostro sito",
	), $Parametri,"canccookies");
	$Parametri=sanitize_type($Parametri,array("%s","%s"));
	ob_start();
	if(strtolower($Parametri['vis'])==="bottone"):?>
		<div><a href="#" id="cancella-cookie" class="badge badge-primary bottone bottoneBorded"> <?php echo $Parametri['testo'];?> </a>
		</div>
	<?php else:?>
		<div><a href="#" id="cancella-cookie"><?php echo $Parametri['testo'];?></a>
		</div>
<?php
	endif;
	return ob_get_clean();
}
/****************************************************** 
* Shortcode per incorporare le cartelle di Google
* [gfolderdrive idfolder=Id della cartella che è l'ultimo elemento del link 
                tipovis=grid/list modaità di visualizzazione
                border=dimensione del bordo (0)
                width=larghezza del frame in %
                height=altezza del frame in px
                scrolling=yes/no/auto]
*******************************************************/
function VisualizzaCartellaDrive($Parametri) {
	$ret="";
	$Parametri=shortcode_atts(array(
		'idfolder' 	=> 0,
		'tipovis'	=>"grid",
		'border'	=> 0,
		'width'		=>"100%",
		'height'	=>"500px",
		'scrolling' =>"auto",
	), $Parametri,"gdrive");
	$Parametri=sanitize_type($Parametri,array("%s","%s","%d","%s","%s","%s"));
	ob_start();?>
	<iframe src = "https://drive.google.com/embeddedfolderview?id=<?php echo $Parametri['idfolder'];?>#<?php echo $Parametri['tipovis'];?>" frameborder = "<?php echo $Parametri['border'];?>" width = "<?php echo $Parametri['width'];?>" height = "<?php echo $Parametri['height'];?>" scrolling = "<?php echo $Parametri['scrolling'];?>"> </iframe>
<?php return ob_get_clean();
}
/**
 * Funzione per la sanificazione e conversione dei formati 
 * @param $Campi: Array dei valori da saniticare controllo/modifica  del formato
 * @param $Formati: Array dei formati degli elementi dell'Array $Campi 
 * 
 * @return Array $Campi sanificato e convertito
 */
function sanitize_type($Campi=array(),$Formati=array()){
	$NC=count($Campi);
	if($NC!=count($Formati)){
		return FALSE;
	}
	$i=0;
	foreach($Campi as $Index=>$Value){
		$Campi[$Index]=sanitize_text_field($Campi[$Index]);
		switch($Formati[$i]){
			case "%d":
				$Campi[$Index]=intval($Campi[$Index]);
				break;
			default:
			$Campi[$Index]=$Campi[$Index];
		}
		$i++;
	}
	return $Campi;
}
/**
 * Funzione per la sanificazione e verifica del formato di un campo 
 * @param $Istance: array dei valori le widget
 * @param $Campo: campo da saniticare controllo/modifica  del formato
 * @param $Formato: formati dell'elemento da Verificare
 * @param $Default Valore di default se passato
 * 
 * @return Valore Verificato o Valore di Deult o ""
 */
function sanitize_field_widget($Istance,$Campo,$Formato="%s",$Default="_def_"){
	$Stato=True;
	if(!isset($Istance[$Campo])){
		$Stato=False;
	}
	else{
		$Istance[$Campo]=sanitize_text_field($Istance[$Campo]);
		if($Formato=="%d" And !is_numeric($Istance[$Campo]))
			$Stato=False;
	}
	if($Stato)
		return $Istance[$Campo];
	if(!$Stato And $Default=="_def_")
		return "";
	else
		return $Default;
}
/****************************************************** 
* Shortcode per visualizzare gli articoli filtrati
* [articoli id_categoria=Id della categoria da filtrare 
            id_tag=ID del tag da filtrare
            numero=numero di articoli da filtrare
            imgevidenza=immagine in evidenza<center></center>]
*******************************************************/
function GetArticoliCategoria($Parametri){
	$ret="";
	$Parametri=shortcode_atts(array(
		'id_categoria' => 0,
		'id_tag' => 0,
		'numero' => 5,
		'imgevidenza' => 'no'
	), $Parametri,"articoli");
	$Parametri=sanitize_type($Parametri,array("%d","%d","%d","%s"));
	if($Parametri['id_categoria']!=0){
		$Catargs = array( 'cat' => $Parametri['id_categoria'],
					   'posts_per_page'  => $Parametri['numero'],
					   'post_status' => (is_user_logged_in()? array('publish','private'):'publish'));
		$Articoli = get_posts( $Catargs );
		$TipoComNoArt=__('Non ci sono articoli nella categoria','wpscuola');
		$TipoComTit=__('Articoli della categoria','wpscuola');
		$TipoComReadAll=__('Leggi tutti gli articoli della categoria','wpscuola');
		$Tax=get_cat_name($Parametri['id_categoria']);
		$TaxLink=get_category_link($Parametri['id_categoria']);
	}	
	if($Parametri['id_categoria']==0 And $Parametri['id_tag']!=0){
		$Tagargs = array( 'tag_id' => $Parametri['id_tag'],
					   'posts_per_page'  => $Parametri['numero'],
					   'post_status' => (is_user_logged_in()? array('publish','private'):'publish'));
		$Articoli = get_posts( $Tagargs );		
		$TipoComNoArt=__('Non ci sono articoli con etichetta','wpscuola');
		$TipoComTit=__('Articoli con etichetta','wpscuola');
		$TipoComReadAll=__('Leggi tutti gli articoli con etichetta','wpscuola');
		$Tax=get_tag($Parametri['id_tag']);
		$Tax=$Tax->name;
		$TaxLink=get_tag_link($Parametri['id_tag']);
	} 
	ob_start();
	if(count($Articoli)==0){?>
		<div class="alert alert-info mt-3" role="alert">
  			<?php echo $TipoComNoArt.": <strong>".$Tax."</strong>";?>
		</div>
<?php return ob_get_clean();
	}?>
	<div class="it-list-wrapper mt-3">
		<p class="h4"><?php echo $TipoComTit.": ".$Tax;?></p>
		<ul class="it-list p-3 mb-2 shadow-sm">
<?php	foreach($Articoli as $Articolo){	?>
			<li class="nolist">
			    <a href="<?php echo get_permalink($Articolo->ID);?>">
				<?php if($Parametri['imgevidenza']=='si' ) :
				    	$Thumbnail=scuola_get_thumbnail($Articolo->ID);
				    	if($Thumbnail):?>
				    <div class="it-thumb">
		             <?php   echo scuola_get_thumbnail($Articolo->ID);?>					          
					</div>
				     <?php endif;
				      endif;?>
		        	<div class="it-right-zone  border-0">
		        		<span class="text"><?php echo $Articolo->post_title;?></span>
		        		<span class="it-multiple">
<?php 	if(get_theme_mod('scuola_MTdataLink_attiva')):?>
		        			<span class="metadata metadatasmall">
		        				<span class="far fa-calendar-alt"></span> 
<?php echo date_i18n( get_option( 'date_format' ), strtotime($Articolo->post_date) );?></span>
<?php	endif;
  	  	if(get_theme_mod('scuola_MTautore_attiva')):?>
							<span class="metadata metadatasmall">
								<span class="fas fa-user-edit"></span> 
<?php echo get_the_author_meta('display_name', $Articolo->post_author);?>
							</span>
<?php 		endif;?>
						</span>
					</div>
			    </a>
			</li>
<?php			} ?>
		</ul>
	</div>
	<p class="read-more p-2 ">
	    <a href="<?php echo $TaxLink;?>" title="Archio articoli della categoria <?php echo $Tax;?>"><span class="fas fa-book-reader p-1"></span> <?php echo $TipoComReadAll." ".$Tax;?></a>
	</p>
<?php	return ob_get_clean();
}
/****************************************************** 
* Funzione per la gestione del download dei files
*******************************************************/
function Gestione_DwnLink(){
	if(isset($_REQUEST['action'])){
		switch ($_REQUEST['action']){
		case "dwattachment":
/*			var_dump($_SERVER);
			if(!isset($_SERVER["HTTP_REFERER"])){
				wp_die(__('Oooooo!<br />
				        Stai tentando di fare il furbo!<br />
				        Non puoi accedere a questo file direttamente.','albo-online'));
				break;
			}
*/			$file_path	= get_attached_file($_REQUEST['id']);
//			$file_path	=$file_path[0]->Allegato;
//				echo "<pre>".$file_path."</pre>";
			global $is_IE;
			$chunksize	= 2*(1024*1024);
//				wp_die($file_path);
			$stat 		= @stat($file_path);
			$etag		= sprintf('%x-%x-%x', $stat['ino'], $stat['size'], $stat['mtime'] * 1000000);
			$path 		= pathinfo($file_path);
			if ( isset($path['extension']) && strtolower($path['extension']) == 'zip' && $is_IE && ini_get('zlib.output_compression') ) {
				ini_set('zlib.output_compression', 'Off');
				// apache_setenv('no-gzip', '1');
			}

			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private', FALSE);
			header('Content-Type: application/force-download', FALSE);
			header('Content-Type: application/octet-stream', FALSE);
			header('Content-Type: application/download', FALSE);
			header('Content-Disposition: attachment; filename="'.basename($file_path).'";');
			header('Content-Transfer-Encoding: binary');
			header('Last-Modified: ' . date('r', $stat['mtime']));
			header('Etag: "' . $etag . '"');
			header('Content-Length: '.$stat['size']);
			header('Accept-Ranges: bytes');
			ob_flush();
			flush();
			if ($stat['size'] < $chunksize) {
				@readfile($file_path);
			}
			else {
				$handle = fopen($file_path, 'rb');
				while (!feof($handle)) {
					echo fread($handle, $chunksize);
					ob_flush();
					flush();
				}
				fclose($handle);
			}
			exit();
			break;
		}
	}
}
	
function mytheme_add_editor_styles() {
    add_editor_style( 'style-editor.css' );
}
add_post_type_support ('page', 	'excerpt');

/**
*
* Aggiunta attributi immagine impostati
*
*/
function scuola_attributi_img( $attr, $attachment = null) {
    $attr["title"]=get_post( $attachment->ID )->post_title;
// 	$attr["longdesc"]=get_post( $attachment->ID )->post_content;
    return $attr;
};

/**
*
* Aggiunta del conteggio delle visualizzazioni dei post
*
*/

function scuola_get_post_view() {
    $count = get_post_meta( get_the_ID(), 'post_views_count', true );
    return intval($count);
}

function scuola_set_post_view() {
	if(!get_theme_mod('scuola_docconteggio_attiva')) return;
	$key = 'post_views_count';
    $post_id = get_the_ID();
    $count = (int) get_post_meta( $post_id, $key, true );
    $count++;
    update_post_meta( $post_id, $key, $count );
}

function scuola_posts_column_views( $columns ) {
    $columns['post_views'] = __('Visualizzazioni','wpscuola');
    return $columns;
}

function scuola_posts_custom_column_views( $column ) {
    if ( $column === 'post_views') {
        echo sprintf("%s %s %s",__('visto','wpscuola'),scuola_get_post_view(),__('volte','wpscuola'));
    }
}

/**
* 
* Personalizzazione blocco file dell'editor Gutenberg
*
*/
function personaliza_file_render( $block_content, $block ) {
	if( "core/file" !== $block['blockName'] ) {
    	return $block_content;
  	}
  	if(!isset($block["attrs"]['id'])){
		return $block_content;
	}
//	echo "<pre>";var_dump($block);echo "</pre>";
  $IDFile=$block["attrs"]['id'];
  $Allegato=get_post($IDFile);
  if(is_null($Allegato)) return;
  $Link=$block["attrs"]['href'];
  $Title = $Allegato->post_title; //The Title
  $Description = $Allegato->post_content; // The Description	
  $filesize = size_format(filesize( get_attached_file( $IDFile ) ), 2); 
  $filetype = wp_check_filetype($Link);
  $IconaFile='<span class="far fa-file"></span>';
  $TipoFile="File";

/*  ob_start();
  var_dump($IDFile  );
	var_dump($content);
	$a=ob_get_contents();
  ob_end_clean();
	echo $a;
*/	$PosT=strpos($block_content,"target");
	$Target="";
	if($PosT!==FALSE){
		$Target=substr($block_content,$PosT,strpos($block_content," ",$PosT)-$PosT);	
	}
	ob_start();
	$Div=substr($block_content,0,strpos($block_content,">")+1);
//	echo "Div ";var_dump($block_content);echo "<br>";
	
	if(($PosObj=strpos($block_content,"</object>"))>0){
		$PosST=strpos($block_content,">",strlen($Div)+$PosObj)+1;
	}else{
		$PosST=strpos($block_content,">",strlen($Div)+1)+1;
	}
//	echo "PosObj ";var_dump($PosObj);echo "<br>";
//	echo "PosST ";var_dump($PosST);echo "<br>";
	$Titolo=substr($block_content,$PosST,strpos($block_content,"<",$PosST+1)-$PosST);
//	echo "Titolo ";var_dump($Titolo);echo "<br>";
	if($Title!=$Titolo) $Title=$Titolo;
  switch ($filetype['ext']){
  	case "txt": 
  	case "odt": $IconaFile='<span class="far fa-file-alt fa-2x"></span>'; $TipoFile="Open Document Format for Office Applications";break;
  	case "pdf": $IconaFile='<span class="far fa-file-pdf fa-2x"></span>'; $TipoFile="Portable Document Format";break;
 	case "csv": $IconaFile='<span class="fas fa-file-csv fa-2x"></span>'; $TipoFile="comma-separated values";break;
  	case "doc":
  	case "rtf":
  	case "docx": $IconaFile='<span class="far fa-file-word fa-2x"></span>'; $TipoFile="Microsoft Word document";break;
  	case "xls":
  	case "ods":
  	case "xlsx": $IconaFile='<span class="far fa-file-excel fa-2x"></span>'; $TipoFile="Foglio di calcolo";break;
  	case "ppt":
  	case "odp": 
  	case "pptx": $IconaFile='<span class="far fa-file-powerpoint fa-2x"></span>'; $TipoFile="Presentazione";break;
  	case "zip":  $IconaFile='<span class="far fa-file-archive fa-2x"></span>';$TipoFile="Archivio compresso"; break;
  	case "mp4":  $IconaFile='<span class="far fa-file-video fa-2x"></span>'; $TipoFile="File multimediale video e audio digitali";break;
  	case "png":
  	case "jpg":
  	case "jpeg":
  	case "bmp":
  	case "ico":$IconaFile='<span class="far fa-file-image fa-2x"></span>'; $TipoFile="Immagine";break;
  }
//ob_start();
	if(isset($block["attrs"]["displayPreview"]) And $block["attrs"]["displayPreview"]==true){?>  
		<object class="wp-block-file__embed" 
			data="<?php echo $Link;?>" 
			type="application/pdf" 
			style="width:100%;height:<?php echo isset($block["attrs"]["previewHeight"])?$block["attrs"]["previewHeight"]:"600";?>px" 
			aria-label="Embed of file.">
		</object>
<?php }?>
		<div class="card-wrapper border rounded">
		    <div class="card-body p-2">
		        <div class="media stack-xs">
		            <div class="media-body">
		                <div class="media">
		                    <div class="mr-3 img-48">
		                    	<?php echo $IconaFile;?>
		                    </div>
		                    <div class="media-body">
		                        <h3 class="h5"><a href="<?php echo $Link;?>" title="<?php echo $Title;?>"><?php echo  $Title;?></a></h3>
		                        <div class="text-muted text-small"><span class="fas fa-hdd ml-3"></span> <?php echo $filesize;?> <span class="fas fa-file"></span> <?php echo $TipoFile;?></div>
		                        <?php if($Description!=="") :?>
		                        <div class="text-muted"><?php echo $Description;?></div>
		                        <?php endif;?>
		                    </div>
		                </div>
		            </div>
		            <?php if(strpos($block_content,"wp-block-file__button")!==FALSE) :?>
		            <div class="ml-3 wpdmdl-btn">
		                <a class="btn btn-primary " href="<?php echo $Link;?>" title="<?php echo $Title;?>" download><?php _e("Scarica","wpscuola");?>
		                </a>
		            </div>
		            <?php endif;?>
		        </div>
		    </div>
		</div>
<?php
  return ob_get_clean();
}
$mesi = array(1=>__( 'Gennaio', 'wpscuola' ), 
                 __( 'Febbraio', 'wpscuola' ), 
                 __( 'Marzo', 'wpscuola' ), 
                 __( 'Aprile', 'wpscuola' ), 
                 __( 'Maggio', 'wpscuola' ),
                 __( 'Giugno', 'wpscuola' ),
                 __( 'Luglio', 'wpscuola' ),
                 __( 'Agosto', 'wpscuola' ),
                 __( 'Settembre', 'wpscuola' ),
                 __( 'Ottobre', 'wpscuola' ),
                 __( 'Novembre', 'wpscuola' ),
                 __( 'Dicembre', 'wpscuola' )
            );
function scuola_setup()	{
	load_theme_textdomain('wpscuola', get_template_directory() . '/languages');
  
/**
*	Theme Support 
*/  
   if ( function_exists( 'add_theme_support' ) ) { 
   	add_theme_support( 'responsive-embeds' );
   	add_theme_support ('editor-styles');
 	add_theme_support('title-tag');
	add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails' );
    add_theme_support( 'align-wide' );
    add_theme_support('editor-color-palette', array(
			array(
				'name'  => __( 'Blu', 'wpscuola' ),
				'slug' => 'blu',
				'color' => '#004d99',
			),
			array(
				'name'  => __( 'Grigio scuro', 'wpscuola' ),
				'slug' => 'grigioscuro',
				'color' => '#3d4955',
			),
			array(
				'name'  => __( 'Blu scuro', 'wpscuola' ),
				'slug' => 'bluscuro',
				'color' => '#17324d',
			),
			array(
				'name'  => __( 'Azzurro', 'wpscuola' ),
				'slug' => 'azzurro',
				'color' => '#0073e6',
			),
			array(
				'name'  => __( 'Grigio', 'wpscuola' ),
				'slug' => 'grigio',
				'color' => '#5c6f82',
			),
			array(
				'name'  => __( 'Grigio chiaro', 'wpscuola' ),
				'slug' => 'grigiochiaro',
				'color' => '#94a1ae',
			),
			array(
				'name'  => __( 'Verde', 'wpscuola' ),
				'slug' => 'verde',
				'color' => '#00cc85',
			),
			array(
				'name'  => __( 'Rosso', 'wpscuola' ),
				'slug' => 'rosso',
				'color' => '#f73e5a',
			),
			array(
				'name'  => __( 'Arancione', 'wpscuola' ),
				'slug' => 'arancione',
				'color' => '#ff9900',
			),
			array(
				'name'  => __( 'Argento', 'wpscuola' ),
				'slug' => 'argento',
				'color' => '#eef0f6',
			),
			array(
				'name'  => __( 'Bianco', 'wpscuola' ),
				'slug' => 'bianco',
				'color' => '#ffffff',
			)
		)
	);
	add_theme_support( 'editor-font-sizes', array(
    array(
      'name' => __( 'Piccolo', 'wpscuola' ),
      'size' => 14,
      'slug' => 'small'
    ),
    array(
      'name' => __( 'Normale', 'wpscuola' ),
      'size' => 18,
      'slug' => 'normal'
    ),
    array(
      'name' => __( 'Medio', 'wpscuola' ),
      'size' => 22,
      'slug' => 'medium'
    ),
    array(
      'name' => __( 'Grande', 'wpscuola' ),
      'size' => 32,
      'slug' => 'big'
    )
) );
}
 
if( function_exists('add_image_size')){
 	add_image_size( 'large-thumb', 640, 480, true);
    add_image_size( 'mansory-thumb', 600, 350, true);
    add_image_size( 'single-alignfull-thumb', 1280, 350, true );
    add_image_size( 'img-wrapper-thumb', 660, 300, true);
}
         
global $content_width;
if (!isset($content_width)) $content_width = 640;
register_nav_menus(array(
	'menu-principale' => __('Main Menu', 'wpscuola'),
	'menu-social'  => __( 'Social Menu', 'wpscuola' ),
	'menu-footer'  => __( 'Footer Menu', 'wpscuola' ),
	'menu-footer-ente'  => __( 'Footer Menu Ente', 'wpscuola' ),
	'menu-footer-legale'  => __( 'Footer Menu Legale', 'wpscuola' ),
	'menu-footer-secondo'  => __( 'Footer Menu secondo', 'wpscuola' ),
));
}
function scuola_custom_login_logo() {
    echo '<style type="text/css">';
    echo '.login h1 a { background-image:url('.esc_url(get_site_icon_url()).') !important; }';
    echo '</style>';
}

/**
* Inclusione Moduli del tema
*/
if(get_theme_mod('scuola_circolari_attiva')){
	require get_template_directory() . '/plugins/gestione-circolari/GestioneCircolari.php';
}
if(get_theme_mod('scuola_faq_attiva')){ 
	require get_template_directory() . '/plugins/faq/scuola_faq.php';
	$my_faq=new ScuolaFAQ();
}
if(get_theme_mod('scuola_eventi_attiva')){
	require get_template_directory() . '/plugins/eventi/scuola_eventi.php';
	$my_eventi=new ScuolaEventi();
}	 
if(get_theme_mod('scuola_servizi_attiva')){
	require get_template_directory() . '/plugins/servizi/scuola_servizi.php';
	$my_servizi=new ScuolaServizi();
}	  
if(get_theme_mod('scuola_modpren_attiva')){
	require get_template_directory() . '/plugins/prenotazioni/Prenotazioni.php';
}
if(get_theme_mod('scuola_modorarioArgo_attiva')){
	require get_template_directory() . '/plugins/orario_argo_darwin/OrarioDarwin.php';
}

function calc_NumArticoliMA($ArchivioDate){
	$Dati=array();
	foreach ($ArchivioDate as $Data) {
		$Dati[$Data->Anno]=(isset($Dati[$Data->Anno])?$Dati[$Data->Anno]:0)+$Data->NumArt;
	}
	return $Dati;
}
if (function_exists("at_sezioni_shtc")){
	remove_shortcode('at-sezioni');
	function my_at_sezioni_shtc($atts) {
	    ob_start();
	    include( get_stylesheet_directory()."/plugins/amministrazione-trasparente/shortcodes/shortcodes-sezioni.php");
	    $atshortcode = ob_get_clean();
	    return $atshortcode;
	} add_shortcode('at-sezioni', 'my_at_sezioni_shtc');
}
if (function_exists("at_search_shtc")){
	remove_shortcode('at-search');
	function my_at_search_shtc($atts)  {
	    ob_start();
	    include( get_stylesheet_directory()."/plugins/amministrazione-trasparente/shortcodes/shortcodes-search.php");
	    $atshortcode = ob_get_clean();
	    return $atshortcode;
	} add_shortcode('at-search', 'my_at_search_shtc');	
}

if ( !class_exists( 'SimplePie' ) ) {

	class Registry_FixSimplePieErrors {

	    static public $sFilePath = __FILE__; 
	    static public $sDirPath  = '';
	    
	    static public function setUp() {
	        self::$sDirPath = dirname( self::$sFilePath );
	    }
	}

Registry_FixSimplePieErrors::setUp();

include( get_template_directory() . '/inc/class-simplepie.php' );
}

 function scuola_image_sizes($sizes) {
      $addsizes = array(
          "large-thumb" => __( "Large image")
      );
      $newsizes = array_merge($sizes, $addsizes);
      return $newsizes;
  }

function enqueue_scuola_admin() {
	wp_enqueue_script( 'scuola-customize-controls', get_template_directory_uri() . '/static/js/customize-controls.js',array( 'jquery', 'customize-controls' ), false, true );
    wp_enqueue_style('scuola_fonts_Awesome', get_template_directory_uri() . '/static/css/all.css');
	wp_enqueue_style('scuola_style_admin', get_template_directory_uri() . '/static/css/backend.css');
	wp_enqueue_script('jquery-ui-tooltip');
    if (is_front_page()) {
		wp_enqueue_script( 'scuola-image_hover_effects_JS', get_template_directory_uri() . '/static/js/production.min.js' );
		wp_enqueue_style( 'scuola-image_hover_effects_CSS', get_template_directory_uri() . '/static/css/image_hover_effects.css');	
	}

}

function enqueue_scuola_public() {
    wp_enqueue_style('wpscuola_bootstrap-italia.min_css', get_template_directory_uri() . '/static/css/bootstrap-italia.min.css');
//    wp_enqueue_style( 'bootstrap-italia-map', get_template_directory_uri() . "/static/css/bootstrap-italia.min.css.map");
    wp_enqueue_style('wpscuola_owl.carousel.min_css', get_template_directory_uri() . '/static/css/owl.carousel.min.css');
    wp_enqueue_style('wpscuola_owl.theme.default.min_css', get_template_directory_uri() . '/static/css/owl.theme.default.min.css');
	wp_enqueue_script("jquery");
    wp_enqueue_style('wpscuola_jquery-ui_css', get_template_directory_uri() . '/static/css/jquery-ui.css');
    wp_enqueue_style('wpscuola_tema_css', get_template_directory_uri() . '/static/css/tema.css');
    wp_enqueue_style('wpscuola_magnific-popup_css', get_template_directory_uri() . '/inc/magnific-popup/magnific-popup.css');    
    wp_enqueue_style( 'scuola_fonts_Awesome', get_template_directory_uri() . '/static/css/all.css');
   if (is_front_page()) {
		wp_enqueue_script( 'scuola-image_hover_effects_JS', get_template_directory_uri() . '/static/js/production.min.js' );
		wp_enqueue_style( 'scuola-image_hover_effects_CSS', get_template_directory_uri() . '/static/css/image_hover_effects.css');	
	}
	if (is_page_template("tmpl_page-servizi.php") Or is_page_template("tmpl_page-siti.php")){
			wp_enqueue_script( 'scuola-image_hover_effects_JS', get_template_directory_uri() . '/static/js/production.min.js' );
			wp_enqueue_style( 'scuola-image_hover_effects_CSS', get_template_directory_uri() . '/static/css/image_hover_effects.css');	
		}
//	wp_enqueue_style('wpscuola_adjustments_css', get_template_directory_uri() . '/inc/adjustments.css');

}
function scuola_enqueue_comment_reply_script() {
if (get_option('thread_comments')) {
	wp_enqueue_script('comment-reply');
	}
}

function scuola_filter_wp_title($title) {
	return $title . esc_attr(get_bloginfo('name'));
}

if (function_exists('register_sidebar')) {
// function wpscuola_widgets_init() {
	register_sidebar(array(
		'name' => __('Home Widget Area', 'wpscuola') ,
		'id' => 'home-widget-area',
		'description'   => __( 'Widget area che compare in homepage.', 'wpscuola' ),
		'before_widget' => '<section id="%1$s" class="home-widget container %2$s">',
		'after_widget' => "</section>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
	
	register_sidebar(array(
		'name' => __('Post Sidebar Widget Area', 'wpscuola') ,
		'id' => 'primary-widget-area',
		'description'   => __( 'Widget area che compare nella sidebar dei post.', 'wpscuola' ),
		'before_widget' => '<div id="%1$s" class="widget-container shadow p-2 %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

  register_sidebar(array(
  	'name' => __('Page Sidebar Widget Area', 'wpscuola') ,
  	'id' => 'page-widget-area',
  	'description'   => __( 'Widget area che compare nella sidebar delle pagine.', 'wpscuola' ),
  	'before_widget' => '<div id="%1$s" class="widget-container shadow p-2 %2$s">',
  	'after_widget' => "</div>",
  	'before_title' => '<h3 class="widget-title">',
  	'after_title' => '</h3>',
  ));

	register_sidebar( array(
		'name' => __('Footer Widget Area', 'wpscuola') ,
		'id' => 'footer-widget-area',
		'description'   => __( 'Widget area che compare nel footer.', 'wpscuola' ),
		'before_widget' => '<div id="%1$s" class="col-lg widget-container p-2 %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="text-primary">',
		'after_title' => '</h3>',
	));

	register_sidebar( array(
		'name' => __('Footer Sub Widget Area', 'wpscuola') ,
		'id' => 'footer-sub-widget-area',
		'description'   => __( 'Widget area che compare sotto la footer widget area.', 'wpscuola' ),
		'before_widget' => '<div id="%1$s" class="col-lg widget-container text-white p-2 %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar( array(
		'name' => __('Post Footer Widget Area', 'wpscuola') ,
		'id' => 'single-footer-widget-area',
		'description'   => __( 'Widget area che compare sotto il contenuto del singolo post.', 'wpscuola' ),
		'before_widget' => '<div id="%1$s" class="col-lg widget-container shadow p-2 mb-4 mt-4 %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar( array(
		'name' => __('Page Footer Widget Area', 'wpscuola') ,
		'id' => 'page-footer-widget-area',
		'description'   => __( 'Widget area che compare sotto il contenuto della singola pagina.', 'wpscuola' ),
		'before_widget' => '<div id="%1$s" class="col-lg widget-container shadow p-2 mb-4 %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
	if(function_exists("at_sezioni_shtc")){
		register_sidebar(array(
			'name' => __('Amministrazione Trasparente', 'wpscuola') ,
			'id' => 'amm-trasparente-widget-area',
			'description'   => __( 'Widget area visibile solo nelle pagine dell\'Amministrazione Trasparente.', 'wpscuola' ),
			'before_widget' => '<div id="%1$s" class="widget-container shadow p-2 %2$s">',
			'after_widget' => "</div>",
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
	}
}

function scuola_custom_pings($comment) {
	$GLOBALS['comment'] = $comment;
?>
<li <?php
	comment_class(); ?> id="li-comment-<?php
	comment_ID(); ?>"><?php
	echo comment_author_link(); ?></li>
<?php
	} 

function scuola_comments_number($count) {
	if (!is_admin()) 	{
		global $id;
		// $comments_by_type = & separate_comments(get_comments('status=approve&post_id=' . $id));
		$cc = get_comments('status=approve&post_id=' . $id);
    $comments_by_type = separate_comments($cc);
		return count($comments_by_type['comment']);
		} else {
		return $count;
		}
	}
	
function scuola_inizialize() {
 	add_editor_style('bootstrap-italia/css/bootstrap-italia.min.css');
	if(!get_theme_mod("scuola_circolari_attiva"))
		update_option('wps_Circolari_ModuloCircolari', 'No');
	if ( ! get_option('opt_AP_UrlSprite') ) 
		update_option( 'opt_AP_UrlSprite', get_site_url().'/wp-content/themes/wp-scuola/static/svg/sprite.svg');
}

function scuola_register_Widget(){
	register_widget( 'Articoli' );
	register_widget( 'Articoli_Griglia' );
	register_widget( 'Trasparenza' );
	register_widget( 'Feed_RSS' );
	register_widget( 'Link' );
	register_widget( 'Bacheca' );
	register_widget( 'Pulsanti' );
	if(get_theme_mod('scuola_servizi_attiva'))		register_widget( 'Servizi' );
	if(function_exists("at_sezioni_shtc"))			register_widget( 'my_ATWidget' );	
	if(get_theme_mod("scuola_circolari_attiva"))	register_widget( 'CircolariScuola' );
}
/**
* Inclusione librerie dei Widget
*/
require get_template_directory() . '/widget/widget_feedRSS.php';
require get_template_directory() . '/widget/widget_trasparenza.php';
require get_template_directory() . '/widget/widget_articoli.php';
require get_template_directory() . '/widget/widget_articoli_griglia.php';
require get_template_directory() . '/widget/widget_link.php';
require get_template_directory() . '/widget/widget_bacheca.php';
require get_template_directory() . '/widget/widget_pulsanti.php';
if(get_theme_mod('scuola_servizi_attiva'))		require get_template_directory() . '/widget/widget_servizi.php';
if(function_exists("at_sezioni_shtc"))			require get_template_directory() . '/widget/widget_AT.php';
if(get_theme_mod("scuola_circolari_attiva"))	require get_template_directory() . '/widget/widget_circolari.php';

function add_opengraph_doctype($output) {
    return $output . '
    xmlns="https://www.w3.org/1999/xhtml"
    xmlns:og="https://ogp.me/ns#" 
    xmlns:fb="http://www.facebook.com/2008/fbml"';
}

function scuola_opengraph() {

  if( is_single() || is_page() ) {

    $post_id = get_queried_object_id();

    $url = get_permalink($post_id);
    $title = get_the_title($post_id);
    $site_name = get_bloginfo('name');

    $description = wp_trim_words( get_post_field('post_content', $post_id), 25 );

    $image = get_the_post_thumbnail_url($post_id);
    if( !empty( get_post_meta($post_id, 'og_image', true) ) ) $image = get_post_meta($post_id, 'og_image', true);

    $locale = get_locale();

    echo '<meta property="og:locale" content="' . esc_attr($locale) . '" />';
    echo '<meta property="og:type" content="article" />';
    echo '<meta property="og:title" content="' . esc_attr($title) . ' | ' . esc_attr($site_name) . '" />';
    echo '<meta property="og:description" content="' . esc_attr($description) . '" />';
    echo '<meta property="og:url" content="' . esc_url($url) . '" />';
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '" />';

    if($image) echo '<meta property="og:image" content="' . esc_url($image) . '" />';

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image" />';
    // echo '<meta name="twitter:site" content="@account" />';
    // echo '<meta name="twitter:creator" content="@account" />';

  }

}

function scuola_customize_footer(){ ?>
  <script>
    var isMobile = false; //initiate as false
    // device detection
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
    document.body.classList.add('touch')
    }
    
    const elements = document.querySelectorAll('.menu-item-has-children');
    elements.forEach(function(el, index){
      el.onclick = function() {
        el.classList.toggle('active');
      }
    })

  </script>
<?php }


function scuola_SearchFilter($query) {
    // If 's' request variable is set but empty
    if (isset($_GET['s']) && empty($_GET['s']) && $query->is_main_query()){
        $query->is_search = true;
        $query->is_home = false;
    }
 if( is_tag() && $query->is_main_query() ) {
        // this gets all post types:
        $post_types = get_post_types();
        // alternately, you can add just specific post types using this line instead of the above:
        // $post_types = array( 'post', 'your_custom_type' );
        $query->set( 'post_type', $post_types );
    }
    return $query;
}
/* Enqueue WordPress theme styles within Gutenberg. */
function gutenberg_styles() {
	 wp_enqueue_style( 'scuola-gutenberg', get_template_directory_uri() . '/lib/block/block.css', false, '@@pkg.version', 'all' );
}

function custom_excerpt_length( $length ) {
	return 25;
}
function new_excerpt_more( $more ) {
    return ' ... ';
}
function scuola_password_form() {  
global $post;
	$content = '
  		  <form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post" class="pt-5">
			<div class="form-group">
			    <div class="form-row">
					<div class="form-group col-md-6">
						<input name="post_password" type="password" class="form-control input-password" id="exampleInputPassword" aria-labelledby="infoPassword" >
				    	<span class="password-icon" aria-hidden="true"></span><span class="far fa-eye"></span>
				        <label for="exampleInputPassword">Inserire la password per visualizzare '.(get_post_type( $post->ID) =="circolari_scuola"?"la Circolare":"l'Articolo").'</label>
			    	</div>
		    	<div class="form-group col-md-3">
	      		<button name="Submit" type="submit" class="btn btn-primary" value="' . __( "Submit" ) . '">Invia</button>
			  </div>
		  </form>';
  	return $content;
 }
 function scuola_get_thumbnail($IDogg){
 	$img_thumbnail=get_the_post_thumbnail($IDogg,'img-wrapper-thumb');
   	if(!isset($img_thumbnail) Or $img_thumbnail==""){
		$IDImgEvidenza=get_theme_mod('scuola_DefautlImg');
		if($IDImgEvidenza===FALSE){
			return '<img src="'.get_template_directory_uri().'/imgages/thumbnail-default.png" title="Immagine di default" alt="Immagine di default">'; 
		}
		$Image=wp_get_attachment_url($IDImgEvidenza);
		$ImageTitle = get_post($IDImgEvidenza)->post_title; //The Title
		$ImageAlt = get_post_meta($IDImgEvidenza, '_wp_attachment_image_alt', TRUE); //The Caption
		$ImageDescription = get_post($IDImgEvidenza)->post_content; // The Description	
     	return '<img src="'.$Image.'" title="'.$ImageTitle.'" alt="'.$ImageAlt.'" >';
	}else return $img_thumbnail;
 }
function current_url() {
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $validURL = str_replace("&", "&amp", $url);
    return $validURL;
}

// Supporto ai tags per le pagine
function tags_support_all() {
    register_taxonomy_for_object_type('post_tag', 'page');
}
add_action('init', 'tags_support_all');

// Inserimento dei tags nelle queries
function tags_support_query($wp_query) {
    if ($wp_query->get('tag')) {
        $wp_query->set('post_type', 'any');
    }
}
add_action('pre_get_posts', 'tags_support_query');

function scuola_customize_head() { 

	$Regole="";
	if (!is_admin_bar_showing ()) {
//		$Regole=".sticky .it-header-navbar-wrapper, #myHeader{margin-top: 0!important;padding-top:0;}";
		$Regole="#mainheader.ridotto {top: 0!important;}";
	}else{
		$Regole="#mainheader.ridotto {top: 30px!important;}
	@media (max-width: 780px){
		#mainheader.ridotto {top: 45px!important;}
	}"; 
	}
	$ColoreHeader=get_theme_mod( 'scuola_head_color', "#0066cc" );
	$ColoreTestoHeader=get_theme_mod( 'scuola_head_text_color', "#fff" );
	$ColoreLinkHeader=get_theme_mod( 'scuola_head_link_color', "#65dcdf" );
	
	$ColoreBody=get_theme_mod( 'scuola_background_color', "#fff" );
	$ColoreTestoBody=get_theme_mod( 'scuola_text_color', "#000" );
	$ColoreLinkBody=get_theme_mod( 'scuola_link_color', "#0066cc" );
	
	$ColoreBottone=get_theme_mod( 'scuola_button_color', "#0066cc" );
	$ColoreTestoBottone=get_theme_mod( 'scuola_button_text_color', "#fff" );
	
	$ColoreFooter=get_theme_mod( 'scuola_footer_color', '#00264d' );
	$ColoreTestoFooter=get_theme_mod('scuola_footer_text_color', "#fff");
	$ColoreLinkFooter=get_theme_mod('scuola_footer_link_color', "#65dcdf");
	?>
<!-- Custom <head> content -->
  <style type="text/css">
  <?php echo $Regole; ?>
    body,.bootstrap-select-wrapper button, .coloreTesto {color: <?php echo $ColoreTestoBody; ?>!important;}
    .navbar .navbar-collapsable .navbar-nav li a.nav-link,#mainheader, .my-bg-primary, .it-header-navbar-wrapper, .it-header-wrapper, .collapse-header { background-color: <?php echo $ColoreHeader; ?>!important;}
    body, .affix-top,. {background-color:<?php echo $ColoreBody; ?>;}
    #mainheader .cerca input{
		color: <?php echo $ColoreTestoHeader; ?>;
    	border-bottom: 1px solid <?php echo $ColoreLinkHeader; ?>;
	}
	table th, .thead-dark th {
		color: <?php echo $ColoreLinkHeader; ?>;
	    background-color: <?php echo $ColoreBottone; ?>;
	}
 	#mainheader, #mainheader.soclial, .navbar .navbar-collapsable .navbar-nav li a.nav-link {color: <?php echo $ColoreTestoHeader; ?>!important }
    .mysearchform input[type="text"], .it-header-wrapper .mysearchform input[type="text"]{box-shadow: 0 1px 0px <?php echo $ColoreTestoHeader; ?>!important;} 
  
   #menu-principale a.dropdown-toggle, #mainheader .social a,#mainheader .social a:hover, #mainheader .amministrazione .logotxt h1 a, #mainheader .amministrazione .logotxt h1 a:hover,#mainheader #sub_nav {color: <?php echo $ColoreLinkHeader; ?>!important }
   .wp-block-file .wp-block-file__button{background-color: <?php echo $ColoreLinkBody; ?>!important }
   
    .it-footer-main{color: <?php echo get_theme_mod( 'scuola_footer_text_color', "#fff" ); ?>;}
    a, a:hover, a.read-more,.ui-widget-content a{ color: <?php echo $ColoreLinkBody; ?>; }
	#collapseDivFAQ button.faq[aria-expanded="true"]{color: <?php echo $ColoreTestoBottone; ?>!important;}
    #collapseDivFAQ button.faq  {color: <?php echo $ColoreLinkBody; ?>!important;}
    button, input[type="submit"], .btn-primary, .btn-primary:hover, .btn-primary:not(:disabled):not(.disabled):active,.badge-primary, #ListaServizi a:hover, #ListaServizi a:visited,  #ListaServizi a:active, #collapseDivFAQ button.faq  { color: <?php echo $ColoreTestoBottone; ?>!Important;background-color: <?php echo $ColoreBottone; ?>; box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.1); }
    .bottone, a.bottone :hover, a.bottone :active  { color: <?php echo $ColoreTestoBottone; ?>!Important;background-color: <?php echo $ColoreBottone; ?>!Important;}
    a.badge-primary:active,a.badge-primary:hover{color: <?php echo $ColoreBottone; ?>;background-color: <?php echo $ColoreTestoBottone; ?>; box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.1);}
    .btn-outline-primary { color: <?php echo $ColoreLinkBody; ?>!Important; box-shadow: inset 0 0 0 1px <?php echo $ColoreLinkBody; ?>; }
    .btn-outline-primary:hover, .btn-outline-primary:not(:disabled):not(.disabled):active { color: <?php echo $ColoreLinkBody; ?>!Important; box-shadow: inset 0 0 0 2px <?php echo $ColoreLinkBody; ?>; }
    #footer, .it-footer-main { background-color: <?php echo $ColoreFooter; ?>; }
    #footer a { color: <?php echo $ColoreLinkFooter; ?>!important; }
    #footer {color: <?php echo $ColoreTestoFooter; ?>!important; 
}
	@media (min-width:1200px) {
	     .it-list-wrapper .it-list a:hover {color: <?php echo $ColoreLinkBody; ?>;}
	}   
   	
 </style>
<!-- Fine Custom <head> content -->
  <?php 
  }
function HasSocial(){
	if (   get_theme_mod('scuola_social_facebook') == "" && 
		   get_theme_mod('scuola_social_twitter') == "" && 
	       get_theme_mod('scuola_social_youtube') == "" && 
	       get_theme_mod('scuola_social_instagram') == "" && 
	       get_theme_mod('scuola_social_telegram') == "" && 
	       get_theme_mod('scuola_social_linkedin') == "" )
	    return FALSE;
	else
		return TRUE;
}
function get_MenuSocial(){?>
                <?php if (get_theme_mod('scuola_social_facebook') != ""): ?>
                    <li class="list-inline-item">
                        <a  target="_blank" class="social-icon"
                        aria-label="<?php echo __('Link ad un sito esterno','wpscuola'); ?> - Facebook"
                        href="<?php echo get_theme_mod('scuola_social_facebook');?>"
                        title="<?php echo __('Seguici su Facebook','wpscuola'); ?>">
                            <span class="fab fa-facebook-f"></span> 
                            <span class="sr-only"> <?php echo __('Seguici su Facebook','wpscuola'); ?></span>
                        </a>
                    </li>
                <?php endif; 
                    if (get_theme_mod('scuola_social_twitter') != ""): ?>
                    <li class="list-inline-item">
                        <a  target="_blank" class="social-icon"
                        aria-label="<?php echo __('Link ad un sito esterno','wpscuola'); ?> - Facebook"
                        href="<?php echo get_theme_mod('scuola_social_twitter');?>"
                        title="<?php echo __('Seguici su Twitter','wpscuola'); ?>">
                            <span class="fab fa-twitter"></span> 
                            <span class="sr-only"> <?php echo __('Seguici su Twitter','wpscuola'); ?></span>
                        </a>
                    </li>
                <?php endif;
                 if (get_theme_mod('scuola_social_youtube') != ""): ?>
                    <li class="list-inline-item">
                        <a  target="_blank" class="social-icon"
                        aria-label="<?php echo __('Link ad un sito esterno','wpscuola'); ?> - Facebook"
                        href="<?php echo get_theme_mod('scuola_social_youtube');?>"
                        title="<?php echo __('Seguici su YouTube','wpscuola'); ?>">
                            <span class="fab fa-youtube"></span> 
                            <span class="sr-only"> <?php echo __('Seguici su YouTube','wpscuola'); ?></span>
                        </a>
                    </li>
                <?php endif; 
                 if (get_theme_mod('scuola_social_instagram') != ""): ?>
                    <li class="list-inline-item">
                        <a  target="_blank" class="social-icon"
                        aria-label="<?php echo __('Link ad un sito esterno','wpscuola'); ?> - Facebook"
                        href="<?php echo get_theme_mod('scuola_social_instagram');?>"
                        title="<?php echo __('Seguici su Instagram','wpscuola'); ?>">
                            <span class="fab fa-instagram"></span> 
                            <span class="sr-only"> <?php echo __('Seguici su Instagram','wpscuola'); ?></span>
                        </a>
                    </li>
                <?php endif; 
                 if (get_theme_mod('scuola_social_telegram') != ""): ?>
                    <li class="list-inline-item">
                        <a  target="_blank" class="social-icon"
                        aria-label="<?php echo __('Link ad un sito esterno','wpscuola'); ?> - Facebook"
                        href="<?php echo get_theme_mod('scuola_social_telegram');?>"
                        title="<?php echo __('Seguici su Telegram','wpscuola'); ?>">
                            <span class="fab fa-telegram-plane"></span> 
                            <span class="sr-only"> <?php echo __('Seguici su Telegram','wpscuola'); ?></span>
                        </a>
                    </li>
                <?php endif; 
                 if (get_theme_mod('scuola_social_linkedin') != ""): ?>
                    <li class="list-inline-item">
                        <a  target="_blank" class="social-icon"
                        aria-label="<?php echo __('Link ad un sito esterno','wpscuola'); ?> - Facebook"
                        href="<?php echo get_theme_mod('scuola_social_linkedin');?>"
                        title="<?php echo __('Seguici su LinkedIn','wpscuola'); ?>">
                            <span class="fab fa-linkedin-in"></span> 
                            <span class="sr-only"> <?php echo __('Seguici su LinkedIn','wpscuola'); ?></span>
                        </a>
                    </li>
                <?php endif;
                 if (get_theme_mod('scuola_social_Nomefree1') != ""): ?>
                    <li class="list-inline-item">
                        <a  target="_blank" class="social-icon"
                        aria-label="<?php echo __('Link ad un sito esterno','wpscuola'); ?> - Facebook"
                        href="<?php echo get_theme_mod('scuola_social_Indirizzofree1');?>"
                        title="<?php echo __('Seguici su ','wpscuola')." ".get_theme_mod('scuola_social_Nomefree1'); ?>">
                            <?php echo get_theme_mod('scuola_social_Iconafree1');?> 
                            <span class="sr-only"> <?php echo __('Seguici su ','wpscuola')." ".get_theme_mod('scuola_social_Nomefree1'); ?></span>
                        </a>
                    </li>
                <?php endif;
                 if (get_theme_mod('scuola_social_Nomefree2') != ""): ?>
                    <li class="list-inline-item">
                        <a  target="_blank" class="social-icon"
                        aria-label="<?php echo __('Link ad un sito esterno','wpscuola'); ?> - Facebook"
                        href="<?php echo get_theme_mod('scuola_social_Indirizzofree2');?>"
                        title="<?php echo __('Seguici su ','wpscuola')." ".get_theme_mod('scuola_social_Nomefree2'); ?>">
                            <?php echo get_theme_mod('scuola_social_Iconafree2');?> 
                            <span class="sr-only"> <?php echo __('Seguici su ','wpscuola')." ".get_theme_mod('scuola_social_Nomefree2'); ?></span>
                        </a>
                    </li>
                <?php endif; 
}
// Breadcrumbs
// Breadcrumbs
function custom_breadcrumbs() {
       
    // If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
    $custom_taxonomy    = 'product_cat';
       
    // Get the query & post information
    global $post,$wp_query;
       
    // Do not display on the homepage
    if ( !is_front_page() ) {
       
        // Build the breadcrums
        echo '<nav class="breadcrumb-container" aria-label="breadcrumb">
  <ol class="breadcrumb">';
           
        // Home page
        echo '<li class="breadcrumb-item"><a href="' . get_home_url() . '" title="Home Page"> Home</a></li>';
        echo '<span class="separator">/</span>';
           
 		if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {
              
            // If post is a custom post type
            $post_type = get_post_type();
              
            // If it is a custom post type display name and link
            if($post_type != 'post') {
                  
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
              
                echo '<li class="ibreadcrumb-item"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
                echo '<span class="separator">/</span>';
              
            }
              
            $custom_tax_name = get_queried_object()->name;
            echo '<li class="breadcrumb-item active">' . $custom_tax_name . '</li>';
              
        } else if ( is_single() ) {
              
            // If post is a custom post type
            $post_type = get_post_type();
              
            // If it is a custom post type display name and link
            if($post_type != 'post') {
                  
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
              
                echo '<li class="breadcrumb-item"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
                echo '<span class="separator">/</span>';
              
            }
              
            // Get post category info
            $category = get_the_category();
             
            if(!empty($category)) {
                // Get last category post is in
                $last_category = end($category);
                  
                // Get parent any categories and create array
                $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','),',');
                $cat_parents = explode(',',$get_cat_parents);
                  
                // Loop through parent categories and store in variable $cat_display
                $cat_display = '';
                foreach($cat_parents as $parents) {
                    $cat_display .= '<li class="breadcrumb-item">'.$parents.'</li>';
                    $cat_display .= '<span class="separator">/</span>';
                }
             
            }
            $tax=get_queried_object()->taxonomy;
 			global $wp_taxonomies;
    		$cpt=isset( $wp_taxonomies[$tax] )  ? $wp_taxonomies[$tax]->object_type : array();
            $post_type=end($cpt);
             // If it's a custom post type within a custom taxonomy
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);
            if(empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
                   
                $taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );
                $cat_id         = $taxonomy_terms[0]->term_id;
                $cat_nicename   = $taxonomy_terms[0]->slug;
                $cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name       = $taxonomy_terms[0]->name;
               
            }
              
            // Check if the post is in a category
            if(!empty($last_category)) {
                echo $cat_display;
                echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
                  
            // Else if post is in a custom taxonomy
            } else if(!empty($cat_id)) {
                  
                echo '<li class="breadcrumb-item"><a href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';
                echo '<span class="separator">/</span>';
                echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
              
            } else {
                  
                echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
                  
            }
              
        } else if ( is_category() ) {
               
            // Category page
            echo '<li class="breadcrumb-item active">' . single_cat_title('', false) . '</li>';
               
        } else if ( is_page() ) {
               
            // Standard page
            if( $post->post_parent ){
                   
                // If child page, get parents 
                $anc = get_post_ancestors( $post->ID );
                   
                // Get parents in the right order
                $anc = array_reverse($anc);
                   
                // Parent page loop
                if ( !isset( $parents ) ) $parents = null;
                foreach ( $anc as $ancestor ) {
                    $parents .= '<li class="breadcrumb-item"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
                    $parents .= '<span class="separator">/</span>';
                }
                   
                // Display parent pages
                echo $parents;
                   
                // Current page
                echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
                   
            } else {
                   
                // Just display current page if not parents
                echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
                   
            }
               
        } else if ( is_tag() ) {
               
            // Tag page
               
            // Get tag information
            $term_id        = get_query_var('tag_id');
            $taxonomy       = 'post_tag';
            $args           = 'include=' . $term_id;
            $terms          = get_terms( $taxonomy, $args );
			if(isset($terms)) {
            	$get_term_name  = $terms[0]->name;
			}else{
				$get_term_name="";
			}
            // Display the tag name
            echo '<li class="breadcrumb-item active">' . $get_term_name . '</li>';
           
        } elseif ( is_day() ) {
               
            // Day archive
               echo "ciccio";
            // Year link
            echo '<li class="breadcrumb-item"><a href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') .'</a></li>';
            echo '<span class="separator">/</span>';
               
            // Month link
            echo '<li class="breadcrumb-item active"><a href="' . get_month_link( get_the_time('Y'), get_the_time('m') ) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . '</a></li>';
            echo '<span class="separator">/</span>';
               
            // Day display
            echo '<li class="breadcrumb-item active">' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</li>';
               
        } else if ( is_month() ) {
               
            // Month Archive
               
            // Year link
            echo '<li class="breadcrumb-item"><a href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y').'</a></li>';
            echo '<span class="separator">/</span>';
               
            // Month display
            echo '<li class="breadcrumb-item">' . get_the_time('F') . '</li>';
               
        } else if ( is_year() ) {
               
            // Display year archive
            echo '<li class="breadcrumb-item active">' . get_the_time('Y') .'</li>';
               
        } else if ( is_author() ) {
               
            // Auhor archive
               
            // Get the author information
            global $author;
            $userdata = get_userdata( $author );
               
            // Display author name
            echo '<li class="breadcrumb-item active">' . ''.__("Articoli di","wpscuola").': ' . $userdata->display_name . '</li>';
           
        } else if ( get_query_var('paged') ) {
               
            // Paginated archives
            echo '<li class="breadcrumb-item active">'.__('Pagina','wpscuola') . ' ' . get_query_var('paged') . '</li>';
               
        } else if ( is_search() ) {
           
            // Search results page
            echo '<li class="breadcrumb-item active">'.__('Risultati della ricerca','wpscuola') . ': ' . get_search_query() . '</li>';
           
        } elseif ( is_404() ) {
               
            // 404 page
            echo '<li>' . 'Error 404' . '</li>';
        }
       
        echo '</ol>
    </nav>';
           
    }
       
}