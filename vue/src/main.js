import Vue from 'vue'
import Taikor from './components/Taikor.vue'
import './registerServiceWorker'

Vue.config.productionTip = false

new Vue({
  render: h => h(Taikor),
}).$mount('#app')
