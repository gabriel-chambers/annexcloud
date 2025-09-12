import 'select2';

$( () => {
  $( 'body' ).on( 'click', 'a.widget-option', function () {
    setTimeout( function () {
      $( '.post-selector-widget-select2' ).select2( {
        dropdownParent: $( '#cboxLoadedContent' ),
      } );
    }, 1000 );
  } );
} );
