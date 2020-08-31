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
add_filter( 'image_size_names_choose', 		'scuola_image_sizes');
add_filter( 'wp_title', 					'scuola_filter_wp_title');
add_filter( 'get_comments_number', 			'scuola_comments_number');
add_filter( 'language_attributes', 			'add_opengraph_doctype');
add_filter( 'excerpt_more', 				'new_excerpt_more');
add_filter( 'excerpt_length', 				'custom_excerpt_length', 10 );
add_filter( 'pre_get_posts',				'scuola_SearchFilter');
add_filter( 'the_password_form', 			'scuola_password_form' );
/**
* Riattiva la gestione dei link standard di Wordpress 
* I link vengono utilizzati in home page nel widget GalleraLinks
*/
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

add_action( 'wp_enqueue_scripts', 			'enqueue_scuola_public' );
add_action( 'comment_form_before', 			'scuola_enqueue_comment_reply_script');
add_action( 'init',							'scuola_inizialize');
add_action( 'widgets_init', 				'scuola_register_Widget' );
add_action( 'customize_register', 			'scuola_customize_register');
add_action( 'wp_head', 						'scuola_opengraph');
add_action( 'wp_head', 						'scuola_customize_head' );
add_action( 'enqueue_block_editor_assets', 	'gutenberg_styles' );
add_action( 'wp_footer', 					'scuola_customize_footer');
add_action( 'admin_enqueue_scripts', 		'enqueue_scuola_admin' );
add_action( 'after_setup_theme', 			'scuola_setup');
add_action( 'init', 						'personaliza_blocco_file' );

add_post_type_support ('page', 	'excerpt');
/**
* 
* Personalizzazione blocco file dell'editor Gutenberg
*
*/
function personaliza_file_render( $attributes, $content ) {
  $IDFile=$attributes['id'];
  $Link=$attributes['href'];
  $Title = get_post($IDFile)->post_title; //The Title
  $Description = get_post($IDFile)->post_content; // The Description	
  $filesize = size_format(filesize( get_attached_file( $IDFile ) ), 2); 
  $filetype = wp_check_filetype($Link);
  $IconaFile='<i class="far fa-file"></i>';
/* 
  ob_start();
	var_dump($content);
	$a=ob_get_contents();
  ob_end_clean();
*/
	$PosT=strpos($content,"target");
	$Target="";
	if($PosT!==FALSE){
		$Target=substr($content,$PosT,strpos($content," ",$PosT)-$PosT);	
	}
	$Div=substr($content,0,strpos($content,">")+1);
	$PosST=strpos($content,">",strlen($Div)+1)+1;
	$Titolo=substr($content,$PosST,strpos($content,"<",$PosST+1)-$PosST);
	if($Title!=$Titolo) $Title=$Titolo;
  switch ($filetype['ext']){
  	case "txt": 
  	case "odt": $IconaFile='<i class="far fa-file-alt fa-2x"></i>'; break;
  	case "pdf": $IconaFile='<i class="far fa-file-pdf fa-2x"></i>'; break;
 	case "csv": $IconaFile='<i class="fas fa-file-csv fa-2x"></i>'; break;
  	case "doc":
  	case "rtf":
  	case "docx": $IconaFile='<i class="far fa-file-word fa-2x"></i>'; break;
  	case "xls":
  	case "ods":
  	case "xlsx": $IconaFile='<i class="far fa-file-excel fa-2x"></i>'; break;
  	case "ppt":
  	case "odp": 
  	case "pptx": $IconaFile='<i class="far fa-file-powerpoint fa-2x"></i>'; break;
  	case "mp4":  $IconaFile='<i class="far fa-file-video fa-2x"></i>'; break;
  	case "zip":  $IconaFile='<i class="far fa-file-archive fa-2x"></i>'; break;
  	case "png":
  	case "jpg":
  	case "jpeg":
  	case "bmp":
  	case "ico":$IconaFile='<i class="far fa-file-image fa-2x"></i>'; break;
  }
  $Contenuto=$Div.
  $IconaFile.' <a href="'.$Link.'" title="'.$Title.'" '.$Target.'>'.$Title.' ('.$filesize .')</a>';
    	
  if(strpos($content,"wp-block-file__button")!==FALSE) $Contenuto.='<a href="'.$Link.'" class="wp-block-file__button" download="">Download</a>';
  if($Description) $Contenuto.='<br /><span>'.$Description.'</span>';
  $Contenuto.='</div>';
  return $Contenuto;
}

