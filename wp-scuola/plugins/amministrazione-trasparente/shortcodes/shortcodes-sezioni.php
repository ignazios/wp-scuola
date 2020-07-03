<?php

extract(shortcode_atts(array('col' => '1', 'bar' => '0', 'con' => '0'), $atts));

?>
<div class="container">
 	<div class="row shadow-sm p-3 mb-5 c-line analogue-1-bg-a1">
  		<div class="col-10">
  			<?php echo do_shortcode( '[at-search]' );?>
  		</div>
		<div class="col-2 text-monospace">
			<a href="<?php echo get_post_type_archive_link( 'amm-trasparente' );?>"><?php _e("Ultimi inseriti", 'wpscuola');?></a>
		</div>
  	</div>
<?php
$atcontatore = $atnumvoci=0;
foreach (amministrazionetrasparente_getarray() as $inner) {
	$atnumvoci++;
	if($atcontatore%2==0) {?>	<div class="row"><?php }	
    //  Scan through inner loop
    $atreturn = '<ul>';
    $atcounter = 0;
    foreach ($inner[1] as $value) {
        $args = array( 'taxonomy' => 'tipologie', 'term' => $value );
        $query = new WP_Query( $args );
        $fount_posts = $query->found_posts;
        $atcounter = $atcounter + $fount_posts;
        if ( !$fount_posts && at_option('opacity') ) {
            $opty = 'style="opacity: 0.5;"';
        } else { $opty = ''; }
        $atreturn .= '<li '.$opty.'>';
        $atreturn .= '<a href="' . get_term_link( get_term_by('name', $value, 'tipologie'), 'tipologie' ) . '" title="' . $value . '">' . $value . '</a>';
        $atreturn .= '</li>';
    }
    $atreturn .= '</ul>';
    if($atnumvoci<23){
?>			
		<div class="col-lg-6 col-12 col-sm-12 rounded p-2">
<?php }else{ ?>
		<div class="col rounded p-2">
<?php } ?>
		
				<h3 style="font-size: 1.4rem;"><a id="<?php echo $sez_l;?>" href="#<?php echo $sez_l;?>"><?php echo $inner[0];?></a>
    	<?php if ($con) ?>
    		 <span class="badge badge-primary"><?php echo$atcounter;?></span>
    		 	</h3>    	
    	<?php echo $atreturn;?>
    	</div>
    	<?php if($atcontatore%2==1) {?></div><?php }
	    $atcontatore++;	
}

if ( at_option('show_love') ) {
    echo '<span style="width:98%;border: 1px solid #eee;padding: 8px 10px;background: #FBFBFB;float: left;font-size: 0.7em;">
        <span style="float:right;">
            <a href="http://www.wpgov.it" target="_blank" alt="Software WPGov" title="Software WPGov">wpgov.it</a>
        </span>
        Powered by <a href="http://wordpress.org/plugins/amministrazione-trasparente/" rel="nofollow" title="'.__("Plugin Amministrazione Trasparente per Wordpress", 'wpscuola').'">'.__("Amministrazione Trasparente", 'wpscuola').'</a>
        </span>';
}
?>
</div>