<?php
/**
 * Plugin Name: Servizi
 * Plugin URI:
 * Description: Plugin per la gestione dei Servizi
 *
 * Version: 0.1
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

class ScuolaServizi {

/**
 * Definisce post type e tassonomie relative ai servizi
 */
 
    public function __construct() {
        $this->load_dependencies();

    }

    private function load_dependencies() {
    	add_action( 'init',  									array( $this, 'scuola_register_servizi' ) );
	  	add_action( 'edit_form_after_title', 					array( $this, 'servizio_add_descrizione_titolo' ) );
		add_filter( 'enter_title_here', 						array( $this, 'servizio_change_title_text' ) );
//		add_filter( 'rwmb_meta_boxes',                          array( $this, 'servizi_mbox_destinazione' ) );
		add_action( 'add_meta_boxes',							array( $this, 'servizi_add_custom_box' ) );
		add_action( 'save_post',      							array( $this, 'save_metabox' ), 10, 2 );
		add_filter( 'manage_posts_columns', 					array( $this, 'servizi_NuoveColonne') );  
     }
		function servizi_add_custom_box()
		{
	        add_meta_box(
	            'Servizio_destinazione',    			// Unique ID
	            'Dati del Servizio',  					// Box title
	            array($this , 'servizi_destinazione'),  // Content callback, must be of type callable
	            'servizio'	               				// Post type
	        );
		}
 
	 function servizi_destinazione( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'servizio_destinazione_nonce_action', 'servizio_nonce' );

		// Retrieve an existing value from the database.
		$servizio_link_servizio = get_post_meta( $post->ID, 'servizio_link_servizio', true );
		$servizio_link2_servizio = get_post_meta( $post->ID, 'servizio_link2_servizio', true );
		$servizio_link_descrizione = get_post_meta( $post->ID, 'servizio_link_descrizione', true );
		$servizio_titolo_servizio = get_post_meta( $post->ID, 'servizio_titolo_servizio', true );
		$servizio_titolo2_servizio = get_post_meta( $post->ID, 'servizio_titolo2_servizio', true );
		$servizio_titolo_descrizione = get_post_meta( $post->ID, 'servizio_titolo_descrizione', true );
		$servizio_attivazione_servizio = get_post_meta( $post->ID, 'servizio_attivazione_servizio', true );
		$servizio_codice_ipa = get_post_meta( $post->ID, 'servizio_codice_ipa', true );
		$servizio_ordine = get_post_meta( $post->ID, 'servizio_ordine', true );
		$servizio_targetServizio = get_post_meta( $post->ID, 'servizio_targetservizo', true );
		$servizio_target2Servizio = get_post_meta( $post->ID, 'servizio_target2servizo', true );
		$servizio_targetDescrizione = get_post_meta( $post->ID, 'servizio_targetdescrizione', true );
		// Set default values.
		if( empty( $servizio_link_servizio ) ) $servizio_link_servizio = 'https://';
		if( empty( $servizio_link2_servizio ) ) $servizio_link2_servizio = 'https://';
		if( empty( $servizio_link_descrizione ) ) $servizio_link_descrizione = 'https://';
		if( empty( $servizio_attivazione_servizio ) ) $servizio_attivazione_servizio = '';
		if( empty( $servizio_codice_ipa ) ) $servizio_codice_ipa = '';
		if( empty( $servizio_ordine ) ) $servizio_ordine = '';
		if( !isset( $servizio_targetServizio ) ) $servizio_targetServizio = '_blank';
		if( !isset( $servizio_targetDescrizione ) ) $servizio_targetDescrizione = '_blank';

		// Form fields.?>
		<table class="form-table">
			<tr>
				<th>
					<label for="servizio_attivazione_servizio" class="servizio_attivazione_servizio"><?php _e( 'Stato del Servizio', 'wpscuola' );?></label>
				</th>
				<td>
					<input type="checkbox" id="servizio_attivazione_servizio" name="servizio_attivazione_servizio" class="" value="on" <?php echo ($servizio_attivazione_servizio=="si"?"checked":"");?> > 
					<span class="description"><?php _e( 'Selezionare per attivare il Servizio.', 'wpscuola' );?></span>
				</td>
			</tr>
			<tr>
				<th>
					<label for="servizio_ordine" class="servizio_ordine"><?php _e( 'Ordine di visualizzazione del Servizio', 'wpscuola' );?></label>
				</th>
				<td>
					<input type="number" id="servizio_ordine" size="4" name="servizio_ordine" class="" value="<?php echo $servizio_ordine;?>" > 
					<span class="description"><?php _e( 'Indicare l\'ordine di visualizzaznione del Servizio.', 'wpscuola' );?></span>
				</td>
			</tr>
			<tr>
				<th>
					<label for="servizio_link_servizio" class=""> <?php _e( 'URL di erogazione del Servizio', 'wpscuola' );?> </label>
				</th>
				<td>
					<input type="text" id="servizio_link_servizio" name="servizio_link_servizio" class="" placeholder="<?php esc_attr_e( 'https://', 'wpscuola' );?>" value="<?php esc_attr_e( $servizio_link_servizio );?>" style="width:90%;">
				</td>
			</tr>
			<tr>
				<th>
					<label for="servizio_titolo_servizio" class=""> <?php _e( 'Titolo del pulsante', 'wpscuola' );?> </label>
				</th>
				<td>
					<input type="text" id="servizio_titolo_servizio" name="servizio_titolo_servizio" class="" value="<?php esc_attr_e( $servizio_titolo_servizio );?>" style="width:90%;">
					<p><em><?php _e( 'Se lasciato vuoto verrà visualizzato Erogazione', 'wpscuola' );?></em></p>
				</td>
			</tr>
			<tr>
				<th>
					<label> <?php _e( 'Destinazione link Servizio', 'wpscuola' );?> </label>
				</th>
				<td>
					<p>
						<label for="linkservizio_target_blank" class="selectit">
							<input id="linkservizio_target_blank" type="radio" name="linkservizio_target" value="_blank" <?php echo ( isset( $servizio_targetServizio ) && ($servizio_targetServizio == '_blank') ? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_blank</code> &mdash; new window or tab.'); ?>
						</label>
					</p>
					<p>
						<label for="linkservizio_target_top" class="selectit">
							<input id="linkservizio_target_top" type="radio" name="linkservizio_target" value="_top" <?php echo ( isset( $servizio_targetServizio ) && ($servizio_targetServizio == '_top') ? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_top</code> &mdash; current window or tab, with no frames.'); ?>
						</label>
					</p>
					<p>
						<label for="linkservizio_target_none" class="selectit">
							<input id="linkservizio_target_none" type="radio" name="linkservizio_target" value=""  <?php echo ( isset( $servizio_targetServizio ) && ($servizio_targetServizio == '') ? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_none</code> &mdash; same window or tab.'); ?>
						</label>
					</p>
				</td>
			</tr>

			<tr>
				<th>
					<label for="servizio_link2_servizio" class=""> <?php _e( 'URL di erogazione del Secondo Servizio', 'wpscuola' );?> </label>
				</th>
				<td>
					<input type="text" id="servizio_link2_servizio" name="servizio_link2_servizio" class="" placeholder="<?php esc_attr_e( 'https://', 'wpscuola' );?>" value="<?php esc_attr_e( $servizio_link2_servizio );?>" style="width:90%;">
				</td>
			</tr>
			<tr>
				<th>
					<label for="servizio_titolo2_servizio" class=""> <?php _e( 'Titolo del pulsante', 'wpscuola' );?> </label>
				</th>
				<td>
					<input type="text" id="servizio_titolo2_servizio" name="servizio_titolo2_servizio" class="" value="<?php esc_attr_e( $servizio_titolo2_servizio );?>" style="width:90%;">
					<p><em><?php _e( 'Se lasciato vuoto verrà visualizzato Erogazione', 'wpscuola' );?></em></p>
				</td>
			</tr>
			<tr>
				<th>
					<label> <?php _e( 'Destinazione link Secondo Servizio', 'wpscuola' );?> </label>
				</th>
				<td>
					<p>
						<label for="linkservizio2_target_blank" class="selectit">
							<input id="linkservizio2_target_blank" type="radio" name="linkservizio2_target" value="_blank" <?php echo ( isset( $servizio_target2Servizio ) && ($servizio_target2Servizio == '_blank') ? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_blank</code> &mdash; new window or tab.'); ?>
						</label>
					</p>
					<p>
						<label for="linkservizio2_target_top" class="selectit">
							<input id="linkservizio2_target_top" type="radio" name="linkservizio2_target" value="_top" <?php echo ( isset( $servizio_target2Servizio ) && ($servizio_target2Servizio == '_top') ? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_top</code> &mdash; current window or tab, with no frames.'); ?>
						</label>
					</p>
					<p>
						<label for="linkservizio2_target_none" class="selectit">
							<input id="linkservizio2_target_none" type="radio" name="linkservizio2_target" value=""  <?php echo ( isset( $servizio_target2Servizio ) && ($servizio_target2Servizio == '') ? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_none</code> &mdash; same window or tab.'); ?>
						</label>
					</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="servizio_link_descrizione" class="servizio_link_descrizione"><?php  _e( 'URL di descrizione del Servizio', 'wpscuola' );?></label>
				</th>
				<td>
					<input type="text" id="servizio_link_descrizione" name="servizio_link_descrizione" class="" placeholder="<?php  esc_attr_e( 'https://', 'wpscuola' );?>" value="<?php esc_attr_e( $servizio_link_descrizione );?>" style="width:90%;">
				</td>
			</tr>
			<tr>
				<th>
					<label for="servizio_titolo_descrizione" class=""> <?php _e( 'Titolo del pulsante', 'wpscuola' );?> </label>
				</th>
				<td>
					<input type="text" id="servizio_titolo_descrizione" name="servizio_titolo_descrizione" class="" value="<?php esc_attr_e( $servizio_titolo_descrizione );?>" style="width:90%;">
					<p><em><?php _e( 'Se lasciato vuoto verrà visualizzato Descrizione', 'wpscuola' );?></em></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="servizio_link_servizio" class=""> <?php _e( 'Destinazione link Descrizione', 'wpscuola' );?> </label>
				</th>
				<td>
					<p>
						<label for="linkdescrizione_target_blank" class="selectit">
							<input id="linkdescrizione_target_blank" type="radio" name="linkdescrizione_target" value="_blank" <?php echo ( isset( $servizio_targetDescrizione ) && ($servizio_targetDescrizione == '_blank') ? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_blank</code> &mdash; new window or tab.'); ?>
						</label>
					</p>
					<p>
						<label for="linkdescrizione_target_top" class="selectit">
							<input id="linkdescrizione_target_top" type="radio" name="linkdescrizione_target" value="_top" <?php echo ( isset( $servizio_targetDescrizione ) && ($servizio_targetDescrizione == '_top') ? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_top</code> &mdash; current window or tab, with no frames.'); ?>
						</label>
					</p>
					<p>
						<label for="linkdescrizione_target_none" class="selectit">
							<input id="linkdescrizione_target_none" type="radio" name="linkdescrizione_target" value=""  <?php echo ( isset( $servizio_targetDescrizione ) && ($servizio_targetDescrizione == '') ? 'checked="checked"' : ''); ?> />
							<?php _e('<code>_none</code> &mdash; same window or tab.'); ?>
						</label>
					</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="servizio_codice_ipa" class=""> <?php _e( 'Codice IPA', 'wpscuola' );?> </label>
				</th>
				<td>
					<input type="text" id="servizio_codice_ipa" name="servizio_codice_ipa" class="" placeholder="<?php esc_attr_e( '', 'wpscuola' );?>" value="<?php esc_attr_e( $servizio_codice_ipa );?>">
					<span class="description"><?php _e( 'Codice Indice dei domicili digitali della Pubblica Amministrazione (https://indicepa.gov.it/).', 'wpscuola' );?></span>
				</td>
			</tr>
		</table>
<?PHP	}
 
 	public function save_metabox( $post_id, $post ) {

		if ( ! isset( $_POST['servizio_nonce'] ) )
			return;

		// Add nonce for security and authentication.
		$nonce_name   = $_POST['servizio_nonce'];
		$nonce_action = 'servizio_destinazione_nonce_action';

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
		$servizio_link_servizio = isset( $_POST[ 'servizio_link_servizio' ] ) ? sanitize_text_field( $_POST[ 'servizio_link_servizio' ] ) : '';
		$servizio_titolo_servizio = isset( $_POST[ 'servizio_titolo_servizio' ] ) ? sanitize_text_field( $_POST[ 'servizio_titolo_servizio' ] ) : '';
		$servizio_link2_servizio = isset( $_POST[ 'servizio_link2_servizio' ] ) ? sanitize_text_field( $_POST[ 'servizio_link2_servizio' ] ) : '';
		$servizio_titolo2_servizio = isset( $_POST[ 'servizio_titolo2_servizio' ] ) ? sanitize_text_field( $_POST[ 'servizio_titolo2_servizio' ] ) : '';
		$servizio_titolo_descrizione = isset( $_POST[ 'servizio_titolo_descrizione' ] ) ? sanitize_text_field( $_POST[ 'servizio_titolo_descrizione' ] ) : '';
		$servizio_link_descrizione = isset( $_POST[ 'servizio_link_descrizione' ] ) ? sanitize_text_field( $_POST[ 'servizio_link_descrizione' ] ) : '';
		$servizio_attivazione_servizio = isset( $_POST[ 'servizio_attivazione_servizio' ] ) ? 'si' : 'no';
		$servizio_codice_ipa = isset( $_POST[ 'servizio_codice_ipa' ] ) ? sanitize_text_field( $_POST[ 'servizio_codice_ipa' ] ) : '';
		$servizio_ordine = isset( $_POST[ 'servizio_ordine' ] ) ? sanitize_text_field( $_POST[ 'servizio_ordine' ] ) : 0;
		$servizio_targetServizio = isset( $_POST[ 'linkservizio_target' ] ) ? sanitize_text_field( $_POST[ 'linkservizio_target' ] ) : 0;
		$servizio_target2Servizio = isset( $_POST[ 'linkservizio2_target' ] ) ? sanitize_text_field( $_POST[ 'linkservizio2_target' ] ) : 0;
		$servizio_targetdescrizione = isset( $_POST[ 'linkdescrizione_target' ] ) ? sanitize_text_field( $_POST[ 'linkdescrizione_target' ] ) : '';
		// Update the meta field in the database.
		update_post_meta( $post_id, 'servizio_link_servizio', 			$servizio_link_servizio );
		update_post_meta( $post_id, 'servizio_titolo_servizio', 		$servizio_titolo_servizio );
		update_post_meta( $post_id, 'servizio_link2_servizio', 			$servizio_link2_servizio );
		update_post_meta( $post_id, 'servizio_titolo2_servizio', 		$servizio_titolo2_servizio );
		update_post_meta( $post_id, 'servizio_link_descrizione', 		$servizio_link_descrizione );
		update_post_meta( $post_id, 'servizio_titolo_descrizione', 		$servizio_titolo_descrizione );
		update_post_meta( $post_id, 'servizio_attivazione_servizio',	$servizio_attivazione_servizio );
		update_post_meta( $post_id, 'servizio_codice_ipa', 				$servizio_codice_ipa );
		update_post_meta( $post_id, 'servizio_ordine', 					$servizio_ordine );
		update_post_meta( $post_id, 'servizio_targetservizo', 			$servizio_targetServizio );
		update_post_meta( $post_id, 'servizio_target2servizo', 			$servizio_target2Servizio );
		update_post_meta( $post_id, 'servizio_targetdescrizione', 		$servizio_targetdescrizione );
	}
	 
	function scuola_register_servizi() {

	$labels = array(
		'name'                  => _x( 'Servizi', 'Post Type General Name', 'wpscuola' ),
		'singular_name'         => _x( 'Servizio', 'Post Type Singular Name', 'wpscuola' ),
		'menu_name'             => __( 'Servizi', 'wpscuola' ),
		'name_admin_bar'        => __( 'Servizi', 'wpscuola' ),
		'archives'              => __( 'Archivio Servizi', 'wpscuola' ),
		'attributes'            => __( 'Attributi Servizio', 'wpscuola' ),
		'parent_item_colon'     => __( 'Servizio Padre:', 'wpscuola' ),
		'all_items'             => __( 'Tutti i Servizi', 'wpscuola' ),
		'add_new_item'          => __( 'Aggiungi nuovo Servizio', 'wpscuola' ),
		'add_new'               => __( 'Aggiungi Servizio', 'wpscuola' ),
		'new_item'              => __( 'Nuovo Servizio', 'wpscuola' ),
		'edit_item'             => __( 'Modifica Servizio', 'wpscuola' ),
		'update_item'           => __( 'Aggiorna Servizio', 'wpscuola' ),
		'view_item'             => __( 'Visualizza Servizio', 'wpscuola' ),
		'view_items'            => __( 'Visualizza Servizi', 'wpscuola' ),
		'search_items'          => __( 'Cerca Servizio', 'wpscuola' ),
		'not_found'             => __( 'Servizio non trovato', 'wpscuola' ),
		'not_found_in_trash'    => __( 'Servizio non trovato nel cestino', 'wpscuola' ),
		'featured_image'        => __( 'Logo Servizio', 'wpscuola' ),
		'set_featured_image'    => __( 'Imposta Logo Servizio', 'wpscuola' ),
		'remove_featured_image' => __( 'Rimuovi Logo Servizio', 'wpscuola' ),
		'use_featured_image'    => __( 'Usa Logo Servizio', 'wpscuola' ),
		'insert_into_item'      => __( 'Inserisci nel Servizio', 'wpscuola' ),
		'uploaded_to_this_item' => __( 'Carica in questo Servizio', 'wpscuola' ),
		'items_list'            => __( 'Lista Servizi', 'wpscuola' ),
		'items_list_navigation' => __( 'Naviga la Lista dei Servizi', 'wpscuola' ),
		'filter_items_list'     => __( 'Filtra lista Servizi', 'wpscuola' ),
	);
	$args = array(
		'label'                 => __( 'Servizi', 'wpscuola' ),
		'description'           => __( "I servizi interni ed esterni della scuola.", 'wpscuola' ),
		'labels'                => $labels,
		'supports'              => false,
//		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 21,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'show_in_rest' 			=> true,
		'supports'          	=> array( 'title', 'editor', 'revisions', 'thumbnail'),
	);
	register_post_type( 'servizio', $args );

	$labels = array(
		'name'                       => _x( 'Tipi di Servizio', 'Taxonomy General Name', 'wpscuola' ),
		'singular_name'              => _x( 'Tipo di Servizio', 'Taxonomy Singular Name', 'wpscuola' ),
		'menu_name'                  => __( 'Tipi di Servizio', 'wpscuola' ),
		'all_items'                  => __( 'Tutti i tipi di Servizio', 'wpscuola' ),
		'parent_item'                => __( 'Tipo di Servizio padre', 'wpscuola' ),
		'parent_item_colon'          => __( 'Tipo di Servizio padre', 'wpscuola' ),
		'new_item_name'              => __( 'Nuovo Tipo di Servizio', 'wpscuola' ),
		'add_new_item'               => __( 'Aggiungi nuovo Tipo di Servizio', 'wpscuola' ),
		'edit_item'                  => __( 'Modifica Tipo di Servizio', 'wpscuola' ),
		'update_item'                => __( 'Aggiorna Tipo di Servizio', 'wpscuola' ),
		'view_item'                  => __( 'Visualizza Tipo di Servizio', 'wpscuola' ),
		'separate_items_with_commas' => __( 'Separere i Tipi di Servizio con le virgole', 'wpscuola' ),
		'add_or_remove_items'        => __( 'Aggiungi o Rimuovi Tipi di Servizio', 'wpscuola' ),
		'choose_from_most_used'      => __( 'Seleziona tra i Tipi di Servizio magiornamente utilizzati', 'wpscuola' ),
		'popular_items'              => __( 'Tipi di Servizo più popolari', 'wpscuola' ),
		'search_items'               => __( 'Cerca Tipo di Servizio', 'wpscuola' ),
		'not_found'                  => __( 'Tipo di Servizio non trovato', 'wpscuola' ),
		'no_terms'                   => __( 'Nessun Tipo di Servizio', 'wpscuola' ),
		'items_list'                 => __( 'Elenco Tipi di Servizio', 'wpscuola' ),
		'items_list_navigation'      => __( 'Naviga nella Lista dei Tipi di Servizio', 'wpscuola' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest'               => true,
	);
	register_taxonomy( 'tiposervizio', array( 'servizio' ), $args );

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
	function servizi_NuoveColonne($defaults) {  
		if (isset($_GET['post_type']) And $_GET['post_type']=="servizio"){
			$defaults['ordine'] = 'Ordine di visualizzazione'; 
		}
	   return $defaults;  
	}  
	
}

/**
* Aggiunta del campo alla Tassonomia tiposervizio  
*/
add_action( 'tiposervizio_add_form_fields', 'add_tiposervizio_field', 10, 2 );
function add_tiposervizio_field($taxonomy) {
    global $feature_groups;
    ?><div class="form-field term-ordine">
        <label for="ordine"><?php _e('Ordine', 'wpscuola' );; ?></label>
 		<input type="number" name="term_meta[ordine]" id="term_meta[ordine]"  min="1" max="100" style="width: 5em;" value="<?php echo $term_meta['ordine'] ? $term_meta['ordine'] : '1'; ?>" min="1">
 		<br />
		<span class="description"><?php _e('Ordine di visualizzazione', 'wpscuola' ); ?></span>
    </div><?php
}
/**
* Memorizzazione del campo alla Tassonomia tiposervizio  
*/
add_action( 'created_tiposervizio', 'save_tiposervizio_field', 10, 2 );
function save_tiposervizio_field( $term_id, $tt_id ){
    if( isset( $_POST["term_meta"]['ordine'] ) && is_numeric($_POST["term_meta"]['ordine']) ){
         $ordine = intval(sanitize_title( $_POST["term_meta"]['ordine']));
        add_term_meta( $term_id, 'ordine', $ordine, true );
    }
}
/**
* Modifica del campo alla Tassonomia tiposervizio  
*/
add_action( 'tiposervizio_edit_form_fields', 'tiposervizio_addCusomField', 10, 2);
	function tiposervizio_addCusomField($term, $taxonomy ){
 	$ordine = get_term_meta( $term->term_id, 'ordine', true );?>
 			<tr class="form-field">
			<th scope="row" valign="top">
				<label for="ordine"><?php _e('Ordine', 'wpscuola' );; ?></label>
			</th>
			<td>
				<input type="number" name="term_meta[ordine]" id="term_meta[ordine]"  min="1" max="100" style="width: 5em;" value="<?php echo $ordine ? $ordine : '0'; ?>"><br />
		            <span class="description"><?php _e('Ordine di visualizzazione', 'wpscuola' ); ?></span>
		        </td>
		</tr>
<?php
}
/**
* Aggiornamento del campo alla Tassonomia tiposervizio  
*/
add_action( 'edited_tiposervizio', 'update_tiposervizio_field', 10, 2 );
function update_tiposervizio_field( $term_id, $tt_id ){
    if( isset( $_POST["term_meta"]['ordine'] ) && is_numeric($_POST["term_meta"]['ordine']) ){
        $ordine = intval(sanitize_title( $_POST["term_meta"]['ordine'] ));
        update_term_meta( $term_id, 'ordine', $ordine );
    }
}
/**
* Aggiunta del campo nella visualizzazione della Tassonomia tiposervizio  
*/
add_filter( 'manage_edit-tiposervizio_columns', 'tiposervizio_add_column' );
function tiposervizio_add_column( $columns ) {
	$columns['ordine'] = __( 'Ordine', 'wpscuola' );
	return $columns;
}
add_filter('manage_tiposervizio_custom_column', 'tiposervizio_column_content',10,3);
function tiposervizio_column_content($content,$column_name,$term_id){
	if ($column_name == 'ordine'){
		 $content=get_term_meta( $term_id, "ordine",TRUE);
	}
	return $content;
}
/** 
* Aggiungo contenuto alla colonna ordine di visualizzazione
*/
add_action( 'manage_posts_custom_column', 'servizi_NCContenuto', 10, 2 );
function servizi_NCContenuto($column_name, $post_ID) { 
	if (isset($_GET['post_type']) And $_GET['post_type']=="servizio"){
		 if ($column_name == 'ordine'){
		 	echo get_post_meta($post_ID, "servizio_ordine",TRUE);
		}
	}
	
}
