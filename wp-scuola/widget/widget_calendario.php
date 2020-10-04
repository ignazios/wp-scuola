<?php
/**
* Widget my_EM_Widget_Calendar
*	   Titolo:	($title) Testo che viene visualizzato in Testa all'elenco degli articoli estratti
*/
class my_EM_Widget_Calendar extends WP_Widget {
	var $defaults = array();
		
    /** constructor */
    function __construct() {
    	$this->defaults = array(	"title"		=> "Eventi",
							"long_events"	=> 0,
							"category"	=> "",
							"limit"		=> "5",
							"scope"		=> "12-months",
							"orderby"	=> "event_start_date,event_start_time,event_name",
							'format' 	=> '<li>#_EVENTLINK<ul><li>#_EVENTDATES</li><li>#_LOCATIONTOWN</li></ul></li>',
							"order"		=> "ASC",
							"all_events"=> "on",
							"all_events_text"=>"tutti gli eventi",
							'no_events_text' => '<li>'.__('No events', 'events-manager').'</li>',
							'vis_eventi'=>"on",
							'vis_localita'=>"on",
							'scope_loc' => 'future',
    						'order_loc' => 'ASC',
    						'limit_loc' => 5,
    						'format_loc' => '<li>#_LOCATIONLINK<ul><li>#_LOCATIONADDRESS</li><li>#_LOCATIONTOWN</li></ul></li>',
    	    				'no_locations_text' => '<li>'.__('No locations', 'events-manager').'</li>',
    						'orderby_loc' => 'event_start_date,event_start_time,location_name');
		$this->em_orderby_options = apply_filters('em_settings_events_default_orderby_ddm', 
					array(	'event_start_date,event_start_time,event_name' => __('start date, start time, event name','events-manager'),
							'event_name,event_start_date,event_start_time' => __('name, start date, start time','events-manager'),
							'event_name,event_end_date,event_end_time' => __('name, end date, end time','events-manager'),
							'event_end_date,event_end_time,event_name' => __('end date, end time, event name','events-manager'),));
    	$widget_ops = array('description' => "Widget per la visualizzazione del calendario EM Events." );
        parent::__construct(false, $name = __('Scuola Calendario EM Events','events-manager'), $widget_ops);	
    }

