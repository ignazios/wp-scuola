<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			comments.php
 * Description:       	Template dei commenti
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/

if ( 'comments.php' == basename( $_SERVER['SCRIPT_FILENAME'] ) ) return; 
if ( !have_comments() and !comments_open()) return;?>
<section id="comments">
<?php 
	if ( have_comments() ) : 
		global $comments_by_type;
		$comments_by_type = separate_comments( $comments );
		if ( ! empty( $comments_by_type['comment'] ) ) : 
		?>
		<section id="comments-list" class="comments">
			<h3 class="comments-title"><?php comments_number(); ?></h3>
			<?php if ( get_comment_pages_count() > 1 ) : ?>
				<nav id="comments-nav-above" class="comments-navigation" role="navigation">
				<div class="paginated-comments-links"><?php paginate_comments_links(); ?></div>
				</nav>
			<?php endif; ?>
			<ul>
			<?php wp_list_comments( 'type=comment' ); ?>
			</ul>
			<?php if ( get_comment_pages_count() > 1 ) : ?>
				<nav id="comments-nav-below" class="comments-navigation" role="navigation">
				<div class="paginated-comments-links"><?php paginate_comments_links(); ?></div>
				</nav>
			<?php endif; ?>
		</section>
		<?php endif; 
		if ( ! empty( $comments_by_type['pings'] ) ) : 
			$ping_count = count( $comments_by_type['pings'] ); 
			?>
			<section id="trackbacks-list" class="comments">
			<h3 class="comments-title"><?php echo '<span class="ping-count">' . $ping_count . '</span> ' . ( $ping_count > 1 ? __( 'Trackbacks', 'wpscuola' ) : __( 'Trackback', 'wpscuola' ) ); ?></h3>
			<ul>
			<?php wp_list_comments( 'type=pings&callback=scuola_custom_pings' ); ?>
			</ul>
			</section>
		<?php endif; 
endif;
if ( comments_open() ) comment_form(array(
	'submit_button'	=>'<button class="btn btn-primary btn-icon" type="submit"><span class="fas fa-share-square pr-2"></span>'.__( 'Invia Commento', 'wpscuola' ).'</button>',
	'comment_field' =>'<div>
  		<div class="form-group">
    		<textarea id="comment" rows="5" name="comment" aria-required="true"></textarea>
    		<label for="comment">'._x('Commento','wpscuola').'</label>
  		</div>
</div>',));
?>
</section>