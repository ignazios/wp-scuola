jQuery(document).ready(function($){

	$( "#mod_data_scadenza" ).datepicker({dateFormat : 'dd/mm/yy'});
 	$( "#data_scadenza" ).datepicker({dateFormat : 'dd/mm/yy'});
   	$( "#sortable" ).disableSelection();
	$("#sortable").sortable({ connectWith: '#ArticoliDisp',
		update: function(event, ui) {
				$('#loading-animation').show(); // Show the animate loading gif while waiting
	 
				opts = {
					url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
					type: 'POST',
					async: true,
					cache: false,
					dataType: 'json',
					data:{
						action: 'evidenza_comunicazioni_sort', // Tell WordPress how to handle this ajax request
						order: $('#sortable').sortable('toArray').toString() // Passes ID's of list items in	1,3,2 format
					},
					success: function(response) {
						$('#loading-animation').hide(); // Hide the loading animation
						return; 
					},
					error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
						alert('Si è verificato un errore durante la memorizzazione degli aggiornamenti');
						$('#loading-animation').hide(); // Hide the loading animation
						return; 
					}
				};
				$.ajax(opts);
			}
	});
	$( document ).on( "click",".buttonMod",function(){
		var IdComunicazione=parseInt(this.id.substr(9,(this.id.length)-9), 10);	
			$("#TitoloComunicazione").text("Comunicazione : "+$("#"+IdComunicazione).text());
			$("#dialog-form").dialog({
				resizable: false,
				height:300,
				modal: true,
				width: 400,
				title: "Data Scadenza visualizzazione Comunicazione",
				buttons: [ { text: "Memorizza le modifiche", 
				  click:function() {
						$.ajax({type: "post",url: "admin-ajax.php",data: { action: 'modscadenza', 
																			   id: IdComunicazione, 
																		  P15ULIC:$("#P15ULIC").attr("value"), 
																			 data:$("#mod_data_scadenza").attr("value")},
							success: function(html){
								$("#"+IdComunicazione).html(html);
							}
						});   	
						$( this ).dialog( "close" ); }
				 } ,
				 { text: "Annulla", 
				  click: function() { $( this ).dialog( "close" ); } 
				 } 
			 ]		 
						 
						 
			});
	});
			

});