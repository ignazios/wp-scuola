<?php
/****************************************************************************
  Widget Articoli in Griglia
	    Titolo:	($title) Testo che viene visualizzato in Testa all'elenco degli articoli estratti
		Categoria Blocco Sinistra:  ($catprimoblocco) ID della categoria del blocco di Sinistra
		Categoria Blocco Destra:  ($catsecondoblocco) ID della categoria del blocco di Destra
		Numero di Elementi: ($numelementi) Numero di elementi da visualizzare
		Visualizza Link Leggi Tutto: ($linkLT) Flag che indica se deve essere visualizzato il link Leggi Tutto
*/ 
class Articoli_Griglia extends WP_Widget {

   function __construct() {
		parent::__construct( false, 'Scuola - Articoli a griglia',
			array('classname' => 'Articoli_Griglia',
			    'description' => 'Widget che visualizza gli articoli di una categoria selezionata, a griglia.') );
	      add_action( 'save_post', [$this, 'flush_widget_cache'] );
	      add_action( 'deleted_post', [$this, 'flush_widget_cache'] );
	      add_action( 'switch_theme', [$this, 'flush_widget_cache'] );
    }

    public function widget( $args, $instance ) 
    {
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

        $title          = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Articoli per categoria' );
        /** This filter is documented in wp-includes/default-widgets.php */
        $title          = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        $number         = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number ) {
            $number = 5;
        }
        $cat_id         = $instance['cat_id'];
		$cats_notin     = isset($instance['cats_notin'])?strip_tags( $instance['cats_notin'] ):"";
        $random         = $instance['rand'] ? true : false; 
        $excerpt        = $instance['excerpt'] ? true : false; 
        $thumbnail      = $instance['thumbnail'] ? true : false; 
        $categories     = $instance['categories'] ? true : false; 
        $date           = $instance['date'] ? true : false; 
        $shadow         = $instance['shadow'] ? true : false; 
        $leggitutto 	= isset( $instance['leggitutto'] ) ? $instance['leggitutto'] :"";

        /**
         * Filter the arguments for the Category Posts widget.
         * @since 1.0.0
         * @see WP_Query::get_posts()
         * @param array $args An array of arguments used to retrieve the category posts.
         */ 
        if( true === $random ) {
            $query_args = [
                'posts_per_page'    => $number,
                'cat'               => $cat_id,
                'orderby'           => 'rand'
            ];
        }else{
            $query_args = [
                'posts_per_page'    => $number,
                'cat'               => $cat_id,
            ];
        }
        if($cat_id==-1 And $cats_notin!=""){
			$query_args['category__not_in'] = explode(",", $cats_notin);
			$query_args['ignore_sticky_posts'] = 1;
		}
		if($cat_id==-1)
			$LinkLeggiTutto=get_permalink( get_option( 'page_for_posts' ) );
		else 
			$LinkLeggiTutto=get_category_link($cat_id);
        $q = new WP_Query( apply_filters( 'category_posts_args', $query_args ) );

