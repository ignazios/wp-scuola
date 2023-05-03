<?php

function scuola_customize_register($wp_customize) {

$categories = get_categories('hide_empty=0');
$cate=array();
if (count($categories)!=0)
	foreach ($categories as $category) {
		$cate[$category->term_id]=$category->cat_name." (".$category->category_count.")";
	}
$pages=get_pages();	
$pagine=array();
if (count($pages)!=0)
	foreach ($pages as $page) {
		$pagine[$page->ID]=$page->post_title;
	} 
$posts=get_posts();	
$articoli=array();
if (count($posts)!=0)
	foreach ($posts as $post) {
		$articoli[$post->ID]=$post->post_title;
	}  
$wp_customize->add_setting( 'Scuola_Titolo_Corto' , array(
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'Scuola_Titolo_Corto', array(
    'type' 		=> 'text',
    'label' 	=> 'Titolo corto:',
    'section' 	=> 'title_tagline',
    'settings' => 'Scuola_Titolo_Corto',
    'priority'	=> 1));
/**
*  Crea pannello per impostazione Tema
*/
$wp_customize->add_panel('scuola_settings',array(
    'title'=>'Impostazioni Scuola',
    'description'=> 'Opzioni del tema per la Scuola',
    'priority'=> 1));
/**
*  Sotto Pannello Dati Amministrazione
*/
$wp_customize->add_section('Scuola_Amministrazione_Settings',array(
    'title'			 =>'Dati Amministrazione',
    'priority'		 =>1,
    'description'	 => '<h2>Dati Amministrazione</h2>
                         Gestione dei dati dell\'amministrazione:
                         <ul>
                         	<li>Indirizzo</li>
                         	<li>N° di telefono</li>
                         	<li>Indirizzi email</li>
                         	<li>Codici amministrazione</li>
                         	<li>Codice IPA</li>
                         	<li>Codici IBAN</li>
                         </ul>',
    'panel'			 =>'scuola_settings',
));
// Campo Indirizzo
    $wp_customize->add_setting( 'Scuola_Amministrazione_Indirizzo' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_Indirizzo', array(
        'type' 		=> 'text',
        'label' 	=> 'Indirizzo:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_Indirizzo',
        'priority'	=> 1));	
// Campo CAP
    $wp_customize->add_setting( 'Scuola_Amministrazione_CAP' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_CAP', array(
        'type' 		=> 'text',
        'label' 	=> 'CAP:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_CAP',
        'priority'	=> 2));	
// Campo Città
    $wp_customize->add_setting( 'Scuola_Amministrazione_Citta' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_Citta', array(
        'type' 		=> 'text',
        'label' 	=> 'Città:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_Citta',
        'priority'	=> 3));
// Campo Des Telefono
    $wp_customize->add_setting( 'Scuola_Amministrazione_DesTelefono' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_DesTelefono', array(
        'type' 		=> 'text',
        'label' 	=> 'Descrizione Telefono:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_DesTelefono',
        'priority'	=> 4));        
// Campo Telefono
    $wp_customize->add_setting( 'Scuola_Amministrazione_Telefono' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_Telefono', array(
        'type' 		=> 'text',
        'label' 	=> 'Telefono:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_Telefono',
        'priority'	=> 5));        
// Campo Des Telefono2
    $wp_customize->add_setting( 'Scuola_Amministrazione_DesTelefono2' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_DesTelefono2', array(
        'type' 		=> 'text',
        'label' 	=> 'Descrizione Secondo Telefono:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_DesTelefono2',
        'priority'	=> 6));        
// Campo Telefono2
    $wp_customize->add_setting( 'Scuola_Amministrazione_Telefono2' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_Telefono2', array(
        'type' 		=> 'text',
        'label' 	=> 'Secondo Telefono:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_Telefono2',
        'priority'	=> 7));        
// Campo Des Telefono3
    $wp_customize->add_setting( 'Scuola_Amministrazione_DesTelefono3' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_DesTelefono3', array(
        'type' 		=> 'text',
        'label' 	=> 'Descrizione Terzo Telefono:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_DesTelefono3',
        'priority'	=> 8));        
// Campo Telefono3
    $wp_customize->add_setting( 'Scuola_Amministrazione_Telefono3' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_Telefono3', array(
        'type' 		=> 'text',
        'label' 	=> 'Terzo Telefono:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_Telefono3',
        'priority'	=> 9));        
// Campo Des Telefono4
    $wp_customize->add_setting( 'Scuola_Amministrazione_DesTelefono4' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_DesTelefono4', array(
        'type' 		=> 'text',
        'label' 	=> 'Descrizione Quarto Telefono:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings'  => 'Scuola_Amministrazione_DesTelefono4',
        'priority'	=> 10));        
// Campo Telefono4
    $wp_customize->add_setting( 'Scuola_Amministrazione_Telefono4' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_Telefono4', array(
        'type' 		=> 'text',
        'label' 	=> 'Quarto Telefono:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings'  => 'Scuola_Amministrazione_Telefono4',
        'priority'	=> 11));
// Campo Email
    $wp_customize->add_setting( 'Scuola_Amministrazione_Email' , array(
        'sanitize_callback' => 'scuola_sanitize_email',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_Email', array(
        'type' 		=> 'text',
        'label' 	=> 'Email:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings'  => 'Scuola_Amministrazione_Email',
        'priority'	=> 12));
// Campo Seconda Email
    $wp_customize->add_setting( 'Scuola_Amministrazione_Email2' , array(
        'sanitize_callback' => 'scuola_sanitize_email',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_Email2', array(
        'type' 		=> 'text',
        'label' 	=> 'Seconda Email:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings'  => 'Scuola_Amministrazione_Email2',
        'priority'	=> 13));
// Campo PEC
    $wp_customize->add_setting( 'Scuola_Amministrazione_PEC' , array(
        'sanitize_callback' => 'scuola_sanitize_email',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_PEC', array(
        'type' 		=> 'text',
        'label' 	=> 'PEC:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_PEC',
        'priority'	=> 14));
