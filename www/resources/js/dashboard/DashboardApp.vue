<template>
  <div class="h-64 md:h-80">
    <canvas ref="chartEl" class="w-full h-full"></canvas>
  </div>
  
</template>

<script setup>
import { onMounted, ref, watch, nextTick } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  labels: { type: Array, default: () => [] },
  data: { type: Array, default: () => [] },
});

const chartEl = ref(null);
let chart;

function render() {
  if (!chartEl.value) return;
  if (chart) chart.destroy();
  chart = new Chart(chartEl.value.getContext('2d'), {
    type: 'line',
    data: {
      labels: props.labels,
      datasets: [{
        label: 'Sales',
        data: props.data,
        borderColor: 'rgb(59, 130, 246)',
        backgroundColor: 'rgba(59, 130, 246, 0.15)',
        fill: true,
        tension: 0.3,
        pointRadius: 2,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: { y: { beginAtZero: true } },
      plugins: { legend: { display: false } }
    }
  });
}

onMounted(() => nextTick(render));
watch(() => [props.labels, props.data], render);
</script>

<style scoped>
</style>


