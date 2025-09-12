import Vue from 'vue'

export default Vue.component('notification', {
  template: '#opinionstage-notification',

  methods: {
    initiateUpdate() {
      this.$emit('update-btn-click')
    },
  }
})
