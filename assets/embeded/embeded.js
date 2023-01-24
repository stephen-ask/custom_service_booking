jQuery( function( $ )
{
	'use strict';

	$( '.rwmb-oembed-wrapper .spinner' ).hide();

	$( 'body' ).on( 'click', '.rwmb-oembed-wrapper .show-embed', function() {
		jQuery('.embed-code').show();
		var $this = $( this ),
			$spinner = $this.siblings( '.spinner' ),
			data = {
				action: 'rwmb_getembededcode',
				url: $this.siblings( 'input' ).val()
			};

		$spinner.show();
		$.post( ajaxurl, data, function( r )
		{
			$spinner.hide();
			$this.siblings( '.embed-code' ).html( r.data );
		}, 'json' );

		return false;
	} );
} );
