( $ => {
  //To slide between first slide and last slide smoothly
  const slider = $( '[data-slick]' );
  slider.on( 'beforeChange', ( e, slick, currentSlide, nextSlide ) => {
    const slidesCount = slick.$slides.length,
      cls =
                'slick-current slick-active' +
                ( slick.options.centerMode ? ' slick-center' : '' );

    let selector = false;
    if ( nextSlide === 0 ) {
      selector = `[data-slick-index="0"], [data-slick-index="${slidesCount}"]`;
    } else if ( nextSlide === slidesCount - 1 ) {
      selector = `[data-slick-index="-1"], [data-slick-index="${
        slidesCount - 1
      }"]`;
    }

    if ( !selector ) return;
    setTimeout( () => {
      $( '.slick-slide', slick.$slider ).removeClass( cls );
      $( selector, slick.$slider ).addClass( cls );
    }, 10 );
  } );
} )( jQuery );