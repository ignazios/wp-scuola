<?php
function my_get_terms_dropdown($taxonomies, $args){
    $myterms = get_terms($taxonomies, $args);
    $optionname = "tipologie";
    $output ="<select style='width: 100px;' name='".$optionname."'><option value=''>Filtra</option>'";

    foreach($myterms as $term){
        $term_taxonomy=$term->YOURTAXONOMY; //CHANGE ME
        $term_slug=$term->slug;
        $term_name =$term->name;
        $link = $term_slug;
        $output .="<option name='".$link."' value='".$link."'>".$term_name."</option>";
    }
    $output .="</select>";
return $output;
}
$taxonomies = array('tipologie'); // CHANGE ME
$args = array('order'=>'ASC','hide_empty'=>true);
?>
<div class="container-fluid mb-5">
	<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
	   		<input type="hidden" name="post_type" value="amm-trasparente" />
	<div class="row">
		<div class="col-md-9 col-8">
 			<input type="text" name="s" placeholder="Cerca..." />
    	</div>
 		<div class="col-md-1 col-4">
    		<button class="btn btn-primary text-center" type="submit" id="searchsubmit" value="Cerca"><span class="fas fa-search"></span></button>
    	</div>
	  	<div class="col-md-2">
			<?php echo my_get_terms_dropdown($taxonomies, $args);?>
		</div>
	</div>
	</form>
</div>