// Campo C.F.
    $wp_customize->add_setting( 'Scuola_Amministrazione_CFPA' , array(
        'sanitize_callback' => 'scuola_sanitize_number',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_CFPA', array(
        'type' 		=> 'text',
        'label' 	=> 'Codice Fiscale:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_CFPA',
        'priority'	=> 15));
// Campo Partita IVA
    $wp_customize->add_setting( 'Scuola_Amministrazione_PIVA' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_PIVA', array(
        'type' 		=> 'text',
        'label' 	=> 'Partita IVA:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_PIVA',
        'priority'	=> 16));
// Campo Codice Meccanografico
    $wp_customize->add_setting( 'Scuola_Amministrazione_CM' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_CM', array(
        'type' 		=> 'text',
        'label' 	=> 'Cod. Meccanografico:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_CM',
        'priority'	=> 17));
// Campo Codice IPA
    $wp_customize->add_setting( 'Scuola_Amministrazione_IPA' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_IPA', array(
        'type' 		=> 'text',
        'label' 	=> 'Cod. IPA:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_IPA',
        'priority'	=> 18));
// Campo Link Amministrazione By IPA
    $wp_customize->add_setting( 'Scuola_Amministrazione_LinkIndicePaByIPA' , array(
        'sanitize_callback' => 'scuola_sanitize_url',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_LinkIndicePaByIPA', array(
        'type' 		=> 'text',
        'label' 	=> 'Link Amministrazione by IPA:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_LinkIndicePaByIPA',
        'priority'	=> 19));
// Campo Cod. Univoco
    $wp_customize->add_setting( 'Scuola_Amministrazione_CodUni' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_CodUni', array(
        'type' 		=> 'text',
        'label' 	=> 'Cod. Univoco:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_CodUni',
        'priority'	=> 20));
// Campo IBAN
    $wp_customize->add_setting( 'Scuola_Amministrazione_IBAN' , array(
        'sanitize_callback' => 'scuola_sanitize_string',
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'Scuola_Amministrazione_IBAN', array(
        'type' 		=> 'text',
        'label' 	=> 'IBAN:',
        'section' 	=> 'Scuola_Amministrazione_Settings',
        'settings' => 'Scuola_Amministrazione_IBAN',
        'priority'	=> 21));
/**
*  Sotto Pannello Impostazione Header
*/
$wp_customize->add_section('Scuola_Header_Settings',array(
    'title'			 =>'Impostazione dell\'Header',
    'priority'		 =>2,
    'description'	 => '<h2>Personalizzazione dell\'Header</h2>',
    'panel'			 =>'scuola_settings'));
$wp_customize->add_setting( 'Scuola_AmmAfferente_Nome' , array(
    'sanitize_callback' => 'scuola_sanitize_string',
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'Scuola_AmmAfferente_Nome', array(
    'type' 		=> 'text',
    'label' 	=> 'Nome Amministrazione Afferente:',
    'section' 	=> 'Scuola_Header_Settings',
    'settings' => 'Scuola_AmmAfferente_Nome',
    'priority'	=> 1));
$wp_customize->add_setting( 'Scuola_AmmAfferente_URL' , array(
    'sanitize_callback' => 'scuola_sanitize_url',
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'Scuola_AmmAfferente_URL', array(
    'type' 		=> 'text',
    'label' 	=> 'URL Amministrazione Afferente (con HTTP o HTTPS):',
    'section' 	=> 'Scuola_Header_Settings',
    'settings' => 'Scuola_AmmAfferente_URL',
    'priority'	=> 2));	
$wp_customize->add_setting('Scuola_AmmAfferente_Logo', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => 'absint'
));
$wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'Scuola_AmmAfferente_Logo', array(
    'section' => 'Scuola_Header_Settings',
    'label' => 'Immagine Amministrazione Afferente dim. 120x40',
    'width' => 120,
    'height' => 40,
    'priority'	=> 3)));
$wp_customize->add_setting( 'Scuola_Login' , array(
	'capability' => 'edit_theme_options',
	'sanitize_callback' => 'scuola_sanitize_checkbox') );
$wp_customize->add_control( 'Scuola_Login', array(
    'type' => 'checkbox',
    'label' 	=> 'Nascondi Login ',
    'section' 	=> 'Scuola_Header_Settings',
    'settings' => 'Scuola_Login',
    'priority'	=> 4));
$wp_customize->add_setting('Scuola_Logo', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => 'absint'
));$wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'Scuola_Logo', array(
    'section' => 'Scuola_Header_Settings',
    'label' => 'Logo Scuola dim. 100x100',
    'width' => 100,
    'height' => 100,
    'priority'	=> 5)));
/**
*  Sotto Pannello Footer
*/
$wp_customize->add_section('Scuola_Footer_Settings',array(
    'title'			 =>'Impostazione del footer',
    'priority'		 =>3,
    'description'	 => 'Personalizzazione del footer',
    'panel'			 =>'scuola_settings'));   
$wp_customize->add_setting('scuola_mappa_attiva', array(
    'default' => false, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_mappa_attiva', array(
    'label' => 'Visualizza riferimento Mappa nel footer',
    'priority'	=> 1,
    'type' => 'checkbox', 
    'section' => 'Scuola_Footer_Settings', 
    'settings' => 'scuola_mappa_attiva'));
$wp_customize->add_setting('scuola_mappa_titolo', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_mappa_titolo', array(
    'type' => 'text', 
    'label' => 'Titolo del link',
    'section' => 'Scuola_Footer_Settings', 
    'settings' => 'scuola_mappa_titolo',
    'priority'	=> 2));
$wp_customize->add_setting('scuola_mappa_link', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_url',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_mappa_link', array(
    'type' => 'textarea', 
    'label' => 'URL della Mappa',
    'section' => 'Scuola_Footer_Settings', 
    'settings' => 'scuola_mappa_link',
    'priority'	=> 3));  
