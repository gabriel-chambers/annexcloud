/* eslint-env jquery */
import debounce from 'lodash/debounce';
( $ => {
  const marqueeRows = $( '.bs-div--r-crlcol-l1f-asn2-logo-row' );
  $( () => {
    let minLogoCount = 1000;
    marqueeRows.each( ( ind, ele ) => {
      const logoCount = $( ele ).find( '.bs-blurb--r-crlcol-l1f-asn2' ).length;
      if ( minLogoCount > logoCount ) {
        minLogoCount = logoCount;
      }
    } );

    marqueeRows.each( ( ind, ele ) => {
      const marqueeRow = $( ele ),
        marqueeDefaultSpeed = marqueeRow.find( '>.bs-div__inner' ).css( 'animation-duration' ),
        marqueeItems = marqueeRow.find( '.bs-blurb--r-crlcol-l1f-asn2' ).parent().parent(),
        marqueeItemsCount = marqueeItems.length,
        marqueeSpeed = ( parseFloat( marqueeDefaultSpeed ) / minLogoCount ) * marqueeItemsCount;
      marqueeRow
        .find( '>.bs-div__inner' )
        .append( marqueeItems.clone() )
        .append( marqueeItems.clone() )
        .addClass( 'start-marquee' )
        .css( 'animation-duration', `${marqueeSpeed}s` );
    } );
  } );

  const debouncedHandlers = () => {
    const logosWrapper = '.bs-div--r-crlcol-l1f-asn2-logo-row > .bs-div__inner';
    marqueeRows
      .on( 'mouseover', '.bs-blurb__trigger', e => {
        const targetElem = $( e.currentTarget );
        const targetElemLink = targetElem.attr( 'href' );
        const parentElem = targetElem.parents( logosWrapper );

        parentElem.toggleClass( 'continue-marquee', targetElemLink === '' );
      } )
      .on( 'mouseleave', '.bs-blurb__trigger', e => {
        const targetElem = $( e.currentTarget );
        const parentElem = targetElem.parents( logosWrapper );

        parentElem.removeClass( 'continue-marquee' );
      } );
  };

  $( window ).on( 'load', debounce( debouncedHandlers, 800 ) );
} )( jQuery );
