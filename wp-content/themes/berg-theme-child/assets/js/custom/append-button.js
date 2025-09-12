/* eslint-env jquery */
import debounce from 'lodash/debounce';
( $ => {
  const debouncedHandlers = () => {
    const button = $( '.r-crlcol-l1f--asn2 span.bs-pro-button' );
    const elementLeft = $( '.r-crlcol-l1f--asn2 .bs-column--col-one > .bs-div > .bs-div__inner' );
    const elementRight = $( '.r-crlcol-l1f--asn2 .bs-column--col-two' );

    const handleMediaQuery = mq => {
      if ( mq.matches ) {
        elementRight.append( button );
      } else {
        elementLeft.append( button );
      }
    };

    const mediaQuery = window.matchMedia( '(max-width: 992px)' );
    handleMediaQuery( mediaQuery );
    mediaQuery.addEventListener( 'change', e => {
      handleMediaQuery( e.currentTarget );
    } );
  };
  $( window ).on( 'load resize', debounce( debouncedHandlers ) );
} )( jQuery );