$wp_customize->add_setting('scuola_mappa_frame', array(
    'default' => "", 
   'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_mappa_frame', array(
    'type' => 'textarea', 
    'label' => 'Frame della miniatura della Mappa',
    'description' => 'Inserire un frame width="100%" height="100%" o lasciare vuoto per mantenere solo il link testuale',
    'section' => 'Scuola_Footer_Settings', 
    'settings' => 'scuola_mappa_frame',
    'priority'	=> 4));  
/**
*  Sotto Pannello Cookies
*/
$wp_customize->add_section('Scuola_Cookies_Settings',array(
    'title'			 =>'Impostazione Cookies',
    'priority'		 =>4,
    'description'	 => 'Personalizzazione del Banner di gestione dei Cookies',
    'panel'			 =>'scuola_settings'));   
$wp_customize->add_setting('scuola_cookies_attiva', array(
    'default' => false, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_cookies_attiva', array(
    'label' => 'attiva la gestione dell\'informativa sui Cookies',
    'priority'	=> 1,
    'type' => 'checkbox', 
    'section' => 'Scuola_Cookies_Settings', 
    'settings' => 'scuola_cookies_attiva'));
$wp_customize->add_setting('scuola_cookies_testoMSG', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',    
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_cookies_testoMSG', array(
    'type' => 'textarea', 
    'label' => 'Testo messaggio',
    'priority'	=> 2,
    'description' => 'testo alternativo a quello standard <br /><strong>Questo sito utilizza cookie tecnici, analytics e di terze parti.
Proseguendo nella navigazione accetti l’utilizzo dei cookie.</strong><br />Si possono utilizzare tag HTML come &lt;em&gt; &lt;strong&gt; &lt;br /&gt;',
    'section' => 'Scuola_Cookies_Settings', 
    'settings' => 'scuola_cookies_testoMSG',));
// Pulsante di sinistra
$wp_customize->add_setting( 'scuola_cookies_pulsSXText' , array(
    'default'	 => 'Privacy Policy',
    'sanitize_callback' => 'scuola_sanitize_string',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_cookies_pulsSXText', array(
    'type' 		=> 'text',
    'label' 	=> 'Testo link sinistra:',
    'section' 	=> 'Scuola_Cookies_Settings',
    'settings' => 'scuola_cookies_pulsSXText',
    'priority'	=> 3));
$wp_customize->add_setting( 'scuola_cookies_pulsSXlink' , array(
    'sanitize_callback' => 'scuola_sanitize_url',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_cookies_pulsSXlink', array(
    'type' 		=> 'select',
    'label' 	=> 'Seleziona la pagina della Privacy policy',
    'section' 	=> 'Scuola_Cookies_Settings',
    'settings' => 'scuola_cookies_pulsSXlink',
    'priority'	=> 4,
    'choices'   => $pagine));	   
// Pulsante di Accetta
    $wp_customize->add_setting( 'scuola_cookies_accettaText' , array(
        'default'	=> 'Accetto',
        'sanitize_callback' => 'scuola_sanitize_string',    
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'scuola_cookies_accettaText', array(
        'type' 		=> 'text',
        'label' 	=> 'Testo link Accetta:',
        'section' 	=> 'Scuola_Cookies_Settings',
        'settings' => 'scuola_cookies_accettaText',
        'priority'	=> 5)); 
/**
*  Sotto Pannello Social
*/
$wp_customize->add_section('Scuola_Social_Settings',array(
    'title'			 =>'Impostazione Social',
    'priority'		 =>5,
    'description'	 => 'Sezione che permette di impostare i canali social della scuola',
    'panel'			 =>'scuola_settings'));   
// Campo Facebook
$wp_customize->add_setting( 'scuola_social_facebook' , array(
    'sanitize_callback' => 'scuola_sanitize_url',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_facebook', array(
    'type' 		=> 'text',
    'label' 	=> 'Facebook:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_facebook',
    'priority'	=> 1));
// Campo Twitter
$wp_customize->add_setting( 'scuola_social_twitter' , array(
    'sanitize_callback' => 'scuola_sanitize_url',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_twitter', array(
    'type' 		=> 'text',
    'label' 	=> 'Twitter:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_twitter',
    'priority'	=> 2));
// Campo YouTube
$wp_customize->add_setting( 'scuola_social_youtube' , array(
    'sanitize_callback' => 'scuola_sanitize_url',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_youtube', array(
    'type' 		=> 'text',
    'label' 	=> 'YouTube:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_youtube',
    'priority'	=> 3));
// Campo Instagram
$wp_customize->add_setting( 'scuola_social_instagram' , array(
    'sanitize_callback' => 'scuola_sanitize_url',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_instagram', array(
    'type' 		=> 'text',
    'label' 	=> 'Instagram:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_instagram',
    'priority'	=> 4));        
// Campo Telegram
$wp_customize->add_setting( 'scuola_social_telegram' , array(
    'sanitize_callback' => 'scuola_sanitize_url',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_telegram', array(
    'type' 		=> 'text',
    'label' 	=> 'Telegram:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_telegram',
    'priority'	=> 5));                
// Campo LinkedIn
$wp_customize->add_setting( 'scuola_social_linkedin' , array(
    'sanitize_callback' => 'scuola_sanitize_url',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_linkedin', array(
    'type' 		=> 'text',
    'label' 	=> 'LinkedIn:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_linkedin',
    'priority'	=> 6));     
// Campo Social Free 1
$wp_customize->add_setting( 'scuola_social_Nomefree1' , array(
    'sanitize_callback' => 'scuola_sanitize_string',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_Nomefree1', array(
    'type' 		=> 'text',
    'label' 	=> 'Nome Social personale 1:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_Nomefree1',
    'priority'	=> 7));  
$wp_customize->add_setting( 'scuola_social_Iconafree1' , array(
    'sanitize_callback' => 'scuola_sanitize_string',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_Iconafree1', array(
    'type' 		=> 'text',
    'label' 	=> 'Icona Social personale 1:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_Iconafree1',
    'priority'	=> 8));  
$wp_customize->add_setting( 'scuola_social_Indirizzofree1' , array(
    'sanitize_callback' => 'scuola_sanitize_url',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_Indirizzofree1', array(
    'type' 		=> 'text',
    'label' 	=> 'Link Social personale 1:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_Indirizzofree1',
    'priority'	=> 9));  
// Campo Social Free 2
$wp_customize->add_setting( 'scuola_social_Nomefree2' , array(
    'sanitize_callback' => 'scuola_sanitize_string',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_Nomefree2', array(
    'type' 		=> 'text',
    'label' 	=> 'Nome Social personale 2:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_Nomefree2',
    'priority'	=> 10));  
$wp_customize->add_setting( 'scuola_social_Iconafree2' , array(
    'sanitize_callback' => 'scuola_sanitize_string',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_Iconafree2', array(
    'type' 		=> 'text',
    'label' 	=> 'Icona Social personale 2:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_Iconafree2',
    'priority'	=> 11));  
$wp_customize->add_setting( 'scuola_social_Indirizzofree2' , array(
    'sanitize_callback' => 'scuola_sanitize_url',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_Indirizzofree2', array(
    'type' 		=> 'text',
    'label' 	=> 'Link Social personale 2:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_Indirizzofree2',
    'priority'	=> 12));           
// Campo Titolo Sezione Footer
$wp_customize->add_setting( 'scuola_social_footersectionTesto' , array(
	'default'	=> 'Seguici su',
    'sanitize_callback' => 'scuola_sanitize_string',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_social_footersectionTesto', array(
    'type' 		=> 'text',
    'label' 	=> 'Titolo della sezione Footer:',
    'section' 	=> 'Scuola_Social_Settings',
    'settings' => 'scuola_social_footersectionTesto',
    'priority'	=> 13));         
/**
*  Sotto Pannello Default
*/
$wp_customize->add_section('Scuola_Default_Settings',array(
    'title'			 =>'Default',
    'priority'		 =>7,
    'description'	 => 'Miscellanea di impostazioni di default',
    'panel'			 =>'scuola_settings'));   
$wp_customize->add_setting('scuola_DefautlImg', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => 'absint'));
$wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'scuola_DefautlImg', array(
    'section' => 'Scuola_Default_Settings',
    'label' => 'Immagine che verrà visualizzata se l\'articolo non possiede l\'immagine in Evidenza',
    'height' => 200,
    'width' => 200,
    'priority'	=> 5)));

