import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Vue dashboard mount
import { createApp } from 'vue';
import DashboardApp from './dashboard/DashboardApp.vue';

function mountDashboardChart() {
  const el = document.getElementById('vue-dashboard-root');
  if (!el) return;
  const app = createApp(DashboardApp, {
    labels: JSON.parse(el.dataset.labels || '[]'),
    data: JSON.parse(el.dataset.series || '[]'),
  });
  app.mount('#vue-dashboard-root');
}

function mountStagger() {
  const root = document.getElementById('vue-dashboard-stagger');
  if (!root) return;
  createApp({
    data() {
      return {
        show: {
          infoBoxes: false,
          revenue: false,
          sales: false,
          lowStock: false,
          recentOrders: false,
          recentLogins: false,
        },
      };
    },
    mounted() {
      const delays = {
        infoBoxes: 1000,
        revenue: 350,
        sales: 650,
        lowStock: 900,
        recentOrders: 2000,
        recentLogins: 150,
      };
      Object.entries(delays).forEach(([k, base]) => {
        const jitter = Math.floor(Math.random() * 120);
        setTimeout(() => { this.show[k] = true; }, base + jitter);
      });
    },
  }).mount('#vue-dashboard-stagger');
}

function init() {
  // Mount parent container first so it doesn't overwrite the chart subtree
  mountStagger();
  mountDashboardChart();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}