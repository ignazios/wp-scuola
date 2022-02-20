<?php 
/**
 * @wordpress-theme
 * Theme Name:       	WPScuola
 * File Name:			category.php
 * Description:       	Template della pagina che automaticamente crea l'archivio delle categorie (sulla destra per widht>767px o in basso per widht<768px ) in una sidebar con 
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

function my_get_archives()
{
	global $wpdb, $wp_locale,$mesi;
	$Result="";

	$cate=get_categories('child_of='.get_query_var('cat'));
	$CateDef="";
	if (get_query_var('cat'))
		$CateDef=get_query_var('cat').",";
	$C="(".$CateDef;
	foreach ($cate as $catx) {
		$C.=$catx->term_id.",";
	}
	$C=substr($C,0,strlen($C)-1).")";
	$Sql = "SELECT YEAR(post_date) AS `Anno`, MONTH(post_date) AS `Mese`, count(DISTINCT ID) as NumArt
FROM $wpdb->posts, $wpdb->term_taxonomy, $wpdb->term_relationships
WHERE $wpdb->posts.post_status = 'publish'
AND $wpdb->posts.post_type = 'post'
AND $wpdb->term_taxonomy.term_id in ".$C."
AND $wpdb->posts.ID = $wpdb->term_relationships.object_id
AND $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
GROUP BY YEAR(post_date), MONTH(post_date)
ORDER BY YEAR(post_date), MONTH(post_date) DESC";
//			echo "<br />".$Sql;die();
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
					<div class="col-1">
						<a class="list-item" href="'."#Cat".get_query_var('cat').'M'.$Data->Mese.'" data-toggle="collapse" aria-expanded="false" aria-controls="Cat'.get_query_var('cat').'M'.$Data->Mese.'">
							<span class="fas fa-angle-down espandi align-middle"></span>
						</a>							
					</div>
					<div class="col-9 mb-0">
						<a class="list-item large" href="'.esc_url(home_url('/')).$Data->Anno."/?cat=".get_query_var('cat').'"><span class="m-0"><strong>'.$Data->Anno.'</strong></span>
						</a>						
					</div>
					<div class="col-2 m-0 p-0">
						<span class="badge badge-pill badge-primary text-white">'.$ArticoliAnni[$Data->Anno].'</span>
					</div>
		  		</div>			
				<ul class="link-sublist collapse" id="Cat'.get_query_var('cat').'M'.$Data->Mese.'">';
			$Anno=$Data->Anno	;
		}
		$Result.='
					<li>
						<div class="row">
							<div class="col-10">
								<a class="list-item medium pl-0 pt-2 pb-2" href="'.esc_url(home_url('/')).$Data->Anno."/".$Data->Mese."/?cat=".get_query_var('cat').'"><span class="m-0">'.$mesi[$Data->Mese].'</span>
								</a>
							</div>
							<div class="col-2 m-0">
								<span class="badge badge-pill badge-primary text-white">'.$Data->NumArt.'</span>
							</div>
						</div>

					</li>';			  			
	}
	return $Result;
}

function count_PostCategory($IDCategory){
	$category = get_category($IDCategory);
	return $category->category_count;
}
function make_category($ID_Categoria,$NomeCategoria,$PL=true){
	$CatFiglie=get_categories(array( 'parent' => $ID_Categoria,'hide_empty'	=> False ));
					  if (count($CatFiglie)==0):
?> 
						  <div class="row">
						  	<div class="col-1"></div>
							<div class="col-9 p-0 mb-2">
								  <a class="list-item medium pl-2 pt-2" href="<?php echo get_category_link($ID_Categoria); ?>">
 									<span class="m-0"><?php echo $NomeCategoria; ?></span> 
 								  </a>
							  	</div>
							  <div class="col-2 m-0 p-0">
								  <span class="badge badge-pill badge-primary text-white m-0"><?php echo count_PostCategory($ID_Categoria); ?></span>
							  </div>
						 </div>
<?php 				  else : ?>	
						<div class="row">
							<div class="col-1">
								<a class="list-item pt-1" href="#SubCat<?php echo $ID_Categoria; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="<?php echo $ID_Categoria; ?>">							
								<span class="fas fa-angle-down espandi align-middle"></span>
								</a>
							</div>
							<div class="col-9 p-0 mb-0">
							  <a class="list-item medium pl-2 pt-2" href="<?php echo get_category_link($ID_Categoria); ?>" style="line-height: 1.5em;">				  
								<span class="m-0"><?php echo $NomeCategoria; ?></span>
							 </a>
							</div>
 							<div class="col-2 m-0 p-0">
								<span class="badge badge-pill badge-primary text-white"><?php echo count_PostCategory($ID_Categoria); ?></span>
							</div>
 					  	</div>
			  			<ul class="link-sublist collapse" id="SubCat<?php echo $ID_Categoria; ?>">
<?php	  foreach ($CatFiglie as $CategoriaFiglia){ 
		 	  make_category($CategoriaFiglia->term_id,$CategoriaFiglia->name,FALSE);
		  }?>
		  			</ul>
<?php 	  			endif; 
}

$Args=array('hide_empty'=> 0,
   			'echo'         => 1,
    		'title_li'		=>"",
    		'parent' => 0,
    		'hierarchical'	=>false);
$Categorie=get_categories($Args);
$Anno=isset($search_query["year"])?$search_query["year"]:0;
$Mese=isset($search_query["monthnum"])?$mesi[(int)$search_query["monthnum"]]:0;
?>
<section id="content" role="main" class="container-fluid mb-3">
   <div class="container">
      <div class="row">
		  <div class="col-12 col-lg-9">
		  	<header class="header">
		  		<h2 class="entry-title"><?php _e( 'Articoli della Categoria', 'wpscuola' ); ?> <?php echo single_cat_title()." ". ($Mese!=0?__( 'del mese di', 'wpscuola' )." ".$Mese:"").($Anno!=0?" ".__( 'dell\'anno', 'wpscuola' )." ".$Anno:""); ?></h2>
		  	</header>
		  	<div class="container">
<?php
		if ( have_posts() ):
			while ( have_posts() ): the_post();
				get_template_part( 'template-parts/section', 'art_list-item' ); 
			endwhile; 
		endif; 
		wp_reset_postdata();?>
		  	</div>
<?php get_template_part( 'template-parts/pagin' ); ?>
		  </div>
		  <div class="col-12 col-lg-3 mt-5">
			  <div id="archcat" class="container">
				  <div class="link-list-wrapper shadow p-1">
				  	<div class="row">
						<div class="col-1">
							<a class="list-item" href="#ListaCategorie" data-toggle="collapse" aria-expanded="false" aria-controls="ListaCategorie">
								<span class="fas fa-angle-down espandi align-middle"></span>
							</a>
						</div>
						<div class="col-11 mb-0">
							<h3 class="h5"><?php _e( 'Argomenti', 'wpscuola' ); ?></h3>
						</div>
					</div> 
					<ul class="link-list collapse" id="ListaCategorie">
		<?php foreach($Categorie as $Categoria) :?>
				  		<li>
			  			<?php make_category($Categoria->term_id,$Categoria->name);?>
						</li>
		<?php endforeach;?>
				  	</ul>
			   	</div>		  
				<div class="link-list-wrapper shadow p-1 mt-3">
				  	<div class="row">
						<div class="col-1 align-middle">
							<a class="list-item" href="#ListaCategorieData" data-toggle="collapse" aria-expanded="false" aria-controls="ListaCategorieData">
								<span class="fas fa-angle-down espandi align-middle"></span>
							</a>
						</div>
						<div class="col-11 mb-0">
							<h3 class="h5"><?php _e( 'Data di Pubblicazione', 'wpscuola' ); ?></h3>
						</div>
					 </div> 
					<ul class="link-list collapse" id="ListaCategorieData">				
						<?php echo my_get_archives(); ?>
					</ul>
				</div>
			</div>
		  </div>     
		</div>
	</div>
</section>

<?php get_footer(); ?>