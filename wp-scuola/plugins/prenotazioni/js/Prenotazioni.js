jQuery.noConflict();
(function($) {
	$(function() {		 
	$(document).tooltip({
            items: "th , td",
            content: function() {
                var element = $( this );
                if ( element.is( "th" ) ) {
                    var testo=$(this).attr("abbr");
                    var NewTesto="";
                    if (testo && testo.indexOf("img")>-1) {
                            var fine=testo.indexOf(")",testo.indexOf("img("));
                            var inizio=testo.indexOf("img(")+4;
                            //alert("Inizio "+inizio+" fine "+fine);
                            var img=testo.slice(inizio,fine);
                            NewTesto="<img src='"+img+"' /><br />"+testo.slice(fine+1,testo.length);
                    }else
                            NewTesto=testo;					
                    return NewTesto;
                }
                if ( element.is( "td" ) ) {
                    return element.attr("abbr");		
                }
            }
	});
	$("#HelpPrenotazioni").click(function() {
		$( "#dialog-help" ).dialog( {
			resizable: false,
			height:420,
			modal: true,
			width: 480,
			buttons: {Ok: function() {$( this ).dialog( "close" );}}
		});
	});
	$('#config-tabs-container').tabs();
  	$('#ColNonDisponibile').wpColorPicker();
   	$('#coloreRiservato').wpColorPicker();
	$('#colorePrenotato').wpColorPicker();
	$('#colorenonprenotabile').wpColorPicker();
	$.datepicker.setDefaults( $.datepicker.regional[ "it" ] );
	$('#preSelDay').datepicker({
            dateFormat: "dd/mm/yy",
            onClose: function(selectedDate) {
                if(selectedDate!=""){
                    $("#dataCal").text(selectedDate);
                    var eleData=selectedDate.split("/");
                    var giorni = new Array();
                    giorni[0] = "Domenica";
                    giorni[1] = "Lunedì";
                    giorni[2] = "Martedì";
                    giorni[3] = "Mercoledì";
                    giorni[4] = "Giovedì";
                    giorni[5] = "Venerdì";
                    giorni[6] = "Sabato";
                    var data=new Date();		
                    data.setFullYear(eleData[2],eleData[1]-1,eleData[0]);
                    $("#giornodataCal").text(giorni[data.getDay()]);
                    $.ajax({type: "post",url: "admin-ajax.php",data: { action: 'prenSpazi', data: $("#dataCal").text()},
                        beforeSend: function() {$("#loading").fadeIn('fast');}, 
                        success: function(html){$("#loading").fadeOut('fast');
                                  $("#tabPrenotazioniSpazi").html(html);
                                }
                    }); 
                }
         }
    });
    $('td.preset').click(function() { //start function when Random button is clicked
        var id='#v'+$(this).attr('id');
        switch($(id).attr('value')){
            case "1":
                $(this).css("background-color","#FFFFFF");
                $(id).attr('value',"0");	
                break;
            case "0":	
                $(this).css("background-color",$("#ColRiservato").attr('value'));
                $(id).attr('value',"1");
                break;
            case "3":
                alert("Ora non selezionabile");
                break;
        }
	});
    $('#CartellePrenotazioni').tabs();
    $(document).on('change', '#SelSettimana', function(){
        var Settimana=$('#SelSettimana').val();
        var Anno=$('#Anno').val();
        var Spazio=$('#Spazio').val();
        $.ajax({type: "post",url: "admin-ajax.php",
            data: { 
                  action: 'StampPrenSpazi',
                IdSpazio: Spazio, 
               Settimana: Settimana, 
                     Anno: Anno
                 },
        beforeSend: function() {$("#loading").fadeIn('fast');}, 
           success: function(html){$("#loading").fadeOut('fast');
                        $("#TabellaSettimanale").html(html);
                    }                 
        });        
    }); 
	$(document).on("click",".InfoPren",function() {
//		alert($( this ).attr('abr'));
        $('#dialog-confirm').html($( this ).attr('abr'));	
		$( "#dialog-confirm" ).dialog( {
			resizable: false,
			height:300,
			modal: true,
			width: 400,
			title: "Dati della Prenotazione" ,
			buttons: {Ok: function() {$( this ).dialog( "close" );}}
		});
    });
	$(document).on("click",".UserPren",function() {
		$('#dialog-confirm').html($( this ).attr('abr'));	
		$( "#dialog-confirm" ).dialog( {
			resizable: false,
			height:180,
			modal: true,
			width: 350,
			title: "Autore della Prenotazione" ,
			buttons: {Ok: function() {$( this ).dialog( "close" );}}
		});
	});

	$(document).on("click",".DelPren",function() {
    	var NumeroPrenotazione=$( this ).attr('id');
		var TestoDialogo='<p style="font-size: 12px;">Questa operazione cancellerà la prenotazione<br /> n° '+NumeroPrenotazione+'<br /> Sei sicuro di voler continuare?</p>';
		$('#dialog-confirm').html(TestoDialogo);	
		$("#dialog-confirm").dialog( {
			resizable: false,
			height:200,
			modal: true,
			width: 350,
			title: "Conferma Cancellazione",
			buttons: [ { text: "Cancella la prenotazione", 
                        click:function() {
                            $.ajax({type: "post",url: "admin-ajax.php",data: { action: 'delPren', id: NumeroPrenotazione},
	                            beforeSend: function() {$("#loading").fadeIn('fast');}, 
	            				success: function(html){
	                                $("#loading").fadeOut('fast');
	                            	$.ajax({type: "post",url: "admin-ajax.php",data: { action: 'prenSpazi', data: $("#dataCal").text()},
	                                     success: function(html){                                          
	                                            $("#tabPrenotazioniSpazi").html(html);
	                                     },
	                                    complete:function(html){$("#loading").fadeOut('fast');}
	                                }); 
                            	},
                            	error:function(html){alert(html);},
                            });   	
                            $(this).dialog( "close" ); }
                        } ,
                        { text: "Annulla", 
                         click: function() { $( this ).dialog( "close" ); } 
                        } 
					]
			});
		});
	$(document).click(function(e) { //start function when Random button is clicked
            if(e.target.className=="adminpreStyle"){
                PrenPrima=parseInt($("#MinOrePrima").attr("value"));
                var ID=e.target.id;
                eleID=ID.split("-");
                OraI= parseInt(eleID[0], 10);
                Spazio=parseInt(eleID[1], 10);
                var IdSpazio="#Spazio_"+Spazio;					
                var desSpazio=$(IdSpazio).text();
                var dataPren=$("#dataCal").text();
                var NMO=$("#NumMaxOre").attr("value");
                var Maxore=0;
                var IDNext="";
                do{
                        Maxore++;
                        IDNext="#"+(OraI+Maxore)+"-"+eleID[1];	
                }while ($(IDNext).attr('class')=="adminpreStyle" && Maxore<NMO);
                var Risposta="Ora Inizio "+OraI+" Spazio "+Spazio+" Nome "+desSpazio+" Num Max Ore"+Maxore;
                var Opzioni="";
                for(i=1;i<=Maxore;i++)
                        Opzioni+="<option value='"+i+"'>"+i+"</option>";
                $("#NumOrePren").html(Opzioni);
                $("#dataPre").text($("#dataCal").text());
                $("#InizioPre").text(OraI);
                $("#SpazioPre").text(desSpazio);
                var NOP=$("#NumOrePren"),
                    Note=$("#notePrenotazione"),
                    NSet=$("#NumeroSettimane"),
                    Sec=$("#secur"),
                    allFields = $( [] ).add( NOP ).add( Note );
                $("#dialog-form").dialog({
                        resizable: false,
                        height:420,
                        modal: true,
                        width: 440,
                        title: "Dati prenotazione",
                    buttons: {"Memorizza": function() {
                                $.ajax({type: "post",url: "admin-ajax.php",data: { action: 'newPren', 
                                                data: dataPren, 
                                                OraI: OraI, 
                                                 Ore: NOP.val(),
                                                NSet: NSet.val(), 
                                                 IdS: Spazio,
                                                Note: Note.val(),
                                            _wpnonce: Sec.val()},
                                beforeSend: function() {$("#loading").fadeIn('fast');}, 
                                success: function(html){
                                        $.ajax({type: "post",url: "admin-ajax.php",data: { action: 'prenSpazi', data: dataPren},
                                         success: function(html){$("#loading").fadeOut('fast');
                                                    $("#tabPrenotazioniSpazi").html(html);
                                                  }
                                        });

                var Testo=html.substr(html.indexOf("<p id='TestoRisMemo'>"),(html.length));
                $('#dialog-infonew').html(Testo);	
                $("#dialog-infonew").dialog( {
                        resizable: false,
                        height:600,
                        modal: true,
                        width: 600,
                        title: "Riepilogo Prenotazioni Effettuate",
                        buttons: [ { text: "Ok", 
                                                  click: function() { $( this ).dialog( "close" ); } 
                                            } 
                                         ]
                        });										
                                                       }
                                                });   	
                                        $( this ).dialog( "close" );},
                             Cancel: function() {$( this ).dialog( "close" );}
                                 },
                   close: function() {
                                allFields.val( "" );
                                $( this ).dialog( "close" );}
                });
        }	
		if(e.target.className=="CancMiaPren"){
			var NumeroPrenotazione=e.target.attributes['id'].value;
			var TestoDialogo='<p style="font-size: 12px;">Questa operazione cancellerà la prenotazione<br /> n° '+NumeroPrenotazione+'<br /> Sei sicuro di voler continuare?</p>';
			$('#dialog-confirm').html(TestoDialogo);	
			$("#dialog-confirm").dialog( {
				resizable: false,
				height:200,
				modal: true,
				width: 350,
				title: "Conferma Cancellazione",
				buttons: [ { text: "Cancella la prenotazione", 
							  click:function() {
							  	//alert("ci passo");
							  		$.ajax({type: "post",url: "admin-ajax.php",data: { action: 'delPren', id: NumeroPrenotazione},
										beforeSend: function() {$("#loading").fadeIn('fast');}, 
										success: function(html){location.reload();}
									});   	
							  		$( this ).dialog( "close" ); }
							 } ,
						     { text: "Annulla", 
							  click: function() { $( this ).dialog( "close" ); } 
						     } 
						 ]
			});
		}

/*		if(e.target.className=="HelpPrenotazioni"){
			$( "#dialog-help" ).dialog( {
				resizable: false,
				height:420,
				modal: true,
				width: 480,
				buttons: {Ok: function() {$( this ).dialog( "close" );}}
			});
		}
*/	});
	$( "#dispo-range" ).slider({
		range: true,
		min: 1,
		max: 24,
		values: [ $("#OI").val(), $("#OF").val()],
		slide: function( event, ui ) {
		$( "#dispo-valore-range" ).val( ui.values[ 0 ] + "-" + ui.values[ 1 ] );
		$("#OI").val(ui.values[ 0 ]);
		$("#OF").val(ui.values[ 1 ]);
		}
	});
	$( "#dispo-valore-range" ).val( $( "#dispo-range" ).slider( "values", 0 ) +"-"+ $( "#dispo-range" ).slider( "values", 1 ) );
	$( "#max-ore-range" ).slider({
		value:$( "#max-ore-valore" ).val(),
		range: "min",
		min: 1,
		max: 6,
		step: 1,
		slide: function( event, ui ) {
			$( "#max-ore-valore" ).val( ui.value );
		}
	});
	$( "#max-ore-valore" ).val( $( "#max-ore-range" ).slider( "value" ) );
	$( ".XX" ).click(function() {
		$("#preSelDay").datepicker( "show" );
	});
	$( ".navGiorni" ).click(function() {
		var giorni = new Array();
     		giorni[0] = 'Domenica';
     		giorni[1] = 'Lunedì';
		     giorni[2] = 'Martedì';
		     giorni[3] = 'Mercoledì';
		     giorni[4] = 'Giovedì';
		     giorni[5] = 'Venerdì';
		     giorni[6] = 'Sabato';
		var eleData=$("#dataCal").text();
		eleData=eleData.split("/");
		var inc=1;
		if ($(this).attr('data-op')=='<<')
			inc=-1;
		var day = 1000 * 60 *60 * 24;
		var data=new Date();
		data.setFullYear(parseInt(eleData[2], 10));
		data.setMonth(parseInt(eleData[1], 10)-1);
		data.setDate(parseInt(eleData[0], 10));
		var newData=new Date(data.getTime()+(day*inc));
		var gg=newData.getDate();
		if(gg<10)
			gg='0'+gg;
		var mm=newData.getMonth()+1;
		if (mm<10)
			var mm='0'+mm;
		var NuovaData=gg+'/'+mm+'/'+newData.getFullYear();
		$("#giornodataCal").text(giorni[newData.getDay()]);
		$("#dataCal").text(NuovaData);
		$("#dataCalVal").attr('value',NuovaData);
		$.ajax({type: "post",url: "admin-ajax.php",data: { action: 'prenSpazi', data: $("#dataCalVal").attr('value')},
			beforeSend: function() {$("#loading").fadeIn('fast');}, 
			success: function(html){$("#loading").fadeOut('fast');
									$("#tabPrenotazioniSpazi").html(html);
							  	   }
		}); 
		return true;
		});
	});
})(jQuery);
