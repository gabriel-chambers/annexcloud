export const resizeHandler = callback => {
  const mediaQueries = [
    'screen and (max-width: 575px)',
    'screen and (min-width: 576px) and (max-width: 991px)',
    'screen and (min-width: 992px) and (max-width: 1199px)',
    'screen and (min-width: 1200px) and (max-width: 1599px)',
    'screen and (min-width: 1600px)',
  ];

  mediaQueries.forEach( mediaQuery => {
    window.matchMedia( mediaQuery ).addEventListener( 'change', query => {
      if ( query.matches ) {
        callback();
      }
    } );
  } );
};

export const initializeDefaultBergBlock = ( initializeFunction, resizeFunction ) => {
  $( window ).on( 'load', () => {
    if ( typeof initializeFunction === 'function' ) {
      initializeFunction();
    }
    if ( typeof resizeFunction === 'function' ) {
      resizeHandler( resizeFunction );
    }
  } );
};
