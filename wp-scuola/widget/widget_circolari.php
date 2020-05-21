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
				    'description' => 'Blocco Circolari in Home Page') );
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
            $title = apply_filters('widget_title', $instance['titolo']);
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
		echo $args['before_widget'];          
           if ( $title ) {
                echo $args['before_title'] . $title . ' <a href="'.site_url('/wp-admin/edit.php?post_type=circolari_scuola&page=Firma').'">';
                if(is_user_logged_in() and ($CircoDaFirmare=wps_GetCircolariDaFirmare("N"))>0){
                	echo '<span class="badge badge-pill badge-danger" title="Circolari da firmare">'.$CircoDaFirmare.'</span></a>';
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
      			<div class="it-grid-item-wrapper shadow>
			    <!--start card-->
				    <div class="card-wrapper card-space">
				      <div class="card card-bg card-big no-after">
				        <div class="card-body">
				          <div class="head-tags">
				            <ul>
				            	<li><span class="data"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y',strtotime($Circolare->post_date));?> <strong><i class="fas fa-ticket-alt"></i> <?php echo $numero."_".$anno;?></strong></span></li>
				            	<li><span class="users"><i class="fa fa-users" aria-hidden="true"></i> <?php echo $Elenco;?></span></li>
				            </ul>
				            
				          </div>
				          	<h5 class="card-title">
				          		<a href="<?php echo get_permalink($Circolare->ID);?>"><?php echo get_the_title($Circolare->ID); ?></a>
				          	</h5>
				          <p class="card-text"><?php echo $Circolare->post_excerpt; ?></p>
				          <div class="it-card-footer">
				            <span class="card-autore"><i class="fas fa-user-edit"></i> <?php echo get_the_author_meta('display_name', $Circolare->post_author);?></span>
				            <?php	if (wps_Is_Circolare_Da_Firmare($Circolare->ID)){?>
				            	  <span class="card-firma">
							<?php		if (!wps_Is_Circolare_Firmata($Circolare->ID)) {
											$ngiorni=wps_Get_scadenzaCircolare($Circolare->ID,"",True);					
											if(wps_Is_Circolare_Scaduta($Circolare->ID)){
												echo' <i class="fa fa-pencil" aria-hidden="true" style="color:red;"></i> Scaduta e non Firmata ';						
											}else{
												switch ($ngiorni){
													case -1:							
														$entro="";							
														break;													
													case 0:
														$entro="entro OGGI";
														break;
													case 1:
														$entro="entro DOMANI";
														break;
													default:
														$entro="entro $ngiorni giorni";
														break;
												}
												$sign=get_post_meta($Circolare->ID, "_sign",TRUE);
												if ($sign!="Firma")
													$Tipo="Esprimere adesione $entro";
												else
													$Tipo="Firmare $entro";
												echo' <i class="fa fa-pencil" aria-hidden="true" style="color:red;"></i> '.$Tipo;	
										}			
									}else{
										echo' <i class="fa fa-pencil" aria-hidden="true" style="color:blue;"></i> Firmata';				
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
		        	<span class="text"><i class="fas fa-link p-1"></i> Leggi tutte le circolari</span>
		        </a>
		    </div>
<?php 		} ?>
		</div>
<?php   echo $args['after_widget']; ?>
	</section>      	
<?php                   
	wp_reset_postdata();

    if ( ! $this->is_preview() ) {
        $cache[ $args['widget_id'] ] = ob_get_flush();
        wp_cache_set( 'widget_grid_posts', $cache, 'widget' );
    } else {
        ob_end_flush();
    }
}

        function update( $new_instance, $old_instance ) {
//var_dump($new_instance);wp_die();
            $instance = $old_instance;
            $instance['titolo'] = strip_tags($new_instance['titolo']);
            $instance['numelementi']=strip_tags($new_instance['numelementi']);   
			$instance['leggitutto']=strip_tags($new_instance['leggitutto']);
           return $instance;
        }

        function form( $instance ) {
            $instance = wp_parse_args( (array) $instance, array( ) ); 
            $titolo = ! empty( $instance['titolo'] ) ? $instance['titolo'] : esc_html__( 'Comunicazioni', 'text_domain' );
            $numelementi=isset($instance['numelementi'])?$instance['numelementi']:5;
?>           

           <p>
                <label for="<?php echo $this->get_field_id( 'titolo' ); ?>">Titolo Sezione:</label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'titolo' ); ?>" name="<?php echo $this->get_field_name( 'titolo' ); ?>" value="<?php echo esc_attr( $titolo ); ?>" />
            </p>
            <p>
             <label for="<?php echo $this->get_field_id( 'numelementi' ); ?>">N&ordm; elementi da visualizzare:</label>
            <input type="number" min="1" max="10" id="<?php echo $this->get_field_id( 'numelementi' ); ?>" name="<?php echo $this->get_field_name( 'numelementi' ); ?>" value="<?php echo $numelementi; ?>" />
        </p>
     <div class="Servizi">
    	<h3>Link Leggi Tutto</h3>
    	<label for="<?php echo $this->get_field_id( 'leggitutto' ); ?>">Attiva:</label>
    	<input type="checkbox" id="<?php echo $this->get_field_id('leggitutto'); ?>" name="<?php echo $this->get_field_name('leggitutto'); ?>" value="1" <?php echo ($instance['leggitutto'] == '1') ? 'checked="checked"':''; ?>/>
    </div>
      <?php
		}
     }		
