import '../../node_modules/select2/dist/js/select2';

$( () => {
  $( 'select.multi-select' ).select2( {
    placeholder: '',
    closeOnSelect: false,
  } );

  $( '.resource-search .form-group' ).each( function () {
    $( this ).find( '.select2-search > input' ).hide();
    $( this )
      .find( '.select2-search' )
      .append( '<span>' + $( this ).children( 'label' ).text() + '</span>' );
  } );

  /* 2021-07-21 commented by Amith */
  /* eslint-disable */
  /* same according function overwrite in 
  plugins/berg/src/block/post-block/inc/views/layouts/partial/multi-select-filter-with-load-more.php & 
  plugins/berg/src/block/post-block/inc/views/layouts/partial/multi-select-filter-with-pagination.php */
  /* eslint-enable */
} );
