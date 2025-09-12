$( $ => {
  const section = $( '.bs-section--common-counter-fix, .bs-div--blog-inner-counter' ),
    card = $( '.bs-section--common-counter-fix .bs-counter, .bs-div--blog-inner-counter .bs-counter' );

  if ( section.length > 0 ) {
    card.each( ( index, element ) => {
      const $this = $( element );
      const textWidth = $this.find( '.bs-counter__count span' ).width();
      $this.find( '.bs-counter__count span' ).css( { width: textWidth + 'px' } );
      $this.find( '.bs-counter__count' ).css( { display: 'flex' } );
    } );
  }
} );
