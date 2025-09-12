jQuery(document).ready(function ($) {
  if( $('[data-opinionstage-content-popup-template]').length > 0 ) {
    var $modal = $('#opinionistage-my-items-page-modal-wrapper')

    function showModal(selectedWidgetData) {
      $modal.find('#opinionstage-widget-shortcode').val(selectedWidgetData.shortcode)
      $modal.fadeIn()
    }

    $('#opinionstage-dialog-close').on('click', function (e) {
      e.preventDefault()
      $modal.fadeOut(function () {
        $(this).find('#opinionstage-widget-shortcode').val('')
      })
    })

    OpinionStage.widgetList.show(showModal)

    $("body").on("click", '[data-copy-text-from]', function (e) {
      e.preventDefault()
      var t = $(this).data().copyTextFrom
      $("[" + t + "]")[0].select()
      document.execCommand("copy")
    })
  }
})
