<?php
/**
* Widget Link
*	   Titolo:	($title) Testo che viene visualizzato in Testa al blocco
*/
class Link extends WP_Widget {
	var $defaults = array();
		
    /** constructor */
       function __construct() {
			parent::__construct( false, 'Scuola Link',
				array('classname' => 'Link',
				    'description' => __('Blocco Link organizzato con tabs per categoria di Link/Tutti i link/Singolo link','wpscuola')));
         	add_action( 'save_post', 	[$this, 'flush_widget_cache'] );
	      	add_action( 'deleted_post', [$this, 'flush_widget_cache'] );
	      	add_action( 'switch_theme', [$this, 'flush_widget_cache'] );
       }

    function widget($args, $instance) {
 	       $cache = [];
	        if ( ! $this->is_preview() ) {
	            $cache = wp_cache_get( 'widget_link', 'widget' );
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
	        
	         $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Link','wpscuola' );
        	/** This filter is documented in wp-includes/default-widgets.php */
        	 $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
             $tipovis = ! empty( $instance['tipovis'] ) ? $instance['tipovis'] :0;
             $categoria = ! empty( $instance['categoria'] ) ? $instance['categoria'] :0;
             $fulw    = isset( $instance['fulwidth'] ) ? $instance['fulwidth'] : false;
             $nsLg= ! empty($instance["nsLg"]) ? $instance["nsLg"]: 4;
             $nsLg=($nsLg==3)?4:3;
             ?>
 <section id="art_<?php echo $args['widget_id'];?>"  class="home-widget container<?php echo ($fulw?"-fluid pl-3":"");?>" >
		<div class="it-header-block">
	    	<div class="it-header-block-title">
<?php 
            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
            switch ($tipovis){
            	case 0:
            		$Links=get_bookmarks(array('orderby'=>'rating'));
 					$this->Crea_blocchi($Links,$fulw,$nsLg);
					break;
				case 1:
					$CatsLink = get_categories(array('taxonomy' => 'link_category',
								'hide_empty' 	=> false,
								'order' 		=> 'ASC',
           						'meta_key'		=> 'ordine_link',
  								'orderby' 		=> 'meta_value_num'));
//					echo "<pre>";var_dump($Tipologie);echo "</pre>";
					if (count($CatsLink)>0){?>
						<nav>
						  <div class="nav nav-tabs auto" id="nav_<?php echo $args['widget_id'];?>" role="tablist">
<?php					$Primo=true;
						foreach($CatsLink as $CatLink){?>
						    <a class="nav-item nav-link <?php echo ($Primo?"active":'');$Primo=false;?> nav-item-ufficio" id="nav-<?php echo $CatLink->term_id;?>-tab" data-toggle="tab" href="#<?php echo $CatLink->slug;?>" role="tab" aria-controls="<?php echo $CatLink->slug;?>" aria-selected="<?php echo ($Primo?"true":'false');$Primo=false;?>" ><?php echo $CatLink->name;?></a>
						<?php }?>    
						  </div>
						</nav>	
						<div class="tab-content" id="nav-tabContent_<?php echo $args['widget_id'];?>">		
<?php					$Primo=true;
						foreach($CatsLink as $CatLink){
							$Links=get_bookmarks(array('orderby'=>'rating','category'=>$CatLink->term_id));?>
							<div class="tab-pane p-4 fade show <?php echo ($Primo?"active":'');$Primo=false;?> shadow" id="<?php echo $CatLink->slug;?>" role="tabpanel" aria-labelledby="<?php echo $CatLink->slug;?>-tab">
							  	<div class="">
							  		<?php $this->Crea_blocchi($Links, $fulw,$nsLg);?>
							  	</div>
	        				</div>
<?php					}?>
						</div>
<?php				}				
					break;
	           	case 2:
						$Links=get_bookmarks(array('orderby'=>'rating','category'=>$categoria));
						$this->Crea_blocchi($Links, $fulw,$nsLg);
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
	function Crea_blocchi($Links, $fulw,$nsLg){?>
		<div class="container<?php echo ($fulw?"-fluid":"");?>">
	<div class="row">
<?php 
		foreach($Links as $Link){?>
		<div class="col-lg-<?php echo $nsLg;?> col-md-6 col-sm-12 col-xs-12 pb-2">
		    <div class="hovereffect_Link">
		        <img class="img-responsive" src="<?php echo $Link->link_image;?>" alt="logo link <?php echo $Link->link_name;?>" width="100%" height="<?php echo ($fulw?"350":"200");?>">
		            <div class="overlay">
		                <h4 class="text-white"><?php echo $Link->link_name;?></h4>
		                <p class="text-white"><?php echo $Link->link_description;?></p>
						<p>
							<a href="<?php echo $Link->link_url;?>" target="<?php echo $Link->link_target;?>"  class="text-white">Vai al link</a>
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
	        wp_cache_delete('widget_link', 'widget');
	}	

	function update($new_instance, $old_instance){
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['tipovis'] = $new_instance['tipovis'];
            $instance["categoria"]= $new_instance["categoria"];
            $instance["fulwidth"]= $new_instance["fulwidth"];
            $instance["nsLg"]= $new_instance["nsLg"];
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance){
		$instance = wp_parse_args( (array) $instance, array( ) ); 
		$titolo = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Link', 'wpscuola' );
		$tipovis = ! empty( $instance['tipovis'] ) ? $instance['tipovis'] : 0;
		$categoria= ! empty($instance["categoria"]) ? $instance["categoria"]: 0;
		$fulw    = isset( $instance['fulwidth'] ) ? $instance['fulwidth'] : false;
		$nsLg= ! empty($instance["nsLg"]) ? $instance["nsLg"]: 4;
		$args=array('taxonomy' => 'link_category','hide_empty' => false);
		$CatsLink = get_terms($args);
		$Elenco="<select id=\"".$this->get_field_id( 'categoria' )."\" name=\"".$this->get_field_name( 'categoria' )."\">
				<option value=\"\" >--------</option>";
		foreach ( $CatsLink as $CatLink ) {
			$Elenco.= "<option value=\"".$CatLink->term_id."\"";
			if ($CatLink->term_id==$categoria) {
				$Elenco.= " selected ";
			}
			$Elenco.= " >".$CatLink->name."</option>";
		}
		$Elenco.="</select>";
		?>
           <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Titolo Sezione', 'wpscuola' );?>:</label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $titolo ); ?>" />
            </p>
            <p>
  	    		<label for="<?php echo $this->get_field_id( 'tipovis' ); ?>"><?php _e( 'Tipologia di visualizzazione', 'wpscuola' );?>:</label><br />
  	    		<select name="<?php echo $this->get_field_name( 'tipovis' ); ?>" id="<?php echo $this->get_field_id( 'tipovis' ); ?>" class="">
					<option value="0" <?php echo ($tipovis=="0" ?'selected="selected"':"");?>><?php _e( 'Tutti i Link', 'wpscuola' );?></option>
					<option value="1" <?php echo ($tipovis=="1" ?'selected="selected"':"");?>><?php _e( 'Per categoria di Link', 'wpscuola' );?></option>
					<option value="2" <?php echo ($tipovis=="2" ?'selected="selected"':"");?>><?php _e( 'Singola categoria di Link', 'wpscuola' );?></option>
				</select>
        	</p>
        	<p>
				<label for="<?php echo $this->get_field_id( 'categoria' ); ?>"><?php _e( 'Categoria Link', 'wpscuola' );?>:</label><br />
<?php 			echo $Elenco;?>
        	<br />
        	<em><?php _e( 'Questo valore viene utilizzato solo per</em> <strong>Tipologia di visualizzazione</strong>=<em>Singola categoria di Link', 'wpscuola' );?></em>
        	</p>
        	<p>
	            <?php $checked = ( $fulw ) ? ' checked=\"checked\" ' : ''; ?>
	            <input type="checkbox" id="<?php echo $this->get_field_id( 'fulwidth' ); ?>" name="<?php echo $this->get_field_name( 'fulwidth' ); ?>" value="true" <?php echo $checked; ?> />    
	            <label for="<?php echo $this->get_field_id('fulwidth'); ?>"><?php _e( 'Visualizza a pieno schermo' ); ?></label>
        	</p>
            <p>
                <label for="<?php echo $this->get_field_id( 'nsLg' ); ?>"><?php echo __( 'Numero schede su schermi Large', 'wpscuola' );?>:</label>
                <input type="number" class="widefat" id="<?php echo $this->get_field_id( 'nsLg' ); ?>" name="<?php echo $this->get_field_name( 'nsLg' ); ?>" value="<?php echo $nsLg; ?>" min="3" max="4"/>
             </p>
<?php
	}
}