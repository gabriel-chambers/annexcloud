import Vue from 'vue'
import debounce from 'lodash.debounce'

import {
  WIDGET_ALL,
  WIDGET_POLL,
  WIDGET_SET,
  WIDGET_SURVEY,
  WIDGET_SLIDESHOW,
  WIDGET_TRIVIA_QUIZ,
  WIDGET_PERSONALITY_QUIZ,
  WIDGET_LIST,
  WIDGET_STORY,
} from '../widget-types.js'

Vue.use(require('vue-moment'))

const selectedWidgetTitles = {}
selectedWidgetTitles[WIDGET_ALL] = 'All Items'
selectedWidgetTitles[WIDGET_POLL] = 'poll'
selectedWidgetTitles[WIDGET_SET] = 'multi poll set'
selectedWidgetTitles[WIDGET_SURVEY] = 'form / survey'
selectedWidgetTitles[WIDGET_SLIDESHOW] = 'slideshow'
selectedWidgetTitles[WIDGET_TRIVIA_QUIZ] = 'trivia quiz'
selectedWidgetTitles[WIDGET_PERSONALITY_QUIZ] = 'personality quiz'
selectedWidgetTitles[WIDGET_LIST] = 'list'
selectedWidgetTitles[WIDGET_STORY] = 'story article'

export default Vue.component('widget-list', {
  template: '#opinionstage-widget-list',

  props: [
    'widgets',
    'preSelectedWidgetType',
    'dataLoading',
    'noMoreData',
    'showSearch',
  ],

  data() {
    return {
      selectedWidgetType: null,
      widgetTitleSearch: '',
      showMoreBtn: true,
      hasData: true,
      selectedDraftWidget: {}
    }
  },

  computed: {
    selectedWidgetTitle() {
      return selectedWidgetTitles[this.selectedWidgetType || this.preSelectedWidgetType]
    },
  },

  watch: {
    widgetTitleSearch: debounce(function () {
      widgetsSearchUpdate.call(this)
    }, 500),

    widgets() {
      this.hasData = this.dataLoading || this.widgets.length > 0
    },
  },

  methods: {
    select(widget) {
      if (widget.isDraft) {
        this.selectedDraftWidget = widget
      } else {
        this.$emit('widget-selected', widget)
      }
    },

    selectWidgetType(type) {
      this.selectedWidgetType = type
      this.widgetTitleSearch = ''

      widgetsSearchUpdate.call(this)
    },

    showMore() {
      this.$emit('load-more-widgets')
    },
  },
})

function widgetsSearchUpdate() {
  this.$emit('widgets-search-update', {
    widgetType: this.selectedWidgetType,
    widgetTitle: this.widgetTitleSearch
  })
}
