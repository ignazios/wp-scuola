<?php
/**
 * Plugin Name: Link
 * Plugin URI:
 * Description: Personalizzazione della gestione del Link di Wordpress
 *
 * Version: 0.1
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */
/**
* Aggiunta del campo alla Tassonomia link_category  
*/
add_action( 'link_category_add_form_fields', 'add_link_category_field', 10, 2 );
function add_link_category_field($taxonomy) {
    global $feature_groups;
    ?><div class="form-field term-ordine">
        <label for="ordine"><?php _e('Ordine', 'wpscuola' );; ?></label>
 		<input type="number" name="term_meta[ordine]" id="term_meta[ordine]"  min="1" max="100" style="width: 5em;" value="<?php echo $term_meta['ordine'] ? $term_meta['ordine'] : '1'; ?>">
 		<br />
		<span class="description"><?php _e('Ordine di visualizzazione', 'wpscuola' ); ?></span>
    </div><?php
}
/**
* Memorizzazione del campo alla Tassonomia link_category  
*/
add_action( 'created_link_category', 'save_link_category_field', 10, 2 );
function save_link_category_field( $term_id, $tt_id ){
    if( isset( $_POST["term_meta"]['ordine_link'] ) && is_numeric($_POST["term_meta"]['ordine_link']) ){
         $ordine = intval(sanitize_title( $_POST["term_meta"]['ordine_link']));
        add_term_meta( $term_id, 'ordine_link', $ordine, true );
    }
}
/**
* Modifica del campo alla Tassonomia link_category  
*/
add_action( 'link_category_edit_form_fields', 'link_category_addCusomField', 10, 2);
	function link_category_addCusomField($term, $taxonomy ){
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
add_action( 'edited_link_category', 'update_link_category_field', 10, 2 );
function update_link_category_field( $term_id, $tt_id ){
    if( isset( $_POST["term_meta"]['ordine'] ) && is_numeric($_POST["term_meta"]['ordine']) ){
        $ordine = intval(sanitize_title( $_POST["term_meta"]['ordine'] ));
        update_term_meta( $term_id, 'ordine_link', $ordine );
    }
}
/**
* Aggiunta del campo nella visualizzazione della Tassonomia tiposervizio  
*/
add_filter( 'manage_edit-link_category_columns', 'link_category_add_column' );
function link_category_add_column( $columns ) {
	$columns['ordine'] = __( 'Ordine', 'wpscuola' );
	return $columns;
}
add_filter('manage_link_category_custom_column', 'link_category_column_content',10,3);
function link_category_column_content($content,$column_name,$term_id){
	if ($column_name == 'ordine'){
		 $content=get_term_meta( $term_id, "ordine_link",TRUE);
	}
	return $content;
}