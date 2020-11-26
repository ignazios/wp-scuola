(function( $ ) {
	'use strict';
		$(document).delegate('#OrarioDocente', 'click',function(){
			$.ajax({type: 'POST',
                url: ajaxurl, 
                data:{
                    security:wpscuolajaxsec,
                    action:'GetOrarioDocente',
                    IDDocente:$( "#ElencoDocenti" ).val(),
                 },
                beforeSend: function() {
                },
                success: function(risposta){
                     $("#TabellaOrario").html(risposta);
                },                   
                error: function(error) { 
                },
           }); 
		});	
		$(document).delegate('#OrarioClasse', 'click',function(){
			$.ajax({type: 'POST',
                url: ajaxurl, 
                data:{
                    security:wpscuolajaxsec,
                    action:'GetOrarioClasse',
                    IDClasse:$( "#ElencoClassi" ).val(),
                 },
                beforeSend: function() {
                },
                success: function(risposta){
                     $("#TabellaOrario").html(risposta);
                },                   
                error: function(error) { 
                },
           }); 
		});	
		$(document).delegate('#OrarioRicevimentoDocenti', 'click',function(){
			$.ajax({type: 'POST',
                url: ajaxurl, 
                data:{
                    security:wpscuolajaxsec,
                    action:'GetOrarioRicevimentoClasse',
                    IDClasse:$( "#ElencoClassi" ).val(),
                 },
                beforeSend: function() {
                },
                success: function(risposta){
                     $("#TabellaOrario").html(risposta);
                },                   
                error: function(error) { 
                },
           }); 
		});			
		$(document).delegate('#OrarioStruttura', 'click',function(){
			$.ajax({type: 'POST',
                url: ajaxurl, 
                data:{
                    security:wpscuolajaxsec,
                    action:'GetOrarioStruttura',
                    IDStruttura:$( "#ElencoStrutture" ).val(),
                 },
                beforeSend: function() {
                },
                success: function(risposta){
                     $("#TabellaOrario").html(risposta);
                },                   
                error: function(error) { 
                },
           }); 
		});
})( jQuery );
