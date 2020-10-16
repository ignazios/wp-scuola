<?php
function wps_crea_custom_circolari() {
	$ArrSupports=array('title', 'editor', 'author','excerpt', 'thumbnail');
	if(get_option('Circolari_GestPerm')=="int"){
		$cps=array();
		$cp="post";
	}	
	else{
	 	$cps=array(
	        'edit_post'		 		=> "edit_circolare",
			'read_post'		 		=> "read_circolare",
			'delete_post'		 	=> "delete_circolare",
			'edit_posts'		 	=> "edit_circolares",
			'edit_others_posts'	 	=> "edit_others_circolares",
			'publish_posts'		 	=> "publish_circolares",
			'read_private_posts'	 => "read_private_circolares",
	        'delete_posts'           => "delete_circolares",
	        'delete_private_posts'   => "delete_private_circolares",
	        'delete_published_posts' => "delete_published_circolares",
	        'delete_others_posts'    => "delete_others_circolares",
	        'edit_private_posts'     => "edit_private_circolares",
	        'edit_published_posts'   => "edit_published_circolares");
	    $cp="circolare";	
		$role =get_role( 'administrator' );

        /* Aggiunta dei ruoli all'Amministratore */
        if ( !empty( $role ) ) {
            $role->add_cap( 'manage_adesioni' );
            $role->add_cap( 'send_circ@mail' );
        }
	}
	register_post_type('circolari_scuola', array(
		'labels' => array(
		'name' 				=> __( 'Circolari', 'wpscuola' ),
		'singular_name' 	=> __( 'Circolare' , 'wpscuola'),
		'add_new' 			=> __( 'Aggiungi Circolare', 'wpscuola' ),
		'add_new_item' 		=> __( 'Aggiungi nuova Circolare', 'wpscuola'),
		'edit' 				=> __( 'Modifica', 'wpscuola' ),
		'edit_item' 		=> __( 'Modifica Circolare', 'wpscuola' ),
		'new_item' 			=> __( 'Nuova Circolare', 'wpscuola' ),
		'items_archive' 	=> __( 'Circolare Aggiornata', 'wpscuola' ),
		'view' 				=> __( 'Visualizza Circolare', 'wpscuola' ),
		'view_item' 		=> __( 'Visualizza', 'wpscuola' ),
		'search_items' 		=> __( 'Cerca Circolare', 'wpscuola' ),
		'not_found' 		=> __( 'Nessuna Circolare trovata', 'wpscuola' ),
		'not_found_in_trash'=> __( 'Nessuna Circolare trovata nel cestino', 'wpscuola' ),
		'parent' 			=> __( 'Circolare superiore', 'wpscuola' )),
		'public' => true,
		'show_ui' => true,
		'show_in_admin_bar' => true,
		'show_in_rest' => true,
		'menu_position' => 20,
		'capability_type' => $cp,
		'capabilities' => $cps,
		'hierarchical' => false,
		'has_archive' => true,
		'menu_icon' => wps_Circolari_URL.'/img/circolare.png',
	    'taxonomies' => array('post_tag'),  
		'supports' => $ArrSupports));
}
?>