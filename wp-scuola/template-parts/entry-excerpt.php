<?php if(get_the_excerpt()!=""): ?>
				<section class="entry-summary">
					<div class="callout mycallout">
  						<div class="callout-title"><?php _e( 'Riassunto', 'wpscuola' ); ?></div>
  						<?php the_excerpt(); ?>
					</div>
				</section>	
<?php endif;?>