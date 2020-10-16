<?php 
 /**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			archive.php
 * Description:       	Template della pagina dell'archivio per i custom post type circolari_scuola creata dal modulo Gestione Circolari. 
 *                      Il template riporta l'archivio per data sulla destra per widht > 767px, in basso per widht < 768px 
 * Author:            	Ignazio Scimone
 * Author URI:        	http://www.eduva.org/
 * License: 			AGPL-3.0
 * License URI: 		https://opensource.org/licenses/AGPL-3.0
 * Text Domain:       	wpscuola
*/
 
global $query_string,$mesi;
wp_parse_str( $query_string, $search_query );

if(isset($search_query['author'])){
	echo '<meta http-equiv="refresh" content="0;URL='.esc_url(home_url('/'))."author/".$search_query['author'].'">';
	die();
} 
get_header();

function my_get_archivesAnnoMese()
{
	global $wpdb, $wp_locale,$mesi;
	$Result="";
	$Sql = "SELECT YEAR(post_date) AS `Anno`, MONTH(post_date) AS `Mese`, count(DISTINCT ID) as NumArt
				FROM $wpdb->posts
				WHERE $wpdb->posts.post_status = 'publish'
				AND $wpdb->posts.post_type = 'circolari_scuola'
				GROUP BY YEAR(post_date), MONTH(post_date)
				ORDER BY post_date DESC";
	//		echo "<br />".$Sql;exit;
	$ArchivioDate = $wpdb->get_results($Sql);
	$Anno=0;
	$ArticoliAnni=calc_NumArticoliMA($ArchivioDate);
	foreach ($ArchivioDate as $Data) {
//		$Link=esc_url(home_url('/')).'/circolari_scuola/'.$Data->Anno."/".$Data->Mese;
//		$Des=$mesi[$Data->Mese]." ".$Data->Anno." (".$Data->NumArt.")";
		if ($Anno!=$Data->Anno){
			if ($Anno!=0) {
				$Result.='			</ul>
				</li>';
			}
			$Result.='
			<li>
				<div class="row">
					<div class="col-1">
						<a class="list-item" href="#M'.$Data->Anno.$Data->Mese.'" data-toggle="collapse" aria-expanded="false" aria-controls="'.'M'.$Data->Anno.$Data->Mese.'"><span class="fas fa-angle-down  espandi align-middle"></span>
						</a>					
					</div>				
					<div class="col-9 p-0 mb-0">
						<a class="list-item large" href="'.esc_url(home_url('/')).'circolari_scuola/'.$Data->Anno.'">
							<span class="m-0">'.$Data->Anno.'</span>
						</a>						
					</div>

					<div class="col-2 m-0 p-0">
						<span class="badge badge-pill badge-primary text-white">'.$ArticoliAnni[$Data->Anno].'</span>
					</div>
				</div>

				<ul class="link-sublist collapse" id="M'.$Data->Anno.$Data->Mese.'">';
		$Anno=$Data->Anno	;
	}
	$Result.='
				<li>
					<a class="list-item subele pl-0" href="'.esc_url(home_url('/')).'circolari_scuola/'.$Data->Anno."/".$Data->Mese.'">
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
$Anno=(isset($search_query["year"])? $search_query["year"]:"");
$Mese=(isset($search_query["monthnum"])? $mesi[(int)$search_query["monthnum"]]:"") ;
if (!$Anno) $Anno=date("Y");
if (!$Anno And !$Mese) $Mese=$mesi[(int)date("n")];
$Archivio=my_get_archivesAnnoMese();
 $gruppiutenti=get_terms('gruppiutenti', array('hide_empty' => false));
 $Gruppi = array();
 wps_sort_terms_hierarchicaly($gruppiutenti, $Gruppi);
// echo "<pre>";print_r($Gruppi);echo "</pre>";wp_die();
?>
<section id="content" role="main" class="container">
   <div class="container">
      <div class="row">
		  <div class="col-12 col-lg-9">
		  	<header class="header">
		  		<h3 class="entry-title"><?php _e( 'Circolari di:', 'wpscuola' ); ?> <?php echo $Mese." ".$Anno; ?></h3>
		  	</header>
		  	<div class="container">
<?php
		if (have_posts() ):
			while ( have_posts() ): the_post(); 
				get_template_part( 'template-parts/section', 'circ_list-item' ); 
			endwhile; 
		endif; 
		wp_reset_postdata();?>
		  	</div>
<?php get_template_part( 'template-parts/pagin' ); ?>
		  </div>
		  <div class="col-12 col-lg-3">
		  	<div class="container">
			  	<div class="link-list-wrapper shadow p-1">
				  	<div class="row">
						<div class="col mb-0">
							<h3 class="h5"><span class="fas fa-users"></span> <?php _e( 'Destinatari', 'wpscuola' ); ?></h3>
						</div>
					</div> 
					<div class="row">
						<div class="col ml-3" id="archdest">	
							<ul class="link-list">
				<?php foreach($Gruppi as $Gruppo):?>
						  		<li>
					  			<a class="list-item" href="<?php echo esc_url(home_url('/')).'destinatari/'.$Gruppo->name;?>"><?php echo $Gruppo->name;?></a>
					  	<?php	if ($Gruppo->children){
					  				$SottoGruppi=$Gruppo->children;?>
						  			<ul>
						  			<?php foreach($SottoGruppi as $SottoGruppo):?>
						  				<li>
						  				<a class="list-item" href="<?php echo esc_url(home_url('/')).'destinatari/'.$SottoGruppo->name;?>"><?php echo $SottoGruppo->name;?></a>
						  				</li>
						  			<?php endforeach;?>
						  			</ul>
						  		<?php } ?>
								</li>
				<?php endforeach;?>
						  	</ul>
						</div>
			   		</div>		
			   	</div>  
				<div class="link-list-wrapper shadow p-1 mt-3"  id="archcat">
				  	<div class="row">
						<div class="col-1 align-middle">
							<a class="list-item" href="#ListaCategorieData" data-toggle="collapse" aria-expanded="false" aria-controls="ListaCategorieData">
								<span class="fas fa-angle-down  espandi align-middle"></span>
							</a>
						</div>
						<div class="col-11 mb-0">
							<h6><?php _e( 'Data di Pubblicazione', 'wpscuola' ); ?></h6>
						</div>
					 </div> 
					<ul class="link-list collapse" id="ListaCategorieData">				
						<?php echo $Archivio; ?>
					</ul>
				</div>
			</div>
		</div>
	  </div>
	</div>
</section>
<?php get_footer(); ?>