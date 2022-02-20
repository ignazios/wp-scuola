<?php
/****************************************************************************
Widget Amministrazione Trasparente
Titolo:	($title) Testo che viene visualizzato in Testa al blocco del widget
Logic:	($logic) Indica se visualizzare solo nella pagina indicata nelle impostazioni, pagina archivio e singola dei documenti

*/

class Bacheca extends WP_Widget {

    function __construct() {
		parent::__construct( false, 'Scuola Bacheca',
			array('classname' => 'Bacheca',
			    'description' => __('Visualizzazione di max 4 blocchi in in linea con icone che riportano a link specifici','wpscuola')));
     	add_action( 'save_post', 	[$this, 'flush_widget_cache'] );
      	add_action( 'deleted_post', [$this, 'flush_widget_cache'] );
      	add_action( 'switch_theme', [$this, 'flush_widget_cache'] );
    }
    function widget( $args, $instance ) {
       	$cache = [];
        if ( ! $this->is_preview() ) {
            $cache = wp_cache_get( 'widget_bacheca', 'widget' );
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
        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : "";
        $classi= ( ! empty( $instance['classi'] ) ) ? $instance['classi'] : "";
        $tipovis=(isset($instance['TipoVis'])?$instance['TipoVis']:"-1");

  		$Icona1=(isset($instance['Icona1'])?$instance['Icona1']:"");
 		$Link1=(isset($instance['Link1'])?$instance['Link1']:"");
 		$Colore1=(isset($instance['Colore1'])?$instance['Colore1']:"-1");
 		$Testo1=(isset($instance['Testo1'])?$instance['Testo1']:"");
 		$TestoVai1=(isset($instance['TestoVai1'])?$instance['TestoVai1']:"");
 		
  		$Icona2=(isset($instance['Icona2'])?$instance['Icona2']:"");
 		$Link2=(isset($instance['Link2'])?$instance['Link2']:"");
 		$Colore2=(isset($instance['Colore2'])?$instance['Colore2']:"-1");
 		$Testo2=(isset($instance['Testo2'])?$instance['Testo2']:"");
 		$TestoVai2=(isset($instance['TestoVai2'])?$instance['TestoVai2']:"");

  		$Icona3=(isset($instance['Icona3'])?$instance['Icona3']:"");
 		$Link3=(isset($instance['Link3'])?$instance['Link3']:"");
 		$Colore3=(isset($instance['Colore3'])?$instance['Colore3']:"-1");
 		$Testo3=(isset($instance['Testo3'])?$instance['Testo3']:"");
 		$TestoVai3=(isset($instance['TestoVai3'])?$instance['TestoVai3']:"");

  		$Icona4=(isset($instance['Icona4'])?$instance['Icona4']:"");
 		$Link4=(isset($instance['Link4'])?$instance['Link4']:"");
 		$Colore4=(isset($instance['Colore4'])?$instance['Colore4']:"-1");
 		$Testo4=(isset($instance['Testo4'])?$instance['Testo4']:"");
 		$TestoVai4=(isset($instance['TestoVai4'])?$instance['TestoVai4']:"");
 		
 		$Bottoni[0]="bachecaButton_primary";
 		$Bottoni[1]="bachecaButton_secondary";
 		$Bottoni[2]="bachecaButton_success";
 		$Bottoni[3]="bachecaButton_danger";
 		$Bottoni[4]="bachecaButton_warning";
 		
 		$Colori[0]="Blue";
 		$Colori[1]="Grigio";
 		$Colori[2]="Verde";
 		$Colori[3]="Rosso";
 		$Colori[4]="Marrone";
 		
 		$Testo[0]="bachecaText_primary";
 		$Testo[1]="bachecaText_secondary";
 		$Testo[2]="bachecaText_success";
 		$Testo[3]="bachecaText_danger";
 		$Testo[4]="bachecaText_warning"; 		
?>
<section id="bacheca_<?php echo $args['widget_id'];?>"  class="home-widget container <?php echo $classi;?>" >
	<div class="it-header-block">
		<div class="it-header-block-title">
<?php 
            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }?>
        </div>       
<?php switch($tipovis){
		case 1:?>
	<div class="container">
		<div class="row">   	
	<?php if($Link1!=""):?>
	      <div class="col-lg-3 col-md-6 col-sm-12 pl-1 pr-1"> 
	          <a class="mycard-rotate" href="<?php echo $Link1;?>">
	            <div class="mycard mycard-domande-<?php echo $Colori[$Colore1];?>">
	              <div class="clearfix"> 
	              	<?php 
	              		if(filter_var($Icona1, FILTER_VALIDATE_URL)){?>
	              			<img src="<?php echo $Icona1;?>" alt="" aria-hidden="true" class="img-fluid">
	              <?php	}else{?>
	              			<p class="myicona-<?php echo $Colori[$Colore1];?>"><?php echo $Icona1;?></p>
				  <?php	}?>
	                <h3><?php echo $Testo1;?></h3>
	              </div>
	              <div class="mycard-direction mycard-direction-<?php echo $Colori[$Colore1];?>">
	                <?php echo $TestoVai1;?>
	              </div>
	            </div>
	          </a> 
	        </div>
	<?php endif;
		 if($Link2!=""):?>
	      <div class="col-lg-3 col-md-6 col-sm-12 pl-1 pr-1"> 
	          <a class="mycard-rotate" href="<?php echo $Link2;?>">
	            <div class="mycard mycard-domande-<?php echo $Colori[$Colore2];?>">
	              <div class="clearfix"> 
	              	<?php 
	              		if(filter_var($Icona2, FILTER_VALIDATE_URL)){?>
	              			<img src="<?php echo $Icona2;?>" alt="" aria-hidden="true" class="img-fluid">
	              <?php	}else{?>
	              			<p class="myicona-<?php echo $Colori[$Colore2];?>"><?php echo $Icona2;?></p>
				  <?php	}?>
	                <h3><?php echo $Testo2;?></h3>
	              </div>
	              <div class="mycard-direction mycard-direction-<?php echo $Colori[$Colore2];?>">
	                 <?php echo $TestoVai2;?>
	              </div>
	            </div>
	          </a> 
	        </div>
	<?php endif;
		 if($Link3!=""):?>
	      <div class="col-lg-3 col-md-6 col-sm-12 pl-1 pr-1"> 
	          <a class="mycard-rotate" href="<?php echo $Link3;?>">
	            <div class="mycard mycard-domande-<?php echo $Colori[$Colore3];?>">
	              <div class="clearfix"> 
	              	<?php 
	              		if(filter_var($Icona3, FILTER_VALIDATE_URL)){?>
	              			<img src="<?php echo $Icona3;?>" alt="" aria-hidden="true" class="img-fluid">
	              <?php	}else{?>
	              			<p class="myicona-<?php echo $Colori[$Colore3];?>"><?php echo $Icona3;?></p>
				  <?php	}?>
	                <h3><?php echo $Testo3;?></h3>
	              </div>
	              <div class="mycard-direction mycard-direction-<?php echo $Colori[$Colore3];?>">
	                 <?php echo $TestoVai3;?>
	              </div>
	            </div>
	          </a> 
	        </div>
	<?php endif;
		 if($Link4!=""):?>
	      <div class="col-lg-3 col-md-6 col-sm-12 pl-1 pr-1"> 
	          <a class="mycard-rotate" href="<?php echo $Link4;?>">
	            <div class="mycard mycard-domande-<?php echo $Colori[$Colore4];?>">
	              <div class="clearfix"> 
	              	<?php 
	              		if(filter_var($Icona4, FILTER_VALIDATE_URL)){?>
	              			<img src="<?php echo $Icona4;?>" alt="" aria-hidden="true" class="img-fluid">
	              <?php	}else{?>
	              			<p class="myicona-<?php echo $Colori[$Colore4];?>"><?php echo $Icona4;?></p>
				  <?php	}?>
	                <h3><?php echo $Testo4;?></h3>
	              </div>
	              <div class="mycard-direction mycard-direction-<?php echo $Colori[$Colore4];?>">
	                 <?php echo $TestoVai4;?>
	              </div>
	            </div>
	          </a> 
	        </div>
	<?php endif;?>  
	    </div>
	</div>
<?php 		break;
		case 0:
		case -1:?>
		<div class="container">
		  	<div class="row">
	<?php if($Link1!=""):?>
		    	<div class="col-lg-3 col-sm-6 col-12 pb-3">
		    		<div class="d-flex justify-content-center">
		    			<a href="<?php echo $Link1;?>" class="<?php echo $Bottoni[$Colore1];?>" title="<?php echo $Testo1;?>"><?php echo $Icona1;?></a>
		    		</div>
		    		<div class="text-center <?php echo $Testo[$Colore1];?>">
		    			<?php echo $Testo1;?>
		    		</div>
		    	</div>
	<?php endif;
		  if($Link2!=""):?>
		    	<div class="col-lg-3 col-sm-6 col-12 pb-3">
		    		<div class="d-flex justify-content-center">
		    			<a href="<?php echo $Link2;?>" class="<?php echo $Bottoni[$Colore2];?>" title="<?php echo $Testo2;?>"><?php echo $Icona2;?></a>
		    		</div>
		    		<div class="text-center <?php echo $Testo[$Colore2];?>">
		    			<?php echo $Testo2;?>
		    		</div>
		    	</div>
	<?php endif;
	      if($Link3!=""):?>
		    	<div class="col-lg-3 col-sm-6 col-12 pb-3">
		    		<div class="d-flex justify-content-center">
		    			<a href="<?php echo $Link3;?>" class="<?php echo $Bottoni[$Colore3];?>" title="<?php echo $Testo3;?>"><?php echo $Icona3;?></a>
		    		</div>
		    		<div class="text-center <?php echo $Testo[$Colore3];?>">
		    			<?php echo $Testo3;?>
		    		</div>
		    	</div>
	<?php endif;
	      if($Link4!=""):?>
		    	<div class="col-lg-3 col-sm-6 col-12 pb-3">
		    		<div class="d-flex justify-content-center">
		    			<a href="<?php echo $Link4;?>" class="<?php echo $Bottoni[$Colore4];?>" title="<?php echo $Testo4;?>"><?php echo $Icona4;?></a>
		    		</div>
		    		<div class="text-center <?php echo $Testo[$Colore4];?>">
		    			<?php echo $Testo4;?>
		    		</div>
		    	</div>
	<?php endif;?>
			</div>
		</div>
<?php
		break;
	}?>
	</div>
</section>
<?php
	wp_reset_postdata();

    if ( ! $this->is_preview() ) {
        $cache[ $args['widget_id'] ] = ob_get_flush();
        wp_cache_set( 'widget_bacheca', $cache, 'widget' );
    } else {
        ob_end_flush();
    }
}
	public function flush_widget_cache(){
	        wp_cache_delete('widget_bacheca', 'widget');
	}	

