<?php
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			tag.php
 * Description:       	Template della pagina che automaticamente crea l'archivio dei tag (sulla destra per widht>767px o in basso per widht<768px ) in una sidebar con 
 * 						archivio per Argomento e per data
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
get_header();

global $query_string,$mesi;
wp_parse_str( $query_string, $search_query );

function my_get_tags_archives()
{
	global $wpdb, $wp_locale,$mesi;
	$Result="";
	
	$tagID = get_term_by('slug', get_query_var('tag'), 'post_tag');
	if (!isset($tagID)) {
		return "";
	}
	$Sql = "SELECT YEAR(post_date) AS `Anno`, MONTH(post_date) AS `Mese`, count(DISTINCT ID) as NumArt
FROM $wpdb->posts, $wpdb->term_taxonomy, $wpdb->term_relationships
WHERE $wpdb->posts.post_status = 'publish'
AND $wpdb->posts.post_type = 'post'
AND $wpdb->term_taxonomy.term_id =".$tagID->term_id."
AND $wpdb->posts.ID = $wpdb->term_relationships.object_id
AND $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
GROUP BY YEAR(post_date), MONTH(post_date)
ORDER BY post_date DESC";
	//		echo "<br />".$Sql;exit;
	$ArchivioDate = $wpdb->get_results($Sql);
	$Anno=0;
	$ArticoliAnni=calc_NumArticoliMA($ArchivioDate);
	foreach ($ArchivioDate as $Data) {
		$Des=$mesi[$Data->Mese]." ".$Data->Anno." (".$Data->NumArt.")";
		if ($Anno!=$Data->Anno){
			if ($Anno!=0) {
				$Result.='			</ul>
				</li>';
			}
			$Result.='
			<li>
				<div class="row">
					<div class="col-1">
						<a class="list-item" href="'."#Tag".get_query_var('tag').'M'.$Data->Mese.'" data-toggle="collapse" aria-expanded="false" aria-controls="Tag'.get_query_var('tag').'M'.$Data->Mese.'">
							<span class="fas fa-angle-down espandi align-middle"></span>
						</a>					
					</div>
					<div class="col-9 mb-0">
						<a class="list-item large" href="'.esc_url(home_url('/')).$Data->Anno."/?tag=".get_query_var('tag').'">
							<span class="m-0">'.$Data->Anno.'</span>
						</a>						
					</div>
					<div class="col-2 m-0 p-0">
						<span class="badge badge-pill badge-primary text-white">'.$ArticoliAnni[$Data->Anno].'</span>
					</div>
		  		</div>			
				<ul class="link-sublist collapse" id="Tag'.get_query_var('tag').'M'.$Data->Mese.'">';
			$Anno=$Data->Anno;
		}
		$Result.='
					<li>
						<a class="list-item subele pl-0" href="'.esc_url(home_url('/')).$Data->Anno."/".$Data->Mese."/?tag=".get_query_var('tag').'">
							<div class="row">
								<div class="col-10">	
									<span class="m-0">'.$mesi[$Data->Mese].'</span>
								</div>
								<div class="col-2 m-0">
									<span class="badge badge-pill badge-primary text-white">'.$Data->NumArt.'</span>
								</div>
							</div>
						</a>
					</li>';			  			
	}
	return $Result;
}
function count_PostTag($IDTag){
	$tag = get_tag($IDTag);
	return $tag->count;
}
$Args=array('hide_empty'=> 0,
   			'echo'         => 1,
    		'title_li'		=>"");
$Tags=get_tags($Args);
if(isset($search_query["year"])){
	$Anno=$search_query["year"];
}else{
	$Anno=0;
}
if(isset($search_query["monthnum"])){
	$Mese=$mesi[(int)$search_query["monthnum"]];
}else{
	$Mese=0;
}
?>
<section id="content" role="main" class="container-fluid mb-3">
   <div class="container">
      <div class="row">
		  <div class="col-12 col-lg-9">

		  	<header class="header">
				<h3 class="entry-title"><?php _e( 'Articoli con Tag', 'wpscuola' ); ?> <?php echo single_cat_title(). ($Mese!=0?" del mese di ".$Mese:"").($Anno!=0?" dell'anno ".$Anno:""); ?></h3>		  	
			</header>
		  	<div class="container">
		  		<?php
		  if ( have_posts() )
			  :
		  while ( have_posts() )
			  : the_post(); 
			  	if(get_post_type()!="circolari_scuola")
		  			get_template_part( 'template-parts/section', 'art_list-item' ); 
		  		else
		  			get_template_part( 'template-parts/section', 'circ_list-item' ); ?>
		  		<?php endwhile; endif; ?>
		  	</div>
<?php get_template_part( 'template-parts/pagin' ); ?>
		  </div>
		  <div class="col-12 col-lg-3 mt-5">
			  <div id="archcat" class="container">
				  <div class="link-list-wrapper shadow p-1">
				  	<div class="row">
						<div class="col-1">
							<a class="list-item" href="#ListaTag" data-toggle="collapse" aria-expanded="false" aria-controls="ListaTag">
								<span class="fas fa-angle-down espandi align-middle"></span>
							</a>
						</div>
						<div class="col-11 mb-0">
							<h3 class="h5"><?php _e( 'Etichette', 'wpscuola' ); ?></h3>
						</div>
					</div> 
					<ul class="link-list collapse" id="ListaTag">
		<?php foreach($Tags as $Tag) :?>
				  		<li>
							<div class="row">
								<div class="col-10 p-0 mb-0">
								  <a class="list-item medium pl-2" href="<?php echo get_tag_link($Tag->term_id); ?>" style="line-height: 1.5em;">
									<span class="m-0"><?php echo $Tag->name; ?></span>
								 </a>
								</div>
	 							<div class="col-2 m-0 p-0">
									<span class="badge badge-pill badge-primary text-white"><?php echo count_PostTag($Tag->term_id); ?></span>
								</div>
	 					  	</div>						
 					  	</li>
		<?php endforeach;?>
				  	</ul>
			   	</div>		  
				<div class="link-list-wrapper shadow p-1 mt-3">
				  	<div class="row">
						<div class="col-1 align-middle">
							<a class="list-item" href="#ListaTagData" data-toggle="collapse" aria-expanded="false" aria-controls="ListaTagData">
								<span class="fas fa-angle-down espandi align-middle"></span>
							</a>
						</div>
						<div class="col-11 mb-0">
							<h3 class="h5"><?php _e( 'Data di Pubblicazione', 'wpscuola' ); ?></h3>
						</div>
					 </div> 
					<ul class="link-list collapse" id="ListaTagData">				
						<?php echo my_get_tags_archives(); ?>
					</ul>
				</div>
			</div>
		  </div>     
		</div>
	</div>
</section>

<?php get_footer(); ?>