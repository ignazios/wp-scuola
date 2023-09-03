<?php
/****************************************************************************
Widget Amministrazione Trasparente
Titolo:	($title) Testo che viene visualizzato in Testa al blocco del widget
Logic:	($logic) Indica se visualizzare solo nella pagina indicata nelle impostazioni, pagina archivio e singola dei documenti

*/

class my_ATWidget extends WP_Widget {

    function __construct() {
        parent::__construct( 'my_ATWidget', 'Scuola Amministrazione Trasparente', array( 'description' => 'Personalizzazione della lista delle sezioni relative alla trasparenza per il template ItaliaWP' ) );
    }

    function widget( $args, $instance ) {
        extract($args);

        $title          = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Doumenti Trasparenza' );
        $title          = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		if ( $instance['logic'] && !( is_tax( 'tipologie' ) || is_singular( 'amm-trasparente' ) || is_page( at_option('page_id') )) ) {
			return;
		}
        if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
?>
<div class="link-list-wrapper">
  <ul class="link-list">
<?php
		foreach (amministrazionetrasparente_getarray() as $inner) {
			$Id=sanitize_title($inner[0]);
?>
    <li>
    	<a class="list-item large medium right-icon" href="#<?php echo $Id;?>" data-toggle="collapse" aria-expanded="false" aria-controls="<?php echo $Id;?>">
      		<span><?php echo $inner[0];?></span><svg class="icon icon-primary right"><use xlink:href="<?php echo get_template_directory_uri();?>/lib/bootstrap-italia/svg/sprite.svg#it-expand"></use></svg>
      	</a>
      	<ul class="link-sublist collapse" id="<?php echo $Id;?>">
<?php 
		    $atreturn = '';
		    foreach ($inner[1] as $value) {
		        echo '<li class="testoElenco"><a  class="list-item" href="' . get_term_link( get_term_by('name', $value, 'tipologie'), 'tipologie' ) . '" title="' . $value . '"><span>' . $value . '<span></a></li>';
		    }?>
		</ul>
	</li>
<?php	} ?>		
	</ul>
</div>
<?php
    }

    function update( $new_instance, $old_instance ) {
		delete_option( 'at_option_widget' );
		delete_option( 'at_logic_widget' );

		$instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['logic'] = isset($new_instance['logic']) ? 1 : 0;

        return wp_parse_args( (array) $instance, self::get_defaults() );

    }

	 private static function get_defaults() {
        $defaults = array(
            'title' => 'Amministrazione Trasparente',
            'expandable' => 0,
            'logic' => 0
        );
        return $defaults;
    }

    function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, self::get_defaults() );

        $title = esc_attr($instance['title']); ?>
        <p><label for="<?php echo $this->get_field_id('title');?>">
        Titolo: <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo $title; ?>" />
        </label></p>
		<p><input type="checkbox" id="<?php echo $this->get_field_id('logic');?>" name="<?php echo $this->get_field_name('logic');?>"
		<?php checked( $instance[ 'logic' ] ); ?>/> Visualizza solo nella pagina indicata nelle impostazioni, pagina archivio e singola dei documenti</p>
		<input type="hidden" name="submitted" value="1" />
        <?php
    }
}



?>