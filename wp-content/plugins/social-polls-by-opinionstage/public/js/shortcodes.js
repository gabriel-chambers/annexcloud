jQuery(function($) {
  $('[data-opinionstage-embed-url]').each(function( ) {
    var $widgetEmbedded = $(this)
    var url = $widgetEmbedded.data('opinionstage-embed-url')

    $.getJSON( url ).
      done( function( data ) {
        $widgetEmbedded.after(data.code)
      })
      .fail(function(jqxhr, textStatus, error) {
        console.error( "[social-polls-by-opinionstage] can't load widget embed code" )
      })
  })
})
