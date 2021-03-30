 <div class="row metarticoli">
	<div class="col-12 border-bottom border-primary">
		<h2 class="Titolo">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2><?php edit_post_link(); ?>
		<p class="text-muted">
<?php if(get_theme_mod('scuola_MTdata_attiva')):?>
			<span class="fas fa-calendar-alt"></span> 
<?php 	if(get_theme_mod('scuola_MTdataLink_attiva')){?>
			<a href="<?php echo esc_url(home_url('/')).get_the_time('Y')."/".get_the_time('m');?>"><?php the_time('j M y'); ?></a>
<?php 	}else{
			the_time('j M y');
	  	}
	  	endif;	  
	  	if(get_theme_mod('scuola_MTautore_attiva')):?>						
			<span class="fas fa-user-edit"></span> 
<?php 	if(get_theme_mod('scuola_MTautoreLink_attiva')){?>	  		
	  		<a href="<?php echo esc_url(home_url('/'))."author/".get_the_author_meta('user_nicename');?>"><?php the_author_meta('display_name');?></a>
<?php 	}else{
			the_author_meta('display_name');
	  	} ?>
		   <br />
<?php	
	  	endif; 
	    $postcats = get_the_category();
			if ($postcats) :?>
			<span class="fas fa-hashtag" title="categorie"></span> <?php echo  get_the_category_list( " , ","",$post->ID ); ?>
			<br />
<?php		endif;
			$posttags = get_the_tags ();
			if ($posttags) :?>
			<span class="fas fa-tags" title="tags"></span> <?php echo get_the_tag_list('<span class="listCatTag">',', ','</span>'); ?>
<?php		endif;?>
		</p>
<?php if ( has_post_thumbnail() ) {?>
		<div class="row pb-2">
			<div class="col-2">
<?php			the_post_thumbnail('full');?>
			</div>
			<div class="col-10 mb-0">
				<?php the_excerpt(); ?>
			</div>
		</div> 
<?php }else{ ?>
		<p><?php the_excerpt(); ?></p>
<?php } ?>
	</div>
</div>
				