/**
*  Sotto Pannello Moduli
*/
$wp_customize->add_section('Scuola_Moduli_Settings',array(
    'title'			 =>'Moduli funzionalità',
    'priority'		 =>6,
    'description'	 => 'Moduli che aggiungono funzionalità al tema',
    'panel'			 =>'scuola_settings'));   
$wp_customize->add_setting('scuola_faq_attiva', array(
    'default' => false, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_faq_attiva', array(
    'label' => 'Attiva il modulo FAQ',
    'description'	=>' per visualizzare le FAQ in una pagina usare il seguente shortcode<br />
    <strong>[FAQ order="" orderby="" posts_per_page="" gruppi=""]</strong>
    <ul>
    <li>order => [asc, desc] crescente,decrescente</li>
    <li>orderby => [ none (nessun ordine)<br />
					 title (titolo)<br />
					 date  (data di pubblicazione)<br />
					 rand  (casuale)<br />
					 menu_order (attributo ordine) </li>
	<li>gruppi =>[elenco di gruppi di faq separati da <strong>,</strong>]</li>
	</ul>
	<strong>es.</strong>
    <em>[FAQ orderby="menu_order" gruppi="didattica,amministrazione,servizi"]</em>',
    'priority'	=> 1,
    'type' => 'checkbox', 
    'section' => 'Scuola_Moduli_Settings', 
    'settings' => 'scuola_faq_attiva'));
$wp_customize->add_setting('scuola_eventi_attiva', array(
    'default' => false, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_eventi_attiva', array(
    'label' => 'Attiva il modulo Eventi',
    'description'	=>' Il modulo prevede un Custom Post Type <em><strong>scuola_eventi</strong></em> ed un blocco che viene visualizzato in Home Page come primo elemento',
    'priority'	=> 2,
    'type' => 'checkbox', 
    'section' => 'Scuola_Moduli_Settings', 
    'settings' => 'scuola_eventi_attiva'));
$wp_customize->add_setting('scuola_servizi_attiva', array(
    'default' => false, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_servizi_attiva', array(
    'label' => 'Attiva il modulo Servizi',
    'description'	=>' Il modulo prevede un Custom Post Type <em><strong>servizio</strong></em> ed un widget che permette di interfacciare i servizi',
    'priority'	=> 3,
    'type' => 'checkbox', 
    'section' => 'Scuola_Moduli_Settings', 
    'settings' => 'scuola_servizi_attiva'));
$wp_customize->add_setting('scuola_circolari_attiva', array(
    'default' => false, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_circolari_attiva', array(
    'label' => 'Attiva il modulo Circolari',
    'description'	=>' Il modulo permette di integrare la gestione delle Circolari Scolastiche' ,
    'priority'	=> 4,
    'type' => 'checkbox', 
    'section' => 'Scuola_Moduli_Settings', 
    'settings' => 'scuola_circolari_attiva'));
$wp_customize->add_setting('scuola_modpren_attiva', array(
    'default' => false, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_modpren_attiva', array(
    'label' => 'Attiva il modulo Prenotazioni',
    'description'	=>' Il modulo permette di integrare la gestione delle Prenotazione degli spazi scolastici' ,
    'priority'	=> 5,
    'type' => 'checkbox', 
    'section' => 'Scuola_Moduli_Settings', 
    'settings' => 'scuola_modpren_attiva'));
$wp_customize->add_setting('scuola_modorarioArgo_attiva', array(
    'default' => false, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_modorarioArgo_attiva', array(
    'label' => 'Attiva il modulo Orario Argo',
    'description'	=>' Il modulo permette di visualizzare l\'orario scolastico attraverso l\'importazione dei dati dal programma ARGO Darwin' ,
    'priority'	=> 6,
    'type' => 'checkbox', 
    'section' => 'Scuola_Moduli_Settings', 
    'settings' => 'scuola_modorarioArgo_attiva'));
/**
*  Sotto Pannello MetaDati
*/
$wp_customize->add_section('Scuola_Metadati_Settings',array(
    'title'			 =>'Metadati comunicazioni',
    'priority'		 =>6,
    'description'	 => '<strong>Impostazione dei Metadati che vengono visualizzati per i Post, le Pagine e Comunicazioni</strong>',
    'panel'			 =>'scuola_settings'));  
$wp_customize->add_setting('scuola_MTdata_attiva', array(
    'default' => TRUE, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_MTdata_attiva', array(
    'label' => 'Attiva Visualizzazione Data',
    'description'	=>'<strong><em>Questa opzione attiva la visualizzazione della data.</strong></em>' ,
    'type' => 'checkbox', 
    'section' => 'Scuola_Metadati_Settings', 
    'settings' => 'scuola_MTdata_attiva',
    'priority'	=> 1,
));
$wp_customize->add_setting('scuola_MTdataLink_attiva', array(
    'default' => TRUE, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_MTdataLink_attiva', array(
    'label' => 'Attiva link archivio Data',
    'description'	=>'<strong><em>Questa opzione permette di attivare il link all\'archivio della data.<br />Funzionalità solo se è attiva la visualizzazione della Data</strong></em>' ,
    'type' => 'checkbox', 
    'section' => 'Scuola_Metadati_Settings', 
    'settings' => 'scuola_MTdataLink_attiva',
    'priority'	=> 2,
));
$wp_customize->add_setting('scuola_MTautore_attiva', array(
    'default' => TRUE, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_MTautore_attiva', array(
    'label' => 'Attiva Visualizzazione Autore',
    'description'	=>'<strong><em>Questa opzione attiva la visualizzazione dell\'autore.</strong></em>' ,
    'type' => 'checkbox', 
    'section' => 'Scuola_Metadati_Settings', 
    'settings' => 'scuola_MTautore_attiva',
    'priority'	=> 3,
));
$wp_customize->add_setting('scuola_MTautoreLink_attiva', array(
    'default' => TRUE, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_MTautoreLink_attiva', array(
    'label' => 'Attiva link archivio Autore',
    'description'	=>'<strong><em>Questa opzione permette di attivare il link all\'archivio dell\'autore.<br />Funzionalità solo se è attiva la visualizzazione dell\'autore</strong></em>' ,
    'type' => 'checkbox', 
    'section' => 'Scuola_Metadati_Settings', 
    'settings' => 'scuola_MTautoreLink_attiva',
    'priority'	=> 4,
));
$wp_customize->add_setting('scuola_docconteggio_attiva', array(
    'default' => TRUE, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'));
$wp_customize->add_control('scuola_docconteggio_attiva', array(
    'label' => 'Attiva il conteggio delle visite di pagine/articoli/CPT',
    'description'	=>'<strong><em>Questa opzioni permette di visualizzare il numero di visualizzazioni di ogni Pagina/Articolo/Custom Post Type</strong></em>' ,
    'priority'	=>5,
    'type' => 'checkbox', 
    'section' => 'Scuola_Metadati_Settings', 
    'settings' => 'scuola_docconteggio_attiva'));
$wp_customize->add_control( 'scuola_docconteggio_reset', array(
    'type' => 'button',
    'settings' => array(),
    'priority' => 6,
    'section' => 'Scuola_Metadati_Settings',
    'input_attrs' => array(
        'value' => __( 'Azzera Conteggio', 'textdomain' ), 
        'class' => 'button button-primary', 
    ),) );
/**
*  Crea pannello per impostazione Home Page
*/
$wp_customize->add_panel('scuola_settings_home',array(
    'title'=>'Blocchi Home Page per la scuola',
    'description'=> 'Opzioni valide solo se si utilizza il template di pagina Home Page',
    'priority'=> 2));
/**
*  Sotto Pannello Hero
*/
$wp_customize->add_section('scuola_hero_settings',array(
    'title'			 =>'Hero',
    'priority'		 =>3,
    'description'	 => '<h2>Blocco Hero</h2>
    Opzioni valide solo se si utilizza il template di pagina Home Page	
    <ul>
    	<li>Attivazione del blocco</li>
    	<li>La pagina da cui verrà presa l\'immagine in evidenza ed impostata come sfondo, il Titolo ed il riassunto</li>
    	<li>Il testo del pulsante che rimanda alla pagina</li>
    	<li>L\'effetto di Overlay</li>
    </ul>',
    'panel'			 =>'scuola_settings_home',
));
$wp_customize->add_setting('Scuola_Hero_Active', array(
    'default' => false, 
    'capability' => 'edit_theme_options', 
    'sanitize_callback' => 'scuola_sanitize_checkbox'
));
$wp_customize->add_control('Scuola_Hero_Active', array(
    'label' => 'Attiva Blocco',
    'type' => 'checkbox', 
    'section' => 'scuola_hero_settings', 
    'settings' => 'Scuola_Hero_Active',
    'priority'	=> 1,
));
$wp_customize->add_setting( 'Scuola_Hero_Page' , array(
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'Scuola_Hero_Page', array(
    'type' 		=> 'select',
    'label' 	=> 'Seleziona la pagina',
    'section' 	=> 'scuola_hero_settings',
    'settings' => 'Scuola_Hero_Page',
    'priority'	=> 2,
    'choices'   => $pagine));			
$wp_customize->add_setting( 'Scuola_Hero_LeggiTutto' , array(
    'sanitize_callback' => 'scuola_sanitize_string',    
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'Scuola_Hero_LeggiTutto', array(
    'type' 		=> 'text',
    'label' 	=> 'Testo Bottone Leggi Tutto:',
    'section' 	=> 'scuola_hero_settings',
    'settings' => 'Scuola_Hero_LeggiTutto',
    'priority'	=> 3));
$wp_customize->add_setting('Scuola_Hero_Overlay', array(
    'default' => false, 
    'sanitize_callback' => 'scuola_sanitize_string',    
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('Scuola_Hero_Overlay', array(
    'label' => 'Effetto Overlay',
    'type' => 'select', 
    'section' => 'scuola_hero_settings', 
    'settings' => 'Scuola_Hero_Overlay',
    'priority'	=> 4,
    'choices'   => array(""				=> "Senza overlay",
    					 "it-dark"		=> "Overlay scuro",
    					 "it-primary"	=> "Overlay primario")));
/**
*  Sotto Pannello In Evedidenza
*/
$wp_customize->add_section('scuola_comeevidenza_settings',array(
    'title'			 =>'Comunicazioni in evidenza',
    'priority'		 =>4,
    'description'	 => '<h2>Blocco Comunicazioni in evidenza</h2>
    Opzioni valide solo se si utilizza il template di pagina Home Page',
    'panel'			 =>'scuola_settings_home',
));
$wp_customize->add_setting('scuola_comeevidenza_attiva', array(
    'default' => false, 
    'sanitize_callback' => 'scuola_sanitize_checkbox',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_comeevidenza_attiva', array(
    'type' => 'checkbox', 
    'label' => 'Visualizza Blocco in Evidenza',
    'section' => 'scuola_comeevidenza_settings', 
    'settings' => 'scuola_comeevidenza_attiva',
    'priority'	=> 1));
$wp_customize->add_setting( 'scuola_comeevidenza_categoria' , array(
    'sanitize_callback' => 'scuola_sanitize_checkbox',
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_comeevidenza_categoria', array(
    'type' 		=> 'select',
    'label' 	=> 'Seleziona la categoria in Evidenza',
    'section' 	=> 'scuola_comeevidenza_settings',
    'settings' => 'scuola_comeevidenza_categoria',
    'priority'	=> 2,
    'choices'   => $cate));			
$wp_customize->add_setting( 'scuola_comeevidenza_numart' , array(
	'sanitize_callback' => 'scuola_sanitize_number',
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_comeevidenza_numart', array(
    'type' 		=> 'number',
    'input_attrs' => array( 'min' => 0, 'max' => 10),
    'label' 	=> 'Quanti articoli visualizzare?',
    'section' 	=> 'scuola_comeevidenza_settings',
    'settings' => 'scuola_comeevidenza_numart',
    'priority'	=> 3,
    'choices'   => 4));	
$wp_customize->add_setting( 'scuola_comeevidenza_visimevidenza' , array(
	'default' => true,
    'sanitize_callback' => 'scuola_sanitize_checkbox',
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_comeevidenza_visimevidenza', array(
    'type' 		=> 'checkbox',
    'label' 	=> 'Visualizza Immagine in Evidenza',
    'section' 	=> 'scuola_comeevidenza_settings',
    'settings' => 'scuola_comeevidenza_visimevidenza',
    'priority'	=> 3,
    'choices'   => 5));	
/**
*  Sotto Pannello Blocco scuola
*/
$wp_customize->add_section('scuola_scuola_settings_home',array(
    'title'			 =>'Blocco Scuola',
    'priority'		 =>5,
    'description'	 => '<h2>Blocco Scuola</h2>
     Opzioni valide solo se si utilizza il template di pagina Home Page',
    'panel'			 =>'scuola_settings_home',
));
$wp_customize->add_setting('scuola_scuola_attiva', array(
    'default' => false, 
    'sanitize_callback' => 'scuola_sanitize_checkbox',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_attiva', array(
    'type' => 'checkbox', 
    'label' => 'Visualizza Blocco Scuola',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_attiva',
    'priority'	=> 1));
$wp_customize->add_setting('scuola_scuola_titolosezione', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_titolosezione', array(
    'type' => 'text', 
    'label' => 'Titolo della sezione',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_titolosezione',
    'priority'	=> 2));
$wp_customize->add_setting('scuola_scuola_Imgblocco1', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => 'absint'));
$wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'scuola_scuola_Imgblocco1', array(
    'section' => 'scuola_scuola_settings_home',
    'label' => 'Immagine Primo Blocco',
    'width' => 200,
    'height' => 100,
    'priority'	=> 3)));
$wp_customize->add_setting('scuola_scuola_Titoloblocco1', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_Titoloblocco1', array(
    'type' => 'text', 
    'label' => 'Titolo del Primo Blocco',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_Titoloblocco1',
    'priority'	=> 4));
$wp_customize->add_setting('scuola_scuola_Linkblocco1', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_url',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_Linkblocco1', array(
    'type' => 'text', 
    'label' => 'Link del Primo Blocco',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_Linkblocco1',
    'priority'	=> 5));
$wp_customize->add_setting('scuola_scuola_Imgblocco2', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => 'absint'));
$wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'scuola_scuola_Imgblocco2', array(
    'section' => 'scuola_scuola_settings_home',
    'label' => 'Immagine Secondo Blocco',
    'width' => 200,
    'height' => 100,
    'priority'	=> 6)));