    function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
        $instance['title']   = strip_tags($new_instance['title']);
        $instance['classi']  = strip_tags($new_instance['classi']);
        $instance['TipoVis'] = $new_instance['TipoVis'];

        $instance['Icona1']  = $new_instance['Icona1'];
 		$instance['Link1']   =$new_instance['Link1'];
 		$instance['Colore1'] =$new_instance['Colore1'];
 		$instance['Testo1']  =$new_instance['Testo1'];
 		$instance['TestoVai1']  =$new_instance['TestoVai1'];

        $instance['Icona2']  = $new_instance['Icona2'];
 		$instance['Link2']   =$new_instance['Link2'];
 		$instance['Colore2'] =$new_instance['Colore2'];
 		$instance['Testo2']  =$new_instance['Testo2'];
 		$instance['TestoVai2']  =$new_instance['TestoVai2'];

        $instance['Icona3']  = $new_instance['Icona3'];
 		$instance['Link3']   =$new_instance['Link3'];
 		$instance['Colore3'] =$new_instance['Colore3'];
 		$instance['Testo3']  =$new_instance['Testo3'];
 		$instance['TestoVai3']  =$new_instance['TestoVai3'];

        $instance['Icona4']  = $new_instance['Icona4'];
 		$instance['Link4']   =$new_instance['Link4'];
 		$instance['Colore4'] =$new_instance['Colore4'];
 		$instance['Testo4']  =$new_instance['Testo4'];
 		$instance['TestoVai4']  =$new_instance['TestoVai4'];

