<?php
/**
 * Plugin Name: FAQ
 * Plugin URI:
 * Description: Plugin per la gestione delle FAQs
 *
 * Version: 0.1
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

class ScuolaFAQ {

     // Plugin version => 0.1.

 
    /**
     * Initialize the class and set its properties.
     *
     * @since   1.6.0
     */
    public function __construct() {
        $this->load_dependencies();

    }
	function faq_add_descrizione_titolo($post) {
		if($post->post_type == "faq"){
			_e('il <strong>Titolo</strong> &egrave; la <strong>domanda della FAQ</strong>.<br>', 'wpscuola' );
			_e('<h2>Risposta alla domanda della FAQ</h2>', 'wpscuola' );
		}
	}
   function CreaCustoms() {
   	
   		$labels = array(
		'name'                  => _x( 'FAQs', 'Post Type General Name', 'wpscuola' ),
		'singular_name'         => _x( 'FAQ', 'Post Type Singular Name', 'wpscuola' ),
		'menu_name'             => __( 'FAQs', 'wpscuola' ),
		'name_admin_bar'        => __( 'FAQs', 'wpscuola' ),
		'archives'              => __( 'Archivio FAQ', 'wpscuola' ),
		'attributes'            => __( 'Attributi FAQ', 'wpscuola' ),
		'parent_item_colon'     => __( 'FAQ Padre:', 'wpscuola' ),
		'all_items'             => __( 'Tutte le FAQs', 'wpscuola' ),
		'add_new_item'          => __( 'Aggiungi nuova FAQ', 'wpscuola' ),
		'add_new'               => __( 'Aggiungi FAQ', 'wpscuola' ),
		'new_item'              => __( 'Nuova FAQ', 'wpscuola' ),
		'edit_item'             => __( 'Modifica FAQ', 'wpscuola' ),
		'update_item'           => __( 'Aggiorna FAQ', 'wpscuola' ),
		'view_item'             => __( 'Visualizza FAQ', 'wpscuola' ),
		'view_items'            => __( 'Visualizza FAQs', 'wpscuola' ),
		'search_items'          => __( 'Cerca FAQ', 'wpscuola' ),
		'not_found'             => __( 'FAQ non trovata', 'wpscuola' ),
		'not_found_in_trash'    => __( 'FAQ non trovato nel cestino', 'wpscuola' ),
		'items_list'            => __( 'Lista FAQs', 'wpscuola' ),
		'items_list_navigation' => __( 'Naviga la Lista delle FAQs', 'wpscuola' ),
		'filter_items_list'     => __( 'Filtra lista FAQs', 'wpscuola' ),
	);
	$atts = array(
		'label'                 => __( 'FAQ', 'wpscuola' ),
		'description'           => __( "Frequently asked questions (FAQ), alias Le domande più frequenti.", 'wpscuola' ),
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
		'show_in_rest' => true,
		'supports'          	=> array( 'title', 'editor', 'revisions', 'page-attributes' ),
	);
	register_post_type( 'tipo_faq', $atts );

	$labels = array(
		'name'                       => _x( 'Gruppi di FAQs', 'Taxonomy General Name', 'wpscuola' ),
		'singular_name'              => _x( 'Gruppo di FAQ', 'Taxonomy Singular Name', 'wpscuola' ),
		'menu_name'                  => __( 'Gruppi di FAQs', 'wpscuola' ),
		'all_items'                  => __( 'Gruppi di FAQs', 'wpscuola' ),
		'parent_item'                => __( 'Gruppo di FAQs padre', 'wpscuola' ),
		'parent_item_colon'          => __( 'Gruppi di FAQs padre', 'wpscuola' ),
		'new_item_name'              => __( 'Nuovo Gruppo di FAQs', 'wpscuola' ),
		'add_new_item'               => __( 'Aggiungi nuovo Gruppo di FAQs', 'wpscuola' ),
		'edit_item'                  => __( 'Modifica Gruppo di FAQs', 'wpscuola' ),
		'update_item'                => __( 'Aggiorna Gruppo di FAQs', 'wpscuola' ),
		'view_item'                  => __( 'Visualizza Gruppo di FAQs', 'wpscuola' ),
		'separate_items_with_commas' => __( 'Separere i Gruppo di FAQs con le virgole', 'wpscuola' ),
		'add_or_remove_items'        => __( 'Aggiungi o Rimuovi Gruppi di FAQs', 'wpscuola' ),
		'choose_from_most_used'      => __( 'Seleziona tra i Gruppi di FAQs magiornamente utilizzati', 'wpscuola' ),
		'popular_items'              => __( 'Gruppi di FAQs più popolari', 'wpscuola' ),
		'search_items'               => __( 'Cerca Gruppo di FAQs', 'wpscuola' ),
		'not_found'                  => __( 'Gruppo di FAQs non trovato', 'wpscuola' ),
		'no_terms'                   => __( 'Nessun Gruppo di FAQs', 'wpscuola' ),
		'items_list'                 => __( 'Elenco Gruppo di FAQs', 'wpscuola' ),
		'items_list_navigation'      => __( 'Naviga nella Lista dei Gruppi di FAQs', 'wpscuola' ),
	);
	$atts = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest'               => true,
	);
	register_taxonomy( 'faq_gruppi', array( 'tipo_faq' ), $atts );
}

    function messages( $messages ) {
        global $post, $post_ID;
        $post_type = get_post_type( $post_ID );

        $obj = get_post_type_object( $post_type );
        $singular = $obj->labels->singular_name;

        $messages[$post_type] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => sprintf($singular . ' '.__( 'aggiornato', 'wpscuola' ).'. <a href="%s">'.__( 'Visualizza', 'wpscuola' ).' ' . strtolower( $singular ) . '</a>', esc_url( get_permalink( $post_ID ) ) ),
            2  =>__( 'Campo personalizzato Aggiornato', 'wpscuola' ),
            3  =>__( 'Campo personalizzato cancellato', 'wpscuola' ),
            4  =>$singular . ' '.__( 'aggiornato', 'wpscuola' ),
            5  => isset($_GET['revision'] ) ? sprintf( ' '.__( 'recuperato dalla revisione da %s', 'wpscuola' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => sprintf($singular . ' '.__( 'pubblicato', 'wpscuola' ).'. <a href="%s">'.__( 'Visualizza', 'wpscuola' ).' ' . strtolower( $singular ) . '</a>', esc_url( get_permalink( $post_ID ) ) ),
            7  => 'Pagina salvata.',
            8  => sprintf($singular . ' '.__( 'inviata', 'wpscuola' ).'. <a target="_blank" href="%s">'.__( 'Anteprima', 'wpscuola' ).' ' . strtolower( $singular ) . '</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
            9  => sprintf($singular . ' '.__( 'schedulato per: <strong>%1$s</strong>', 'wpscuola' ).'. <a target="_blank" href="%2$s">'.__( 'Anteprima', 'wpscuola' ).' ' . strtolower( $singular ) . '</a>', date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
            10 => sprintf($singular . ' '.__( 'elemento aggiornato', 'wpscuola' ).'. <a target="_blank" href="%s">'.__( 'Anteprima', 'wpscuola' ).' ' . strtolower( $singular ) . '</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
        );

        return $messages;
    }

    function Colonne_FAQ_Intestazioni( $columns ) {
		if (get_post_type()=="tipo_faq"){
	        $columns = array(
	            "cb"          => '<input type="checkbox" />',
	            "title"      => __( 'Titolo FAQ', 'wpscuola' ),
	            "contenuto"   => __( 'Risposta', 'wpscuola' ),
	            'gruppo'      => __( 'Gruppi', 'wpscuola' ), 
	            'shortcode'   => __( 'Shortcode', 'wpscuola' ), 
	            "date"        => __( 'Data', 'wpscuola' ), 
	        );
		}
        return $columns;
    }
    function Colonne_FAQ_Contenuti( $defaults ) {
        global $post;
		if (get_post_type()=="tipo_faq"){
	        switch( $defaults ) {
	            case "contenuto":
	                the_excerpt();
	                break;
	            case "gruppo":
	                echo get_the_term_list( $post->ID, 'faq_gruppi', '', ', ', '' );
	                break;
	            case "shortcode":
	                printf( '[faq p=%d]', get_the_ID() );
	                break;
	            default:
	                break;
	        }
	    }
    }
    private function load_dependencies() {
    	add_action( 'init',  									array( $this, 'CreaCustoms' ) );
    	add_action( 'edit_form_after_title', 					array( $this,'faq_add_descrizione_titolo' ) );
 		add_filter( 'enter_title_here', 						array( $this,'faq_change_title_text' ) );
        add_filter( 'manage_posts_columns',     				array( $this, 'Colonne_FAQ_Intestazioni' ) );
        add_action( 'manage_posts_custom_column',   			array( $this, 'Colonne_FAQ_Contenuti' ) );
        add_filter( 'post_updated_messages',    				array( $this, 'messages' ) );
 		add_action( 'admin_menu', 								array( $this, 'add_faq_metabox' ) );
        add_shortcode( 'FAQ',                       			array( $this, 'visualizza_faq' ) );
     }
 // Add Shortcode
	function visualizza_faq( $atts ) {

		// Attributes
		$atts = shortcode_atts(
			array(	'order'             => 'ASC',
            		'orderby'           => 'title',
            		'posts_per_page'    => -1,
            		'gruppi'           	=> ''),
			$atts,
			'tipo_faq'
		);

	    $html = '<div id="collapseDivFAQ" class="collapse-div collapse-background-active" role="tablist">';
	    $terms = get_terms( 'faq_gruppi' );
	    if(isset($atts['gruppi'])){
	    	$gruppi=array();
	    	$terms = get_terms( array('taxonomy' => 'faq_gruppi','hide_empty' => false,) );
	    	foreach($terms as $term){
	    		$gruppi[]=$term->slug;
			}
		}else{
	    	$gruppi = $atts['gruppi'];
	    	$gruppi=explode(",",str_replace(' ', '', $gruppi));
		}
	    if ( ! empty( $terms ) ) {
	        foreach ( $terms as $term ) {
	            if (in_array($term->slug, $gruppi) ){
	                $query_args = array(
	                    'post_type'         => 'tipo_faq',
	                    'order'             => $atts['order'],
	                    'orderby'           => $atts['orderby'],
	                    'posts_per_page'    => $atts['posts_per_page'],
	                    'tax_query'         => array(
	                        array(
	                            'taxonomy'  => 'faq_gruppi',
	                            'field'     => 'slug',
	                            'terms'     => array( $term->slug ),
	                            'operator'  => 'IN'
	                        )
	                    )
	                );
	                // New query just for the tax term we're looping through
	                $q = new WP_Query( $query_args );
	               // echo $q->request;
	                if ( $q->have_posts() ) {
	                    $html .= '  <div class="collapse-header" id="heading' . $term->term_id . '">
	<button data-toggle="collapse" data-target="#collapse' . $term->term_id . '" aria-expanded="false" aria-controls="collapse' . $term->term_id . '" class="faq">' . $term->name . '</button>';
	                     // If the term has a description, show it
	                    if ( $term->description )
	                        $html .= '<p>' . $term->description . '</p>';
	 					$html .= '</div> 
	 <div id="collapse' . $term->term_id . '" class="collapse " role="tabpanel" aria-labelledby="heading' . $term->term_id . '">
		<div class="collapse-body">';
	                    while ( $q->have_posts() ) : $q->the_post();
							$html .='    	     
	    	<div id="collapseDiv' . $term->term_id . '" class="collapse-div" role="tablist">
				<div class="collapse-header" id="headingInt'. get_the_ID().'">
	      			<button data-toggle="collapse" data-target="#collapseVoce'. get_the_ID().'" aria-expanded="false" aria-controls="collapseVoce'. get_the_ID().'" class="faq">'.get_the_title().'</button>
	    		</div>
		        <div id="collapseVoce'. get_the_ID().'" class="collapse " role="tabpanel" aria-labelledby="headingInt'. get_the_ID().'">
		    		<div class="collapse-body">' . apply_filters( 'the_content', get_the_content() ).' </div>
		        </div>
		    </div>';
	                    endwhile;
	                    $html .= '
	    </div>
	</div>';
	                } // end have_posts()
	                wp_reset_postdata();
	            } // end foreach
	        }
	    } 
	    return $html;
	}

    public function add_faq_metabox() {		
	    add_meta_box( 'faq-box-shortcode', 'Shortcode FAQ', 	array( $this, 'faq_box_shortcode' ), 'faq', 'side' );
	}
    public function faq_box_shortcode() {
        global $post_ID;
        ?>
        <p class="howto">
            <?php _e( "Per visualizzare questa domanda, bisogna copiare il codice di seguito riportato ed incollarlo in un post, pagina o nell'area testo di un Widget Testo", 'wpscuola' ); ?>
        </p>
        <p><input type="text" value="[faq p=<?php echo $post_ID; ?>]" readonly="readonly" class="widefat wp-ui-text-highlight code"></p>
        <?php
    }
	function faq_change_title_text( $title ){
	     $screen = get_current_screen();
	  
	     if  ( 'faq' == $screen->post_type ) {
	          $title = __('Inserisci la Domanda della FAQ', 'wpscuola' );
	     }
	  
	     return $title;
	}

}