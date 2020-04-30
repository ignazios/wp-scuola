			  <div class="container">
				  <div class="link-list-wrapper shadow p-1">
				  	<div class="row p-2">
						<h6 class="TitoloArchivio"><i class="far fa-newspaper"></i> Navigazione Pagine</h6>
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