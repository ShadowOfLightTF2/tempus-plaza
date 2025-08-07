import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/Home.vue';
import Servers from './components/Servers.vue';
import Activity from './components/Activity.vue';
import Leaderboard from './components/Leaderboard.vue';
import Maps from './components/Maps.vue';
import Players from './components/Players.vue';
import PlayerPage from './components/PlayerPage.vue';
import MapPage from './components/MapPage.vue';
import Compare from './components/Compare.vue';
import Lookup from './components/Lookup.vue';
import Donate from './components/Donate.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/servers/:view?',
    name: 'Servers',
    component: Servers,
  },
  {
    path: '/activity/:view?',
    name: 'Activity',
    component: Activity,
  },
  {
    path: '/maps/',
    name: 'Maps',
    component: Maps,
  },
  {
    path: '/maps/:mapId',
    name: 'MapPage',
    component: MapPage,
    props: route => ({
      mapId: Number(route.params.mapId)
    }),
  },
  {
    path: '/players/:category?/:item?',
    name: 'Players',
    component: Players
  },
  {
    path: '/lookup/:playerId?',
    name: 'Lookup',
    component: Lookup,
    props: route => ({
      playerId: Number(route.params.playerId)
    })
  },
  {
    path: '/compare/:playerId1?/:playerId2?/:mapId?',
    name: 'Compare',
    component: Compare,
    props: route => ({
      playerId1: route.params.playerId1 ? Number(route.params.playerId1) : null,
      playerId2: route.params.playerId2 ? Number(route.params.playerId2) : null,
      mapId: route.params.mapId ? Number(route.params.mapId) : null
    })
  },
  {
    path: '/donate',
    name: 'Donate',
    component: Donate
  },
  {
    path: '/players/:playerId',
    name: 'PlayerPage',
    component: PlayerPage,
    props: route => ({
      playerId: Number(route.params.playerId)
    }),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  strict: false,
  scrollBehavior() { return { top: 0 }; },
  linkActiveClass: 'active',
  linkExactActiveClass: 'exact-active'
});

export default router;
