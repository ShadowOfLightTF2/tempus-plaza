import { createApp } from 'vue';
import { createHead } from '@vueuse/head'
import App from './App.vue';
import router from './router';
import SmartLink from './components/SmartLink.vue'
import * as bootstrap from 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import './assets/global.css';

window.bootstrap = bootstrap;

const app = createApp(App);
const head = createHead();

app.use(head);
app.use(router);
app.component('SmartLink', SmartLink)
app.mount('#app');
