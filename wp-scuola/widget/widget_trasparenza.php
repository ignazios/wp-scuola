<?php
/****************************************************************************
 Widget Trasparenza
	   Titolo:	($title) Testo che viene visualizzato in Testa all'elenco degli articoli estratti
	  Pagina Amministrazione trasparente:	($urlAT)  Link alla pagina amministrazione trasparente
	           Pagina Albo Atti Correnti:	($urlAAC) Link alla pagina dell'albo atti correnti
	            Pagina Albo Atti Scaduti:	($urlAAS) Link alla pagina dell'albo atti storico
	  			   Pagina Accesso Civico:	($urlAC)  Link alla pagina dell'accesso civico
	  			              Pagina URP:	($urlURP) Link alla pagina dell'Ufficio relazioni con il pubblico
*/

class Trasparenza extends WP_Widget {

        function __construct() {
			parent::__construct( false, 'Scuola Trasparenza',
				array('classname' => 'Trasparenza',
				    'description' => __('Blocco Trasparenza Amministrazione Trasparente - Albo Pretorio - Accesso Civico - Ufficio Relazioni Con il Pubblico','wpscuola')));
          	add_action( 'save_post', 	[$this, 'flush_widget_cache'] );
	      	add_action( 'deleted_post', [$this, 'flush_widget_cache'] );
	      	add_action( 'switch_theme', [$this, 'flush_widget_cache'] );
       }

        function widget( $args, $instance ) {
	       $cache = [];
	        if ( ! $this->is_preview() ) {
	            $cache = wp_cache_get( 'widget_servizi', 'widget' );
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
  	        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Trasparenza','wpscuola' );
        	/** This filter is documented in twp-includes/default-widgets.php */
        	$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
            $Is_AT=(isset($instance['AT']) And $instance['AT']!="");
            $Link_AT=(isset($instance['LAT']) And $instance['LAT']!="");
            $Is_AP=(isset($instance['AP']) And $instance['AP']!="");
            $Link_AP=(isset($instance['LAP']) And $instance['LAP']!="");
            $Link_AC=(isset($instance['LAC']) And $instance['LAC']!="");
            $Link_URP=(isset($instance['LURP']) And $instance['LURP']!="");
            $Is_AC=(isset($instance['AC']) And $instance['AC']!="");
            $Is_URP=(isset($instance['URP']) And $instance['URP']!="");
            if( !$Is_AT And !$Link_AT And !$Link_AP And !$Link_AC And  $Link_URP And !$Is_AP AND !$Is_AC AND !$Is_URP){
	            	return;
			}
			$LAT=($Is_AT) ? $instance['AT']:$instance['LAT'];
			$AP=($Is_AP) ? $instance['AP']:$instance['LAP'];
			$AC=($Is_AC) ? $instance['AC']:$instance['LAC'];
			$URP=($Is_URP) ? $instance['URP']:$instance['LURP'];
			$ELAT=(!$Is_AT) ? 'target="_blank"':'';
			$EAP=(!$Is_AP) ? 'target="_blank"':'';
			$EAC=(!$Is_AC) ? 'target="_blank"':'';
			$EURP=(!$Is_URP) ? 'target="_blank"':'';
            $Testo_AC=((isset($instance['TAC']) And $instance['TAC']!="")?$instance['TAC']:__("Accesso Civico"));
            $Testo_URP=((isset($instance['TURP']) And $instance['TURP']!="")?$instance['TURP']:__("URP"));
?>
<section id="trasp_<?php echo $args['widget_id'];?>"  class="home-widget container ">
<?php  
            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            } ?>   
  <div class="row d-flex justify-content-center pl-3 pr-3" >
    <div class="col-lg-4 col-sm-6 col-12 m-2 p-5 btn-primary rounded text-center">
    	<a href="<?php echo $LAT;?>" class="lead testo-bianco" <?php echo $ELAT;?>><?php _e('Amministrazione Trasparente','wpscuola');?></a>
    </div>
    <div class="col-lg-3 col-sm-6 col-12 m-2">
    	  <div class="row mb-2 h-48">
		    <div class="col p-2 btn-primary rounded text-center">
				<a href="<?php echo $URP; ?>" class="lead testo-bianco" <?php echo $EURP;?>><?php echo $Testo_URP;?></a>
			</div>
		  </div>
		  <div class="row h-48 pb-1">
		    <div class="col p-2 btn-primary rounded text-center">
		    	<a href="<?php echo $AC;?>" class="lead testo-bianco" <?php echo $EAC;?>><?php echo $Testo_AC;?></a>
		    </div>
		  </div> 	
    </div>
    <div class="col-lg-4 col-sm-6 col-12 m-2 p-5 btn-primary rounded text-center">
		<a href="<?php echo $AP; ?>" class="lead testo-bianco" <?php echo $EAP;?>><?php _e('Albo OnLine','wpscuola');?></a>
    </div>
  </div>
</section>
<?php
	wp_reset_postdata();

    if ( ! $this->is_preview() ) {
        $cache[ $args['widget_id'] ] = ob_get_flush();
        wp_cache_set( 'widget_link', $cache, 'widget' );
    } else {
        ob_end_flush();
    }
}

	function make_List_Pages($Pages,$Id,$Name,$Default){
		$Pagine="<select id=\"".$Id."\" name=\"".$Name."\">
	<option value=\"\" >--------</option>";
		foreach ( $Pages as $Pagina ) {
			$Url=get_permalink($Pagina->ID);
			$Pagine.= "<option value=\"".$Url."\"";
			if($Url==$Default){
				$Pagine.= " selected ";	
			}
			$Pagine.= " >".$Pagina->post_title."</option>";
		}
		$Pagine.="</select>";
		return $Pagine; 
	}
		
