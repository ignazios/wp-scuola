<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			index.php
 * Description:       	Template della pagina del blog Tutti gli articoli
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
get_header();

function my_get_archivesAnnoMese()
{
	global $wpdb, $wp_locale,$mesi;
	$Result="";
	$Sql = "SELECT YEAR(post_date) AS `Anno`, MONTH(post_date) AS `Mese`, count(DISTINCT ID) as NumArt
				FROM $wpdb->posts
				WHERE $wpdb->posts.post_status = 'publish'
				AND $wpdb->posts.post_type = 'post'
				GROUP BY YEAR(post_date), MONTH(post_date)
				ORDER BY post_date DESC";
	//		echo "<br />".$Sql;exit;
	$ArchivioDate = $wpdb->get_results($Sql);
	$Anno=0;
	$ArticoliAnni=calc_NumArticoliMA($ArchivioDate);
	foreach ($ArchivioDate as $Data) {
		$Link=esc_url(home_url('/')).$Data->Anno."/".$Data->Mese."/?cat=".get_query_var('cat');
		$Des=$mesi[$Data->Mese]." ".$Data->Anno." (".$Data->NumArt.")";
		if ($Anno!=$Data->Anno){
			if ($Anno!=0) {
				$Result.='			</ul>
				</li>';
			}
			$Result.='
			<li>
					<div class="row">
						<div class="col-9 p-0 mb-0">
							<a class="list-item large" href="'.esc_url(home_url('/')).$Data->Anno.'">
								<span class="m-0">'.$Data->Anno.'</span>
							</a>						
						</div>
						<div class="col-1 m-0">
							<span class="badge badge-pill badge-primary text-white">'.$ArticoliAnni[$Data->Anno].'</span>
						</div>
						<div class="col-2 p-0">
						<a class="list-item" href="#M'.$Data->Anno.$Data->Mese.'" data-toggle="collapse" aria-expanded="false" aria-controls="'.'M'.$Data->Anno.$Data->Mese.'">
						<span class="fas fa-angle-down fa-2x"></span>
							</a>
						</div>
					</div>

					<ul class="link-sublist collapse" id="M'.$Data->Anno.$Data->Mese.'">';
			$Anno=$Data->Anno	;
		}
		$Result.='
					<li>
						<a class="list-item subele pl-0" href="'.esc_url(home_url('/')).$Data->Anno."/".$Data->Mese.'">
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
$Anno=$search_query["year"];
$Mese=$mesi[(int)$search_query["monthnum"]];
$Archivio=my_get_archivesAnnoMese();?>
<section id="content" role="main" class="container-fluid">
   <div class="container-fluid">
      <div class="row">
		  <div class="col-12 col-lg-9">
		  	<header class="header">
		  		<h3 class="entry-title"><?php _e( 'Archivio articoli', 'wpscuola' ); ?> <?php echo (isset($Mese)?__( 'del mese di', 'wpscuola' )." ".$Mese:"").(isset($Anno)?" ".__( 'dell\'anno', 'wpscuola' )." ".$Anno:""); ?></h3>
		  	</header>
		  	<div class="container">
<?php	if (have_posts() ):
			while ( have_posts() ):the_post(); 
		  		get_template_part( 'template-parts/section', 'art_list-item' );
			endwhile; 
		endif;
		wp_reset_postdata(); ?>
		  	</div>
<?php get_template_part( 'template-parts/pagin' ); ?>
		  </div>
		  <div class="col-12 col-lg-3">
			<div class="link-list-wrapper shadow p-1 mt-3">
			  	<div class="row">
					<div class="col-1 align-middle">
						<a class="list-item" href="#ListaCategorieData" data-toggle="collapse" aria-expanded="false" aria-controls="ListaCategorieData">
							<span class="fas fa-angle-down espandi align-middle"></span>
						</a>
					</div>
					<div class="col-11 mb-0">
						<h4 class="TitoloArchivio"><?php _e( 'Data di Pubblicazione', 'wpscuola' ); ?></h4>
					</div>
				 </div> 
				<ul class="link-list collapse" id="ListaCategorieData">				
					<?php echo $Archivio; ?>
				</ul>
			</div>
		</div>
	  </div>
	</div>
</section>
<?php get_footer(); ?>