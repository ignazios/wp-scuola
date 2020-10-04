<?php
/****************************************************************************
 Widget Feed RSS
	   Titolo:	($title) Testo che viene visualizzato in Testa all'elenco degli articoli estratti
		Url:  ($url) Url del Feed RSS
		Elem: ($numele) Numero di elementi da visualizzare
		VisTitolo: ($vistit) Flag che indicase se visualizzare il Titolo della notizia
		VisAutore: ($visaut) Flag che indicase se visualizzare l'Autore della notizia
		VisData: ($visdata) Flag che indicase se visualizzare la data di pubblicazione della notizia
*/     
class Feed_RSS extends WP_Widget {

       function __construct() {
			parent::__construct( false, 'Scuola Feed RSS',
				array('classname' => 'Feed_RSS',
				    'description' => 'Visualizzatore Feed RSS') );

        }

	public function widget( $args, $instance ) {
        extract($args);
		$titoloSx=isset($instance['design-italia-titleSx']) ? $instance['design-italia-titleSx'] : '';
		$titoloCx	=isset($instance['design-italia-titleCx']) ? $instance['design-italia-titleCx'] : '';
		$titoloDx	=isset($instance['design-italia-titleDx']) ? $instance['design-italia-titleDx'] : '';
		$numele	=isset($instance['design-italia-items']) ? (int)$instance['design-italia-items']:0;
		$viscont=isset($instance['design-italia-vis_contenuto']) ? (int) $instance['design-italia-vis_contenuto'] : 0;
		$visaut	=isset($instance['design-italia-vis_autore']) ? (int) $instance['design-italia-vis_autore'] : 0; 
		$visdata	=isset($instance['design-italia-vis_data']) ? (int) $instance['design-italia-vis_data'] : 0; 		
		$urlSx = ! empty( $instance['design-italia-urlSx'] ) ? $instance['design-italia-urlSx'] : '';
		while ( stristr( $urlSx, 'http' ) != $urlSx ) {
			$urlSx = substr( $urlSx, 1 );
		}
		$urlCx = ! empty( $instance['design-italia-urlCx'] ) ? $instance['design-italia-urlCx'] : '';
		while ( stristr( $urlCx, 'http' ) != $urlCx ) {
			$urlCx = substr( $urlCx, 1 );
		}
		$urlDx = ! empty( $instance['design-italia-urlDx'] ) ? $instance['design-italia-urlDx'] : '';
		while ( stristr( $urlDx, 'http' ) != $urlDx ) {
			$urlDx = substr( $urlDx, 1 );
		}
		if ( empty( $urlSx ) And empty($urlCx) And empty($urlDx)) {
			return;
		}
		if(in_array(untrailingslashit($urlSx),array(site_url(),home_url()))
		And in_array(untrailingslashit($urlCx),array(site_url(),home_url())) 			
		And in_array(untrailingslashit($urlDx),array(site_url(),home_url())))			{
			return;
		}
		$NumFeed=0;
		if ( !empty( $urlSx )){
			$rssSx   = fetch_feed( $urlSx );
			$NumFeed++;
		}
		if ( !empty( $urlCx )){
			$rssSx   = fetch_feed( $urlCx );
			$NumFeed++;
		}
		if ( !empty( $urlDx )){
			$rssSx   = fetch_feed( $urlDx );
			$NumFeed++;
		}
		$title = $instance['design-italia-title'];
		$desc  = '';
		$link  = '';
?>
<section id="feedRSS_<?php echo $args['widget_id'];?>"  class="home-widget container">
		<div class="it-header-block">
	    	<div class="it-header-block-title">
<?php 
 			if ( $title ) {
          		$before_title=str_replace("<h2 class=\"","<h2 class=\"no_toc\"",$before_title);
                echo $before_title . $title . $after_title;
            }
?>	  
	    	</div>
		</div>
		<div class="row">
			<div class="col-lg-4 col-12 pl-2">
				<div class="shadow">
<?php if ( !empty( $urlSx )){?>
					<h3 class="my-bg-primary p-3 text-white"><?php echo $titoloSx; ?></h3>	  
		<?php $this->crea_VociFeed($urlSx,$numele,$viscont,$visaut,$visdata);?>
				</div>
			</div>
<?php } ?> 
			<div class="col-lg-4 col-12 pl-2">
				<div class="shadow">
<?php if ( !empty( $titoloCx )){?>
					<h3 class="my-bg-primary p-3 text-white"><?php echo $titoloCx; ?></h3>	  
		<?php $this->crea_VociFeed($urlCx,$numele,$viscont,$visaut,$visdata);?>
				</div>
			</div>
<?php }?> 
			<div class="col-lg-4 col-12 pl-2">
				<div class="shadow">
<?php if ( !empty( $titoloDx )){?>
					<h3 class="my-bg-primary p-3 text-white"><?php echo $titoloDx; ?></h3>	  
		<?php $this->crea_VociFeed($urlDx,$numele,$viscont,$visaut,$visdata);?>
				</div>
			</div>
<?php }?> 
		</div>
</section>
<?php
	}
	private function crea_VociFeed($rss,$numele,$viscont,$visaut,$visdata){
		if ( is_string( $rss ) ) {
			$rss = fetch_feed($rss);
		} elseif ( is_array($rss) && isset($rss['url']) ) {
			$args = $rss;
			$rss = fetch_feed($rss['url']);
		} elseif ( !is_object($rss) ) {
			return;
		}
		if ( is_wp_error($rss) ) {
			if ( is_admin() || current_user_can('manage_options') )
				echo '<p><strong>' . __( 'RSS Error:' ) . '</strong> ' . $rss->get_error_message() . '</p>';
			return;
		}
		if ( $numele < 1 || 20 < $numele )
			$numele = 10;

		if ( !$rss->get_item_quantity() ) {
			echo '<ul><li>' . __( 'An error has occurred, which probably means the feed is down. Try again later.' ) . '</li></ul>';
			$rss->__destruct();
			unset($rss);
			return;
		}
	?>		
	<div class="it-list-wrapper">
  		<ul class="it-list">
	<?php
		$HTML='';
		foreach ( $rss->get_items( 0, $numele ) as $item ) {
			$link = $item->get_link();
			while ( stristr( $link, 'http' ) != $link ) {
				$link = substr( $link, 1 );
			}
			$link = esc_url( strip_tags( $link ) );

			$title = esc_html( trim( strip_tags( $item->get_title() ) ) );
			if ( empty( $title ) ) {
				$title = __( 'Untitled' );
			}
			$desc = @html_entity_decode( $item->get_description(), ENT_QUOTES, get_option( 'blog_charset' ) );
			$desc = esc_attr( wp_trim_words( $desc, 55, ' [&hellip;]' ) );
			$summary = '';
			if ( $viscont ) {
				$summary = $desc;
				// Change existing [...] to [&hellip;].
				if ( '[...]' == substr( $summary, -5 ) ) {
					$summary = substr( $summary, 0, -5 ) . '[&hellip;]';
				}
				$summary = esc_html( $summary );
			}
			$date = '';
			if ( $visdata ) {
				$date = $item->get_date( 'U' );
				if ( $date ) {
					$date = ' <span class="rss-date">' . date_i18n( get_option( 'date_format' ), $date ) . '</span>';
				}
			}
			$author = '';
			if ( $visaut ) {
				$author = $item->get_author();
				if ( is_object($author) ) {
					$author = $author->get_name();
					$author = ' <cite>' . esc_html( strip_tags( $author ) ) . '</cite>';
				}
			}
			$HTML.='
			<li>';
			if ( !$link == '' ) {
				$HTML.='
				<a class="rsswidget" href="'.$link.'" target="_blank">
			        <div class="it-right p-3">
			          <span class="text">'.$title.'<span class="nolink"><em><strong>'.$date.' '.$author."</strong>".($viscont?"<br />".$summary:"").'</em></span></span>
			        </div>
		      	</a>';
			}
			$HTML.='
    		</li>';
		}
		echo $HTML.'
		</ul>
		</div>';
		$rss->__destruct();
		unset($rss);
	}
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['design-italia-title']	=$new_instance['design-italia-title'];
		$instance['design-italia-titleSx']	=$new_instance['design-italia-titleSx'];
		$instance['design-italia-urlSx']		=$new_instance['design-italia-urlSx'];
		$instance['design-italia-titleCx']	=$new_instance['design-italia-titleCx'];
		$instance['design-italia-urlCx']		=$new_instance['design-italia-urlCx'];
		$instance['design-italia-titleDx']	=$new_instance['design-italia-titleDx'];
		$instance['design-italia-urlDx']		=$new_instance['design-italia-urlDx'];
		$instance['design-italia-items']		=$new_instance['design-italia-items'];
		$instance['design-italia-vis_contenuto']=$new_instance['design-italia-vis_contenuto'];
		$instance['design-italia-vis_autore']=$new_instance['design-italia-vis_autore']; 
		$instance['design-italia-vis_data']	=$new_instance['design-italia-vis_data']; 
 
