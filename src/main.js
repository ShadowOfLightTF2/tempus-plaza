import { createApp } from 'vue';
import { createHead } from '@vueuse/head'
import App from './App.vue';
import router from './router';
import 'bootstrap/dist/js/bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import './assets/global.css';

const app = createApp(App);
const head = createHead();

app.use(head);
app.use(router);
app.mount('#app');