        if( $q->have_posts() ) {?>

 	<section id="ag_<?php echo $args['widget_id'];?>"  class="home-widget container ">
<?php           
           if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            } ?>
		<div class="it-grid-list-wrapper it-image-label-grid">
			<div class="grid-row">
     <?php   while( $q->have_posts() ) {
                $q->the_post(); ?>
			<div class="col-12 col-sm-6 col-lg-4">
      			<div class="it-grid-item-wrapper <?php echo ($shadow?"shadow":"");?>">
                   <?php 
                    if ( $thumbnail ) { ?>
		          <div class="img-responsive-wrapper">
		            <div class="img-responsive">
		                <div class="img-wrapper">
		                <?php   echo scuola_get_thumbnail($q->get_the_ID());?>
		                </div>
		            </div>
		          </div>
		          <?php }?>
                   <?php //if(is_sticky()) { echo '<div class="flag-icon"></div>'; } ?>
                  <div class="card-body">
                    <div class="category-top">
<?php				if($categories):?>
                         <strong>
                          <?php the_category(', '); ?>
                        </strong>
<?php				endif;?>
                      <?php if( true === $date ) { ?> 
                        <span class="data">
                          <?php echo get_the_date( 'j M Y' ); ?>
                        </span>
                      <?php } ?>
                    </div>
                    <header class="entry-header">
                      <?php the_title( '<h4 class="card-title big-heading h5">', '</h4>' ); ?>
                    </header><!-- .entry-header -->

                    <?php if( true === $excerpt ) { ?>    

                        <div class="card-text">
                            <?php the_excerpt(); ?>
                        </div><!-- .entry-summary -->
                    <?php } ?>
                   </div>
		          <p  class="read-more text-right pb-2 pr-2">
		          	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><span class="fas fa-link p-1"></span> <?php _e( 'Leggi di pi&ugrave','wpscuola' ); ?></a>
		          </p>
            	</div>
        	</div>
                <?php  }?>
			</div>
      <?php   if($leggitutto):?>
					<div class="it-card-footer">
          				<a class="read-more" href="<?php echo $LinkLeggiTutto;?>">
              				<span class="text"><span class="fas fa-link p-1"></span> <?php _e( 'Leggi tutto','wpscuola' );?></span>
            			</a>
		          	</div>
		        <?php endif; ?>
		</div>
	</section>
<?php            
            wp_reset_postdata();
        }

        if ( ! $this->is_preview() ) {
            $cache[ $args['widget_id'] ] = ob_get_flush();
            wp_cache_set( 'widget_grid_posts', $cache, 'widget' );
        } else {
            ob_end_flush();
        }
    }

    public function update( $new_instance, $old_instance ) 
    {
        $instance                   = $old_instance;
        $instance['title']          = strip_tags( $new_instance['title'] );
        $instance['number']         = (int) $new_instance['number'];
        $instance['cat_id']         = (int) $new_instance['cat_id'];
        $instance['cats_notin']     = strip_tags( $new_instance['cats_notin'] );
        $instance['rand']           = $new_instance['rand'];
        $instance['excerpt']        = $new_instance['excerpt'];
        $instance['thumbnail']      = $new_instance['thumbnail'];
        $instance['date']           = $new_instance['date'];
        $instance['shadow']         = $new_instance['shadow'];
        $instance['categories']     = $new_instance['categories'];
        $instance['leggitutto']		= $new_instance['leggitutto'];
        $this->flush_widget_cache();
        return $instance;
    }

    public function flush_widget_cache() 
    {
        wp_cache_delete('widget_grid_posts', 'widget');
    }

    public function form( $instance ) 
    {

        $title      = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number     = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $cat_id     = isset( $instance['cat_id'] ) ? intval( $instance['cat_id'] ) : -1;
        $catnotin	= isset( $instance['cats_notin'] ) ? esc_attr( $instance['cats_notin'] ) : '';
        $random     = isset( $instance['rand'] ) ? $instance['rand'] : false; 
        $excerpt    = isset( $instance['excerpt'] ) ? $instance['excerpt'] : false; 
        $thumbnail  = isset( $instance['thumbnail'] ) ? $instance['thumbnail'] : false; 
        $date       = isset( $instance['date'] ) ? $instance['date'] : false; 
        $shadow     = isset( $instance['shadow'] ) ? $instance['shadow'] : false; 
        $categories = isset( $instance['categories'] ) ? $instance['categories'] : false; 
        $leggitutto = isset( $instance['leggitutto'] ) ? $instance['leggitutto'] :false;
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Titolo:','wpscuola' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Numero di articoli da visualizzare:','wpscuola' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" value="<?php echo $number; ?>" size="3" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('cat_id'); ?>"><?php _e( 'Seleziona la categoria:','wpscuola' )?></label>
            <select id="<?php echo $this->get_field_id('cat_id'); ?>" name="<?php echo $this->get_field_name('cat_id'); ?>">
            	<option <?php echo ( -1 == esc_attr( $cat_id ) ) ? ' selected = "selected" ' : '';?> value="-1">Tutti gli articoli</option>
                <?php 
                $this->categories = get_categories(array('hide_empty' => false));
                foreach ( $this->categories as $cat ) {
                    $selected = ( $cat->term_id == esc_attr( $cat_id ) ) ? ' selected = "selected" ' : '';
                    $option = '<option '.$selected .'value="' . $cat->term_id;
                    $option = $option .'">';
                    $option = $option .$cat->name;
                    $option = $option .'</option>';
                    echo $option;
                }
                ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'cats_notin' ); ?>"><?php _e( 'ID delle Categorie da escludere (separati da ,):' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'cats_notin' ); ?>" name="<?php echo $this->get_field_name( 'cats_notin' ); ?>" type="text" value="<?php echo $catnotin; ?>" />
            <?php _e( 'Impostazione valida solo se selezionato "Tutti gli articoli"','wpscuola' ); ?>
        </p>

       <p>
            <?php $checked = ( $random ) ? ' checked=\"checked\" ' : ''; ?>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'rand' ); ?>" name="<?php echo $this->get_field_name( 'rand' ); ?>" value="true" <?php echo $checked; ?> />    
            <label for="<?php echo $this->get_field_id('rand'); ?>"><?php _e( 'Visualizza articoli casualmente. Se deselezionato, verranno visualizzati prima i pi&ugrave; recenti.','wpscuola' ); ?></label>
        </p>

        <p>
            <?php $checked = ( $excerpt ) ? ' checked=\"checked\" ' : ''; ?>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'excerpt' ); ?>" name="<?php echo $this->get_field_name( 'excerpt' ); ?>" value="true" <?php echo $checked; ?> />    
            <label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e( 'Visualizza estratto. Se deselezionato, visualizza solo il titolo dell\'articolo.','wpscuola' ); ?></label>
        </p>

        <p>
            <?php $checked = ( $thumbnail ) ? ' checked=\"checked\" ' : ''; ?>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail' ); ?>" value="true" <?php echo $checked; ?> />    
            <label for="<?php echo $this->get_field_id('thumbnail'); ?>"><?php _e( 'Visualizza immagine in evidenza degli articoli.','wpscuola' ); ?></label>
        </p>

        <p>
            <?php $checked = ( $categories ) ? ' checked=\"checked\" ' : ''; ?>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'categories' ); ?>" name="<?php echo $this->get_field_name( 'categories' ); ?>" value="true" <?php echo $checked; ?> />    
            <label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e( 'Visualizza le categorie degli articoli.','wpscuola' ); ?></label>
        </p>

        <p>
            <?php $checked = ( $date ) ? ' checked=\"checked\" ' : ''; ?>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" value="true" <?php echo $checked; ?> />    
            <label for="<?php echo $this->get_field_id('date'); ?>"><?php _e( 'Visualizza le date degli articoli.','wpscuola' ); ?></label>
        </p>

        <p>
            <?php $checked = ( $shadow ) ? ' checked=\"checked\" ' : ''; ?>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'shadow' ); ?>" name="<?php echo $this->get_field_name( 'shadow' ); ?>" value="true" <?php echo $checked; ?> />    
            <label for="<?php echo $this->get_field_id('shadow'); ?>"><?php _e( 'Visualizza l\'ombra delle schede.','wpscuola' ); ?></label>
        </p>

   		<p>
   			<?php $checked = ( $leggitutto ) ? ' checked=\"checked\" ' : ''; ?>
    		<input type="checkbox" id="<?php echo $this->get_field_id('leggitutto'); ?>" name="<?php echo $this->get_field_name('leggitutto'); ?>" value="true" <?php echo $checked; ?>/>
    		<label for="<?php echo $this->get_field_id('leggitutto'); ?>"><?php _e( 'Visualizza il link Leggi tutto.','wpscuola' ); ?></label>
    	</p>
    <?php
    }

}