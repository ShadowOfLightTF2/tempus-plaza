import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/Home.vue';
import Activity from './components/Activity.vue';
import Leaderboard from './components/Leaderboard.vue';
import Maps from './components/Maps.vue';
import Players from './components/Players.vue';
import PlayerPage from './components/PlayerPage.vue';
import MapPage from './components/MapPage.vue';
import Lookup from './components/Lookup.vue';
import Donate from './components/Donate.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/activity',
    name: 'Activity',
    component: Activity,
  },
  {
    path: '/maps',
    name: 'Maps',
    component: Maps,
  },
  {
    path: '/maps/:mapId',
    name: 'MapPage',
    component: MapPage,
    props: route => ({
      mapId: Number(route.params.mapId)
    })
  },
  {
    path: '/maps/:mapId/records',
    name: 'Records',
    component: Leaderboard,
    props: route => ({
      mapId: Number(route.params.mapId)
    })
  },
  {
    path: '/players/:category?/:item?',
    name: 'Players',
    component: Players
  },
  {
    path: '/lookup',
    name: 'Lookup',
    component: Lookup
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
    })
  },
];

const router = createRouter({
  history: createWebHistory('/tempus-plaza/'),
  routes,
});

export default router;
