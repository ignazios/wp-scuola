 <?php
 	$IDCircolare=get_the_ID();
 	$numero=get_post_meta($IDCircolare, "_numero",TRUE);
	$anno=get_post_meta($IDCircolare, "_anno",TRUE);
	$Elenco=wps_GetEencoDestinatari($IDCircolare);
 ?>
 <div class="row">
	<div class="col-12 border-bottom border-primary">
		<h3 class="Titolo">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
		<p class="text-muted">
			<i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y',strtotime(get_the_date()));?> <strong> <i class="fas fa-user-edit"></i> <a href="<?php echo esc_url(home_url('/'))."author/".get_the_author_meta('user_nicename');?>"><?php the_author_meta('display_name');?></a><br />
			<i class="fas fa-ticket-alt"></i> <?php echo $numero."_".$anno;?></strong> <span class="users"><i class="fa fa-users" aria-hidden="true"></i> <?php echo $Elenco;?>
<?php	
	if (wps_Is_Circolare_Da_Firmare($IDCircolare)){?>
<?php		if (!wps_Is_Circolare_Firmata($IDCircolare)) {
				$ngiorni=wps_Get_scadenzaCircolare($IDCircolare,"",True);					
				if(wps_Is_Circolare_Scaduta($IDCircolare)){
					echo' <i class="fa fa-pencil" aria-hidden="true"></i> 
						<p>
							<span class="rounded bg-danger text-white p-2 text-center">'.__("Scaduta e non Firmata","wpscuola").'</span>
						</p>';						
				}else{
					switch ($ngiorni){
						case -1:							
							$entro="";							
							break;													
						case 0:
							$entro="entro OGGI";
							break;
						case 1:
							$entro="entro DOMANI";
							break;
						default:
							$entro="entro $ngiorni giorni";
							break;
					}
					$sign=get_post_meta($IDCircolare, "_sign",TRUE);
					if ($sign!="Firma")
						$Tipo="Esprimere adesione $entro";
					else
						$Tipo="Firmare $entro";
					echo' <i class="fa fa-pencil" aria-hidden="true"></i> 
						<p>
							<span class="rounded bg-danger text-white p-2 text-center">'.$Tipo.'</span>
						</p>';	
			}			
		}else{
			echo' <i class="fa fa-pencil" aria-hidden="true"></i> 
						<p>
							<span class="rounded bg-primary text-white p-2 text-center">'.__("Firmata","wpscuola").'</span>
						</p>';			
		} 
	}?>
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
				