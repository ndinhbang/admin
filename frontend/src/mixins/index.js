import Vue from 'vue'

import currentUser from '@/mixins/currentUser'
import jumpTo from '@/mixins/jumpTo'

Vue.mixin(currentUser)
Vue.mixin(jumpTo)
