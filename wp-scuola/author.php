<?php
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			author.php
 * Description:       	Template della pagina dell'archivio degli autori che automaticamente crea l'archivio per data sulla destra per widht>767px, in basso per widht<768px 
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
get_header();

global $query_string;
wp_parse_str( $query_string, $search_query );



$mesi = array(1=>'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre','Dicembre');

function sum_ArtAnno($ArchivioDate,$Anno){
	$TotPost=0;
	foreach($ArchivioDate[$Anno] as $Mese=>$CountArt)
		$TotPost=$TotPost+$CountArt;
	return $TotPost;
}
function my_get_archivesAnnoMese($IDAuthor,$NameAuthor)
{
	global $wpdb, $wp_locale,$mesi,$query_string;
	$Result="";
	$Sql = "SELECT YEAR(post_date) AS `Anno`, MONTH(post_date) AS `Mese`, count(DISTINCT ID) as NumArt
				FROM $wpdb->posts
				WHERE $wpdb->posts.post_status = 'publish'
				AND $wpdb->posts.post_type = 'post'
				AND $wpdb->posts.post_author = ".$IDAuthor."
				GROUP BY YEAR(post_date), MONTH(post_date)
				ORDER BY YEAR(post_date), MONTH(post_date) DESC";
//			echo "<br />".$Sql;exit;
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
							<a class="list-item large" href="'.esc_url(home_url('/')).$Data->Anno.'/?author='.$NameAuthor.'">
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
						<a class="list-item subele pl-0" href="'.esc_url(home_url('/')).$Data->Anno."/".$Data->Mese.'/?author='.$NameAuthor.'">
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
$Anno=isset($search_query["year"])?$search_query["year"]:"";
$Mese=isset($search_query["monthnum"])?$mesi[(int)$search_query["monthnum"]]:"";
$Archivio=my_get_archivesAnnoMese(get_the_author_meta("ID"),get_the_author_meta("user_login"));
?>
<section id="content" role="main" class="container-fluid">
   <div class="container">
      <div class="row">
		  <div class="col-12">
		  	<header class="header">
				<h3 class="entry-title"><?php _e( 'Archivio articoli di', 'wpscuola' ); ?> <?php echo get_the_author_meta("display_name")." ".($Mese!=""?__( 'del mese di', 'wpscuola' )." ".$Mese:"").($Anno!=""?" ".__( 'dell\'anno', 'wpscuola' )." ".$Anno:""); ?></h3>
		  	</header>
		  	<div class="container">
<?php	if (have_posts() ):
			while ( have_posts() ):the_post(); 
		  		get_template_part( 'template-parts/section', 'art_list-item' );
			endwhile; 
		endif; ?>
		  	</div>
<?php get_template_part( 'template-parts/pagin' ); ?>
		  </div>
	  </div>
	</div>
</section>

<?php get_footer(); ?>