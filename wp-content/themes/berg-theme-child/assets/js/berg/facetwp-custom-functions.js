( function ( $ ) {
  //active 'All' checkbox when reset button click
  $( '.reset-filters' ).on( 'click', function () {
    $( '.facet-checkbox-select-all' ).addClass( 'checked' );
  } );

  //on page load hide the reset button
  if ( $( '.facetwp-selection-value' ).length <= 0 ) {
    $( '.reset-selection' ).hide();
  }

  //'All' check box on click action
  $( '.facet-checkbox-select-all' ).on( 'click', event => {
    $( event.target ).addClass( 'checked' );
  } );

  $( document ).on( 'facetwp-loaded', () => {
    /* eslint-disable no-undef */
    let queryString = FWP.buildQueryString();
    /* eslint-enable no-undef */
    if ( '' === queryString ) {
      // no facets are selected
      $( '.reset-selection' ).hide();
    } else {
      $( '.reset-selection' ).show();
    }

    //remove facetwp-all-* class when any facet has selected item
    /* eslint-disable no-undef */
    const facets = FWP.facets;
    /* eslint-enable no-undef */
    $.each( facets, key => {
      if ( facets[key].length ) {
        $( `.facetwp-all-${key}` ).removeClass( 'checked' );
      } else {
        $( `.facetwp-all-${key}` ).addClass( 'checked' );
      }
    } );
  } );
} )( jQuery );
