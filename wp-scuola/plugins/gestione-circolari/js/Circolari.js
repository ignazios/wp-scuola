jQuery.noConflict();
(function($) {
    $(function() {	
         $('#NavPag_Circolari').keypress(function() 
            {
			window.location = $('#UrlNavPagCircolari').attr('value')+$('#NavPag_Circolari').attr('value');
            });
    $( "#DataScadenza" ).datepicker({ 
        minDate: new Date($("#aa").attr('value'),$("#mm").attr('value')-1,$("#jj").attr('value')), 
        dateFormat: 'dd/mm/yy' 
    });
    var l = window.location;
    var url = l.protocol + "//" + l.host +"/"+  l.pathname.split('/')[0]+"/wp-content/plugins/gestione-circolari/swf/copy_csv_xls_pdf.swf";
     var ColOrder=$('#ColOrd').attr('sorted');
    if (ColOrder<0){
            ColOrder=ColOrder*(-1);
            OrdOrder='desc';
    }else{
            OrdOrder='asc';
    }
    $('#TabellaCircolari').dataTable( {
          dom: 'T<"clear">lfrtip',
          order: [],
          pageLength: 25,
          tableTools: {
         	"sSwfPath": url,
          	"aButtons": [ 
          		{
					"sExtends": "copy",
					"sButtonText": "Copia negli Appunti"
				},
          		{
					"sExtends": "print",
					"sButtonText": "Stampa"
				},
				{
                    "sExtends":    "collection",
                    "sButtonText": "Salva",
                    "aButtons":    [ "csv", "xls",                 
                    {
                    	"sExtends": "pdf",
                    	"sPdfOrientation": "landscape",
                    	"sPdfMessage": "Tabella generata con il plugin Gestione Circolari."
                	},]
                }
			]
         },
        language:{
		    "sEmptyTable":     "Nessun dato presente nella tabella",
		    "sInfo":           "Vista da _START_ a _END_ di _TOTAL_ elementi",
		    "sInfoEmpty":      "Vista da 0 a 0 di 0 elementi",
		    "sInfoFiltered":   "(filtrati da _MAX_ elementi totali)",
		    "sInfoPostFix":    "",
		    "sInfoThousands":  ",",
		    "sLengthMenu":     "Visualizza _MENU_ elementi",
		    "sLoadingRecords": "Caricamento...",
		    "sProcessing":     "Elaborazione...",
		    "sSearch":         "Cerca:",
		    "sZeroRecords":    "La ricerca non ha portato alcun risultato.",
		    "oPaginate": {
		        "sFirst":      "Inizio",
		        "sPrevious":   "Precedente",
		        "sNext":       "Successivo",
		        "sLast":       "Fine"
		    },
		    "oAria": {
		        "sSortAscending":  ": attiva per ordinare la colonna in ordine crescente",
		        "sSortDescending": ": attiva per ordinare la colonna in ordine decrescente"
		    }
		}
    } );
	});
})(jQuery);