        return $instance;
    }

	 private static function get_defaults() {
        $defaults = array(
            'title' => 'Bacheca'
        );
        return $defaults;
    }

    function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( ) ); 
        
        $title = esc_attr($instance['title']); 
        $classi= esc_attr($instance['classi']); 
        $tipovis=(isset($instance['TipoVis'])?$instance['TipoVis']:"-1");
 
  		$Icona1=(isset($instance['Icona1'])?$instance['Icona1']:"");
 		$Link1=(isset($instance['Link1'])?$instance['Link1']:"");
 		$Colore1=(isset($instance['Colore1'])?$instance['Colore1']:"-1");
 		$Testo1=(isset($instance['Testo1'])?$instance['Testo1']:"");
 		$TestoVai1=(isset($instance['TestoVai1'])?$instance['TestoVai1']:"");
 		
  		$Icona2=(isset($instance['Icona2'])?$instance['Icona2']:"");
 		$Link2=(isset($instance['Link2'])?$instance['Link2']:"");
 		$Colore2=(isset($instance['Colore2'])?$instance['Colore2']:"-1");
 		$Testo2=(isset($instance['Testo2'])?$instance['Testo2']:"");
 		$TestoVai2=(isset($instance['TestoVai2'])?$instance['TestoVai2']:"");

  		$Icona3=(isset($instance['Icona3'])?$instance['Icona3']:"");
 		$Link3=(isset($instance['Link3'])?$instance['Link3']:"");
 		$Colore3=(isset($instance['Colore3'])?$instance['Colore3']:"-1");
 		$Testo3=(isset($instance['Testo3'])?$instance['Testo3']:"");
 		$TestoVai3=(isset($instance['TestoVai3'])?$instance['TestoVai3']:"");

  		$Icona4=(isset($instance['Icona4'])?$instance['Icona4']:"");
 		$Link4=(isset($instance['Link4'])?$instance['Link4']:"");
 		$Colore4=(isset($instance['Colore4'])?$instance['Colore4']:"-1");
 		$Testo4=(isset($instance['Testo4'])?$instance['Testo4']:"");
  		$TestoVai4=(isset($instance['TestoVai4'])?$instance['TestoVai4']:"");
		?>
        <p><label for="<?php echo $this->get_field_id('title');?>">
        Titolo: <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo $title; ?>" />
        </label></p>
        <p><label for="<?php echo $this->get_field_id('classi');?>">
        Classi da aggiungere al blocco: <input class="widefat" id="<?php echo $this->get_field_id('classi');?>" name="<?php echo $this->get_field_name('classi');?>" type="text" value="<?php echo $classi; ?>" />
        </label></p>
        <p>
  	    	<label for="<?php echo $this->get_field_id( 'TipoVis' ); ?>"><?php _e( 'Tipo Visualizzazione', 'wpscuola' );?>:</label><br />
  	    	<select name="<?php echo $this->get_field_name( 'TipoVis' ); ?>" id="<?php echo $this->get_field_id( 'TipoVis' ); ?>" class="">
				<option value="0" <?php echo ($tipovis=="0" ?'selected="selected"':"");?>><?php _e( 'Piatto', 'wpscuola' );?></option>
				<option value="1" <?php echo ($tipovis=="1" ?'selected="selected"':"");?>><?php _e( 'Card', 'wpscuola' );?></option>
			</select>
        </p>        
        <div><h3><?php _e('Primo blocco','wpscuola');?></h3>
        <p>
			<label for="<?php echo $this->get_field_id( 'Icona1' );?>"><?php _e('Icona','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Icona1' ); ?>" name="<?php echo $this->get_field_name( 'Icona1' ); ?>" value="<?php echo esc_attr( $Icona1); ?>" />
       		<em>Inserire un'icona da https://fontawesome.com/ con tag &lt;span&gt; o un'immagine (110x90) indicando l'URL</em>
        </p>        
        <p>
  	    	<label for="<?php echo $this->get_field_id( 'Colore1' ); ?>"><?php _e( 'Colore', 'wpscuola' );?>:</label><br />
  	    	<select name="<?php echo $this->get_field_name( 'Colore1' ); ?>" id="<?php echo $this->get_field_id( 'Colore1' ); ?>" class="">
				<option value="0" <?php echo ($Colore1=="0" ?'selected="selected"':"");?>><?php _e( 'Blue', 'wpscuola' );?></option>
				<option value="1" <?php echo ($Colore1=="1" ?'selected="selected"':"");?>><?php _e( 'Grigio', 'wpscuola' );?></option>
				<option value="2" <?php echo ($Colore1=="2" ?'selected="selected"':"");?>><?php _e( 'Verde', 'wpscuola' );?></option>
				<option value="3" <?php echo ($Colore1=="3" ?'selected="selected"':"");?>><?php _e( 'Rosso', 'wpscuola' );?></option>
				<option value="4" <?php echo ($Colore1=="4" ?'selected="selected"':"");?>><?php _e( 'Marrone', 'wpscuola' );?></option>
			</select>
        </p>        
        <p>
			<label for="<?php echo $this->get_field_id( 'Link1' );?>"><?php _e('Link','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Link1' ); ?>" name="<?php echo $this->get_field_name( 'Link1' ); ?>" value="<?php echo esc_attr( $Link1); ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'Testo1' );?>"><?php _e('Testo','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Testo1' ); ?>" name="<?php echo $this->get_field_name( 'Testo1' ); ?>" value="<?php echo esc_attr( $Testo1); ?>" />
        </p>        
         <p>
			<label for="<?php echo $this->get_field_id( 'TestoVai1' );?>"><?php _e('Testo Vai a ....','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'TestoVai1' ); ?>" name="<?php echo $this->get_field_name( 'TestoVai1' ); ?>" value="<?php echo esc_attr( $TestoVai1); ?>" />
        </p>  
	</div>
	<div><h3><?php _e('Secondo blocco','wpscuola');?></h3>
        <p>
			<label for="<?php echo $this->get_field_id( 'Icona2' );?>"><?php _e('Icona','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Icona2' ); ?>" name="<?php echo $this->get_field_name( 'Icona2' ); ?>" value="<?php echo esc_attr( $Icona2); ?>" />
        </p>        
        <p>
  	    	<label for="<?php echo $this->get_field_id( 'Colore2' ); ?>"><?php _e( 'Colore', 'wpscuola' );?>:</label><br />
  	    	<select name="<?php echo $this->get_field_name( 'Colore2' ); ?>" id="<?php echo $this->get_field_id( 'Colore2' ); ?>" class="">
				<option value="0" <?php echo ($Colore2=="0" ?'selected="selected"':"");?>><?php _e( 'Blue', 'wpscuola' );?></option>
				<option value="1" <?php echo ($Colore2=="1" ?'selected="selected"':"");?>><?php _e( 'Grigio', 'wpscuola' );?></option>
				<option value="2" <?php echo ($Colore2=="2" ?'selected="selected"':"");?>><?php _e( 'Verde', 'wpscuola' );?></option>
				<option value="3" <?php echo ($Colore2=="3" ?'selected="selected"':"");?>><?php _e( 'Rosso', 'wpscuola' );?></option>
				<option value="4" <?php echo ($Colore2=="4" ?'selected="selected"':"");?>><?php _e( 'Marrone', 'wpscuola' );?></option>
			</select>
        </p>        
        <p>
			<label for="<?php echo $this->get_field_id( 'Link2' );?>"><?php _e('Link','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Link2' ); ?>" name="<?php echo $this->get_field_name( 'Link2' ); ?>" value="<?php echo esc_attr( $Link2); ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'Testo2' );?>"><?php _e('Testo','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Testo2' ); ?>" name="<?php echo $this->get_field_name( 'Testo2' ); ?>" value="<?php echo esc_attr( $Testo2); ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'TestoVai2' );?>"><?php _e('Testo Vai a ....','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'TestoVai2' ); ?>" name="<?php echo $this->get_field_name( 'TestoVai2' ); ?>" value="<?php echo esc_attr( $TestoVai2); ?>" />
        </p>        
	</div>
	<div><h3><?php _e('Terzo blocco','wpscuola');?></h3>
        <p>
			<label for="<?php echo $this->get_field_id( 'Icona3' );?>"><?php _e('Icona','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Icona3' ); ?>" name="<?php echo $this->get_field_name( 'Icona3' ); ?>" value="<?php echo esc_attr( $Icona3); ?>" />
        </p>        
        <p>
  	    	<label for="<?php echo $this->get_field_id( 'Colore3' ); ?>"><?php _e( 'Colore', 'wpscuola' );?>:</label><br />
  	    	<select name="<?php echo $this->get_field_name( 'Colore3' ); ?>" id="<?php echo $this->get_field_id( 'Colore3' ); ?>" class="">
				<option value="0" <?php echo ($Colore3=="0" ?'selected="selected"':"");?>><?php _e( 'Blue', 'wpscuola' );?></option>
				<option value="1" <?php echo ($Colore3=="1" ?'selected="selected"':"");?>><?php _e( 'Grigio', 'wpscuola' );?></option>
				<option value="2" <?php echo ($Colore3=="2" ?'selected="selected"':"");?>><?php _e( 'Verde', 'wpscuola' );?></option>
				<option value="3" <?php echo ($Colore3=="3" ?'selected="selected"':"");?>><?php _e( 'Rosso', 'wpscuola' );?></option>
				<option value="4" <?php echo ($Colore3=="4" ?'selected="selected"':"");?>><?php _e( 'Marrone', 'wpscuola' );?></option>
			</select>
        </p>        
        <p>
			<label for="<?php echo $this->get_field_id( 'Link3' );?>"><?php _e('Link','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Link3' ); ?>" name="<?php echo $this->get_field_name( 'Link3' ); ?>" value="<?php echo esc_attr( $Link3); ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'Testo3' );?>"><?php _e('Testo','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Testo3' ); ?>" name="<?php echo $this->get_field_name( 'Testo3' ); ?>" value="<?php echo esc_attr( $Testo3); ?>" />
        </p>        
        <p>
			<label for="<?php echo $this->get_field_id( 'TestoVai3' );?>"><?php _e('Testo Vai a ....','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'TestoVai3' ); ?>" name="<?php echo $this->get_field_name( 'TestoVai3' ); ?>" value="<?php echo esc_attr( $TestoVai3); ?>" />
        </p>  
	</div>
	
	<div><h3><?php _e('Quarto blocco','wpscuola');?></h3>
        <p>
			<label for="<?php echo $this->get_field_id( 'Icona4' );?>"><?php _e('Icona','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Icona4' ); ?>" name="<?php echo $this->get_field_name( 'Icona4' ); ?>" value="<?php echo esc_attr( $Icona4); ?>" />
        </p>        
        <p>
  	    	<label for="<?php echo $this->get_field_id( 'Colore4' ); ?>"><?php _e( 'Colore', 'wpscuola' );?>:</label><br />
  	    	<select name="<?php echo $this->get_field_name( 'Colore4' ); ?>" id="<?php echo $this->get_field_id( 'Colore4' ); ?>" class="">
				<option value="0" <?php echo ($Colore4=="0" ?'selected="selected"':"");?>><?php _e( 'Blue', 'wpscuola' );?></option>
				<option value="1" <?php echo ($Colore4=="1" ?'selected="selected"':"");?>><?php _e( 'Grigio', 'wpscuola' );?></option>
				<option value="2" <?php echo ($Colore4=="2" ?'selected="selected"':"");?>><?php _e( 'Verde', 'wpscuola' );?></option>
				<option value="3" <?php echo ($Colore4=="3" ?'selected="selected"':"");?>><?php _e( 'Rosso', 'wpscuola' );?></option>
				<option value="4" <?php echo ($Colore4=="4" ?'selected="selected"':"");?>><?php _e( 'Marrone', 'wpscuola' );?></option>
			</select>
        </p>        
        <p>
			<label for="<?php echo $this->get_field_id( 'Link4' );?>"><?php _e('Link','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Link4' ); ?>" name="<?php echo $this->get_field_name( 'Link4' ); ?>" value="<?php echo esc_attr( $Link4); ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'Testo4' );?>"><?php _e('Testo','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'Testo4' ); ?>" name="<?php echo $this->get_field_name( 'Testo4' ); ?>" value="<?php echo esc_attr( $Testo4); ?>" />
        </p>        
        <p>
			<label for="<?php echo $this->get_field_id( 'TestoVai4' );?>"><?php _e('Testo Vai a ....','wpscuola');?>:</label><br />
       		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'TestoVai4' ); ?>" name="<?php echo $this->get_field_name( 'TestoVai4' ); ?>" value="<?php echo esc_attr( $TestoVai4); ?>" />
        </p>
	</div>	
<?php
    }
}



?>