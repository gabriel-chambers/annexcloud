// /* eslint-env jquery */
( $ => {
  const accordionBtn = $( '.bs-section--privacy-content .accordion__block__btn' );
  accordionBtn.on( 'click', e => {
    const $this = $( e.target );
    setTimeout( function () {
      let topPosition = $( $this ).offset().top - 120;
      $( 'html,body' ).animate(
        {
          scrollTop: topPosition,
        },
        'slow'
      );
    }, 300 );
  } );
} )( jQuery );
