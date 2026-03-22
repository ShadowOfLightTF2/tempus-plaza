import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/Home.vue';
import Servers from './components/Servers.vue';
import Activity from './components/Activity.vue';
import Maps from './components/Maps.vue';
import Players from './components/Players.vue';
import PlayerPage from './components/PlayerPage.vue';
import MapPage from './components/MapPage.vue';
import Compare from './components/Compare.vue';
import Lookup from './components/Lookup.vue';
import Donate from './components/Donate.vue';
import History from './components/History.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { title: 'Tempus Plaza | TF2 Jump Stats & Leaderboards' },
  },
  {
    path: '/servers/:view?',
    name: 'Servers',
    component: Servers,
    meta: { title: 'Tempus Servers | Tempus Plaza' },
  },
  {
    path: '/activity/:view?',
    name: 'Activity',
    component: Activity,
    meta: { title: 'Tempus Activity Dashboard | Tempus Plaza' },
  },
  {
    path: '/maps/',
    name: 'Maps',
    component: Maps,
    meta: { title: 'Tempus Maps | Tempus Plaza' },
  },
  {
    path: "/maps/:mapId/:type?/:index?",
    name: "MapPage",
    component: MapPage,
    meta: { title: 'Tempus Plaza' },
    props: route => ({
      mapId: Number(route.params.mapId),
      type: route.params.type || "map",
      index: route.params.index ? Number(route.params.index) : null,
    }),
  },
  {
    path: '/players/:category?/:item?',
    name: 'Players',
    component: Players,
    meta: { title: 'Top Tempus Players | Tempus Plaza' },
  },
  {
    path: '/lookup/player/:playerId',
    name: 'LookupPlayer',
    component: Lookup,
    meta: { title: 'Tempus Player Records | Tempus Plaza' },
    props: route => ({ playerId: Number(route.params.playerId) })
  },
  {
    path: '/lookup/map/:mapId',
    name: 'LookupMap',
    component: Lookup,
    meta: { title: 'Tempus Map Records | Tempus Plaza' },
    props: route => ({ mapId: Number(route.params.mapId) })
  },
  {
    path: '/lookup',
    name: 'Lookup',
    component: Lookup,
    meta: { title: 'Tempus Lookup | Search Players and Maps' },
  },
  {
    path: '/history',
    name: 'History',
    component: History,
    meta: { title: 'Tempus Record History | Tempus Plaza' },
  },
  {
    path: '/compare/:playerId1?/:playerId2?/:mapId?',
    name: 'Compare',
    component: Compare,
    meta: { title: 'Compare Tempus Players | Tempus Plaza' },
    props: route => ({
      playerId1: route.params.playerId1 ? Number(route.params.playerId1) : null,
      playerId2: route.params.playerId2 ? Number(route.params.playerId2) : null,
      mapId: route.params.mapId ? Number(route.params.mapId) : null
    })
  },
  {
    path: '/donate',
    name: 'Donate',
    component: Donate,
    meta: { title: 'Support Tempus Plaza' },
  },
  {
    path: '/players/:playerId',
    name: 'PlayerPage',
    component: PlayerPage,
    meta: { title: 'Tempus Plaza' },
    props: route => ({ playerId: Number(route.params.playerId) }),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  strict: false,
  scrollBehavior(to, from, savedPosition) {
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth', block: 'start' }
    }
    if (savedPosition) return savedPosition;
    if (to.name === from.name) return false;
    return { left: 0, top: 0, behavior: 'auto' };
  },
  linkActiveClass: 'active',
  linkExactActiveClass: 'exact-active'
});

router.afterEach((to) => {
  document.title = to.meta?.title ?? 'Tempus Plaza'
});

export default router;