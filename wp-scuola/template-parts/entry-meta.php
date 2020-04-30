<section class="entry-meta">
	<div class="row metarticoli p-2">
		<i class="fas fa-calendar-alt pr-2"></i> <?php the_time('j M y'); ?>
		<i class="fas fa-user-edit pr-2 pl-1"></i> <a href="<?php echo esc_url(home_url('/'))."author/".get_the_author_meta('user_nicename');?>"><?php the_author_meta('display_name'); ?></a>
	</div>
<?php	    $postcats = get_the_category();
			if ($postcats) :?>
	<div class="row metarticoli p-2">
		<i class="fas fa-hashtag pr-2" title="categorie"></i> <?php echo  get_the_category_list( " , ","",get_the_ID() ); ?>
	</div>
<?php		endif;
			$posttags = get_the_tags ();
			if ($posttags) :?>	
	<div class="row metarticoli p-2">
		<i class="fas fa-tags pr-2" title="tags"></i> <?php echo get_the_tag_list('<span class="listCatTag">',', ','</span>'); ?>
	</div>
<?php		endif;?>
	
	<!-- <span class="meta-sep"> | </span>
	<span class="author vcard"><?php the_author_posts_link(); ?></span> -->
</section>