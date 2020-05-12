<?php
/**
 * Prenotazioni
 * Libreria funzioni generali
 * @package Prenotazioni
 * @author Scimone Ignazio
 * @copyright 2014-2099
 * @version 1.6.6
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

class PrenotazioniWidget extends WP_Widget
{
	public function __construct()
	{
	   parent::__construct("prenotazioni", 'Prenotazioni', array('description' => 'Widget per la gestione delle prenotazione degli spazi. Permette di visualizzare le ultime prnotazioni e di creare nuove prenotazioni',array( 'width' => 300, 'height' => 350)));
	 }
    
	public function form($instance)
    {
    
	 $defaults = array(
 		'titolo' => 'Prenotazioni',
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
       global $Gest_Prenotazioni,$G_Spaces;
       if (!is_user_logged_in())
	   	return;
	    extract( $args );
        $titolo = apply_filters('widget_title', $instance['titolo'] );
 		if ($titolo=='')
			$titolo="Prenotazioni";
		echo $before_widget;
        echo $before_title .$titolo. $after_title;
		echo "<div>";
		$Elenco=$Gest_Prenotazioni->get_Prenotazioni("<");
		if(count($Elenco)>0){
			if (count($Elenco)<5)
				echo "
		<p>Ultime 5 prenotazione passate</p>";
			else	
				echo "
		<p>Ultime ".count($Elenco)." prenotazione passate</p>";
			foreach ($Elenco as $Elemento) {
				$Spazio=$G_Spaces->get_NomeSpazio($Elemento->IdSpazio);
				$data=$Elemento->DataPrenotazione;
				$oraI=$Elemento->OraInizio;
				$oraF=$Elemento->OraFine;
				echo "
			<ul>
				<li>$Spazio $data $oraI $oraF </li>";
			}
			echo "</ul>";
		}else
			echo "
		<p>Al momento non risultano prenotazioni</p>";		
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
function PrenotazioniWidget_register()
{
    register_widget( 'PrenotazioniWidget' );
}

add_action( 'widgets_init', 'PrenotazioniWidget_register' );


?>