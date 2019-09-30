import Vue from 'vue'
import 'normalize.css/normalize.css' // a modern alternative to CSS resets

import App from './App.vue'
import router from './router'
import store from './store'
import './registerServiceWorker'
import './plugins/element.js'

import './scss/style.scss'

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