$wp_customize->add_setting('scuola_scuola_Titoloblocco2', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_Titoloblocco2', array(
    'type' => 'text', 
    'label' => 'Titolo del Secondo Blocco',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_Titoloblocco2',
    'priority'	=> 7));
$wp_customize->add_setting('scuola_scuola_Linkblocco2', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_url',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_Linkblocco2', array(
    'type' => 'text', 
    'label' => 'Link del Secondo Blocco',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_Linkblocco2',
    'priority'	=> 8));
$wp_customize->add_setting('scuola_scuola_Imgblocco3', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => 'absint'));
$wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'scuola_scuola_Imgblocco3', array(
    'section' => 'scuola_scuola_settings_home',
    'label' => 'Immagine Terzo Blocco',
    'width' => 200,
    'height' => 100,
    'priority'	=> 9)));
$wp_customize->add_setting('scuola_scuola_Titoloblocco3', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_Titoloblocco3', array(
    'type' => 'text', 
    'label' => 'Titolo del Terzo Blocco',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_Titoloblocco3',
    'priority'	=> 10));
$wp_customize->add_setting('scuola_scuola_Linkblocco3', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_url',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_Linkblocco3', array(
    'type' => 'text', 
    'label' => 'Link del Terzo Blocco',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_Linkblocco3',
    'priority'	=> 11));