	public function flush_widget_cache(){
	    wp_cache_delete('widget_servizi', 'widget');
	}	

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['AT'] = strip_tags($new_instance['AT']);
        $instance['LAT'] = strip_tags($new_instance['LAT']);
        $instance['LAP'] = strip_tags($new_instance['LAP']);
        $instance['TAC'] = strip_tags($new_instance['TAC']);
        $instance['LAC'] = strip_tags($new_instance['LAC']);
        $instance['TURP'] = strip_tags($new_instance['TURP']);
        $instance['LURP'] = strip_tags($new_instance['LURP']);
        $instance['AP'] = strip_tags($new_instance['AP']);
        $instance['AC'] = strip_tags($new_instance['AC']);
        $instance['URP'] = strip_tags($new_instance['URP']);
        return $instance;
    }

    function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( ) ); 
		$titolo = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Trasparenza', 'wpscuola' );
        $args = array(
		'post_status' => 'publish',
		'sort_order' => 'asc',
		'sort_column' => 'post_date'
		);		
        $Pagine=get_pages( $args );
        $ElencoAT=$this->make_List_Pages($Pagine,$this->get_field_id( 'AT' ),$this->get_field_name( 'AT' ),! empty( $instance['AT'] ) ? $instance['AT'] :"");
    	$Link_AT=(isset($instance['LAT'])?$instance['LAT']:"");
       	$Link_AP=(isset($instance['LAP'])?$instance['LAP']:"");
       	$Link_AC=(isset($instance['LAC'])?$instance['LAC']:"");
       	$Testo_AC=(isset($instance['TAC'])?$instance['TAC']:"Accesso Civico");
       	$Link_URP=(isset($instance['LURP'])?$instance['LURP']:"");
       	$Testo_URP=(isset($instance['TURP'])?$instance['TURP']:"URP");
        $ElencoAP=$this->make_List_Pages($Pagine,$this->get_field_id( 'AP' ),$this->get_field_name( 'AP' ),! empty( $instance['AP'] ) ? $instance['AP'] :"");
        $ElencoAC=$this->make_List_Pages($Pagine,$this->get_field_id( 'AC' ),$this->get_field_name( 'AC' ),! empty( $instance['AC'] ) ? $instance['AC'] :"");
        $ElencoURP=$this->make_List_Pages($Pagine,$this->get_field_id( 'URP' ),$this->get_field_name( 'URP' ),! empty( $instance['URP'] ) ? $instance['URP'] :"");
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Titolo Sezione','wpscuola');?>:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $titolo ); ?>" />
        </p>
         <p>
		    <label for="<?php echo $this->get_field_id( 'LAT' );?>"><?php _e('Link Esterno Amministrazione Trasparente','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'LAT' ); ?>" name="<?php echo $this->get_field_name( 'LAT' ); ?>" value="<?php echo esc_attr( $Link_AT ); ?>" />
        </p>
       <p>
		    <label for="<?php echo $this->get_field_id( 'LAP' );?>"><?php _e('Link Esterno Albo OnLine','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'LAP' ); ?>" name="<?php echo $this->get_field_name( 'LAP' ); ?>" value="<?php echo esc_attr( $Link_AP ); ?>" />
        </p>
     	<p>
		    <label for="<?php echo $this->get_field_id( 'TURP' );?>"><?php _e('Etichetta blocco centrale piccolo alto (URP)','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'TURP' ); ?>" name="<?php echo $this->get_field_name( 'TURP' ); ?>" value="<?php echo esc_attr( $Testo_URP ); ?>" />
        </p>
      	<p>
		    <label for="<?php echo $this->get_field_id( 'LURP' );?>"><?php _e('Link blocco centrale piccolo alto (URP)','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'LURP' ); ?>" name="<?php echo $this->get_field_name( 'LURP' ); ?>" value="<?php echo esc_attr( $Link_URP ); ?>" />
        </p>
       	<p>
		    <label for="<?php echo $this->get_field_id( 'TAC' );?>"><?php _e('Etichetta blocco centrale piccolo basso (Accesso Civico)','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'TAC' ); ?>" name="<?php echo $this->get_field_name( 'TAC' ); ?>" value="<?php echo esc_attr( $Testo_AC ); ?>" />
     	</p>
       	<p>
		    <label for="<?php echo $this->get_field_id( 'LAC' );?>"><?php _e('Link blocco centrale piccolo basso (Accesso Civico)','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'LAC' ); ?>" name="<?php echo $this->get_field_name( 'LAC' ); ?>" value="<?php echo esc_attr( $Link_AC ); ?>" />
        </p>
       	<p>
		    <label for="<?php echo $this->get_field_id( 'AT' );?>"><?php _e('Pagina Amministrazione Trasparente','wpscuola');?>:</label><br />
       		<?php echo $ElencoAT; ?>
        </p>
       	<p>
		    <label for="<?php echo $this->get_field_id( 'AP' );?>"><?php _e('Pagina Albo','wpscuola');?>:</label><br />
       		<?php echo $ElencoAP; ?>
       </p>
       <p>
		    <label for="<?php echo $this->get_field_id( 'AC' );?>"><?php _e('Pagina Accesso Civico','wpscuola');?>:</label><br />
       		<?php echo $ElencoAC; ?>
        </p>
       <p>
		    <label for="<?php echo $this->get_field_id( 'URP' );?>"><?php _e('Pagina Ufficio Relazioni con il Pubblico','wpscuola');?>:</label><br />
       		<?php echo $ElencoURP; ?>
        </p>
  <?php
    }
}