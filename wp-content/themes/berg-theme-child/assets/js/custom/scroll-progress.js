$( $ => {
  const blogProgress = $( '.bs-section--common-scroll-progress' );
  const progressFunc = () => {
    let elHeight = 0;

    $( '.bs-section--progress-content-wrapper' ).each( function () {
      elHeight += $( this ).height();
    } );

    const windowScroll = $( window ).scrollTop();
    const windowHeight = $( window ).height();
    const scrollHeight = elHeight;
    const scrolledPer = ( windowScroll / ( scrollHeight - windowHeight ) ) * 100;
    $( '.blog-inner-progress__bar' ).css( 'width', `${scrolledPer}%` );
  };

  $( window ).on( 'load scroll', progressFunc );

  if ( blogProgress.length !== 0 ) {
    const progressBarEl = '<div class="blog-inner-progress"><span class="blog-inner-progress__bar"></span></div>';
    blogProgress.prepend( progressBarEl );
  }
} );