$wp_customize->add_setting('scuola_scuola_Imgblocco4', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => 'absint'
));
$wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'scuola_scuola_Imgblocco4', array(
    'section' => 'scuola_scuola_settings_home',
    'label' => 'Immagine Quarto Blocco',
    'width' => 200,
    'height' => 100,
    'priority'	=> 12)));
$wp_customize->add_setting('scuola_scuola_Titoloblocco4', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_Titoloblocco4', array(
    'type' => 'text', 
    'label' => 'Titolo del Quarto Blocco',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_Titoloblocco4',
    'priority'	=> 13));
$wp_customize->add_setting('scuola_scuola_Linkblocco4', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_url',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_Linkblocco4', array(
    'type' => 'text', 
    'label' => 'Link del Quarto Blocco',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_Linkblocco4',
    'priority'	=> 14));
$wp_customize->add_setting('scuola_scuola_PrimaTabTesto', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_PrimaTabTesto', array(
    'type' => 'text', 
    'label' => 'Testo della prima Cartella',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_PrimaTabTesto',
    'priority'	=> 15));
$wp_customize->add_setting( 'scuola_scuola_PrimaTabPagina' , array(
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_scuola_PrimaTabPagina', array(
    'type' 		=> 'select',
    'label' 	=> 'Seleziona la pagina Prima Cartella',
    'section' 	=> 'scuola_scuola_settings_home',
    'settings' => 'scuola_scuola_PrimaTabPagina',
    'priority'	=> 17,
    'choices'   => $pagine));	
