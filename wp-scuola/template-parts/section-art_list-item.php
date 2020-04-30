 <div class="row">
	<div class="col-12 border-bottom border-primary">
		<h3 class="Titolo">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3><?php edit_post_link(); ?>
		<p class="text-muted">
			<i class="fas fa-calendar-alt"></i> <a href="<?php echo esc_url(home_url('/')).get_the_time('Y')."/".get_the_time('m');?>"><?php the_time('j M y'); ?></a>
			<i class="fas fa-user-edit"></i> <a href="<?php echo esc_url(home_url('/'))."author/".get_the_author_meta('user_nicename');?>"><?php the_author_meta('display_name');?></a><br />
<?php	    $postcats = get_the_category();
			if ($postcats) :?>
			<i class="fas fa-hashtag" title="categorie"></i> <?php echo  get_the_category_list( " , ","",$post->ID ); ?>
			<br />
<?php		endif;
			$posttags = get_the_tags ();
			if ($posttags) :?>
			<i class="fas fa-tags" title="tags"></i> <?php echo get_the_tag_list('<span class="listCatTag">',', ','</span>'); ?>
<?php		endif;?>
		</p>
<?php if ( has_post_thumbnail() ) {?>
		<div class="row">
			<div class="col-2">
<?php			the_post_thumbnail();?>
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
				