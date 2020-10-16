<?php	global $figli;
		if(substr_count($figli, '<li')>5){?>
			<div class="container">
<?php	}else{?>
			<div class="container affix-top">
<?php	}?>				
				<div class="link-list-wrapper shadow p-1">
				  	<div class="row p-2">
						<h3 class="h5"><span class="far fa-newspaper"></span> Navigazione Pagine</h3>
					</div> 
					<ul class="link-list" id="ListaPagine">
		<?php global $figli;
/*			  foreach($figli as $figlio) :?>
				  		<li>
				  			<a class="list-item medium pl-2" href="<?php echo get_permalink($figlio->ID); ?>">
 								<span class="m-0"><?php echo $figlio->post_title; ?></span> 
 							</a>
						</li>
		<?php endforeach;?>
*/				echo $figli;?>
				  	</ul>
			   	</div>
			   </div>	