$wp_customize->add_setting('scuola_scuola_PrimaTabLinkVideoImg', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => 'absint'
));
$wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'scuola_scuola_PrimaTabLinkVideoImg', array(
    'section' => 'scuola_scuola_settings_home',
    'label' => 'Immagine del video',
    'width' => 600,
    'height' => 280,
    'priority'	=> 18)));
$wp_customize->add_setting( 'scuola_scuola_PrimaTabVideo' , array(
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options') );
$wp_customize->add_control('scuola_scuola_PrimaTabVideo', array(
    'type' => 'text', 
    'label' => 'Codice del video',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_PrimaTabVideo',
    'priority'	=> 19));
$wp_customize->add_setting( 'scuola_scuola_PrimaTabVideoDidascalia' , array(
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options') );
$wp_customize->add_control('scuola_scuola_PrimaTabVideoDidascalia', array(
    'type' => 'text', 
    'label' => 'Didascalia del video',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_PrimaTabVideoDidascalia',
    'priority'	=> 20));
$wp_customize->add_setting('scuola_scuola_PrimaTabLinkImg', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => 'absint'
));
$wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'scuola_scuola_PrimaTabLinkImg', array(
    'section' => 'scuola_scuola_settings_home',
    'label' => 'Immagine Che viene visualizzata prima del link',
    'width' => 600,
    'height' => 280,
    'priority'	=> 21)));
$wp_customize->add_setting( 'scuola_scuola_PrimaTabLinkTesto' , array(
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options') );
$wp_customize->add_control('scuola_scuola_PrimaTabLinkTesto', array(
    'type' => 'text', 
    'label' => 'Testo Link',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_PrimaTabLinkTesto',
    'priority'	=> 22));    
$wp_customize->add_setting( 'scuola_scuola_PrimaTabLink' , array(
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options') );
$wp_customize->add_control('scuola_scuola_PrimaTabLink', array(
    'type' => 'text', 
    'label' => 'Link',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_PrimaTabLink',
    'priority'	=> 23));  
$wp_customize->add_setting('scuola_scuola_PrimaTabLeggiTutto', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
   'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_PrimaTabLeggiTutto', array(
    'type' => 'text', 
    'label' => 'Testo del link leggi tutto',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_PrimaTabLeggiTutto',
    'priority'	=> 24));
$wp_customize->add_setting('scuola_scuola_SecondaTabTesto', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_SecondaTabTesto', array(
    'type' => 'text', 
    'label' => 'Testo della seconda Cartella',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_SecondaTabTesto',
    'priority'	=> 25));
$wp_customize->add_setting( 'scuola_scuola_SecondaTabPagina' , array(
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_scuola_SecondaTabPagina', array(
    'type' 		=> 'select',
    'label' 	=> 'Seleziona la pagina Seconda Cartella',
    'section' 	=> 'scuola_scuola_settings_home',
    'settings' => 'scuola_scuola_SecondaTabPagina',
    'priority'	=> 26,
    'choices'   => $pagine));
$wp_customize->add_setting('scuola_scuola_SecondaTabLeggiTutto', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_SecondaTabLeggiTutto', array(
    'type' => 'text', 
    'label' => 'Testo del link leggi tutto',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_SecondaTabLeggiTutto',
    'priority'	=> 27));
$wp_customize->add_setting('scuola_scuola_TerzaTabTesto', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_TerzaTabTesto', array(
    'type' => 'text', 
    'label' => 'Testo della terza Cartella',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_TerzaTabTesto',
    'priority'	=> 28));
$wp_customize->add_setting( 'scuola_scuola_TerzaTabPagina' , array(
	'capability' => 'edit_theme_options') );
$wp_customize->add_control( 'scuola_scuola_TerzaTabPagina', array(
    'type' 		=> 'select',
    'label' 	=> 'Seleziona la pagina Terza Cartella',
    'section' 	=> 'scuola_scuola_settings_home',
    'settings' => 'scuola_scuola_TerzaTabPagina',
    'priority'	=> 29,
    'choices'   => $pagine));
$wp_customize->add_setting('scuola_scuola_TerzaTabLeggiTutto', array(
    'default' => "", 
    'sanitize_callback' => 'scuola_sanitize_string',
    'capability' => 'edit_theme_options'));
$wp_customize->add_control('scuola_scuola_TerzaTabLeggiTutto', array(
    'type' => 'text', 
    'label' => 'Testo del link leggi tutto',
    'section' => 'scuola_scuola_settings_home', 
    'settings' => 'scuola_scuola_TerzaTabLeggiTutto',
    'priority'	=> 30));
/**
*  Pannello Colori
*/ 
//Colori del Corpo 
$wp_customize->add_setting( 'scuola_background_color' , array(
    'default'     => "#ffffff",
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'scuola_background_color', array(
    'label'       => __( 'Colore di sfondo', 'scuola' ),
    'section'     => 'colors',
    'settings'     => 'scuola_background_color'
  ) ) ); 
$wp_customize->add_setting( 'scuola_text_color' , array(
    'default'     => "#000000",
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'scuola_text_color', array(
    'label'       => __( 'Colore del testo', 'scuola' ),
    'section'     => 'colors',
    'settings'     => 'scuola_text_color'
  ) ) );
  $wp_customize->add_setting( 'scuola_link_color' , array(
    'default'     => "#0066cc",
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
   $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'scuola_link_color', array(
    'label'       => __( 'Colore dei link', 'scuola' ),
    'section'     => 'colors',
    'settings'     => 'scuola_link_color'
  ) ) );
//Colori dell'Head
 $wp_customize->add_setting( 'scuola_head_color', array(
    'default' => '#0066cc',
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'scuola_head_color', array(
    'label'      => __( 'Header Colore di sfondo', 'scuola' ),
    'section'    => 'colors',
    'settings'   => 'scuola_head_color'
  ) ) );
  $wp_customize->add_setting( 'scuola_head_text_color' , array(
    'default'     => "#fff",
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'scuola_head_text_color', array(
    'label'       => __( 'Header Colore del testo', 'scuola' ),
    'section'     => 'colors',
    'settings'     => 'scuola_head_text_color'
  ) ) );
  $wp_customize->add_setting( 'scuola_head_link_color' , array(
    'default'     => "#65dcdf",
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'scuola_head_link_color', array(
    'label'       => __( 'Header Colore dei link', 'scuola' ),
    'section'     => 'colors',
    'settings'     => 'scuola_head_link_color'
  ) ) );
//Colori dei Bottoni
  $wp_customize->add_setting( 'scuola_button_color' , array(
    'default'     => "#0066cc",
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'scuola_button_color', array(
    'label'       => __( 'Colore dei Bottoni', 'scuola' ),
    'section'     => 'colors',
    'settings'     => 'scuola_button_color'
  ) ) );
  $wp_customize->add_setting( 'scuola_button_text_color' , array(
    'default'     => "#fff",
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'scuola_button_text_color', array(
    'label'       => __( 'Colore del testo dei Bottoni', 'scuola' ),
    'section'     => 'colors',
    'settings'     => 'scuola_button_text_color'
  ) ) );
//Colori del Footer  
  $wp_customize->add_setting( 'scuola_footer_color' , array(
    'default'     => "#00264d",
    'sanitize_callback' => 'sanitize_hex_color',
  ) );

  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'scuola_footer_color', array(
    'label'       => __( 'Footer Colore di sfondo', 'scuola' ),
    'section'     => 'colors',
    'settings'     => 'scuola_footer_color'
  ) ) );
  $wp_customize->add_setting( 'scuola_footer_text_color' , array(
    'default'     => "#fff",
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'scuola_footer_text_color', array(
    'label'       => __( 'Footer Colore del testo', 'scuola' ),
    'section'     => 'colors',
    'settings'     => 'scuola_footer_text_color'
  ) ) );
  $wp_customize->add_setting( 'scuola_footer_link_color' , array(
    'default'     => "#65dcdf",
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'scuola_footer_link_color', array(
    'label'       => __( 'Footer Colore dei link', 'scuola' ),
    'section'     => 'colors',
    'settings'     => 'scuola_footer_link_color'
  ) ) );
  /**
*  Crea pannello per impostazione Script
*/
	$wp_customize->add_panel('scuola_script',array(
	    'title'=>'Script',
	    'description'=> 'Impostazione script nell\'Header e nel Footer',
	    'priority'=> 3));
	$wp_customize->add_section('Scuola_script_header',array(
	    'title'			 =>'Script Header',
	    'priority'		 =>1,
	    'description'	 => '<h2>Script da inserire nell\'Header</h2>',
	    'panel'			 =>'scuola_script',
	));
