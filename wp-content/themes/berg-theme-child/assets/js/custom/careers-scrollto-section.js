( $ => {
  const scrollBtn = $( '.bs-section--careers-banner .bs-pro-button' );
  scrollBtn.on( 'click', e => {
    const $this = $( '#current-job' );
    const topPosition = $( $this ).offset().top - 60;
    e.preventDefault();
    $( 'html,body' ).animate(
      {
        scrollTop: topPosition,
      },
      800
    );
  } );
} )( jQuery );
