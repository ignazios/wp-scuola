<?php
/**
* Widget Servizi
*	   Titolo:	($title) Testo che viene visualizzato in Testa al blocco
*/
class Servizi extends WP_Widget {
	var $defaults = array();
		
    /** constructor */
       function __construct() {
			parent::__construct( false, 'Scuola Servizi',
				array('classname' => 'Servizi',
				    'description' => __('Blocco Servizi organizzato con tabs per tipologia di servizio','wpscuola')));
         	add_action( 'save_post', 	[$this, 'flush_widget_cache'] );
	      	add_action( 'deleted_post', [$this, 'flush_widget_cache'] );
	      	add_action( 'switch_theme', [$this, 'flush_widget_cache'] );
        }

    function widget($args, $instance) {
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
	        
 	         $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Servizi','wpscuola' );
        	/** This filter is documented in twp-includes/default-widgets.php */
        	 $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
             $tipovis = ! empty( $instance['tipovis'] ) ? $instance['tipovis'] :0;
             $tipologias = ! empty( $instance['tipologias'] ) ? $instance['tipologias'] :0;
             $tipologiasDaEscludere=isset($instance['tipologiasDaEscludere'])?$instance['tipologiasDaEscludere']:"";?>
 <section id="art_<?php echo $args['widget_id'];?>"  class="home-widget container">
		<div class="it-header-block">
	    	<div class="it-header-block-title">
<?php 
            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
             switch ($tipovis){
            	case 0:
            		$argServizi = array('numberposts' => -1,
            							'post_type'   => 'servizio',
             							'order' => 'ASC',
  										'meta_key'			=> 'servizio_ordine',
										'orderby'			=> 'meta_value_num',
										'meta_query'	=> array(array('key'	 	=> 'servizio_attivazione_servizio',
																	   'value'	  	=> 'si',
																	   'compare' 	=> '=')));
					$Servizi=get_posts($argServizi);
					$this->Crea_blocchi($Servizi);
					break;
				case 1:
					$Tipologie=get_categories(array('taxonomy'  	=>"tiposervizio",
													'hide_empty' 	=> true,
													'exclude'		=> $tipologiasDaEscludere,
													'order' 		=> 'ASC',
           											'meta_key'		=> 'ordine',
  													'orderby' 		=> 'meta_value_num'));
					if (count($Tipologie)>0){?>
						<nav>
						  <div class="nav nav-tabs auto" id="nav_<?php echo $args['widget_id'];?>" role="tablist">
<?php					$Primo=true;
						foreach($Tipologie as $Tipologia){?>
						    <a class="nav-item nav-link <?php echo ($Primo?"active":'');$Primo=false;?> nav-item-ufficio" id="nav-<?php echo $Tipologia->term_id;?>-tab" data-toggle="tab" href="#<?php echo $Tipologia->slug;?>" role="tab" aria-controls="<?php echo $Tipologia->slug;?>" aria-selected="<?php echo ($Primo?"true":'false');$Primo=false;?>" ><?php echo $Tipologia->name;?></a>
						<?php }?>    
						  </div>
						</nav>	
						<div class="tab-content" id="nav-tabContent_<?php echo $args['widget_id'];?>">		
<?php					$Primo=true;
						foreach($Tipologie as $Tipologia){
           					$argServizi = array('numberposts' 	=> -1,
           										'post_type'   	=> 'servizio',
           										'order' 		=> 'ASC',
           										'meta_key'		=> 'servizio_ordine',
  												'orderby' 		=> 'meta_value_num',
  												'meta_query'	=> array(array('key'	 	=> 'servizio_attivazione_servizio',
																				'value'	  	=> 'si',
																				'compare' 	=> '=')),
		            							'tax_query' 	=> array(array( 'taxonomy' => 'tiposervizio',
															                	'field' => 'slug',
															                	'terms' => $Tipologia->slug)));
							$Servizi=get_posts($argServizi);?>
							<div class="tab-pane p-4 fade show <?php echo ($Primo?"active":'');$Primo=false;?> shadow" id="<?php echo $Tipologia->slug;?>" role="tabpanel" aria-labelledby="nav-<?php echo $Tipologia->term_id;?>-tab">
							  	<div class="">
							  		<?php $this->Crea_blocchi($Servizi);?>
							  	</div>
	        				</div>
<?php					}?>
						</div>
<?php				}				
					break;
	           	case 2:
	            		$argServizi = array('numberposts' 	=> -1,
	            							'post_type'   	=> 'servizio',
	            							'meta_key'		=> 'servizio_ordine',
	            							'order' 		=> 'ASC',
  											'orderby' 		=> 'meta_value_num',		            							
											'meta_query'	=> array(array('key'	 	=> 'servizio_attivazione_servizio',
																			'value'	  	=> 'si',
																			'compare' 	=> '=')),
	            							'tax_query'		=> array(array( 'taxonomy' 	=> 'tiposervizio',
																        	'field' 	=> 'id',
																        	'terms' 	=> $tipologias)));
						$Servizi=get_posts($argServizi);
						$this->Crea_blocchi($Servizi," shadow pl-3 pt-4 pb-4");
						break;
            } ?>   
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
/**
* 
* @param {object} $Servizi
* Funzione che stampa il codice per visualizzare i blocchi dei servizi
* @return
*/
	function Crea_blocchi($Servizi,$ClassRow=""){?>
		<div class="container">
	<div class="row<?php echo $ClassRow;?>">
<?php 
		foreach($Servizi as $Servizio){
			$Servizio_Image=get_the_post_thumbnail_url($Servizio->ID);
			$Servizio_Link=get_permalink($Servizio->ID);?>
		<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 pb-2">
		    <div class="hovereffect">
		        <img class="img-responsive" src="<?php echo $Servizio_Image;?>" alt="logo servizo <?php echo $Servizio->post_title;?>" width="100%" height="200">
		            <div class="overlay">
		                <h2><?php echo $Servizio->post_title;?></h2>
						<p>
							<a href="<?php echo $Servizio_Link;?>" class="text-white">Vai al Servizio</a>
						</p>
		            </div>
		    </div>
		</div>
		<div class="visible-sm-block"></div>
<?php } ?>		
	</div>
  </div>
<?php
	} 

	public function flush_widget_cache(){
	        wp_cache_delete('widget_servizi', 'widget');
	}	
	
	function update($new_instance, $old_instance){
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['tipovis'] = $new_instance['tipovis'];
        $instance['tipologias'] = $instance['tipovis']==2?$new_instance['tipologias']:0;
        $instance['tipologiasDaEscludere']=strip_tags($new_instance['tipologiasDaEscludere']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance){
		$instance = wp_parse_args( (array) $instance, array( ) ); 
		$titolo = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Servizi', 'wpscuola' );
		$tipovis = ! empty( $instance['tipovis'] ) ? $instance['tipovis'] :0;
		$tipologias = ! empty( $instance['tipologias'] ) ? $instance['tipologias'] :0;	
		$tipologiasDaEscludere=isset($instance['tipologiasDaEscludere'])?$instance['tipologiasDaEscludere']:"";
		?>
           <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Titolo Sezione', 'wpscuola' );?>:</label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $titolo ); ?>" />
            </p>
            <p>
  	    		<label for="<?php echo $this->get_field_id( 'tipovis' ); ?>"><?php _e( 'Tipologia di visualizzazione', 'wpscuola' );?>:</label><br />
  	    		<select name="<?php echo $this->get_field_name( 'tipovis' ); ?>" id="<?php echo $this->get_field_id( 'tipovis' ); ?>" class="">
					<option value="0" <?php echo ($tipovis=="0" ?'selected="selected"':"");?>><?php _e( 'Tutti i servizi', 'wpscuola' );?></option>
					<option value="1" <?php echo ($tipovis=="1" ?'selected="selected"':"");?>><?php _e( 'Per tipologia di Servizio', 'wpscuola' );?></option>
					<option value="2" <?php echo ($tipovis=="2" ?'selected="selected"':"");?>><?php _e( 'Singola tipologia di Servizio', 'wpscuola' );?></option>
				</select>
        	</p>
        	<p>
				<label for="<?php echo $this->get_field_id( 'tipologias' ); ?>"><?php _e( 'Tipologia di servizio', 'wpscuola' );?>:</label><br />
<?php  
	$args = array(
	'option_none_value'  => '-1',
	'orderby'            => 'name',
	'order'              => 'ASC',
	'show_count'         => 0,
	'hide_empty'         => FALSE,
	'child_of'           => 0,
	'echo'               => TRUE,
	'selected'           => $tipologias,
	'name'               => $this->get_field_name('tipologias'),
	'id'                 => $this->get_field_id('tipologias'),
	'taxonomy'			 => 'tiposervizio',
	'class'              => '',);
	wp_dropdown_categories( $args );
?>
        	<br />
        	<em><?php _e( 'Questo valore viene utilizzato solo per</em> <strong>Tipologia di visualizzazione</strong>=<em>Singola tipologia di Servizio', 'wpscuola' );?></em>
        	</p>
      	<p>
			<label for="<?php echo $this->get_field_id( 'tipologiasDaEscludere' ); ?>"><?php _e( 'Tipologia di servizio da escludere', 'wpscuola' );?>:</label>
			<input type="text"  id="<?php echo $this->get_field_id( 'tipologiasDaEscludere' ); ?>" name="<?php echo $this->get_field_name( 'tipologiasDaEscludere' ); ?>" value="<?php echo $tipologiasDaEscludere; ?>">
        	<br />
        	<em><?php _e( 'Indicare gli <strong>ID</strong> (separati da ,) della Tipologia di visualizzazione da escludere. Valido solo per Tipologia di Visualizzazione</strong>=Tutti i servizi', 'wpscuola' );?></em>
        </p>
<?php
	}
}