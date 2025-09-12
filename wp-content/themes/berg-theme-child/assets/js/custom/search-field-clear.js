( $ => {
  const sectionSearchEl = $( '.bs-section--search-results' ),
    btnInner = '<div class="btn-search-clear"></div>';
  sectionSearchEl.find( '.searchform>div' ).prepend( btnInner );

  //clear the form input when button click
  $( '.btn-search-clear' ).on( 'click', ( e ) => {
    e.preventDefault();
    sectionSearchEl.find( '.searchform input[type=text]' ).val( '' );
  } );
} )( jQuery );