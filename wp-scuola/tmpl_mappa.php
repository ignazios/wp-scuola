<?php
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * Template Name: 		Pagina Mappa
 * File Name:			tmpl_mappa.php
 * Description:       	Template di pagina a larghezza piena. Pagina senza sidebar e senza margini
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
get_header();
scuola_set_post_view();
?>
<section id="content" role="main">
   	<div class="container" id="Mappa">
		<div class="row">
			<div class="col-lg-4 col-md-12">
				<h2>Pagine</h2>
				<div class="it-list-wrapper">
  					<ul class="it-list">
  						<?php wp_list_pages (array("sort_column" 	=>"post_title",
  												   "title_li" 	 	=>"",
  												   "link_before"	=> "<div class=\"it-right-zone\"><span class=\"text\">",
  												   "link_after"		=> "</span></div>")); ?>
	                </ul>
	            </div>
			</div>
			<div class="col-lg-4 col-md-12">
				<h2><?php _e('Categorie','wpscuola');?></h2>
				<div class="it-list-wrapper">
  					<ul class="it-list">
  						<?php 
		echo my_wp_list_categories(array('hide_empty'         => 0,
									'echo'         => 1,
									'taxonomy'     => 'category',
									'title_li'		=>"",
									'hierarchical'  =>1,
								   	"link_before"	=> "<div class=\"it-right-zone\"><span class=\"text\">",
								   	"link_after"	=> "</span>",
								   	"link_close"	=> "</div>",
								   	"count_before"	=> "<span class=\"it-multiple\"><span class=\"metadata\">",
								   	"count_after"	=> "</span></span>",
									'show_count' 	=> 1));
 									?>
	                </ul>
	            </div>
			</div>	
			<div class="col-lg-4 col-md-12">
				<h2><?php _e('Tag','wpscuola');?></h2>
				<div class="it-list-wrapper">
  					<ul class="it-list">
  						<?php wp_tag_cloud(array("show_count"	=>1,
  												 "smallest"		=>10,
  												 "largest"		=>18,
												 "link_before"	=> "<div class=\"it-right-zone\"><span class=\"text\">",
										   		 "link_after"	=> "</span></div>")); ?>
					</ul>
	            </div>
			</div>
		</div>
	</div>	
</section>
<?php	
get_footer();