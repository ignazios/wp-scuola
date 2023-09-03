jQuery.noConflict();
(function($) {
    $(function() {
    	$('[data-toggle="tooltip"]').tooltip();	
         $('#NavPag_Circolari').keypress(function() 
            {
			window.location = $('#UrlNavPagCircolari').attr('value')+$('#NavPag_Circolari').attr('value');
            });
		$( "#DataScadenza" ).datepicker({ 
			minDate: new Date($("#aa").attr('value'),$("#mm").attr('value')-1,$("#jj").attr('value')), 
			dateFormat: 'dd/mm/yy' 
		});
		var l = window.location;
		var ColOrder=$('#ColOrd').attr('sorted');
		if (ColOrder<0){
				ColOrder=ColOrder*(-1);
				OrdOrder='desc';
		}else{
				OrdOrder='asc';
		}
		$('#TabellaCircolari').dataTable( {
			dom: 'Bfrtip',
			buttons: [
				{
					extend: 'copy',
					text: '<i class="fa-solid fa-copy"></i> Copia negli Appunti',
 				},
				{
					extend: 'excelHtml5',
					autoFilter: true,
					text: '<i class="fa-solid fa-file-excel"></i> Esporta in Excel',
					sheetName: 'Firme Circolari',
					title: 'Firme Circolari',
				} ,
				{
					extend: 'pdfHtml5',
					download: 'open',
					text: '<i class="fa-solid fa-file-pdf"></i> Crea Pdf',
					title: 'Firme Circolari',
				},
				{
					extend: 'print',
					text: '<i class="fa-solid fa-print"></i> Stampa',
					messageTop: 'Stampa'
				}
			],
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
