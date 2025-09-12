( $ => {
  $( '.bs-blurb--loyalty-item-common' ).each( ( index, element ) => {
    const $this = $( element ),
      $title = $this.find( '.bs-blurb__container .bs-blurb__title' ),
      $description = $this.find( '.bs-blurb__container .bs-blurb__description' ),
      $wrapper = $( '<div class="bs-blurb__content-wrapper"></div>' ).append( $title,$description );
    $this.find( '.bs-blurb__container' ).append( $wrapper );

    /**
     * on hover add class
     */
    const $contentWrapper = $this.find( '.bs-blurb__content-wrapper' );
    
    $contentWrapper.on( 'mouseover', () => {
      $description.addClass( 'expanded' );
    } );

    $contentWrapper.on( 'mouseout', () => {
      $description.removeClass( 'expanded' );
    } );
  } );

  /**
   *  append content wrapper div for wrap title and description
   */
  if( $( '.bs-section--home-loyalty-sm .slick-slider' ).length ){

    //after slider init, update slider inner element 
    $( '.bs-section--home-loyalty-sm .slick-slider' ).on( 'init', () => {

      $( '.bs-section--home-loyalty-sm .slick-slide' ).each( ( index, element ) => {
        const $this = $( element ),
          $titleSm = $this.find( '.bs-blurb__container .bs-blurb__title' ),
          $descriptionSm = $this.find( '.bs-blurb__container .bs-blurb__description' ),
          $wrapperInnerSm = $( '<div class="bs-blurb__content-wrapper"></div>' ).append( $titleSm,$descriptionSm );
        $this.find( '.bs-blurb__container' ).append( $wrapperInnerSm );

      } );
    }
    );
  }

} )( jQuery );