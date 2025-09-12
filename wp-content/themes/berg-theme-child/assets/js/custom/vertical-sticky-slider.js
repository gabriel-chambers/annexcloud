( $ => {
  const section = $( '.bs-section--home-built-for-lifetime' ), // section
    items = $( section ).find( '.bs-advance-accordion__right-container .card' ), // each content section
    images = $( section ).find( '.bs-advance-accordion__left-content-panel' ); // sticky wrapper

  if ( section.length ) {
    // function
    const scrollFunc = ( media, contents ) => {
      if ( media.matches ) {
        // add hide class to each element
        images.addClass( 'hide' );

        // default active item
        $( items ).each( index => {
          if ( index === 0 ) {
            $( this ).addClass( 'active' );
            $( images[index] ).removeClass( 'hide' ).addClass( 'visible' );
          }
        } );

        if ( $( section ).length === 1 ) {
          contents.each( ( index, item ) => {
            const menuInf = item.getBoundingClientRect();
            const reducedAmount = $( window ).width() > 1920 ? 230 : 0;
            const eleCenter = $( window ).height() / 2 - reducedAmount;
            if ( eleCenter >= menuInf.top ) {
              $( items ).removeClass( 'active' );
              $( items[index] ).addClass( 'active' );
              $( images ).removeClass( 'visible' ).addClass( 'hide' );
              $( images[index] ).removeClass( 'hide' ).addClass( 'visible' );
            }
          } );
        }
      }
    };

    // create media object to check window width
    const mediaObject = window.matchMedia( '(min-width: 992px)' );

    scrollFunc( mediaObject, items );

    // scroll
    $( window ).on( 'scroll', () => {
      scrollFunc( mediaObject, items );
    } );

    // page load
    mediaObject.addEventListener( 'change', () => {
      scrollFunc( mediaObject, items );
    } );
  }
} )( jQuery );
