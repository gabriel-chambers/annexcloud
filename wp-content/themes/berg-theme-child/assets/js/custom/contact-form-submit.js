$( '.bs-section--about-contact' ).bind( 'DOMSubtreeModified', function () {
  if ( $( '.submitted-message' ).length > 0 ) {
    const input = $( this ).find( '.bs-embedded-forms' );
    input.closest( '.bs-section--about-contact' ).next().addClass( 'remove-margin-top' );
  }
} );