// Campo Script
    $wp_customize->add_setting( 'scuola_header_script' , array(
		'capability' => 'edit_theme_options') );
	$wp_customize->add_control( 'scuola_header_script', array(
        'type' 		=> 'textarea',
        'label' 	=> 'Codice da inserire nell\'header',
 		'description' => 'Il codice inserito in questa textarea verrà riportata nell\'header di ogni pagina<br />FARE ATTENZIONE AL CODICE CHE SI INSERISCE, VALIDARLO PRIMA DELL\'INSERIMENTO',       
 		'section' 	=> 'Scuola_script_header',
        'settings' => 'scuola_header_script',
        'priority'	=> 1));	

	$wp_customize->add_section('Scuola_script_footer',array(
	    'title'			 =>'Script Footer',
	    'priority'		 =>1,
	    'description'	 =>'<h2>Script da inserire nel Footer</h2>',
	    'panel'			 =>'scuola_script',
	));
	$wp_customize->add_setting('scuola_footer_script', array(
	    'default' => "", 
 	    'capability' => 'edit_theme_options'));
	$wp_customize->add_control('scuola_footer_script', array(
	    'type' => 'textarea', 
	    'label' => 'Codice da inserire nel footer',
	    'description' => 'Il codice inserito in questa textarea verrà riportata nel footer di ogni pagina<br />FARE ATTENZIONE AL CODICE CHE SI INSERISCE, VALIDARLO PRIMA DELL\'INSERIMENTO',
	    'section' => 'Scuola_script_footer', 
	    'settings' => 'scuola_footer_script',
	    'priority'	=> 5));  
}
function scuola_sanitize_number( $input ) {
    if (is_numeric($input)) { 
    	return $input; 
  } 
  return 0; 
}
function scuola_sanitize_string( $input ) {
  return sanitize_text_field( $input ); 
}
function scuola_sanitize_email( $input ) {
    return sanitize_email( $input ); 
}
function scuola_sanitize_text( $input ) {
    return sanitize_textarea_field( $input ); 
}
function scuola_sanitize_url( $input ) {
    return sanitize_url( $input ); 
}
function scuola_sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true == $checked ) ? true : false );
}
if( class_exists( 'WP_Customize_Control' ) ):
	class WP_Customize_Title_Control extends WP_Customize_Control {
		public $type = 'title';
 
		public function render_content() {
		?>
			<label>
				<h2><?php echo esc_html( $this->label ); ?></h2>
			</label>
		<?php
		}
	}
endif;
