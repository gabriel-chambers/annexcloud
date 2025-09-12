/* eslint-disable max-len*/
$( ( $ ) => {
  setTimeout( function () {
    const $input = $( '.hs-input' );
    // move label on input focus
    $input
      .on( 'focus', function () {
        $( this ).closest( '.hs-form-field' ).addClass( 'active' );
      } )
      .blur();

    // move label if input is empty or not
    $input
      .focusout( function () {
        if ( !$( this ).val() ) {
          $( this ).closest( '.hs-form-field' ).removeClass( 'active' );
        } else {
          $( this ).closest( '.hs-form-field' ).addClass( 'active' );
        }
      } )
      .focusout();

    $(
      '.hs-fieldtype-text, .hs-fieldtype-textarea, .hs-fieldtype-phonenumber, .hs-fieldtype-select, .hs-fieldtype-number'
    ).each( ( index, element ) => {
      const labelValue = $( element ).find( 'label' );
      $( element ).find( 'input, textarea, select' ).after( labelValue );
    } );
  }, 1500 );
} );