function personaliza_blocco_file() {
	register_block_type( 'core/file', array(
		'render_callback' => 'personaliza_file_render',
	) );
}


/* UPDATER THEME VERSION */
require 'inc/theme-update-checker.php';
$update_checker = new ThemeUpdateChecker(
    'wp-scuola',
    'https://raw.githubusercontent.com/ignazios/wp-scuola/master/wp-scuola.json'
);

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
 	add_theme_support('title-tag');
	add_theme_support('automatic-feed-links');
	add_theme_support('post-thumbnails');
    add_theme_support('post-thumbnails' );
     add_theme_support('custom-background', array('default-color' => 'ffffff','default-image' => get_template_directory_uri() . '/img/blank.png',));
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
	'menu-main' => __('Main Menu', 'wpscuola'),
	'mega-main' => __('Mega Menu', 'wpscuola'),
	'menu-social'  => __( 'Social Menu', 'wpscuola' ),
	'menu-footer'  => __( 'Footer Menu', 'wpscuola' ),
	'menu-footer-ente'  => __( 'Footer Menu Ente', 'wpscuola' ),
	'menu-footer-legale'  => __( 'Footer Menu Legale', 'wpscuola' ),
	'menu-footer-secondo'  => __( 'Footer Menu secondo', 'wpscuola' ),
));
}
// Inclusione libreria personalizzazione link
require get_template_directory() . '/plugins/link/custom_link.php';
// Inclusione libreria per la personalizzazione delle impostazioni del tema
require get_template_directory() . '/inc/customizer.php';
// Inclusione libreria per la personalizzazione dell'elenco delle categorie
require get_template_directory() . '/inc/my_class-walker-category.php';
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
if(get_theme_mod('scuola_servizi_attiva')){
	require get_template_directory() . '/plugins/servizi/scuola_servizi.php';
	$my_servizi=new ScuolaServizi();
}	  

