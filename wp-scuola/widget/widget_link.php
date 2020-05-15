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
				    'description' => 'Blocco Link organizzato con tabs per categoria di Link/Tutti i link/Singolo link') );
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
             $categoria = ! empty( $instance['categoria'] ) ? $instance['categoria'] :0;?>
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
            		$Links=get_bookmarks(array('orderby'=>'rating'));
 					$this->Crea_blocchi($Links);
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
							  		<?php $this->Crea_blocchi($Links);?>
							  	</div>
	        				</div>
<?php					}?>
						</div>
<?php				}				
					break;
	           	case 2:
						$Links=get_bookmarks(array('orderby'=>'rating','category'=>$categoria));
						$this->Crea_blocchi($Links);
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
	function Crea_blocchi($Links){?>
		<div class="container">
	<div class="row">
<?php 
		foreach($Links as $Link){?>
		<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 pb-2">
		    <div class="hovereffect_Link">
		        <img class="img-responsive" src="<?php echo $Link->link_image;?>" alt="logo link <?php $Link->link_name;?>" width="100%" height="200">
		            <div class="overlay">
		                <h2><?php echo $Link->link_name;?></h2>
		                <p class="text-white"><?php echo $Link->link_description;?></p>
						<p>
							<a href="<?php echo $Link->link_url;?>" target="<?php echo $Link->link_target;?>"  class="text-white">Vai al link</a>
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
            $instance["categoria"]= $new_instance["categoria"];
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance){
		$instance = wp_parse_args( (array) $instance, array( ) ); 
		$titolo = ! empty( $instance['titolo'] ) ? $instance['titolo'] : esc_html__( 'Servizi', 'wpscuola' );
		$tipovis = ! empty( $instance['tipovis'] ) ? $instance['tipovis'] : 0;
		$categoria= ! empty($instance["categoria"]) ? $instance["categoria"]: 0;
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
                <label for="<?php echo $this->get_field_id( 'titolo' ); ?>"><?php echo __( 'Titolo Sezione', 'wpscuola' );?>:</label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'titolo' ); ?>" name="<?php echo $this->get_field_name( 'titolo' ); ?>" value="<?php echo esc_attr( $titolo ); ?>" />
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
<?php
	}
}