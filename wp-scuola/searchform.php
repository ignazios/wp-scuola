<?php
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			searchform.php
 * Description:       	Modello di form di ricerca
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
?>
<form role="search" method="get" class="mysearchform" action="<?php echo home_url( '/' );?>" >
    <div>
    	<label class="screen-reader-text" for="s"><?php _e("Ricerca all'interno del sito","wpscuola");?></label>
    	<input type="text" value="<?php echo get_search_query();?>" name="s" class="border-bottom mr-2"/>
    	<button type="submit" >
    		<i class="fas fa-search"></i><span class="sr-only"><?php _e("Cerca","wpscuola");?></span>
    	</button>
    </div>
</form>