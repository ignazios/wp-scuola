<?php
/****************************************************************************
 Widget Pulsanti
	   Titolo:	($title) Testo che viene visualizzato in Testa all'elenco degli articoli estratti
*/

class Pulsanti extends WP_Widget {

        function __construct() {
			parent::__construct( false, 'Scuola Pulsanti',
				array('classname' => 'Pulsanti',
				    'description' => __('Blocco Pulsanti','wpscuola')));
          	add_action( 'save_post', 	[$this, 'flush_widget_cache'] );
	      	add_action( 'deleted_post', [$this, 'flush_widget_cache'] );
	      	add_action( 'switch_theme', [$this, 'flush_widget_cache'] );
       }

        function widget( $args, $instance ) {
	       $cache = [];
	        if ( ! $this->is_preview() ) {
	            $cache = wp_cache_get( 'widget_pulsanti', 'widget' );
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
  	        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] :"";
        	$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
	    	$EP1=(isset($instance['EP1'])?$instance['EP1']:"");
	       	$LP1=(isset($instance['LP1'])?$instance['LP1']:"");
	    	$EP2=(isset($instance['EP2'])?$instance['EP2']:"");
	       	$LP2=(isset($instance['LP2'])?$instance['LP2']:"");
	    	$EP3=(isset($instance['EP3'])?$instance['EP3']:"");
	       	$LP3=(isset($instance['LP3'])?$instance['LP3']:"");
	    	$EP4=(isset($instance['EP4'])?$instance['EP4']:"");
	       	$LP4=(isset($instance['LP4'])?$instance['LP4']:"");
?>
<section id="pulsanti_<?php echo $args['widget_id'];?>"  class="home-widget container ">
<?php  
            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            } ?>   
		<div class="row justify-content-center pt-3">
		    <div class="col-lg-3 col-sm-6 col-12 pr-3">
		    	<div class="btn-primary text-center align-middle rounded p-2" style="height: 3em;">
	    			<a href="<?php echo $LP1;?>" class="lead testo-bianco" target="_blank"><?php echo $EP1;?></a>
	    		</div>
	    	</div>
		    <div class="col-lg-3 col-sm-6 col-12 pr-3 pt-2 pt-sm-0">
		    	<div class="btn-primary text-center align-middle rounded p-2" style="height: 3em;">
	    			<a href="<?php echo $LP2;?>" class="lead testo-bianco" target="_blank"><?php echo $EP2;?></a>
	    		</div>
	    	</div>
		    <div class="col-lg-3 col-sm-6 col-12 pr-3 pt-2 pt-lg-0">
		    	<div class="btn-primary text-center align-middle rounded p-2" style="height: 3em;">
	    			<a href="<?php echo $LP3;?>" class="lead testo-bianco" target="_blank"><?php echo $EP3;?></a>
	    		</div>
	    	</div>
		    <div class="col-lg-3 col-sm-6 col-12 pr-3 pt-2 pt-lg-0">
		    	<div class="btn-primary text-center align-middle rounded p-2" style="height: 3em;">
	    			<a href="<?php echo $LP4;?>" class="lead testo-bianco" target="_blank"><?php echo $EP4;?></a>
	    		</div>
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

	public function flush_widget_cache(){
	    wp_cache_delete('widget_servizi', 'widget');
	}	

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['EP1'] = strip_tags($new_instance['EP1']);
        $instance['LP1'] = strip_tags($new_instance['LP1']);
        $instance['EP2'] = strip_tags($new_instance['EP2']);
        $instance['LP2'] = strip_tags($new_instance['LP2']);
        $instance['EP3'] = strip_tags($new_instance['EP3']);
        $instance['LP3'] = strip_tags($new_instance['LP3']);
        $instance['EP4'] = strip_tags($new_instance['EP4']);
        $instance['LP4'] = strip_tags($new_instance['LP4']);
        return $instance;
    }

    function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( ) ); 
		$titolo = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Pulsanti', 'wpscuola' );
        $args = array(
		'post_status' => 'publish',
		'sort_order' => 'asc',
		'sort_column' => 'post_date'
		);		
    	$EP1=(isset($instance['EP1'])?$instance['EP1']:"");
       	$LP1=(isset($instance['LP1'])?$instance['LP1']:"");
    	$EP2=(isset($instance['EP2'])?$instance['EP2']:"");
       	$LP2=(isset($instance['LP2'])?$instance['LP2']:"");
    	$EP3=(isset($instance['EP3'])?$instance['EP3']:"");
       	$LP3=(isset($instance['LP3'])?$instance['LP3']:"");
    	$EP4=(isset($instance['EP4'])?$instance['EP4']:"");
       	$LP4=(isset($instance['LP4'])?$instance['LP4']:"");
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Titolo Sezione','wpscuola');?>:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $titolo ); ?>" />
        </p>
       	<p>
		    <label for="<?php echo $this->get_field_id( 'EP1' );?>"><?php _e('Etichetta Primo pulsante','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'EP1' ); ?>" name="<?php echo $this->get_field_name( 'EP1' ); ?>" value="<?php echo esc_attr( $EP1 ); ?>" />
        </p>
      	<p>
		    <label for="<?php echo $this->get_field_id( 'LP1' );?>"><?php _e('Link Primo pulsante','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'LP1' ); ?>" name="<?php echo $this->get_field_name( 'LP1' ); ?>" value="<?php echo esc_attr( $LP1 ); ?>" />
        </p>
      	<p>
		    <label for="<?php echo $this->get_field_id( 'EP2' );?>"><?php _e('Etichetta Secondo pulsante','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'EP2' ); ?>" name="<?php echo $this->get_field_name( 'EP2' ); ?>" value="<?php echo esc_attr( $EP2 ); ?>" />
        </p>
      	<p>
		    <label for="<?php echo $this->get_field_id( 'LP2' );?>"><?php _e('Link Secondo pulsante','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'LP2' ); ?>" name="<?php echo $this->get_field_name( 'LP2' ); ?>" value="<?php echo esc_attr( $LP2 ); ?>" />
        </p> 
        <p>
		    <label for="<?php echo $this->get_field_id( 'EP3' );?>"><?php _e('Etichetta Terzo pulsante','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'EP3' ); ?>" name="<?php echo $this->get_field_name( 'EP3' ); ?>" value="<?php echo esc_attr( $EP3 ); ?>" />
        </p>
      	<p>
		    <label for="<?php echo $this->get_field_id( 'LP3' );?>"><?php _e('Link Terzo pulsante','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'LP3' ); ?>" name="<?php echo $this->get_field_name( 'LP3' ); ?>" value="<?php echo esc_attr( $LP3 ); ?>" />
        </p> 
       <p>
		    <label for="<?php echo $this->get_field_id( 'EP4' );?>"><?php _e('Etichetta Quarto pulsante','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'EP4' ); ?>" name="<?php echo $this->get_field_name( 'EP4' ); ?>" value="<?php echo esc_attr( $EP4 ); ?>" />
        </p>
      	<p>
		    <label for="<?php echo $this->get_field_id( 'LP4' );?>"><?php _e('Link Terzo pulsante','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'LP4' ); ?>" name="<?php echo $this->get_field_name( 'LP4' ); ?>" value="<?php echo esc_attr( $LP4 ); ?>" />
        </p> 
  <?php
    }
}