	function my_em_get_link( $text = '' ) {
		$text = ($text == '') ? get_option ( "dbem_events_page_title" ) : $text;
		$text = ($text == '') ? __('Events','events-manager') : $text; //In case options aren't there....
		return '<a href="'.esc_url(EM_URI).'" title="'.esc_attr($text).'"><span class="fas fa-link p-1"></span><span class="text">'.esc_html($text).'</span></a>';
	}

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    	$instance = array_merge($this->defaults, $instance);
    	$VisEventi=($instance['vis_eventi']==0?FALSE:TRUE);
		$VisLocalita=($instance['vis_localita']==0?FALSE:TRUE);?>
<section id="sec_<?php echo $args['widget_id'];?>"  class="home-widget container Eventi">
<?php 
     	if( !empty($instance['title']) ){
		    echo $args['before_title'];
		    echo apply_filters('widget_title',$instance['title'], $instance, $this->id_base);
		    echo $args['after_title'];
    	}
?>
	<div class="row d-flex justify-content-center" id="ContenitoreCalendario">
    	<div class="col-lg-4 col-12">
<?php 
    	//Shall we show a specific month?
		if ( !empty($_REQUEST['calendar_day']) ) {
			$date = explode('-', $_REQUEST['calendar_day']);
			$instance['month'] = $date[1];
			$instance['year'] = $date[0];
		}else{
			$instance['month'] = date("m");
			$instance['year'] = date('Y');
		}?>
			<div class="shadow"> 
<?php	    //Our Widget Content  
//var_dump($instance);wp_die();
		$CalInstance=array( "title"			=>  $instance["title"],			
							"long_events" 	=>	$instance["long_events"],
							"category" 		=>	$instance["category"],
							"month" 		=>	$instance["month"],
							"year" 			=>	$instance["year"]);
	    echo EM_Calendar::output(apply_filters('em_widget_calendar_get_args',$CalInstance));?>
			</div>
		</div>
		<div class="col-lg-8 col-12">
<?php
	if($VisEventi AND $VisLocalita){?>
	<nav>
		<div class="nav nav-tabs auto" id="nav-tab-calendar" role="tablist">
		<a class="nav-item nav-link active" id="Eventi-tab" data-toggle="tab" href="#EventiCartella" role="tab" aria-controls="Eventi" aria-selected="true"><span class="far fa-calendar-alt pr-3"></span>Eventi</a>
		<a class="nav-item nav-link"  id="Localita-tab" data-toggle="tab" href="#LocalitaCartella" role="tab" aria-controls="Localita" aria-selected="false"><span class="fas fa-map-marked pr-3"></span>Località</a>
		</div>
	</nav>	    
  	<div class="tab-content" id="EM-Events_EventiLocalita">
  		<div class="tab-pane fade show active primary-border-color" id="EventiCartella" role="tabpanel" aria-labelledby="Eventi">
<?php	}
	if($VisEventi){?>
		<div id="Eventi">
<?php		//orderby fix for previous versions with old orderby values
		if(!$VisLocalita) echo '<h4><span class="far fa-calendar-alt pr-3"></span> Eventi</h4>';
		if( !array_key_exists($instance['orderby'], $this->em_orderby_options) ){
			//replace old values
			$old_vals = array(
				'name' => 'event_name',
				'end_date' => 'event_end_date',
				'start_date' => 'event_start_date',
				'end_time' => 'event_end_time',
				'start_time' => 'event_start_time'
			);
			foreach($old_vals as $old_val => $new_val){
				$instance['orderby'] = str_replace($old_val, $new_val, $instance['orderby']);
			}
		}
		
		//get events
		$events = EM_Events::get(apply_filters('em_widget_events_get_args',$instance));?>
		
				<ul>
<?php	if ( count($events) > 0 ){
//			echo "<pre>";var_dump($events);echo "</pre>";
			foreach($events as $event){				
				echo $event->output( $instance['format'] );
			}
		}else{
		    echo $instance['no_events_text'];
		}?>
				</ul>
<?php	if ( !empty($instance['all_events']) ){
			$events_link = (!empty($instance['all_events_text'])) ? self::my_em_get_link($instance['all_events_text']) : self::my_em_get_link(__('all events','events-manager'));
			echo '<div class="clearfix"><p class="float-right">'.$events_link."</p></div>";
		}
?>				
			</div>
<?php   }
		if($VisEventi AND $VisLocalita){?>
		</div>
		<div class="tab-pane fade primary-border-color" id="LocalitaCartella" role="tabpanel" aria-labelledby="Localita" >
<?php	}
	if($VisLocalita){?>
			<div id="Localita">
<?php	    //make sure no owner searches are being run
		if(!$VisEventi) echo '<h4>span class="fas fa-map-marked pr-3"></span>Località</h4>';
		$loc_instance=array('scope' 	=> $instance["scope_loc"],
    							'order' 	=> $instance['order_loc'],
    							'limit' 	=> $instance['limit_loc'],
    							'format' 	=> $instance['format_loc'],
    	    					'no_locations_text' => $instance['no_locations_text'],
    							'orderby' 	=> $instance['orderby_loc']);
		$loc_instance['owner'] = false;
		//legacy sanitization
		if( !preg_match('/^<li/i', trim($instance['format_loc'])) ) $instance['format_loc'] = '<li>'. $instance['format_loc'] .'</li>';
		//get locations
		$locations = EM_Locations::get(apply_filters('em_widget_locations_get_args',$loc_instance));
		//output locations
		echo "<ul>";
		if ( count($locations) > 0 ){
			foreach($locations as $location){
				echo $location->output($instance['format_loc']);
			}
		}else{
		    echo $instance['no_locations_text'];
		}
		echo "</ul>";?>
			</div>
		</div>
<?php }
	if($VisEventi AND $VisLocalita){?>
		</div>
<?php }?>
	</div>
  </div>
</section>	
<?php
    }
