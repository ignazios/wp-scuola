jQuery.noConflict();
(function($) {
    $(function() {	
        $( document ).tooltip({
            content: function(){
                var element = $( this );
                return element.attr('title')
            },
            position: {
                my: "center bottom-20",at: "center top",
                using: function( position, feedback ) {
                    $( this ).css( position );
                    $( "<div>" )
                    .addClass( "arrow" )
                    .addClass( feedback.vertical )
                    .addClass( feedback.horizontal )
                    .appendTo( this );
                }
            }
        });
	});
})(jQuery);
