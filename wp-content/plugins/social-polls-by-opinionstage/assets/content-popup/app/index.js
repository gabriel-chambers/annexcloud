import Vue from 'vue'
import './components/popup-content.js'
import './components/widget-list.js'
import './components/notification.js'
import '../styles/content-popup.scss'

import {
  WIDGET_ALL,
} from './widget-types.js'

export default function (modal) {
  return new Vue({
    el: '[data-opinionstage-content-popup]',

    data: {
      widgetType: WIDGET_ALL,
      widgetSelectCb: function (widget) {
        console.log('dumb widget insert callback, widget:', widget)
      },
      isClientLoggedIn: null,
      isModalOpened: false,
      isMyItemsPage: false,
    },

    beforeMount() {
      this.isClientLoggedIn = this.$el.dataset.opinionstageClientLoggedIn === '1'
    },

    methods: {
      closePopup(/*event*/) {
        if (modal) {
          modal.close()
        }
      },

      selectWidgetAndExit(widget) {
        this.widgetSelectCb(widget)

        this.closePopup()
      },
    },
  })
}
