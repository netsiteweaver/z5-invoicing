import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Vue dashboard mount
import { createApp } from 'vue';
import DashboardApp from './dashboard/DashboardApp.vue';

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('vue-dashboard-root');
  if (el) {
    createApp(DashboardApp, {
      labels: JSON.parse(el.dataset.labels || '[]'),
      data: JSON.parse(el.dataset.series || '[]')
    }).mount('#vue-dashboard-root');
  }

  const staggerRoot = document.getElementById('vue-dashboard-stagger');
  if (staggerRoot) {
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
        // Varied, slightly randomized delays for a more organic feel
        const delays = {
          infoBoxes: 1000,
          revenue: 350,
          sales: 650,
          lowStock: 900,
          recentOrders: 2000,
          recentLogins: 150,
        };
        Object.entries(delays).forEach(([k, base]) => {
          const jitter = Math.floor(Math.random() * 120); // 0-119ms
          setTimeout(() => { this.show[k] = true; }, base + jitter);
        });
      },
    }).mount('#vue-dashboard-stagger');
  }
});