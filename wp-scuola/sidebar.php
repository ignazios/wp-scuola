<?php
 /**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			sidebar.php
 * Description:       	Template della barra laterale destra
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/	
?>
<aside id="sidebar" role="complementary">
   <?php if ( is_active_sidebar( 'primary-widget-area' ) ) : ?>
   <div id="primary" class="widget-area primary-widget-area">
      <ul class="xoxo">
         <?php dynamic_sidebar( 'primary-widget-area' ); ?>
      </ul>
   </div>
   <?php endif; ?>
</aside>