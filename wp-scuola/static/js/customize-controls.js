/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function() {

	wp.customize.bind( 'ready', function() {

		wp.customize.control( 'scuola_docconteggio_reset', function( control ) {
		    control.container.find( '.button' ).on( 'click', function() {
		        jQuery.ajax({type: 'POST', url: ajaxurl, 
			        data:{
			            action:'ResetCounter',
			        },
			        success: function(risposta){
			        	alert(risposta);
			        },                   
			        error: function (xhr, ajaxOptions, thrownError) {
		        		alert(xhr.status+ " "+thrownError);
			        }
		        }); 						
		    } );
		} );
	});

})( jQuery );
