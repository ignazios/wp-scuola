<?php
/****************************************************************************
  Widget Circolari
	    Titolo:	($title) Testo che viene visualizzato in Testa all'elenco delle circolari estratti
		Numero di Elementi: ($numelementi) Numero di circolari da visualizzare
		Visualizza Link Leggi Tutto: ($linkLT) Flag che indica se deve essere visualizzato il link Leggi Tutto
*/     
    class CircolariScuola extends WP_Widget {

        function __construct() {
			parent::__construct( false, 'Scuola Circolari',
				array('classname' => 'Circolari',
				    'description' => __('Blocco Circolari in Home Page','wpscuola')));
        	add_action( 'save_post', 	[$this, 'flush_widget_cache'] );
	      	add_action( 'deleted_post', [$this, 'flush_widget_cache'] );
	      	add_action( 'switch_theme', [$this, 'flush_widget_cache'] );
		 }

        function widget( $args, $instance ) {
	       $cache = [];
	        if ( ! $this->is_preview() ) {
	            $cache = wp_cache_get( 'widget_circolari', 'widget' );
	        }

	        if ( ! is_array( $cache ) ) {
	            $cache = [];
	        }

	        if ( ! isset( $args['widget_id'] ) ) {
	            $args['widget_id'] = $this->id;
	        }

	        if ( isset( $cache[ $args['widget_id'] ] ) ) {
	            echo $cache[ $args['widget_id'] ];
	            return;
	        }

	        ob_start();
//      	    var_dump($args);
	        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Circolari','wpscuola' );
        	/** This filter is documented in wp-includes/default-widgets.php */
        	$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
 			$numelementi=isset($instance['numelementi'])?$instance['numelementi']:"";
 			$linkLT=isset($instance['leggitutto'])?$instance['leggitutto']:"";

			$Circolariargs = array( 
			   'post_type' => array('circolari_scuola'),
			   'numberposts'=>-1,
			   'post_status' => 'publish');
			$Circolari = get_posts($Circolariargs);
			$NunEleVis=0;
?>
  	<section id="ag_<?php echo $args['widget_id'];?>"  class="home-widget container ">
<?php           
           if ( $title ) {
                echo $args['before_title'] . $title;
                if(is_user_logged_in() and ($CircoDaFirmare=wps_GetCircolariDaFirmare("N"))>0){
                	echo ' <a href="'.site_url('/wp-admin/edit.php?post_type=circolari_scuola&page=Firma').'"> <span class="badge badge-pill badge-danger" title="Circolari da firmare">'.$CircoDaFirmare.'</span></a>';
                }
                echo $args['after_title'];
            } ?>
		<div class="it-grid-list-wrapper it-image-label-grid">
			<div class="grid-row">
     <?php   foreach( $Circolari as $Circolare) {
     			$visibilita=get_post_meta($Circolare->ID, "_visibilita");
				if(count($visibilita)==0)
					$visibilita="p";
				else 
					$visibilita=$visibilita[0];
     			if ((wps_Is_Circolare_per_User($Circolare->ID) and $visibilita=="d") or $visibilita=="p"){
     				$NunEleVis++;
     				if ($NunEleVis<=$numelementi){
						$numero=get_post_meta($Circolare->ID, "_numero",TRUE);
						$anno=get_post_meta($Circolare->ID, "_anno",TRUE);
						$Elenco=wps_GetEencoDestinatari($Circolare->ID);
?>
 			<div class="col-12 col-sm-6 col-lg-4">
 			    <!--start card-->
				    <div class="card-wrapper card-space">
				      <div class="card card-bg no-after">
				        <div class="card-body">
				          <div class="head-tags mb-0">
				            <ul>
				            	<li><span class="data"><span class="far fa-calendar-alt"></span> <?php echo date('d/m/Y',strtotime($Circolare->post_date));?> <strong><span class="fas fa-ticket-alt"></span> <?php echo $numero."_".$anno;?></strong></span></li>
				            	<li><span class="users"><span class="fa fa-users" aria-hidden="true"></span> <?php echo $Elenco;?></span></li>
				            </ul>
				            
				          </div>
				          	<h4 class="card-title">
				          		<a href="<?php echo get_permalink($Circolare->ID);?>"><?php echo get_the_title($Circolare->ID); ?></a>
				          	</h4>
				          <p class="card-text"><?php echo $Circolare->post_excerpt; ?></p>
				          <div class="it-card-footer">
<?php 	if(get_theme_mod('scuola_MTautore_attiva')):?>	
				            <span class="card-autore"><span class="fas fa-user-edit"></span> <?php echo get_the_author_meta('display_name', $Circolare->post_author);?></span>
<?php	endif;
								if (wps_Is_Circolare_Da_Firmare($Circolare->ID)){?>
				            	  <span class="card-firma">
							<?php		if (!wps_Is_Circolare_Firmata($Circolare->ID)) {
											$ngiorni=wps_Get_scadenzaCircolare($Circolare->ID,"",True);					
											if(wps_Is_Circolare_Scaduta($Circolare->ID)){?>
									<span class="fa fa-pencil" aria-hidden="true" style="color:red;"></span> <?php _e('Scaduta e non Firmata','wpscuola');						
											}else{
												switch ($ngiorni){
													case -1:							
														$entro="";							
														break;													
													case 0:
														$entro= __('entro OGGI','wpscuola');
														break;
													case 1:
														$entro= __('entro DOMANI','wpscuola');
														break;
													default:
														$entro= sprintf(__('entro %s giorni','wpscuola'),$ngiorni);
														break;
												}
												$sign=get_post_meta($Circolare->ID, "_sign",TRUE);
												if ($sign!="Firma")
													$Tipo=sprintf(__('Esprimere adesione %s','wpscuola'),$entro);
												else
													$Tipo=sprintf(__('Firmare %s','wpscuola'),$entro);?>
									<span class="fa fa-pencil" aria-hidden="true" style="color:red;"></span> <?php echo $Tipo;	
										}			
									}else{ ?>
									<span class="fa fa-pencil" aria-hidden="true" style="color:blue;"></span> <?php _e('Firmata','wpscuola');
						   			}?>
								</span>
				           <?php } ?>
				          </div>
				        </div>
				      </div>
				    </div>
    			<!--end card-->
      		</div>
<?php			}
			}
		} ?>
			</div>
<?php		if($linkLT){?>							
			<div class="it-card-footer">
		    	<a class="read-more" href="<?php echo get_post_type_archive_link("circolari_scuola");?>">
		        	<span class="text"><span class="fas fa-link p-1"></span> <?php _e('Leggi tutte le circolari','wpscuola');?></span>
		        </a>
		    </div>
<?php 		} ?>
		</div>
	</section>      	
<?php                   
	wp_reset_postdata();

    if ( ! $this->is_preview() ) {
        $cache[ $args['widget_id'] ] = ob_get_flush();
        wp_cache_set( 'widget_circolari', $cache, 'widget' );
    } else {
        ob_end_flush();
    }
}
	    public function flush_widget_cache() 
	    {
	        wp_cache_delete('widget_circolari', 'widget');
	    }
	    
        function update( $new_instance, $old_instance ) {
//var_dump($new_instance);wp_die();
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['numelementi']=strip_tags($new_instance['numelementi']);   
			$instance['leggitutto']=strip_tags($new_instance['leggitutto']);
           return $instance;
        }

        function form( $instance ) {
            $instance = wp_parse_args( (array) $instance, array( ) ); 
            $titolo = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Circolari', 'wpscuola' );
            $numelementi=isset($instance['numelementi'])?$instance['numelementi']:5;
			$linkLT=(isset($instance['numelementi']) And $instance['leggitutto'] == '1') ? 'checked="checked"':'';
?>           

           <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Titolo Sezione','wpscuola');?>:</label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $titolo ); ?>" />
            </p>
            <p>
             <label for="<?php echo $this->get_field_id( 'numelementi' ); ?>"><?php _e('N° elementi da visualizzare','wpscuola');?>:</label>
            <input type="number" min="1" max="10" id="<?php echo $this->get_field_id( 'numelementi' ); ?>" name="<?php echo $this->get_field_name( 'numelementi' ); ?>" value="<?php echo $numelementi; ?>" />
        </p>
     <div class="Servizi">
    	<h3><?php _e('Link Leggi Tutto','wpscuola');?></h3>
    	<label for="<?php echo $this->get_field_id( 'leggitutto' ); ?>"><?php _e('Attiva','wpscuola');?>:</label>
    	<input type="checkbox" id="<?php echo $this->get_field_id('leggitutto'); ?>" name="<?php echo $this->get_field_name('leggitutto'); ?>" value="1" <?php echo $linkLT; ?>/>
    </div>
      <?php
		}
     }		
