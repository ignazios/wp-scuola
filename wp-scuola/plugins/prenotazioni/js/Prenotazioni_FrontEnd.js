jQuery.noConflict();
(function($) {
	$(function() {
 	$(document).click(function(e) { //start function when Random button is clicked
		var IdEle=e.target.id;
		if(IdEle.slice(0, 13)=="adminpreStyle"){
			var OraI=parseInt(IdEle.substr(13,IdEle.length-13), 10);
			var Maxore=0;
			var IDNext="";
			var OI=$("#OraInizio").val();
			var OF=$("#OraFine").val();
			var NMO=$("#NumMaxOre").val();
			var ColSel=$("#ColPrenotato").val();
			var oldsel="#"+$("#OldSel").val();
			var oldselBottone="#adminpreStyle"+$("#OldSel").val();
			//$(oldsel).attr("style","background-color:#FFFFFF;");
			$(oldselBottone).attr("style","background-color:rgba(105,130,206,1);");
			e.target.style="background-color:"+ColSel;
			//$("#"+OraI).attr("style","background-color:"+ColSel+";");
			$("#OldSel").attr('value',OraI);
			$("#OraInizioPrenotazione").attr('value',OraI);
			$("#VisOraInizio").text(OraI);
			$("#NumOrePren").empty();
			do{
				Maxore++;
				IDNext="#"+(OraI+Maxore);
				$("#NumOrePren").append($('<option value="'+Maxore+'">'+Maxore+'</option>'));
			}while ($(IDNext).attr('class')=="adminpreStyle" && Maxore<NMO);		
//			alert("Inizio "+OraI+" Max ore pren "+Maxore);
//			alert($("#OraInizioPrenotazione").val());
		}
	});	
    $(document).on('change', '#SelSettimana', function(){
        var dati = $('#SelSettimana').val().split(";");
        var Settimana=dati[0];
        var Anno=dati[1];
        var Spazio=$('#Spazio').val();
 //       alert($("#UrlAjax").attr("value"));
 //       alert(Settimana);alert(Anno);alert(Spazio);
        $.ajax({type: "post",url: ajaxurl,
            data: { 
                  action: 'VisPrenSpazi',
                IdSpazio: Spazio, 
               Settimana: Settimana, 
                     Anno: Anno,
                    secur:ajaxsec,
                    para:para
                 },
	  beforeSend: function() {$("#loading").fadeIn('fast');}, 
         success: function(html){   		
                $("#TabellaSettimanale").html(html);
                $("#loading").fadeOut('slow');
            },                 
           error: function(html){
                $("#TabellaSettimanale").html("Errore");
                $("#loading").fadeOut('slow');
            }                 
        });        
    }); 
    
    $( "#SHInfo" ).click(function() {
      $( "#info" ).toggle( "fast" );
    });
    
    
    $( "#FormPrenotazione" ).submit(function( event ) {
    	var datai="";
    	var orei="";
    	var numo="";
    	var motivo="";
    	if($("#DataPrenotazione").val()==0){
      		datai="Devi Impostare la Data di Inizio della prenotazione\n";
		}
      	if($("#OraInizioPrenotazione").val()==0){
      		orei="Devi Impostare l'Ora di Inizio della prenotazione\n";
		}
      	if($("#NumOrePren").val()==0){
      		numo="Devi Impostare il numero di ore della prenotazione\n";
		}
		if($("#motivoprenotazione").val()==""){
      		motivo="Devi Impostare il Motivo della prenotazione\n";
		}
		if(datai=="" && orei=="" && numo=="" && motivo==""){
			return;
		}else{
			alert("Errori riscontrati\n"+datai+orei+numo+motivo);
			event.preventDefault();
		}
    });
    
    
    
    
    
    
    $( "#SpazioP" ).change(function() {
 		 $( "#SpazioP option:selected" ).each(function() {
			$("#imgSpazio").attr('src',$( this ).attr('data'));
				$.ajax({type: "post",url: $("#UrlAjax").attr("value"),data: { 
								action: 'FEprenSpazi', 
		                        data: $('#DataPrenotazione').val(), 
		                        spazio: $( "#SpazioP" ).val(),
		                        sorg: "FE"}, 
							beforeSend: function() {
								$("#loading").fadeIn('fast');
							}, 
							success: function(html){
								$("#InizioPre").html(html);
								$("#NumOrePren").empty();
								$("#NumOrePren").append($('<option value="0">----</option>'));
								$("#loading").fadeOut('fast');
							},
							error: function() {
          						alert('Error occurs! action: FEprenSpazi  data: '+selectedDate+' spazio: '+ $( "#SpazioP" ).attr("value")+' sorg: "FE"');
          						$("#loading").fadeOut('fast');
          					}
					});
		});
	});

	$.datepicker.setDefaults( $.datepicker.regional[ "it" ] );
	$('#DataPrenotazione').datepicker({
		firstDay: 1,
      	dateFormat: "dd/mm/yy",
      	onClose: function(selectedDate) {
        			if(selectedDate!=""){
        				//var Spazio=${"#SpazioP"}.attr('value');
 						$.ajax({type: "post",url: $("#UrlAjax").val() ,data: { 
 							action: 'FEprenSpazi', 
                            data: selectedDate, 
							spazio: $( "#SpazioP" ).val(),
							sorg: "FE"}, 
							beforeSend: function() {
								$("#loading").fadeIn('fast');
							}, 
							success: function(html){
								$("#NumOrePren").empty();
								$("#NumOrePren").append($('<option value="0">----</option>'));
								$("#InizioPre").html(html);
								$("#loading").fadeOut('fast');
							},
							error: function(xhrRequest, status, errorMessage)  {
	                       		alert("Errore dal server. Status: " + status);
	                    	}
						});
					}
         }
    });
 });
})(jQuery);