//var_dump(get_theme_mod('scuola_prenotazioni_attiva'));wp_die();
if(get_theme_mod('scuola_modpren_attiva')){
	require get_template_directory() . '/plugins/prenotazioni/Prenotazioni.php';
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
if (function_exists("at_sezioni_shtc")){
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
    wp_enqueue_style('scuola_fonts_Awesome', get_template_directory_uri() . '/lib/bootstrap-italia/css/all.css');
            wp_enqueue_script('jquery-ui-tooltip');
}

function enqueue_scuola_public() {
    wp_enqueue_style( 'bootstrap-italia-min', get_template_directory_uri() . "/lib/bootstrap-italia/css/bootstrap-italia.min.css");
    wp_enqueue_style( 'bootstrap-italia-map', get_template_directory_uri() . "/lib/bootstrap-italia/css/bootstrap-italia.min.css.map");
    wp_enqueue_style( 'bootstrap-italia-icon-font', get_template_directory_uri() . "/lib/bootstrap-italia/css/italia-icon-font.css");
    wp_enqueue_style( 'general-style', get_template_directory_uri() . "/style.css");
    wp_enqueue_style( 'scuola_fonts_Awesome', get_template_directory_uri() . '/lib/bootstrap-italia/css/all.css');
//	wp_enqueue_script( 'PopperScript', get_template_directory_uri().'/lib/js/popper.min.js', array('jquery'),null ,true );
    wp_enqueue_script( 'bootstrap-italia_bundle_min_script', get_template_directory_uri().'/lib/bootstrap-italia/js/bootstrap-italia.bundle.min.js', array('jquery'),null ,true );
	wp_enqueue_script('scuola-public-script', get_template_directory_uri().'/js/Public.js', array('jquery'),null ,true );
	if (is_front_page()) {
		wp_enqueue_script( 'scuola-image_hover_effects_JS', get_template_directory_uri() . '/lib/js/production.min.js' );
		wp_enqueue_style( 'scuola-image_hover_effects_CSS', get_template_directory_uri() . '/lib/css/image_hover_effects.css');	
	}
};

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
		'before_widget' => '<section id=""%1$s" class="home-widget container %2$s">',
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
  	'before_title' => '<h6 class="widget-title">',
  	'after_title' => '</h6>',
  ));

	register_sidebar( array(
		'name' => __('Footer Widget Area', 'wpscuola') ,
		'id' => 'footer-widget-area',
		'description'   => __( 'Widget area che compare nel footer.', 'wpscuola' ),
		'before_widget' => '<div id="%1$s" class="col-lg widget-container shadow p-2 %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	));

	register_sidebar( array(
		'name' => __('Footer Sub Widget Area', 'wpscuola') ,
		'id' => 'footer-sub-widget-area',
		'description'   => __( 'Widget area che compare sotto la footer widget area.', 'wpscuola' ),
		'before_widget' => '<div id="%1$s" class="col-lg widget-container shadow p-2 %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	));

	register_sidebar( array(
		'name' => __('Post Footer Widget Area', 'wpscuola') ,
		'id' => 'single-footer-widget-area',
		'description'   => __( 'Widget area che compare sotto il contenuto del singolo post.', 'wpscuola' ),
		'before_widget' => '<div id="%1$s" class="col-lg widget-container shadow p-2 mb-4 %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	));

	register_sidebar( array(
		'name' => __('Page Footer Widget Area', 'wpscuola') ,
		'id' => 'page-footer-widget-area',
		'description'   => __( 'Widget area che compare sotto il contenuto della singola pagina.', 'wpscuola' ),
		'before_widget' => '<div id="%1$s" class="col-lg widget-container shadow p-2 mb-4 %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	));

	if(class_exists("EM_Event")){
		register_sidebar(array(
			'name' => __('Event Sidebar Widget Area', 'wpscuola') ,
			'id' => 'event-widget-area',
			'description'   => __( 'Widget area che compare nella sidebar degli eventi.', 'wpscuola' ),
			'before_widget' => '<div id="%1$s" class="widget-container shadow p-2 %2$s">',
			'after_widget' => "</div>",
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
	}
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

}

function scuola_register_Widget(){
	register_widget( 'Articoli' );
	register_widget( 'Articoli_Griglia' );
	register_widget( 'Trasparenza' );
	register_widget( 'Feed_RSS' );
	register_widget( 'Link' );
	if(class_exists("EM_Event")) 					register_widget( 'my_EM_Widget_Calendar' );
	if(get_theme_mod('scuola_servizi_attiva'))		register_widget( 'Servizi' );
	if(function_exists("at_sezioni_shtc"))			register_widget( 'my_ATWidget' );	
	if(get_theme_mod("scuola_circolari_attiva"))	register_widget( 'CircolariScuola' );
}
/**
* Inclusione librerie dei Widget
*/
require get_template_directory() . '/widget/widget_calendario.php';
require get_template_directory() . '/widget/widget_feedRSS.php';
require get_template_directory() . '/widget/widget_trasparenza.php';
require get_template_directory() . '/widget/widget_articoli.php';
require get_template_directory() . '/widget/widget_articoli_griglia.php';
require get_template_directory() . '/widget/widget_link.php';
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

function scuola_customize_head() { 
	$Regole="";
	if (!is_admin_bar_showing ()) {
//		$Regole=".sticky .it-header-navbar-wrapper, #myHeader{margin-top: 0!important;padding-top:0;}";
		$Regole=".sticky{margin-top: 0!important;padding-top:0;}";
	}else{
		$Regole="@media screen and (min-width: 600px){
	.sticky {
	    margin-top: 25px;
	}
}
@media screen and (min-width: 600px) and (max-width: 782px){
	.sticky {
	    margin-top: 45px;
	}
}
@media screen and (max-width: 599px){
	.sticky {
	    margin-top: 0;
	}
}";
	}?>
<!-- Custom <head> content -->
  <style type="text/css">
  body {color: <?php echo get_theme_mod( 'scuola_text_color', "#000000" ); ?>;}
  .it-header-center-wrapper .it-header-center-content-wrapper .it-brand-wrapper a, .it-header-center-wrapper .it-header-center-content-wrapper .it-right-zone, .it-right-zone .nav li a{color: <?php echo get_theme_mod( 'scuola_head_link_color', "#fff" );!important ?> }
  .wp-block-file .wp-block-file__button{background-color: <?php echo get_theme_mod( 'scuola_link_color', "#0066cc" );!important ?> }
   .mysearchform input[type="text"], .it-header-wrapper .mysearchform input[type="text"], .mysearchform [type="submit"]{color: <?php echo get_theme_mod( 'scuola_head_text_color', "#fff" );!important ?> }
    .mysearchform input[type="text"], .it-header-wrapper .mysearchform input[type="text"]{box-shadow: 0 1px 0px <?php echo get_theme_mod( 'scuola_head_text_color', "#fff" );!important ?>;}
    .it-footer-main{color: <?php echo get_theme_mod( 'scuola_footer_text_color', "#000000" ); ?>;}
  	#content {background-color:#<?php echo get_theme_mod( 'background_color' ); ?>;}
    .it-header-center-wrapper, .it-header-navbar-wrapper, .it-header-wrapper { background-color: <?php echo get_theme_mod( 'scuola_head_color', "#0066cc" ); ?>;}
    a, a:hover, a.read-more, .menu-main .nav li ul a, .menu-main .nav li ul a:hover, .menu-main .nav li:hover ul a { color: <?php echo get_theme_mod('scuola_link_color', "#0066cc"); ?>; }
    button, input[type="submit"], .btn-primary { background-color: <?php echo get_theme_mod( 'scuola_link_color', "#0066cc" ); ?>; }
    .btn-primary:hover, .btn-primary:not(:disabled):not(.disabled):active { background-color: <?php echo get_theme_mod( 'scuola_link_color', "#0066cc" ); ?>; box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.1); }
    .btn-outline-primary { color: <?php echo get_theme_mod( 'scuola_link_color', "#0066cc" ); ?>; box-shadow: inset 0 0 0 1px <?php echo get_theme_mod( 'scuola_link_color', "#0066cc" ); ?>; }
    .btn-outline-primary:hover, .btn-outline-primary:not(:disabled):not(.disabled):active { color: <?php echo get_theme_mod( 'scuola_link_color', "#0066cc" ); ?>; box-shadow: inset 0 0 0 2px <?php echo get_theme_mod( 'scuola_link_color', "#0066cc" ); ?>; }
    html, #footer, .it-footer-main { background-color: <?php echo get_theme_mod( 'scuola_footer_color', '#004080' ); ?>; }
    #footer a { color: <?php echo get_theme_mod('scuola_footer_link_color', "#ffffff"); ?>!important; }
    #footer {background-color: #004080; color: <?php echo get_theme_mod('scuola_footer_text_color', "#ffffff"); ?>!important; 
}
	.my-bg-primary { background-color: <?php echo get_theme_mod( 'scuola_head_color', "#0066cc" ); ?>; }
	@media (min-width:1200px) {
	     .it-list-wrapper .it-list a:hover {color: <?php echo get_theme_mod( 'scuola_link_color', "#0066cc" ); ?>;}
	}   
   	<?php echo $Regole; ?>
 </style>
<!-- Fine Custom <head> content -->
  <?php 
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
/*    if ($query-> is_home () && $query-> is_main_query ()) {
    	$query-> set ( 'posts_per_page' , 1 );
	}
*/    return $query;
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

function get_MenuSocial($ulID="menu-social",$ulClass="nav",$liClass="menu-item"){?>
	<ul id="<?php echo $ulID;?>" class="<?php echo $ulClass;?>">
	<?php if (get_theme_mod('scuola_social_facebook')){?>
		<li class="<?php echo $liClass;?>">
			<a href="<?php echo get_theme_mod('scuola_social_facebook');?>" target="_blank"><i class="fab fa-facebook-f"></i> <span class="sr-only"> link al canale Facebook</span></a>
		</li>
	<?php }
		if (get_theme_mod('scuola_social_twitter')){?>
		<li class="<?php echo $liClass;?>">
			<a href="<?php echo get_theme_mod('scuola_social_twitter');?>" target="_blank"><i class="fab fa-twitter"></i> <span class="sr-only"> link al canale Twitter</span></a>
		</li>
	<?php }		
		if (get_theme_mod('scuola_social_youtube')){?>
		<li class="<?php echo $liClass;?>">
			<a href="<?php echo get_theme_mod('scuola_social_youtube');?>" target="_blank"><i class="fab fa-youtube"></i> <span class="sr-only"> link al canale YouTube</span></a>
		</li>
	<?php }		
		if (get_theme_mod('scuola_social_instagram')){?>
		<li class="<?php echo $liClass;?>">
			<a href="<?php echo get_theme_mod('scuola_social_instagram');?>" target="_blank"><i class="fab fa-instagram"></i> <span class="sr-only"> link al canale Instagram</span></a>
		</li>
	<?php }		
		if (get_theme_mod('scuola_social_telegram')){?>
		<li class="<?php echo $liClass;?>">
			<a href="<?php echo get_theme_mod('scuola_social_telegram');?>" target="_blank"><i class="fab fa-telegram-plane"></i> <span class="sr-only"> link al canale Telegram</span></a>
		</li>
	<?php }		
		if (get_theme_mod('scuola_social_linkedin')){?>
		<li class="<?php echo $liClass;?>">
			<a href="<?php echo get_theme_mod('scuola_social_linkedin');?>" target="_blank"><i class="fab fa-linkedin-in"></i> <span class="sr-only"> link al canale LinkedIn</span></a>
		</li>
	<?php }?>
	</ul>
<?php
}
function scuola_password_form() {  
global $post;
	$content = '
  		  <form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post" class="pt-5">
			<div class="form-group">
			    <div class="form-row">
					<div class="form-group col-md-6">
						<input name="post_password" type="password" class="form-control input-password" id="exampleInputPassword" aria-labelledby="infoPassword" >
				    	<span class="password-icon" aria-hidden="true"><i class="far fa-eye"></i></span>
				        <label for="exampleInputPassword">Inserire la password per visualizzare '.(get_post_type( $PostID) =="circolari_scuola"?"la Circolare":"l'Articolo").'</label>
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
			return '<img src="'.get_template_directory_uri().'/img/thumbnail-default.png" title="Immagine di default" alt="Immagine di default">'; 
		}
		$Image=wp_get_attachment_url($IDImgEvidenza);
		$ImageTitle = get_post($IDImgEvidenza)->post_title; //The Title
		$ImageAlt = get_post_meta($IDImgEvidenza, '_wp_attachment_image_alt', TRUE); //The Caption
		$ImageDescription = get_post($IDImgEvidenza)->post_content; // The Description	
     	return '<img src="'.$Image.'" title="'.$ImageTitle.'" alt="'.$ImageAlt.'" longdesc="'.$ImageDescription.'">';
	}else return $img_thumbnail;
 }