<?php
/**
* Widget Eventi
*	   Titolo:	($title) Testo che viene visualizzato in Testa all'elenco degli articoli estratti
*/
 function eventi_get_thumbnail($IDogg){
 	$img_thumbnail=get_the_post_thumbnail($IDogg,'img-wrapper-thumb');
   	if(!isset($img_thumbnail) Or $img_thumbnail==""){
		return FALSE; 
	}else return $img_thumbnail;
 }
class Eventi extends WP_Widget {
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
    	$widget_ops = array('description' => "Widget per la visualizzazione degli eventi codificati con il plugin Events Manager." );
        parent::__construct(false, $name = __('Scuola Eventi','events-manager'), $widget_ops);	
    }

	function my_em_get_link( $text = '' ) {
		$text = ($text == '') ? get_option ( "dbem_events_page_title" ) : $text;
		$text = ($text == '') ? __('Events','events-manager') : $text; //In case options aren't there....
		return '<a href="'.esc_url(EM_URI).'" title="'.esc_attr($text).'"><span class="fas fa-link p-1"></span><span class="text">'.esc_html($text).'</span></a>';
	}

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    	$instance = array_merge($this->defaults, $instance);
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

    	$title    = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Articoli per categoria' );
	//orderby fix for previous versions with old orderby values
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
		$events = EM_Events::get(apply_filters('em_widget_events_get_args',$instance));
//		var_dump($events);	
		?>
 	<section id="ev_<?php echo $args['widget_id'];?>"  class="home-widget container ">
<?php           
           if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            } ?>
		<div class="it-grid-list-wrapper it-image-label-grid">
			<div class="grid-row">
     <?php   foreach($events as $Event) {?>
			<div class="col-12 col-sm-6 col-lg-4">
      			<div class="it-grid-item-wrapper shadow">
                   <?php 
                   $ImgEvidenza=eventi_get_thumbnail($Event->post_id);
                   if ( $ImgEvidenza!==FALSE ) { ?>
		          <div class="img-responsive-wrapper">
		            <div class="img-responsive">
		                <div class="img-wrapper">
		                <?php   echo $ImgEvidenza;?>
		                </div>
		            </div>
		          </div>
		          <?php }?>
                  <div class="card-body pt-1 pb-1">
                    <div class="category-top">
                        <span class="data">
                          <?php echo date("d/m/Y H:i",$Event->start)."-".date("H:i",$Event->end); ?>
                        </span>
                    </div>
                    <header class="entry-header">
                      <h4 class="card-title big-heading h5"><?php echo get_the_title($Event->post_id); ?></h4>
                    </header><!-- .entry-header -->
                        <div class="card-text pb-2">
                            <?php echo get_the_excerpt($Event->post_id); ?>
                        </div><!-- .entry-summary -->
                  </div>
  				  <div class="it-card-footer">
      				<a class="read-more" href="<?php echo get_the_permalink($Event->post_id);?>">
          				<span class="text"><span class="fas fa-link p-1"></span> <?php _e( 'Leggi tutto','wpscuola' );?></span>
        			</a>
		          </div>
		    	</div>
			</div>
		<?php  }?>
			</div>
		</div>
	</section>
<?php            
        if ( ! $this->is_preview() ) {
            $cache[ $args['widget_id'] ] = ob_get_flush();
            wp_cache_set( 'widget_grid_posts', $cache, 'widget' );
        } else {
            ob_end_flush();
        }
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
<?php
	}
}