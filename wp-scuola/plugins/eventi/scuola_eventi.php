<?php
/**
 * Plugin Name: Eventi
 * Plugin URI:
 * Description: Plugin per la gestione degli Eventi dell'Istituzione che verranno visualizzate in Home Page
 *
 * Version: 0.1
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

class ScuolaEventi {

/**
 * Definisce post type e tassonomie relative ai servizi
 */
 
    public function __construct() {
        $this->load_dependencies();

    }

    private function load_dependencies() {
    	add_action( 'init',  									array( $this, 'scuola_register_eventi' ) );
		add_action( 'add_meta_boxes',							array( $this, 'evento_add_custom_box' ) );
		add_action( 'save_post',      							array( $this, 'save_metabox' ), 10, 2 );
		add_filter( 'manage_posts_columns', 					array( $this, 'eventi_NuoveColonne') );  
     }
		function evento_add_custom_box()
		{
	        add_meta_box(
	            'Evento_parametri',    					// Unique ID
	            'Dati dell\'Evento',  					// Box title
	            array($this , 'evento_pametri'),  		// Content callback, must be of type callable
	            'scuola_eventi'	               			// Post type
	        );
		}
 
	 function evento_pametri( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'evento_destinazione_nonce_action', 'evento_nonce' );

		// Retrieve an existing value from the database.
		$eventi_link_evento     = get_post_meta( $post->ID, 'eventi_link_evento', true );
		$eventi_link_titolo     = get_post_meta( $post->ID, 'eventi_link_titolo', true );
		$eventi_target_evento   = get_post_meta( $post->ID, 'eventi_target_evento', true );
		$eventi_scadenza_evento = get_post_meta( $post->ID, 'eventi_scadenza_evento', true );
		// Set default values.
		if( empty( $eventi_link_evento ) ) $eventi_link_evento = 'https://';
		if( empty( $eventi_scadenza_evento ) ) $eventi_scadenza_evento = date('Y-m-d');

		// Form fields.?>
		<table class="form-table">
			<tr>
				<th>
					<label for="eventi_link_evento" class=""> <?php _e( 'URL dell\'Evento', 'wpscuola' );?> </label>
				</th>
				<td>
					<input type="text" id="eventi_link_evento" name="eventi_link_evento" class="" placeholder="<?php esc_attr_e( 'https://', 'wpscuola' );?>" value="<?php esc_attr_e( $eventi_link_evento );?>" style="width:90%;">
				</td>
			</tr>
			<tr>
				<th>
					<label for="eventi_link_titolo" class=""> <?php _e( 'Titolo del pulsante dell\'Evento', 'wpscuola' );?> </label>
				</th>
				<td>
					<input type="text" id="eventi_link_titolo" name="eventi_link_titolo" class="" placeholder="<?php esc_attr_e( 'Live', 'wpscuola' );?>" value="<?php esc_attr_e( $eventi_link_titolo );?>" style="width:50%;">
					<p>selezionare un'icona su <a href="https://fontawesome.com/search?o=r&m=free" target="_blank">https://fontawesome.com</a> copiala ed incollala prima del Titolo
					</p>
				</td>
			</tr>
			<tr>
				<th>
					<label> <?php _e( 'Destinazione link Evento', 'wpscuola' );?> </label>
				</th>
				<td>
					<p>
						<label for="linkevento_target_blank" class="selectit">
							<input id="linkevento_target_blank" type="radio" name="linkevento_target" value="_blank" <?php echo ( isset( $eventi_target_evento ) && ($eventi_target_evento == '_blank') ? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_blank</code> &mdash; new window or tab.'); ?>
						</label>
					</p>
					<p>
						<label for="linkevento_target_top" class="selectit">
							<input id="linkevento_target_top" type="radio" name="linkevento_target" value="_top" <?php echo ( isset( $eventi_target_evento ) && ($eventi_target_evento == '_top') ? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_top</code> &mdash; current window or tab, with no frames.'); ?>
						</label>
					</p>
					<p>
						<label for="linkevento_target_none" class="selectit">
							<input id="linkevento_target_none" type="radio" name="linkevento_target" value=""  <?php echo ( isset( $eventi_target_evento ) && ($eventi_target_evento == '')  || !isset( $eventi_target_evento )? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_none</code> &mdash; same window or tab.'); ?>
						</label>
					</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="eventi_scadenza_evento" class=""> <?php _e( 'Data di scadenza dell\'Evento', 'wpscuola' );?> </label>
				</th>
				<td>
					<input type="date" id="eventi_scadenza_evento" name="eventi_scadenza_evento" class=""  value="<?php esc_attr_e( $eventi_scadenza_evento );?>">
				</td>
			</tr>
		</table>
<?PHP	}
 
 	public function save_metabox( $post_id, $post ) {

		if ( ! isset( $_POST['evento_nonce'] ) )
			return;

		// Add nonce for security and authentication.
		$nonce_name   = $_POST['evento_nonce'];
		$nonce_action = 'evento_destinazione_nonce_action';

		// Check if a nonce is set.
		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) )
			return;

		// Check if the user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) )
			return;

		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) )
			return;
		// Sanitize user input.
		
		$eventi_link_evento     = isset( $_POST[ 'eventi_link_evento' ] ) ? sanitize_text_field( $_POST[ 'eventi_link_evento' ] ) : '';
		$eventi_target_evento   = isset( $_POST[ 'eventi_target_evento' ] ) ? sanitize_text_field( $_POST[ 'eventi_target_evento' ] ) : 0;
		$eventi_scadenza_evento = isset( $_POST[ 'eventi_scadenza_evento' ] ) ? sanitize_text_field( $_POST[ 'eventi_scadenza_evento' ] ) : 0;
		$eventi_link_titolo     = isset( $_POST[ 'eventi_link_titolo' ] ) ?  $_POST[ 'eventi_link_titolo' ] : "";
		$eventi_link_titolo=str_replace("<i","<span",$eventi_link_titolo);
		$eventi_link_titolo=str_replace("</i","</span",$eventi_link_titolo);
		// Update the meta field in the database.
		update_post_meta( $post_id, 'eventi_link_evento', 			$eventi_link_evento );
		update_post_meta( $post_id, 'eventi_target_evento', 		$eventi_target_evento );
		update_post_meta( $post_id, 'eventi_scadenza_evento', 		$eventi_scadenza_evento );
		update_post_meta( $post_id, 'eventi_link_titolo', 			$eventi_link_titolo );
	}
	 
	function scuola_register_eventi() {

	$labels = array(
		'name'                  => _x( 'Eventi', 'Post Type General Name', 'wpscuola' ),
		'singular_name'         => _x( 'Evento', 'Post Type Singular Name', 'wpscuola' ),
		'menu_name'             => __( 'Eventi Scolastici', 'wpscuola' ),
		'name_admin_bar'        => __( 'Eventi Scolastici', 'wpscuola' ),
		'archives'              => __( 'Archivio Eventi', 'wpscuola' ),
		'attributes'            => __( 'Attributi Evento', 'wpscuola' ),
		'parent_item_colon'     => __( 'Evento Padre:', 'wpscuola' ),
		'all_items'             => __( 'Tutti gli Eventi', 'wpscuola' ),
		'add_new_item'          => __( 'Aggiungi nuovo Evento', 'wpscuola' ),
		'add_new'               => __( 'Aggiungi Evento', 'wpscuola' ),
		'new_item'              => __( 'Nuovo Evento', 'wpscuola' ),
		'edit_item'             => __( 'Modifica Evento', 'wpscuola' ),
		'update_item'           => __( 'Aggiorna Evento', 'wpscuola' ),
		'view_item'             => __( 'Visualizza Evento', 'wpscuola' ),
		'view_items'            => __( 'Visualizza Eventi', 'wpscuola' ),
		'search_items'          => __( 'Cerca Evento', 'wpscuola' ),
		'not_found'             => __( 'Evento non trovato', 'wpscuola' ),
		'not_found_in_trash'    => __( 'Evento non trovato nel cestino', 'wpscuola' ),
		'featured_image'        => __( 'Banner Evento (750x1200)', 'wpscuola' ),
		'set_featured_image'    => __( 'Imposta Banner Evento', 'wpscuola' ),
		'remove_featured_image' => __( 'Rimuovi Banner Evento', 'wpscuola' ),
		'use_featured_image'    => __( 'Usa Banner Evento', 'wpscuola' ),
		'insert_into_item'      => __( 'Inserisci nel Evento', 'wpscuola' ),
		'uploaded_to_this_item' => __( 'Carica in questo Evento', 'wpscuola' ),
		'items_list'            => __( 'Lista Eventi', 'wpscuola' ),
		'items_list_navigation' => __( 'Naviga la Lista dei Eventi', 'wpscuola' ),
		'filter_items_list'     => __( 'Filtra lista Eventi', 'wpscuola' ),
	);
	$args = array(
		'label'                 => __( 'Eventi Scuola', 'wpscuola' ),
		'description'           => __( "Eventi interni ed esterni della scuola.", 'wpscuola' ),
		'labels'                => $labels,
		'supports'              => false,
//		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 20,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'show_in_rest' 			=> true,
		'supports'          	=> array( 'title', 'editor', 'revisions', 'thumbnail', 'excerpt'),
	);
	register_post_type( 'scuola_eventi', $args );

}
/**
 * Aggiungo label sotto il titolo
 */
	function servizio_change_title_text( $title ){
		     $screen = get_current_screen();
		  
		     if  ( 'servizio' == $screen->post_type ) {
		          $title = __('Inserisci il Nome del Servizio', 'wpscuola' );
		     }
		  
		     return $title;
		}

	function servizio_add_descrizione_titolo($post) {
		if($post->post_type == "servizio"){
			_e('<span><em>il <strong>Titolo</strong> &egrave; il <strong>Nome del Servizio</strong>.<br /><br />', 'wpscuola' );
			_e('<span style="font-size: 23px;font-weight: 400;margin: 0;padding: 9px 0 4px 0;line-height: 1.3;">Descrizione del Servizio</span>', 'wpscuola' );
		}
	}
/** 
* Aggiungo colonna con ordine di visualizzazione
*/	
	function eventi_NuoveColonne($defaults) {  
		if (isset($_GET['post_type']) And $_GET['post_type']=="scuola_eventi"){
			$defaults['Scadenza'] = 'Scadenza dell\'Evento'; 
		}
	   return $defaults;  
	}  
}
/** 
* Aggiungo contenuto alla colonna ordine di visualizzazione
*/
function scuola_VisualizzaData($dataDB){
	$dataDB=substr($dataDB,0,10);
	$rsl = explode ('-',$dataDB);
	$rsl = array_reverse($rsl);
	return implode('/',$rsl);
}
add_action( 'manage_posts_custom_column', 'eventi_Scadenza', 10, 2 );
	function eventi_Scadenza($column_name, $post_ID) { 
		if (isset($_GET['post_type']) And $_GET['post_type']=="scuola_eventi"){
			 if ($column_name == 'Scadenza'){
			 	echo scuola_VisualizzaData(get_post_meta($post_ID, "eventi_scadenza_evento",TRUE));
			}
		}
		
	}	
