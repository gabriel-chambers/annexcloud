$( () => {
  const
    hamburgerTrigger = $( 'header .navbar__toggler' ),
    navDropDown = $( 'header .navbar__inner' ),
    searchPopup = $( '.navbar__search-popup' ),
    searchTrigger = $( '.desktop-search-trigger' ),
    searchCloseButton = $( '.search-icon.close-icon' ),
    menuLevel2 = navDropDown.find( 'li.mega-menu-item-has-children' );

  hamburgerTrigger.on( 'click', ( e ) => {
    e.preventDefault();
    if ( navDropDown.hasClass( 'active' ) ) {
      menuLevel2.addClass( 'mega-toggle-on' );
      navDropDown
        .removeClass( 'active' )
        .find( '.navbar__collapse' )
        .removeClass( 'active' );
    } else {
      navDropDown
        .addClass( 'active' )
        .find( '.navbar__collapse' )
        .addClass( 'active' );
    }
  } );

  searchTrigger.on( 'click', ( e ) => {
    e.preventDefault();
    $( 'html, body' ).css( { 'overflow': 'hidden' } );
    searchPopup.addClass( 'active' );
    searchCloseButton.on( 'click', () => {
      $( 'html, body' ).css( { 'overflow': 'unset' } );
      searchPopup.removeClass( 'active' );
    } );
  } );
} );
