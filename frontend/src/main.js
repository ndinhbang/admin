import Vue from 'vue'
import App from './App.vue'
import router from './router/index'
import store from './store/index'

// Global Exception Handler
import '@/utils/exceptionHandler'
// PWA
import '@/registerServiceWorker'
// Register Element UI components
import '@/plugins/element.js'
// CSS reset
import 'normalize.css/normalize.css'
// Custom styles
import './styles/index.scss'

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
