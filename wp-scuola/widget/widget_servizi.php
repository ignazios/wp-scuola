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
				    'description' => 'Blocco Servizi organizzato con tabs per tipologia di servizio') );
        }

    function widget($args, $instance) {
 	       $cache = [];
	        if ( ! $this->is_preview() ) {
	            $cache = wp_cache_get( 'widget_grid_posts', 'widget' );
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
	        
             $title = apply_filters('widget_title', $instance['titolo']);
             $tipovis = ! empty( $instance['tipovis'] ) ? $instance['tipovis'] :0;
             $tipologias = ! empty( $instance['tipologias'] ) ? $instance['tipologias'] :0;?>
 <section id="art_<?php echo $args['widget_id'];?>"  class="home-widget container">
		<div class="it-header-block">
	    	<div class="it-header-block-title">
<?php 
            echo $args['before_widget'];
            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
            switch ($tipovis){
            	case 0:
            		$argServizi = array('numberposts' => -1,
            							'post_type'   => 'servizio',);
					$Servizi=get_posts($argServizi);
					$this->Crea_blocchi($Servizi);
					break;
				case 1:
					$Tipologie=get_categories(array('taxonomy' =>"tiposervizio","hide_empty" => 0));
//					echo "<pre>";var_dump($Tipologie);echo "</pre>";
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
           					$argServizi = array('numberposts' => -1,
           										'post_type'   => 'servizio',
		            							'tax_query' => array(array( 'taxonomy' => 'tiposervizio',
															                'field' => 'slug',
															                'terms' => $Tipologia->slug)));
							$Servizi=get_posts($argServizi);?>
							<div class="tab-pane p-4 fade show <?php echo ($Primo?"active":'');$Primo=false;?> shadow" id="<?php echo $Tipologia->slug;?>" role="tabpanel" aria-labelledby="<?php echo $Tipologia->slug;?>-tab">
							  	<div class="">
							  		<?php $this->Crea_blocchi($Servizi);?>
							  	</div>
	        				</div>
<?php					}?>
						</div>
<?php				}				
					break;
	           	case 2:
	            		$argServizi = array('numberposts' => -1,
	            							'post_type'   => 'servizio',		            							
	            							'tax_query' => array(array( 'taxonomy' 	=> 'tiposervizio',
																        'field' 	=> 'id',
																        'terms' 	=> $tipologias)));
						$Servizi=get_posts($argServizi);
						$this->Crea_blocchi($Servizi);
						break;
            } ?>   
	    	</div>
		</div>
<?php echo $args['after_widget'];?>
</section>	
<?php
    }
/**
* 
* @param {object} $Servizi
* Funzione che stampa il codice per visualizzare i blocchi dei servizi
* @return
*/
	function Crea_blocchi($Servizi){?>
		<div class="container">
	<div class="row">
<?php 
		foreach($Servizi as $Servizio){
			$Servizio_Image=get_the_post_thumbnail_url($Servizio->ID);
			$Servizio_Link=get_permalink($Servizio->ID);?>
		<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 pb-2">
		    <div class="hovereffect">
		        <img class="img-responsive" src="<?php echo $Servizio_Image;?>" alt="logo servizo <?php $Servizio->post_title;?>" width="300" height="200">
		            <div class="overlay">
		                <h2><?php echo $Servizio->post_title;?></h2>
						<p>
							<a href="<?php echo $Servizio_Link;?>" class="text-white">Vai al Servizio</a>
						</p>
		            </div>
		    </div>
		</div>
		<div class="clearfix visible-sm-block"></div>
<?php } ?>		
	</div>
  </div>
<?php
	}
	
/** @see WP_Widget::update */
	function update($new_instance, $old_instance){
            $instance = $old_instance;
            $instance['titolo'] = strip_tags($new_instance['titolo']);
            $instance['tipovis'] = $new_instance['tipovis'];
            $instance['tipologias'] = $new_instance['tipologias'];
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance){
		$instance = wp_parse_args( (array) $instance, array( ) ); 
		$titolo = ! empty( $instance['titolo'] ) ? $instance['titolo'] : esc_html__( 'Servizi', 'wpscuola' );
		$tipovis = ! empty( $instance['tipovis'] ) ? $instance['tipovis'] :0;
		$tipologias = ! empty( $instance['tipologias'] ) ? $instance['tipologias'] :0;
		?>
           <p>
                <label for="<?php echo $this->get_field_id( 'titolo' ); ?>">Titolo Sezione:</label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'titolo' ); ?>" name="<?php echo $this->get_field_name( 'titolo' ); ?>" value="<?php echo esc_attr( $titolo ); ?>" />
            </p>
            <p>
  	    		<label for="<?php echo $this->get_field_id( 'tipovis' ); ?>">Tipologia di visualizzazione:</label><br />
  	    		<select name="<?php echo $this->get_field_name( 'tipovis' ); ?>" id="<?php echo $this->get_field_id( 'tipovis' ); ?>" class="">
					<option value="0" <?php echo ($tipovis=="0" ?'selected="selected"':"");?>>Tutti servizi</option>
					<option value="1" <?php echo ($tipovis=="1" ?'selected="selected"':"");?>>Per tipologia di Servizio</option>
					<option value="2" <?php echo ($tipovis=="2" ?'selected="selected"':"");?>>Singola tipologia di Servizio</option>
				</select>
        	</p>
        	<p>
				<label for="<?php echo $this->get_field_id( 'tipologias' ); ?>">Tipologia di servizio:</label><br />
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
        	<em>Questo valore viene utilizzato solo per</em> <strong>Tipologia di visualizzazione</strong>=<em>Singola tipologia di Servizio</em>
        	</p>
<?php
	}
}