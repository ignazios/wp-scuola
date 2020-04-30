<?php
/**
 * Gestione Circolari - Widget
 * 
 * @package Gestione Circolari
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @ver 2.7.3
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

class CircolariWidget extends WP_Widget
{
	public function __construct()
	{
	   parent::__construct("circolari", 'Circolari', array('description' => 'Utilizzare questo widget per evidenziare lato pubblico del sito le circolari che l\'utente deve firmare',array( 'width' => 300, 'height' => 350)));
	 }
    
	public function form($instance)
    {
    
	 $defaults = array(
 		'titolo' => 'Circolari',
        );
        $instance = wp_parse_args( (array) $instance, $defaults );?>
        <p>
            <label for="<?php echo $this->get_field_id( 'titolo' ); ?>">
                Titolo widget:
            </label>
            <input type="text" id="<?php echo $this->get_field_id( 'titolo' ); ?>" name="<?php echo $this->get_field_name( 'titolo' ); ?>" value="<?php echo $instance['titolo']; ?>" size="30" />
        </p>
       <?php
    }


public function widget( $args, $instance )
    {
       if (!is_user_logged_in())
	   	return;
	    extract( $args );
        $titolo = apply_filters('widget_title', $instance['titolo'] );
 		if ($titolo=='')
			$titolo="Circolari";
		echo $before_widget;
        echo $before_title .$titolo. $after_title;
		$NumCircolari=GetCircolariDaFirmare("N");
		echo "<div>";
		if ($NumCircolari>0)
			echo '
		<p>
			Hai '.$NumCircolari.' Circolari da firmare
		</p>
		<p style="font-size:0.8em;">
			<span style="background-color:red;">&nbsp;&nbsp;&nbsp;</span>
			<a href="'.site_url().'/wp-admin/edit.php?post_type=circolari&page=Firma" style="margin-top:-15px;margin-left:-5px">Firma le circolari</a>
		</p>';
		else
			echo'<p style="font-size:0.8em;"><span style="background-color:green;">&nbsp;&nbsp;</span>Nessuna Circolare da Firmare</p>';
		echo "</div>";
 	   echo $after_widget;
    }

public function update( $new_instance, $old_instance )
	{
			$instance = $old_instance;
	        $instance['titolo'] = strip_tags( $new_instance['titolo'] );
			return $instance;
	}
}	
function CircolariWidget_register()
{
    register_widget( 'CircolariWidget' );
}

add_action( 'widgets_init', 'CircolariWidget_register' );


?>