/** @see WP_Widget::update */
	function update($new_instance, $old_instance){
		foreach ($this->defaults as $key => $value) {
			//		   		echo $key." - ".$value." - ";
			$new_instance[$key]=(!isset($new_instance[$key])) ? $this->defaults[$key]:$new_instance[$key];
			//	   			echo $new_instance[$key]."<br />";
		}
		//	    	wp_die();
		return $new_instance;
	}

	/** @see WP_Widget::form */
	function form($instance){
		$instance = array_merge($this->defaults, $instance);
//		var_dump($instance);
		?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'events-manager'); ?>: </label>
		<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('long_events'); ?>"><?php _e('Show Long Events?', 'events-manager'); ?>: </label>
		<input type="checkbox" id="<?php echo $this->get_field_id('long_events'); ?>" name="<?php echo $this->get_field_name('long_events'); ?>" value="1" <?php echo ($instance['long_events'] == '1') ? 'checked="checked"':''; ?>/>
	</p>
	<h2>Paramentri Lista Eventi</h2>
	<p>
		<label for="<?php echo $this->get_field_id('vis_eventi'); ?>">Visualizza gli Eventi: </label>
		<input type="checkbox" id="<?php echo $this->get_field_id('vis_eventi'); ?>" name="<?php echo $this->get_field_name('vis_eventi'); ?>" value="1" <?php echo ($instance['vis_eventi'] == '1') ? 'checked="checked"':''; ?>/>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('limit'); ?>"><?php esc_html_e('Number of events','events-manager'); ?>: </label>
		<input type="text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" size="3" value="<?php echo esc_attr($instance['limit']); ?>" />
	</p>
	<p>

		<label for="<?php echo $this->get_field_id('scope'); ?>"><?php esc_html_e('Scope','events-manager'); ?>: </label><br/>
		<select id="<?php echo $this->get_field_id('scope'); ?>" name="<?php echo $this->get_field_name('scope'); ?>" class="widefat" >
			<?php
			foreach ( em_get_scopes() as $key => $value)
				: ?>
			<option value='<?php echo esc_attr($key); ?>' <?php echo ($key == $instance['scope']) ? "selected='selected'" : ''; ?>>
				<?php echo esc_html($value); ?>
			</option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('order'); ?>"><?php esc_html_e('Order By','events-manager'); ?>: </label>
		<select  id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>" class="widefat">
			<?php
			foreach ($this->em_orderby_options as $key => $value)
				: ?>
			<option value='<?php echo esc_attr($key); ?>' <?php echo ( !empty($instance['orderby']) && $key == $instance['orderby']) ? "selected='selected'" : ''; ?>>
				<?php echo esc_html($value); ?>
			</option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('order'); ?>"><?php esc_html_e('Order','events-manager'); ?>: </label>
		<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>" class="widefat">
			<?php
			$order_options = apply_filters('em_widget_order_ddm', array(
			'ASC' => __('Ascending','events-manager'),
			'DESC' => __('Descending','events-manager')
			));
			?>
			<?php
			foreach ( $order_options as $key => $value)
				: ?>
			<option value='<?php echo esc_attr($key); ?>' <?php echo ($key == $instance['order']) ? "selected='selected'" : ''; ?>>
				<?php echo esc_html($value); ?>
			</option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('category'); ?>"><?php esc_html_e('Category IDs','events-manager'); ?>: </label>
		<input type="text" id="<?php echo $this->get_field_id('category'); ?>" class="widefat" name="<?php echo $this->get_field_name('category'); ?>" size="3" value="<?php echo esc_attr($instance['category']); ?>" /><br />
		<em><?php esc_html_e('1,2,3 or 2 (0 = all)','events-manager'); ?> </em>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('all_events'); ?>"><?php _e('Show all events link at bottom?', 'events-manager'); ?>: </label>
		<input type="checkbox" id="<?php echo $this->get_field_id('all_events'); ?>" name="<?php echo $this->get_field_name('all_events'); ?>" value="1" <?php echo ($instance['all_events'] == '1') ? 'checked="checked"':''; ?>/>
	</p>
	<p id="<?php echo $this->get_field_id('all_events'); ?>-section">
		<label for="<?php echo $this->get_field_id('all_events'); ?>"><?php esc_html_e('All events link text?','events-manager'); ?>: </label>
		<input type="text" id="<?php echo $this->get_field_id('all_events_text'); ?>" name="<?php echo $this->get_field_name('all_events_text'); ?>" value="<?php echo esc_attr( $instance['all_events_text'] ); ?>" >
	</p>
			<em><?php echo sprintf( esc_html__('The list is wrapped in a %s tag, so if an %s tag is not wrapping the formats below it will be added automatically.','events-manager'), '<code>&lt;ul&gt;</code>', '<code>&lt;li&gt;</code>'); ?></em>
        <p>
			<label for="<?php echo $this->get_field_id('format'); ?>"><?php esc_html_e('List item format','events-manager'); ?>: </label>
			<textarea rows="5" cols="24" id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>" class="widefat"><?php echo esc_textarea($instance['format']); ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('no_events_text'); ?>"><?php _e('No events message','events-manager'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('no_events_text'); ?>" name="<?php echo $this->get_field_name('no_events_text'); ?>" value="<?php echo esc_attr( $instance['no_events_text'] ); ?>" >
		</p>
	<h2>Paramentri Località</h2>
	<p>
		<label for="<?php echo $this->get_field_id('vis_localita'); ?>">Visualizza le Località: </label>
		<input type="checkbox" id="<?php echo $this->get_field_id('vis_localita'); ?>" name="<?php echo $this->get_field_name('vis_localita'); ?>" value="1" <?php echo ($instance['vis_localita'] == '1') ? 'checked="checked"':''; ?>/>
	</p>


		<p>
			<label for="<?php echo $this->get_field_id('limit_loc'); ?>"><?php esc_html_e('Show number of locations','events-manager'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('limit_loc'); ?>" name="<?php echo $this->get_field_name('limit_loc'); ?>" value="<?php echo esc_attr($instance['limit_loc']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('scope_loc'); ?>"><?php esc_html_e('Scope of the locations','events-manager'); ?>:</label><br/>
			<select class="widefat" id="<?php echo $this->get_field_id('scope_loc'); ?>" name="<?php echo $this->get_field_name('scope_loc'); ?>" >
				<?php foreach( em_get_scopes() as $key => $value) : ?>   
				<option value='<?php echo esc_attr($key) ?>' <?php echo ($key == $instance['scope_loc']) ? "selected='selected'" : ''; ?>>
					<?php echo esc_html($value); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order_loc'); ?>"><?php esc_html_e('Order By','events-manager'); ?>: </label>
			<select class="widefat" id="<?php echo $this->get_field_id('orderby_loc'); ?>" name="<?php echo $this->get_field_name('orderby_loc'); ?>">
				<?php foreach($this->em_orderby_options as $key => $value) : ?>   
	 			<option value='<?php echo esc_attr($key); ?>' <?php echo ( !empty($instance['orderby_loc']) && $key == $instance['orderby_loc']) ? "selected='selected'" : ''; ?>>
	 				<?php echo esc_html($value); ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order_loc'); ?>"><?php esc_html_e('Order of the locations','events-manager'); ?>:</label><br/>
			<select class="widefat" id="<?php echo $this->get_field_id('order_loc'); ?>" name="<?php echo $this->get_field_name('order_loc'); ?>" >
				<option value="ASC" <?php echo ($instance['order_loc'] == 'ASC') ? 'selected="selected"':''; ?>><?php esc_html_e('Ascending','events-manager'); ?></option>
				<option value="DESC" <?php echo ($instance['order_loc'] == 'DESC') ? 'selected="selected"':''; ?>><?php esc_html_e('Descending','events-manager'); ?></option>
			</select>
		</p>
		<em><?php echo sprintf( esc_html__('The list is wrapped in a %s tag, so if an %s tag is not wrapping the formats below it will be added automatically.','events-manager'), '<code>&lt;ul&gt;</code>', '<code>&lt;li&gt;</code>'); ?></em>
		<p>
			<label for="<?php echo $this->get_field_id('format_loc'); ?>"><?php esc_html_e('List item format','events-manager'); ?>: </label>
			<textarea rows="10" cols="20" class="widefat" id="<?php echo $this->get_field_id('format_loc'); ?>" name="<?php echo $this->get_field_name('format_loc'); ?>"><?php echo esc_textarea($instance['format_loc']); ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('no_locations_text'); ?>"><?php esc_html_e('No Locations message','events-manager'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('no_locations_text'); ?>" name="<?php echo $this->get_field_name('no_locations_text'); ?>" value="<?php echo esc_attr( $instance['no_locations_text'] ); ?>" >
		</p>







<?php
	}
}