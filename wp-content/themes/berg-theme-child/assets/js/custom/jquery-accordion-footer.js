/* eslint-env jquery */
( $ => {
  $( 'footer' ).on( 'click', '.menu-item-has-children > a', e => {
    if ( window.matchMedia( '(max-width: 767px)' ).matches ) {
      const $this = $( e.target );
      e.preventDefault();
      $this.closest( 'li' ).toggleClass( 'active-menu-item' );
      if ( !$this.closest( 'li' ).hasClass( 'active-menu-item' ) ) {
        $this.closest( 'li' ).children( '.sub-menu' ).slideUp( 600 );
      } else {
        // slide up all expanded lists
        $( '.menu-item-has-children' ).closest( 'li' ).children( '.sub-menu' ).slideUp( 600 );
        $( '.active-menu-item' ).removeClass( 'active-menu-item' );

        // slide down target only
        $this.closest( 'li' ).addClass( 'active-menu-item' );
        $this.closest( 'li' ).children( '.sub-menu' ).slideDown( 600 );
      }
    }
  } );

  $( window ).on( 'resize', e => {
    $( 'footer .menu-item' ).each( ( index, element ) => {
      if ( $( e.target ).width() > 768 ) {
        $( element ).children( '.sub-menu' ).removeAttr( 'style' );
      } else if ( $( element ).hasClass( 'active-menu-item' ) ) {
        $( element ).children( '.sub-menu' ).show();
      }
    } );
  } );
} )( jQuery );
