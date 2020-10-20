require('./bootstrap');

import Vue from 'vue'

Vue.component('upload', require('./components/admin/Upload').default)

window.app = new Vue({
    el: '#app',
})
