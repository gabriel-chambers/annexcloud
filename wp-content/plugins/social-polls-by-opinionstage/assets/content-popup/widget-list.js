import ContentPopupContent from './app/index.js'
import {
  WIDGET_ALL,
} from './app/widget-types.js'

if (window.OpinionStage && typeof (OpinionStage.contentPopup) !== 'undefined') {
  console.warn('[OpinionStage] content-popup APIs was already included')
}

;(function (OS, $) {
  class WidgetList {
    constructor() {
      // vuejs vm instance
      this.app = undefined
    }

    show(onWidgetSelect) {
      if (typeof (onWidgetSelect) !== 'function') {
        onWidgetSelect = function () {
        }
      }

      const content = $('[data-opinionstage-content-popup-template]').html()
      $('[opinionstage-my-items-view]').html(content)

      if (!this.app) {
        this.app = new ContentPopupContent()
      }


      this.app.widgetType = WIDGET_ALL
      this.app.isModalOpened = true
      this.app.isMyItemsPage = true
      this.app.widgetSelectCb = onWidgetSelect
    }
  }

  OS.widgetList = new WidgetList()

  OS.widgetList.WIDGET_ALL = WIDGET_ALL
})(window.OpinionStage = window.OpinionStage || {}, jQuery)