        return $instance;
	}

	/**
	 * Outputs the settings form for the RSS widget.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		if ( empty( $instance ) ) {
			$instance = array(
				'design-italia-title'		   => 'Notizie della Scuola',
				'design-italia-titleSx'        => '',
				'design-italia-urlSx'          => '',
				'design-italia-titleCx'        => '',
				'design-italia-urlCx'          => '',
				'design-italia-titleDx'        => '',
				'design-italia-urlDx'          => '',
				'design-italia-items'        => 10,
				'design-italia-vis_contenuto' => 0,
				'design-italia-vis_autore'  => 0,
				'design-italia-vis_data'    => 0,
			);
		}
		$titolo	=isset($instance['design-italia-title']) ? $instance['design-italia-title'] : 'Notizie della Scuola';
		$titoloSx	=isset($instance['design-italia-titleSx']) ? $instance['design-italia-titleSx'] : '';
		$urlSx	=isset($instance['design-italia-urlSx']) ? $instance['design-italia-urlSx'] : '';
		$titoloCx	=isset($instance['design-italia-titleCx']) ? $instance['design-italia-titleCx'] : '';
		$urlCx	=isset($instance['design-italia-urlCx']) ? $instance['design-italia-urlCx'] : '';
		$titoloDx	=isset($instance['design-italia-titleDx']) ? $instance['design-italia-titleDx'] : '';
		$urlDx	=isset($instance['design-italia-urlDx']) ? $instance['design-italia-urlDx'] : '';
		$numele	=isset($instance['design-italia-items']) ? (int)$instance['design-italia-items']:0;
		$viscont=isset($instance['design-italia-vis_contenuto']) ? (int) $instance['design-italia-vis_contenuto'] : 0;
		$visaut	=isset($instance['design-italia-vis_autore']) ? (int) $instance['design-italia-vis_autore'] : 0; 
		$visdata	=isset($instance['design-italia-vis_data']) ? (int) $instance['design-italia-vis_data'] : 0; 
	if ( ! empty( $instance['error'] ) ) {
		echo '<p class="widget-error"><strong>' . __( 'RSS Error:' ) . '</strong> ' . $instance['error'] . '</p>';
	}
?>
	<label for=<?php echo $this->get_field_id( "design-italia-title");?>>Titolo del Blocco</label>
	<input class="widefat" id=<?php echo $this->get_field_id( "design-italia-title");?> name=<?php echo $this->get_field_name( 'design-italia-title' ); ?> type="text" value="<?php echo esc_attr( $titolo ); ?>" /></p>
	<p><label for=<?php echo $this->get_field_id( "design-italia-urlSx");?>>Url Feed RSS blocco di Sinistra</label>
	<input class="widefat" id=<?php echo $this->get_field_id( "design-italia-urlSx");?> name=<?php echo $this->get_field_name( 'design-italia-urlSx' ); ?> type="text" value="<?php echo esc_url( $urlSx ); ?>" /></p>
	<p><label for=<?php echo $this->get_field_id( "design-italia-titleSx");?>>Titolo del Feed Rss blocco di Sinistra</label>
	<input class="widefat" id=<?php echo $this->get_field_id( "design-italia-titleSx");?> name=<?php echo $this->get_field_name( 'design-italia-titleSx' ); ?> type="text" value="<?php echo esc_attr( $titoloSx ); ?>" /></p>
	<p><label for=<?php echo $this->get_field_id( "design-italia-urlCx");?>>Url Feed RSS blocco Centrale</label>
	<input class="widefat" id=<?php echo $this->get_field_id( "design-italia-urlCx");?> name=<?php echo $this->get_field_name( 'design-italia-urlCx' ); ?> type="text" value="<?php echo esc_url( $urlCx ); ?>" /></p>
	<p><label for=<?php echo $this->get_field_id( "design-italia-titleCx");?>>Titolo del Feed Rss blocco Centrale</label>
	<input class="widefat" id=<?php echo $this->get_field_id( "design-italia-titleCx");?> name=<?php echo $this->get_field_name( 'design-italia-titleCx' ); ?> type="text" value="<?php echo esc_attr( $titoloCx ); ?>" /></p>
	<p><label for=<?php echo $this->get_field_id( "design-italia-urlDx");?>>Url Feed RSS blocco di Destra</label>
	<input class="widefat" id=<?php echo $this->get_field_id( "design-italia-urlDx");?> name=<?php echo $this->get_field_name( 'design-italia-urlDx' ); ?> type="text" value="<?php echo esc_url( $urlDx ); ?>" /></p>
	<p><label for=<?php echo $this->get_field_id( "design-italia-titleDx");?>>Titolo del Feed Rss blocco di Destra</label>
	<input class="widefat" id=<?php echo $this->get_field_id( "design-italia-titleDx");?> name=<?php echo $this->get_field_name( 'design-italia-titleDx' ); ?> type="text" value="<?php echo esc_attr( $titoloDx ); ?>" /></p>
	<p><label for=<?php echo $this->get_field_id( "design-italia-items");?>>Numero elementi da Visualizzare</label>
	<input class="widefat" type="number" id=<?php echo $this->get_field_id( "design-italia-items");?> name=<?php echo $this->get_field_name( 'design-italia-items' ); ?> value="<?php echo esc_attr( $numele ); ?>" min="1" max="20" style="width:4em;">
	</p>
	<p><input id=<?php echo $this->get_field_id( "design-italia-vis_contenuto");?> name=<?php echo $this->get_field_name( 'design-italia-vis_contenuto' ); ?> type="checkbox" value="1" <?php checked( $viscont ); ?> />
	<label for=<?php echo $this->get_field_id( "design-italia-vis_contenuto");?>>Visualizza Contenuto Post</label></p>
	<p><input id=<?php echo $this->get_field_id( "design-italia-vis_autore");?> name=<?php echo $this->get_field_name( 'design-italia-vis_autore' ); ?> type="checkbox" value="1" <?php checked( $visaut ); ?> />
	<label for=<?php echo $this->get_field_id( "design-italia-vis_autore");?>>Visualizza Autore Post</label></p>
	<p><input id=<?php echo $this->get_field_id( "design-italia-vis_data");?> name=<?php echo $this->get_field_name( 'design-italia-vis_data' ); ?> type="checkbox" value="1" <?php checked( $visdata ); ?>/>
	<label for=<?php echo $this->get_field_id( "design-italia-vis_data");?>>Visualizza Data Post</label></p>
<?php }
}?>