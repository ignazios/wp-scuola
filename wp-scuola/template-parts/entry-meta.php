<section class="entry-meta">
	<div class="row metarticoli p-2">
<?php if(get_theme_mod('scuola_MTdata_attiva')):?>
		<span class="fas fa-calendar-alt pr-2" title="<?php _e('Data ultima modifica','wpscuola');?>"></span> 
<?php 	if(get_theme_mod('scuola_MTdataLink_attiva')){?>
		<a href="<?php echo esc_url(home_url('/')).get_the_time('Y')."/".get_the_time('m');?>"><?php the_time('j M y'); ?></a>
<?php 	}else{
			the_time('j M y');
	  	}
	  endif;
	  if(get_theme_mod('scuola_MTautore_attiva')):?>
		<span class="fas fa-user-edit pr-2 pl-2" title="<?php _e("Autore dell'articolo",'wpscuola');?>"></span> 
<?php 	if(get_theme_mod('scuola_MTautoreLink_attiva')){?>
    	  <a href="<?php echo esc_url(home_url('/'))."author/".get_the_author_meta('user_nicename');?>"><?php the_author_meta('display_name'); ?></a>
<?php 	}else{
			the_author_meta('display_name');
	  	} 	
	  endif;
      if(get_theme_mod('scuola_docconteggio_attiva')):?>
		<span class="far fa-eye pr-2 pl-2" title=<?php _e("Visualizzazioni",'wpscuola');?>> <?php echo scuola_get_post_view();?></span>
<?php 	endif;
	   $postcats = get_the_category();
		if ($postcats) :?>
		<span class="fas fa-hashtag pr-2 pl-2" title="<?php _e("Categorie",'wpscuola');?>"></span><?php echo  get_the_category_list( " , ","",get_the_ID() ); 
	endif;
		$posttags = get_the_tags ();
		if ($posttags) :?>	
		<span class="fas fa-tags pr-2 pl-2" title=<?php _e("Tags",'wpscuola');?>></span> <?php echo get_the_tag_list('<span class="listCatTag">',', ','</span>');
	endif;?>
	</div>
	
	<!-- <span class="meta-sep"> | </span>
	<span class="author vcard"><?php the_author_posts_link(); ?></span> -->
</section>