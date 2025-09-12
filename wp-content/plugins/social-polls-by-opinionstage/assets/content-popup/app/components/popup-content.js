import Vue from 'vue'
import RSVP from 'rsvp'
import isEmpty from 'lodash.isempty'
import join from 'lodash.join'

import store from '../store.js'
import JsonApi from '../../lib/jsonapi.js'
import {
  WIDGET_ALL,
} from '../widget-types.js'

export default Vue.component('popup-content', {
  template: '#opinionstage-popup-content',

  props: [
    // dynamic properties:
    'clientIsLoggedIn',
    'modalIsOpened',
    'widgetType',
    'isMyItemsPage',
    // static properties:
    'clientWidgetsUrl',
    'clientWidgetsHasNewUrl',
    'accessKey',
    'pluginVersion',
  ],

  data() {
    return {
      dataLoading: false,
      widgets: [],
      searchCriteria: {},
      noMoreData: true,
      newWidgetsAvailable: false,
      lastUpdateTime: null,
      isCheckingWidgetUpdates: false,
      widgetUpdatesChecker: null,
    }
  },

  store,

  methods: {
    reloadData(search) {
      if (!search) {
        search = {}
      }
      const widgetType = search.widgetType || this.widgetType || this.searchCriteria.type || WIDGET_ALL
      const widgetTitle = search.widgetTitle || ''

      this.newWidgetsAvailable = false
      stopWidgetUpdatesChecking.call(this)
      this.searchCriteria = {page: 1, perPage: 9, type: widgetType, title: widgetTitle}
      this.$store.commit('clearWidgets')

      loadData.call(this).then(() => {
        this.widgets = this.$store.state.widgets[0]
        if (!this.searchCriteria.title) {
          setLastUpdateTimeFromWidget.call(this)
        }

        startWidgetUpdatesChecking.call(this)
      })
    },

    reloadAndRestartCheckingForUpdates() {
      this.reloadData({widgetType: this.searchCriteria.type, widgetTitle: ''})
    },

    appendData() {
      this.searchCriteria.page += 1

      loadData.call(this).then(() => {
        const newWidgets = this.$store.state.widgets[this.searchCriteria.page - 1]
        this.widgets = this.widgets.concat(newWidgets)
      })
    },

    widgetSelected(widget) {
      this.$emit('widget-selected', widget)
    },
  },

  watch: {
    modalIsOpened(isOpened) {
      if (isOpened && this.clientIsLoggedIn) {
        this.reloadData()
      } else {
        this.newWidgetsAvailable = false
        stopWidgetUpdatesChecking.call(this)
      }
    },
  },
})

function loadData() {
  this.dataLoading = true

  return loadClientWidgets.call(this, this.searchCriteria).then(() => {
    this.noMoreData = !hasNextPage(this.$store.state.nextPageNumber)
    this.dataLoading = false
  })
}

function loadClientWidgets(filtering) {
  if (this.clientIsLoggedIn) {
    return this.$store.dispatch({
      type: 'loadClientWidgets',
      widgetsUrl: this.clientWidgetsUrl,
      pluginVersion: this.pluginVersion,
      accessToken: this.accessKey,
      filtering,
    })
  } else {
    return RSVP.resolve()
  }
}

function withParams(url, type, time) {
  const urlParams = []
  if (type) {
    urlParams.push(`type=${type}`)
  }

  if (time) {
    urlParams.push(`updated_at=${time}`)
  }

  if (isEmpty(urlParams)) {
    return url
  } else {
    return url + '?' + join(urlParams, '&')
  }
}

function pullWidgetsUpdateInformation(type, updatedAt) {
  const url = withParams(this.clientWidgetsHasNewUrl, type, updatedAt)

  return JsonApi.get(url, this.pluginVersion, this.accessKey)
    .then((payload) => {
      this.newWidgetsAvailable = payload.data['has-new-widgets']
    })
    .catch((error) => {
      console.error("[social-polls-by-opinionstage][content-popup] can't load widgets:", error.statusText)
    })
}

function hasNextPage(nextPageNumber) {
  return nextPageNumber > 1
}

function checkWidgetUpdates() {
  pullWidgetsUpdateInformation.call(this, this.searchCriteria.type, this.lastUpdateTime).then(() => {
    if (this.newWidgetsAvailable) {
      stopWidgetUpdatesChecking.call(this)
    }
  })
}

function startWidgetUpdatesChecking() {
  if (this.clientIsLoggedIn) {
    this.isCheckingWidgetUpdates = true
    this.widgetUpdatesChecker = setInterval(checkWidgetUpdates.bind(this), 3000)
  }
}

function stopWidgetUpdatesChecking() {
  this.isCheckingWidgetUpdates = false
  clearInterval(this.widgetUpdatesChecker)
}

function setLastUpdateTimeFromWidget() {
  if (typeof this.widgets[0] !== 'undefined') {
    this.lastUpdateTime = this.widgets[0].updatedAt
  } else {
    this.lastUpdateTime = null
  }
}
