jQuery(document).ready(function ($) {
  $('body').on('click', '[data-open-popup-for-widget]', function (e) {
    e.preventDefault()
    var wpWidgetId = $(this).data('open-popup-for-widget')
    OpinionStage.contentPopup.open({
      onWidgetSelect: function (opinionstageWidget) {
        var osWidgeteFieldsWrapper = $('[data-os-widget-id="' + wpWidgetId + '"]')
        if (osWidgeteFieldsWrapper.length > 0) {
          osWidgeteFieldsWrapper.find('.opinionstage-widget-data').val(JSON.stringify(opinionstageWidget)).trigger('change')
          osWidgeteFieldsWrapper.find('.opinionstage-selected-widget').show()
          osWidgeteFieldsWrapper.find('.opinionstage-widget-title').text(opinionstageWidget.title)
          osWidgeteFieldsWrapper.find('.opininstage-view').attr('href', opinionstageWidget.landingPageUrl)
          osWidgeteFieldsWrapper.find('.opininstage-stats').attr('href', opinionstageWidget.statsUrl)
          osWidgeteFieldsWrapper.find('.opininstage-edit').attr('href', opinionstageWidget.editUrl)

          if (opinionstageWidget.imageUrl) {
            osWidgeteFieldsWrapper.find('.opinionstage-widget-img-url-wrapper .inner').html('<img src="' + opinionstageWidget.imageUrl + '">')
          }
        }
      }
    })
  })
})
